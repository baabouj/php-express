<?php

namespace Pexess\Router;

class Router
{
    public array $routes = [];
    public array $middlewares = [];

    public function get(string $url, \Closure|array $callback)
    {
        $this->routes[$url]['get'] = $callback;
    }

    public function post(string $url, \Closure|array $callback)
    {
        $this->routes[$url]['post'] = $callback;
    }

    public function put(string $url, \Closure|array $callback)
    {
        $this->routes[$url]['post'] = $callback;
    }

    public function delete(string $url, \Closure|array $callback)
    {
        $this->routes[$url]['post'] = $callback;
    }

    public function use($url, $middleware = false)
    {

        if ($middleware == false) {
            // url is not passed so the $url holds the middleware
            $middleware = $url;
            $url = '/';
        }
        $this->middlewares[$url] = $middleware;
    }

    public function route(string $url): Route
    {
        return new Route($url);
    }

}