<?php

namespace Pexess;

use Pexess\Database\Database;
use Pexess\Http\Request;
use Pexess\Http\Response;
use Pexess\Http\Session;
use Pexess\Router\Route;
use Pexess\Router\Router;

class Pexess
{
    public Response $response;
    public Request $request;
    public Router $router;

    public static Pexess $app;

    protected array $routes = [];
    protected array $middlewares = [];

    public static string $ROOT_DIR;

    public function __construct()
    {
        $this->response = new Response();
        $this->request = new Request();
        $this->router = new Router();

        self::$ROOT_DIR = dirname(__DIR__);
        self::$app = $this;
    }

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
        $this->routes[$url]['put'] = $callback;
    }

    public function patch(string $url, \Closure|array $callback)
    {
        $this->routes[$url]['patch'] = $callback;
    }

    public function delete(string $url, \Closure|array $callback)
    {
        $this->routes[$url]['delete'] = $callback;
    }

    public function use(string $url, $action)
    {
        if ($action instanceof Router) {
            $routes = [];
            $middlewares = [];

            foreach ($action->routes as $key => $value) {
                $routes[rtrim($url . $key, '/')] = $value;
            }

            foreach ($action->routes as $key => $value) {
                $middlewares[rtrim($url . $key, '/')] = $action->middlewares['/'];
            }

            $this->routes = array_merge($this->routes, $routes);
            $this->middlewares = array_merge($this->middlewares, $middlewares);
            return;
        }
        $this->middlewares[$url] = $action;
    }

    public function route(string $url): Route
    {
        return new Route($url);
    }

    public function resolve()
    {
        $url = $this->request->url ?: '/';
        $method = $this->request->method;
        $callback = $this->routes[$url][$method] ?? false;
        $middleware = $this->middlewares[$url] ?? false;

        $params = [];

        if (!$callback) {
            foreach ($this->routes as $route => $actions) {
                $routeUrl = $route;
                preg_match_all('/{[^}]+}/', $route, $keys);
                $route = preg_replace('/{[^}]+}/', '(.+)', $route);
                if (preg_match("%^{$route}$%", $url, $matches)) {
                    unset($matches[0]);
                    foreach (array_values($matches) as $index => $param) {
                        if (str_contains($param, '/')) {
                            $params = [];
                            break;
                        }

                        $params[trim($keys[0][$index], '{}')] = $param;
                    }
                    if (empty($params)) break;
                    $callback = $actions[$method];
                    $middleware = $this->middlewares[$routeUrl] ?? false;
                    break;
                }
            }
        }

        $this->request->setParams($params);

        if (str_starts_with($url, "/api")) $callback = include_once Application::$ROOT_DIR . str_replace("/", "\\", $url) . ".php";

        if (!$callback) $this->response->throw(404, "Page Not Found!");

        if ($middleware !== false) call_user_func($middleware, $this->request, $this->response);

        if (is_array($callback)) $callback[0] = new $callback[0];

        call_user_func($callback, $this->request, $this->response);
    }

    public function init()
    {
        try {
            $this->resolve();
        } catch (\Exception $e) {
            $this->response->status(is_int($e->getCode()) ? $e->getCode() : 500)->view('[error]', [
                'code' => is_int($e->getCode()) ? $e->getCode() : 500,
                'message' => $e->getMessage()
            ]);
        }
    }

}