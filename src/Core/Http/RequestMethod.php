<?php

namespace Caramel\Core\Http;

enum RequestMethod: string
{

    case GET = "GET";
    case POST = "POST";
    case PATCH = "PATCH";
    case DELETE = "DELETE";
    case PUT = "PUT";

    public static function byName(string $name): static
    {
        foreach (self::cases() as $status) {
            if($status->name === strtoupper($name))
            {
                return $status;
            }
        }
        throw new \ValueError("$name is not a valid backing value for enum " . self::class );
    }

}