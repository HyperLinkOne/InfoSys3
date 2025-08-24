<?php

declare(strict_types=1);

namespace App\Security;

class CsrfTokenManager
{
    private string $sessionKey = '_csrf_tokens';

    public function __construct()
    {
        // Session should already be started in index.php
        // Just ensure the CSRF tokens array exists
        if (!isset($_SESSION[$this->sessionKey])) {
            $_SESSION[$this->sessionKey] = [];
        }
    }

    public function generateToken(string $id): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION[$this->sessionKey][$id] = $token;
        return $token;
    }

    public function isTokenValid(string $id, ?string $token): bool
    {
        // If token is null or empty, it's invalid
        if ($token === null || $token === '') {
            return false;
        }
        
        return isset($_SESSION[$this->sessionKey][$id]) && hash_equals($_SESSION[$this->sessionKey][$id], $token);
    }

    public function getToken(string $id): string
    {
        if (!isset($_SESSION[$this->sessionKey][$id])) {
            return '';
        }
        return $_SESSION[$this->sessionKey][$id];
    }
}
