<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

use \Firebase\JWT\JWT;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            if ($authorization = $request->header('Authorization')) {
                // Split authorization header in format "Bearer {{ token }}"
                $splittedHeader = explode(' ', $authorization);
                
                // If header isn't in the right format, return unauthorized
                if (count($splittedHeader) !== 2) {
                    return null;
                } 
                
                // Get the token from splitted header
                $token = $splittedHeader[1];

                try {
                    // Decode the token and get the user
                    $decodedToken = JWT::decode($token, env('APP_KEY'), [ 'HS512' ]);
                    return $decodedToken->user;
                } catch (\Exception $e) {
                    return null;
                }
            }
        });
    }
}
