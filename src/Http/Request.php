<?php

namespace Pexess\Http;

class Request
{
    public string $method;
    public string $url;
    public array $query;
    public array $body;
    public array $params;
    public array $headers;

    public function __construct()
    {
        $this->setMethod();
        $this->setUrl();
        $this->setQuery();
        $this->setBody();
        $this->setParams();
        $this->headers = getallheaders();
    }

    public function setUrl()
    {
        $path = $_SERVER['REQUEST_URI'];
        $position = strpos($path, '?');
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }
        $this->url = rtrim($path, '/');
    }

    public function setMethod()
    {
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function setBody()
    {
        $data = [];
        if ($this->method !== 'get') {
            if ($this->method == 'post'){
                foreach ($_POST as $key => $value) {
                    $data[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
            $json = json_decode(file_get_contents('php://input'), true);
            if ($json ?? false) {
                foreach ($json as $key => $value) {
                    $data[$key] = $value;
                }
            }
        }
        $this->body = $data;
    }

    public function setQuery()
    {
        $data = [];
        if ($this->method == 'get') {
            foreach ($_GET as $key => $value) {
                $data[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        $this->query = $data;
    }

    public function setParams(array $params = [])
    {
        $this->params = $params;
    }

}