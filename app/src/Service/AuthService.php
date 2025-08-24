<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;

class AuthService
{
    private const SESSION_USER_KEY = 'user_id';
    private const SESSION_USER_ROLE = 'user_role';

    public function __construct()
    {
        // Session should already be started in index.php
        // No need to start it here
    }

    public function login(User $user): void
    {
        $_SESSION[self::SESSION_USER_KEY] = $user->getId();
        $_SESSION[self::SESSION_USER_ROLE] = $user->getRole();
    }

    public function logout(): void
    {
        unset($_SESSION[self::SESSION_USER_KEY]);
        unset($_SESSION[self::SESSION_USER_ROLE]);
        session_destroy();
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION[self::SESSION_USER_KEY]);
    }

    public function getCurrentUserId(): ?int
    {
        return $_SESSION[self::SESSION_USER_KEY] ?? null;
    }

    public function getCurrentUserRole(): ?string
    {
        return $_SESSION[self::SESSION_USER_ROLE] ?? null;
    }

    public function isAdmin(): bool
    {
        return $this->getCurrentUserRole() === 'admin';
    }

    public function isUser(): bool
    {
        return $this->getCurrentUserRole() === 'user';
    }

    public function hasRole(string $role): bool
    {
        return $this->getCurrentUserRole() === $role;
    }

    public function requireAuth(): void
    {
        if (!$this->isLoggedIn()) {
            throw new \RuntimeException('Authentication required');
        }
    }

    public function requireRole(string $role): void
    {
        $this->requireAuth();
        if (!$this->hasRole($role)) {
            throw new \RuntimeException('Insufficient permissions');
        }
    }

    public function requireAdmin(): void
    {
        $this->requireRole('admin');
    }
}
