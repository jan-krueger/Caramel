<?php

namespace Caramel\Core\Middleware;

use Caramel\Core\Http\Request;

abstract class Middleware
{

    public abstract function handle(Request $request);

    public static function resolve(string $class, Request $request)
    {
        (new $class())->handle($request);
    }

}