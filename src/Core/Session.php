<?php

namespace Caramel\Core;

class Session 
{

    private static function &traversePath(string $path): array
    {
        if(strlen($path) === 0) return $_SESSION;

        $current = &$_SESSION;
        if(strlen($path) > 0)
        {
            foreach(explode('.', $path) as $key)
            {
                if(!array_key_exists($key, $current)) return [false, null];
                $current = &$current[$key];
            }
        }
        return [true, $current];
    }

    public static function has($key):bool
    {
        return self::traversePath($key)[0];
    }

    // public static function put($key, $value)
    // {
    //     $_SESSION[$key] = $value;
    // }

    public static function get($key = '', $default = null)
    {
        [$exists, $value] = self::traversePath($key);
        return $exists ? $value : $default;
    }

    public static function flash($key, $value)
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function unflash()
    {
        unset($_SESSION['_flash']);
    }

    public static function csrf_set()
    {
        $_SESSION['_csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));    
    }

    public static function csrf_token(): ?string
    {
        return $_SESSION['_csrf_token'] ?? null;
    }
}