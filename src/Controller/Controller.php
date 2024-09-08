<?php

namespace Caramel\Controller;

use Caramel\Core\App;
use Caramel\Core\Database;

class Controller
{

    protected Database $db;

    public function __construct()
    {
        $this->db = app()->container()->resolve(Database::class);
    }

    public function redirect($uri)
    {
        header("location: $uri");
        exit();
    }

    public function response(string|array $view, array $data = array(), int $code = 200)
    {
        if(is_string($view))
        {
            extract($data);
            require base_path(self::parse_view_input($view));
        }
        else if(is_array($view))
        {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($view);
        }
        else 
        {
            throw new \BadMethodCallException();
        }
    }

    private static function parse_view_input(string $view): string
    {
        $view = str_replace('.', '/', $view);
        return "src/views/{$view}.view.php";
    }

}

