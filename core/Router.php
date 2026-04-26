<?php

namespace Core;

class Router
{
    private $routes = [];
    private $currentUri;
    private $currentMethod;

    public function __construct()
    {
        $this->currentUri = $this->parseUri();
        $this->currentMethod = $_SERVER['REQUEST_METHOD'];
    }

    public function get($uri, $callback)
    {
        $this->routes['GET'][$uri] = $callback;
        return $this;
    }

    public function post($uri, $callback)
    {
        $this->routes['POST'][$uri] = $callback;
        return $this;
    }

    public function put($uri, $callback)
    {
        $this->routes['PUT'][$uri] = $callback;
        return $this;
    }

    public function delete($uri, $callback)
    {
        $this->routes['DELETE'][$uri] = $callback;
        return $this;
    }

    public function dispatch()
    {
        // Check if route exists
        if (!isset($this->routes[$this->currentMethod][$this->currentUri])) {
            http_response_code(404);
            echo "404 - Page not found";
            return;
        }

        $callback = $this->routes[$this->currentMethod][$this->currentUri];

        if (is_callable($callback)) {
            return call_user_func($callback);
        }

        // Handle string callbacks like 'HomeController@index'
        if (is_string($callback)) {
            return $this->executeCallback($callback);
        }

        http_response_code(500);
        echo "500 - Internal Server Error";
    }

    private function executeCallback($callback)
    {
        list($controller, $method) = explode('@', $callback);
        
        $controllerClass = "App\\Controllers\\" . $controller;
        
        if (!class_exists($controllerClass)) {
            die("Controller not found: " . $controllerClass);
        }

        $controllerInstance = new $controllerClass();
        
        if (!method_exists($controllerInstance, $method)) {
            die("Method not found: " . $method);
        }

        return $controllerInstance->$method();
    }

    private function parseUri()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove the public folder from the path
        $basePath = '/EduConnect-RDC/public';
        
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        // Remove trailing slash
        $uri = rtrim($uri, '/');
        
        // Return root if empty
        return $uri ?: '/';
    }

    public function match($pattern, $callback)
    {
        if (preg_match('#^' . $pattern . '$#', $this->currentUri, $matches)) {
            array_shift($matches);
            
            if (is_string($callback)) {
                return $this->executeCallback($callback, $matches);
            }
            
            return call_user_func_array($callback, $matches);
        }
    }
}
