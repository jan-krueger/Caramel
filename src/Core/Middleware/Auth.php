<?php

namespace Caramel\Core\Middleware;

use Caramel\Core\Http\Request;

class Auth extends Middleware
{

    public function handle(Request $request)
    {
        if(!authenticated())
        {
            header('location: /');
            die();
        }
    }

}