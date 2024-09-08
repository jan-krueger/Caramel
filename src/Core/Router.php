<?php 

namespace Caramel\Core;

use Caramel\Core\Http\Request;
use Caramel\Core\Http\RequestMethod;
use Caramel\Core\Middleware\Middleware;
use \Exception;
use \ReflectionMethod;

class Router
{   

    private $_groups = [];

    private $named_routes = [];
    private $routes = [];

    private function _get_controller_arguments(string $class, string $method) : array
    {
        $r = new ReflectionMethod($class, $method);
        $params = [];

        foreach($r->getParameters() as $param)
        {
            $params[$param->name] = $param->hasType() ? $param->getType()->getName() : null;
        }

        return $params;
    }

    private function _parse_route_controller(string $controller): bool|array
    {
        $values = explode('@', $controller);

        // error: invalid input format
        if(count($values) !== 2) return false;

        $class_name = "Caramel\\Controller\\$values[0]";
        $method_name = $values[1];

        // error: class does not exist
        if(!class_exists($class_name)) return false; 
        // error: method does not exist
        if(!method_exists($class_name, $method_name)) return false;

        $result = [
            'class'      => $class_name,
            'method'     => $values[1],
            'parameters' => [],
        ];

        $result['parameters'] = $this->_get_controller_arguments($result['class'], $result['method']);
        return $result;
    }

    /**
     * Parses the input pattern, and returns a Regex pattern to match against as well as all named-paramaters in the URI pattern in-order in which
     *  they appear in the uri-pattern.
     * 
     * The pattern can look just like any valid uri, so e.g.:
     *  - /
     *  - /foo/bar
     *  - /foo/bar/bob/alice
     * or instead of only static parts parameters can be definied such as:
     *  - /{id}
     *  - /foo/{input} 
     *  - /foo/bar/{time}/{alice}
     *
     * @param string $uri
     * @return void
     */
    private function _parse_route_uri(string $uri)
    {
        $re = '/\/(?<path>\w+|\{(?<param>\w+)(:(?<pattern>\S+))?\})/m';

        // --- Prepend group prefix 
        $uri = $this->_get_groups_prefix() . $uri;

        preg_match_all($re, $uri, $matches, PREG_SET_ORDER, 0);


        $params = [];
        $match_pattern = [];
        $structure = [];

        foreach($matches as $match) 
        {
            if(isset($match['param']))
            {
                $params[] = $match['param'];
                $pattern = $match['pattern'] ?? '\w+';
                $match_pattern[] = "\/($pattern)";
                $structure[] = [ 'type' => 'param', 'elem' => $match['param'] ];
            }
            else 
            {
                $match_pattern[] = "\\/{$match['path']}";
                $structure[] = [ 'type' => 'path', 'elem' => $match['path'] ];
            }
        }  

        if($uri === '/')
        {
            $match_pattern = ['\/'];
            $structure[] = [ 'type' => 'path', 'elem' => '' ];
        }

        return [
            'match_pattern' => '/^' . join('', $match_pattern) . '$/m',
            'structure' => $structure,
            'params' => $params,
        ];
    }

    public function register(RequestMethod $method, string $path, $controller): self
    {

        $result = $this->_parse_route_uri($path);
        $result['method'] = $method;
        $result['controller'] = self::_parse_route_controller($controller);
        $result['middleware'] = [];

        $this->routes[] = $result;

        return $this;
    }

    public function get(string $path, $controller)
    {
        return $this->register(RequestMethod::GET, $path, $controller);
    }

    public function post(string $path, $controller)
    {
        return $this->register(RequestMethod::POST, $path, $controller);
    }

    public function delete(string $path, $controller)
    {
        return $this->register(RequestMethod::DELETE, $path, $controller);
    }

    public function put(string $path, $controller)
    {
        return $this->register(RequestMethod::PUT, $path, $controller);
    }

    public function handle_request(string $method, string $uri): bool
    {
    
        $method = RequestMethod::byName(strtoupper($method));
        $request = new Request($method, collect($_POST));
        
        foreach($this->routes as $route)
        {

            if($route['method'] === $method && preg_match($route['match_pattern'], $uri, $matches))
            {
                // --- Handle Middlewares
                foreach($route['middleware'] as $middleware)
                {
                    Middleware::resolve($middleware, $request);
                }

                // --- Call Controller
                $controller = new $route['controller']['class']();
                
                array_shift($matches); // - drop first elemt because it is the path 
                $request_params = array_combine($route['params'], $matches);

                http_response_code(200);
                // --- populate request parameters
                foreach($route['controller']['parameters'] as $param => $type)
                {
                    if($type === Request::class)
                    {
                        $request_params[$param] = $request;
                    }
                }

                $call_params = array_intersect_key($request_params, $route['controller']['parameters']);
                call_user_func_array([$controller, $route['controller']['method']], $call_params);
                return true;    
            }

        }

        self::abort();
        
    }

    public function abort(int $code = 404)
    {
        http_response_code($code);
        echo "Sorry, not found!";
        die();
    }

    public function only(string $key): self
    {
        $this->routes[array_key_last($this->routes)]['middleware'][] = $key;
        return $this;
    }

    public function named(string $name): self
    {
        $route_key = array_key_last($this->routes);
        $this->named_routes[$name] = $route_key;
        return $this;    
    }

    public function get_named_route(string $name, array $arguments = []): string
    {
        $structure = $this->routes[$this->named_routes[$name]]['structure'];

        $uri = [];
        foreach($structure as $element)
        {
            $type = $element['type'];

            if($type === 'path')
            {
                $uri[] = $element['elem'];
            }
            else if($type === 'param')
            {
                if(!array_key_exists($element['elem'], $arguments)) throw new Exception("get_named_route is missing a required named parameter.");
                $uri[] = $arguments[$element['elem']];
            }
        }

        return '/' . join('/', $uri);
    }

    public function group(array $params, \Closure $func)
    {
        $group = [
            'prefix' => $params['prefix'] ?? null
        ];

        array_push($this->_groups, $group);
        $func($this);
        array_pop($this->_groups);

    }

    public function redirectToPrevious()
    {
        header("Location: {$_SERVER['HTTP_REFERER']}");
        die();    
    }

    private function _get_groups_prefix()
    {
        if(count($this->_groups) === 0) return '';

        $prefix = ['/'];
        foreach($this->_groups as $group)
        {
            $prefix[] = $group['prefix'];
        }
        return join('/', $prefix);
    }

}