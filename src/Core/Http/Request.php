<?php

namespace Caramel\Core\Http;

use Caramel\Core\Collection\Collection;

class Request 
{

    public function __construct(public readonly RequestMethod $method, public readonly Collection $post_data)
    {
    }

    public function body(string $path = ''): mixed
    {
        return $this->post_data->get($path);
    }

}