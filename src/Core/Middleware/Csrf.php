<?php

namespace Caramel\Core\Middleware;

use Caramel\Core\Exception\Exceptions\InvalidCsrfTokenException;
use Caramel\Core\Http\Request;
use Caramel\Core\Http\RequestMethod;
use Caramel\Core\Session;

class Csrf extends Middleware
{

    public function handle(Request $request)
    {
        if($request->method !== RequestMethod::GET)
        {
            if($request->post_data->getOrDefault('_csrf_token', false) === Session::csrf_token())
            {
                // --- do not pass this variable on
                $request->post_data->delete('_csrf_token'); 
            }
            else
            {
                throw new InvalidCsrfTokenException();
            }
        }
        else
        {
            Session::csrf_set();
        }
    }

}