<?php

use Caramel\Core\Middleware\Auth;
use Caramel\Core\Middleware\Csrf;
use Caramel\Core\Middleware\Guest;

$router->get('/', 'IndexController@index')
        ->named('home');


$router->group(['prefix' => 'posts'], function($router) {
    $router->get('/', 'PostController@index')
        ->named('posts.index');

    $router->get('/create', 'PostController@create')
        ->only(Auth::class)
        ->only(Csrf::class);
    $router->post('/', 'PostController@store')
        ->named('posts.store')
        ->only(Csrf::class);

    $router->get('/{id:\d+}', 'PostController@show')
        ->named('post.show');
});



$router->get('/register', 'RegisterController@create')->only(Guest::class);
$router->post('/register', 'RegisterController@store');

$router->get('/login', 'LoginController@index')
    ->only(Guest::class)
    ->named('auth.login');
$router->post('/login', 'LoginController@login');