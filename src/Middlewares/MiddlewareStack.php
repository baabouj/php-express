<?php

namespace Pexess\Middlewares;

use Pexess\Http\Request;
use Pexess\Http\Response;

abstract class MiddlewareStack
{
    protected \Closure $start;

    public function add(Middleware $middleware)
    {
        $next = $this->start;
        $this->start = function (Request $request, Response $response) use ($middleware, $next) {
            return $middleware($request, $response, $next);
        };
    }

    public function handler(Request $request, Response $response)
    {
        return call_user_func($this->start, $request, $response);
    }
}