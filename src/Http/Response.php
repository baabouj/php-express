<?php

namespace Pexess\Http;

use Pexess\Pexess;

class Response
{

    public function view(string $view, array $params = []) : void
    {
        //foreach ($params as $key => $value) $$key = $value;
        extract($params);
        require_once Pexess::$ROOT_DIR . '\views\\' . $view . '.php';
    }

    public function status(int $response_code): Response
    {
        http_response_code($response_code);
        return $this;
    }

    public function send(string $message): void
    {
        echo $message;
    }

    /**
     * @throws \Exception
     */

    public function throw(int $code, string $message): \Exception
    {
        throw new \Exception($message, $code);
    }

    public function end(string $message = ""): never
    {
        exit($message);
    }

    public function redirect(string $url): void
    {
        header("Location: $url");
    }

    public function header(string $header) : void
    {
        header($header);
    }

    public function json($data): never
    {
        $this->header('Content-Type: application/json');
        $json = json_encode($data);
        exit($json);
    }
}