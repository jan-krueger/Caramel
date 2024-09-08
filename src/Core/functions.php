<?php

use Caramel\Core\App;
use Caramel\Core\Collection\Collection;
use Caramel\Core\Router;
use Caramel\Core\Session;
use Caramel\Model\User;

function dd(...$values) 
{

    foreach($values as $value)
    {
        echo "<pre>";
        var_dump($value);
        echo "</pre>";
    }
    die();
}


function base_path(string $path = "")
{
    return BASE_PATH . $path;
}

function user(): ?User
{
    static $auth_user = null;
    if(isset($_SESSION['user']))
    {

        if(!$auth_user)
        {
            $auth_user = User::find($_SESSION['user']['id']);
        }
        return $auth_user;
    }

    return null;
}

function guest(): bool
{
    return !user();
}

function authenticated(): bool
{
    return !guest();
}

function app(): App
{
    static $app = null;
    if(!$app) $app = new App();
    return $app;
}

function route(string $name, array $arguments = [])
{
    return app()->container()->resolve(Router::class)->get_named_route($name, $arguments);
}

function collect(array $data = [])
{
    return new Collection($data);
}

function csrf()
{
    return Session::get('_csrf_token');
}

function old(string $field, $default = null): mixed
{
    return Session::get("_flash._old.{$field}", $default);
}

function error(string $field): mixed
{
    return Session::get("_flash._errors.{$field}");
}