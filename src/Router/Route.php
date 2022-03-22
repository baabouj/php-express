<?php

namespace Pexess\Router;

use Pexess\Pexess;

class Route
{
    public string $route;

    public function __construct(string $route)
    {
        $this->route = $route;
    }

    public function get($callback) : Route
    {
        Pexess::$app->routes[$this->route]['get'] = $callback;
        return $this;
    }

    public function post($callback) : Route
    {
        Pexess::$app->routes[$this->route]['post'] = $callback;
        return $this;
    }
}