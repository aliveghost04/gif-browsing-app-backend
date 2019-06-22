<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\User;
use App\Log;
use \Firebase\JWT\JWT;

class AuthenticationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //
    public function login(Request $request) {

        $email = $request->input('email');
        $password = $request->input('password');

        // Query for the user in the database
        $user = User::where('email', $email)
            ->first();
        
        $isValid = false;
        if ($user) {
            $isValid = password_verify($password, $user->password);
        }
        
        if ($isValid) {
            // Log the user login action
            Log::create([
                'user_id' => $user->id,
                'action' => 'login',
                'ip' => $request->ip()
            ]);

            $time = time();
            $payload = [
                'iss' => env('APP_URL'),
                'iat' => $time,
                'exp' => $time + intval(env('JWT_EXPIRE_TIME')),
                'nbf' => $time,
                'user' => $user,
            ];

            // Encode and create JWT
            $token = JWT::encode($payload, env('APP_KEY'), 'HS512');
            return json_encode([
                'token' => $token
            ]);
        } else {
            return response(json_encode([
                'error' => 'Invalid username or password'
            ]), 401);
        }
    }

    public function logout(Request $request) {
        // Log the user logout action
        if ($user = $request->user()) {
            Log::create([
                'user_id' => $user->id,
                'action' => 'logout',
                'ip' => $request->ip()
            ]);
        }

        return response('', 204);
    }

    public function register(Request $request) {
        $user = $request->all();

        $user['first_name'] = $user['firstName'];
        $user['last_name'] = $user['lastName'];
        $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
        
        try {
            DB::beginTransaction();
            $userCreated = User::create($user);
            Log::create([
                'action' => 'register',
                'ip' => $request->ip(),
                'user_id' => $userCreated->id
            ]);
            DB::commit();

            return response('', 204);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Something wrong happened, please try again later'
            ], 500);
        }
    }
}
