<?php

// use App\Http\Controllers;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// Authentication routes
$router->post('/auth', 'AuthenticationController@login');
$router->delete('/auth', 'AuthenticationController@logout');

// Search routes
$router->get('/search', 'HomeController@search');

// History routes
$router->get('/history', 'HomeController@history');

// Favorite routes
$router->get('/favorite', 'FavoriteController@getAll');
$router->post('/favorite', 'FavoriteController@create');
$router->delete('/favorite/{id}', 'FavoriteController@delete');