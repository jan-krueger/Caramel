<?php

namespace Caramel\Controller;

class IndexController extends Controller
{

    public function index()
    {
        return $this->response('index', [
            'heading' => "Headiasng",         
        ]);
    }

}

