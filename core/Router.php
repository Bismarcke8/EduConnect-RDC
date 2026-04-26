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
        // First try exact matches
        if (isset($this->routes[$this->currentMethod][$this->currentUri])) {
            $callback = $this->routes[$this->currentMethod][$this->currentUri];
            return $this->executeCallback($callback);
        }

        // Then try pattern matches
        foreach ($this->routes[$this->currentMethod] as $pattern => $callback) {
            if ($this->match($pattern, $callback)) {
                return;
            }
        }

        http_response_code(404);
        echo "404 - Page not found";
    }

    private function executeCallback($callback, $params = [])
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

        return call_user_func_array([$controllerInstance, $method], $params);
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
        // Convert route pattern to regex
        $regex = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        $regex = preg_replace('/:([^\/]+)/', '([^/]+)', $regex);
        $regex = '#^' . $regex . '$#';

        if (preg_match($regex, $this->currentUri, $matches)) {
            array_shift($matches); // Remove full match
            
            if (is_string($callback)) {
                return $this->executeCallback($callback, $matches);
            }
            
            return call_user_func_array($callback, $matches);
        }
        
        return false;
    }
}
