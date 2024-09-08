<?php

namespace Caramel\Core;

class Container
{

    private $bindings = [];
    private $singletons = [];

    public function bind(string $key, callable $func)
    {
        $this->bindings[$key] = $func;
    }

    public function singleton(string $key, callable $func)
    {
        $this->singletons[$key] = $func();
    }

    public function resolve(string $key)
    {

        if(array_key_exists($key, $this->singletons))
        {
            return $this->singletons[$key];
        }

        if(array_key_exists($key, $this->bindings))
        {
            $binding = $this->bindings[$key];
            return call_user_func($binding);
        }

        throw new \Exception("Unknwing {$key} binding");
    }
}