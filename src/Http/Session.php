<?php

namespace Pexess\Http;

class Session
{

    public function __construct()
    {
        $this->start();
    }

    public function start()
    {
        session_start();
    }

    public function set(string $key, string $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key)
    {
        return $_SESSION[$key];
    }

    public function unset(string $key)
    {
        unset($_SESSION[$key]);
    }

    public function destroy()
    {
        session_destroy();
    }
}