<?php

declare(strict_types=1);

namespace App\Http;

class Request
{
    private array $get;
    private array $post;
    private array $server;
    private array $cookies;
    private array $files;
    private string $rawBody;
    private array $body;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
        $this->cookies = $_COOKIE;
        $this->files = $_FILES;
        // Für PUT, PATCH, DELETE etc. den Raw-Body einlesen
        $this->rawBody = file_get_contents('php://input');
        $parsed = [];
        $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
        if ( $this->rawBody && in_array($method, ['PUT', 'PATCH', 'DELETE'], true)) {
            parse_str( $this->rawBody, $parsed);
        }
        $this->body = array_merge($this->post, $parsed);
    }

    public function get(string $key, $default = null)
    {
        return $this->get[$key] ?? $default;
    }

    public function post(string $key, $default = null)
    {
        return $this->post[$key] ?? $default;
    }

    public function json(): array
    {
        return json_decode($this->rawBody, true) ?? [];
    }

    public function method(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function uri(): string
    {
        return parse_url($this->server['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    }

    public function header(string $key, $default = null)
    {
        $headerKey = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $this->server[$headerKey] ?? $default;
    }

    public function allQuery(): array
    {
        return $this->get;
    }

    public function allPost(): array
    {
        return $this->post;
    }

    /**
     * Holt einen Input-Wert aus Body (POST/PUT/PATCH/DELETE) oder Query-Params
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function input(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->body)) {
            return $this->body[$key];
        }
        if (array_key_exists($key, $this->get)) {
            return $this->get[$key];
        }
        return $default;
    }
}
