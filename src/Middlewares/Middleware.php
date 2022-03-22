<?php

namespace Pexess\Middlewares;

abstract class Middleware
{
  abstract public function __invoke();
}