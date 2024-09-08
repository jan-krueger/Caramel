<?php

namespace Caramel\Core\Collection;

use Caramel\Core\Exception\Exceptions\InvalidPathException;
use SimpleXMLElement;

class Collection implements ICollection
{
    public function __construct(private array $data = []) { }

    
    public function has(string $path): bool
    {
        return $this->traversePath($path)[0];
    }

    public function get(string $path = '')
    {
        [$exists, &$result] = $this->traversePath($path);
        if (!$exists) throw new InvalidPathException();
        return $result;
    }

    public function getOrDefault(string $path, $default)
    {
        [$exists, $result] = $this->traversePath($path);
        return $exists ? $result : $default;
    }

    /**
     * Deletes the item at the given path, if it exists.
     *
     * @param string $path
     * @param [type] $default returned if the path does not exist.
     * @return array [exists, stored value]
     */
    public function delete(string $path, $default = null): array
    {
        $keys = explode('.', $path);
        $current = &$this->data;
        foreach ($keys as $i => $key) 
        {
            if(!array_key_exists($key, $current)) return [false, $default];

            // If it's the last key in the path, delete it
            if ($i === count($keys) - 1) 
            {
                $value = $current[$key];
                unset($current[$key]);
                return [true, $value];
            }

            $current = &$current[$key];
        }

        return [false, $default];
    }

    // public function map(callable $callback, ?string $path = null): void
    // {
    //     if ($path !== null) 
    //     {
    //         [$exists, &$target] = $this->traversePath($path);
    //         if (!$exists || !is_array($target)) 
    //         {
    //             throw new InvalidPathException("Invalid path or not an array at path: $path");
    //         }
    //         $this->applyMap($callback, $target);
    //         dd($this->data, $target);
    //     }
    //     else 
    //     {
    //         $this->applyMap($callback, $this->data);
    //     }
    // }

    public function toJson():string
    {
        return json_encode($this->data);    
    }

    public function toArray(): array
    {
        return $this->data;
    }

    // private function applyMap(callable $callback, array &$data): void
    // {
    //     foreach($data as $key => &$value) 
    //     {
    //         if(is_array($value)) 
    //         {
    //             $this->applyMap($callback, $value); 
    //         }
    //         else
    //         {
    //             $data[$key] = $callback($value);
    //         }
    //     }
    // }

    private function &traversePath(string $path): array
    {
        $current = &$this->data;
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

}