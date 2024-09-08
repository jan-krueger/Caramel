<?php

namespace Caramel\Core;

class App
{

    private Container $container;

    public function __construct()
    {   
        $this->container = new Container();
    }

    public function container(): Container
    {
        return $this->container;
    }
}