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
    return response('Not found', 404);
});

// Authentication routes
$router->post('/auth', 'AuthenticationController@login');
$router->post('/register', 'AuthenticationController@register');
$router->delete('/auth', 'AuthenticationController@logout');

// Search routes
$router->get('/search', 'HomeController@search');

// History routes
$router->get('/history', 'HomeController@history');

// Favorite routes
$router->get('/favorite', 'FavoriteController@getAll');
$router->get('/favorite/{id}/is-favorite', 'FavoriteController@isFavorite');
$router->post('/favorite', 'FavoriteController@create');
$router->delete('/favorite/{id}', 'FavoriteController@delete');