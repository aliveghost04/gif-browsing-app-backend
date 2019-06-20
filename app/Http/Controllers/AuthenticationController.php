<?php

namespace App\Http\Controllers;

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
            ->where('password', $password)
            ->first();

        if ($user) {
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
}
