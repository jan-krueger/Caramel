<?php

namespace Caramel\Core\Middleware;

use Caramel\Core\Http\Request;

class Guest extends Middleware
{

    public function handle(Request $request)
    {
        if(!guest())
        {
            header('location: /');
            die();
        }
    }

}