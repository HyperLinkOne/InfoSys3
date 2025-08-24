<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Http\JsonResponse;
use App\Http\Request;
use App\Http\Response;
use App\Service\AuthService;

class AuthMiddleware implements MiddlewareInterface
{
    private AuthService $authService;
    private array $protectedRoutes;
    private array $adminRoutes;

    public function __construct(AuthService $authService, array $protectedRoutes = [], array $adminRoutes = [])
    {
        $this->authService = $authService;
        $this->protectedRoutes = $protectedRoutes;
        $this->adminRoutes = $adminRoutes;
    }

    public function handle(Request $request, callable $next): Response
    {
        $uri = $request->uri();
        $method = $request->method();

        // Check if route requires authentication
        if ($this->isProtectedRoute($uri, $method)) {
            if (!$this->authService->isLoggedIn()) {
                return new JsonResponse(['error' => 'Authentication required'], 401);
            }
        }

        // Check if route requires admin role
        if ($this->isAdminRoute($uri, $method)) {
            if (!$this->authService->isAdmin()) {
                return new JsonResponse(['error' => 'Admin access required'], 403);
            }
        }

        return $next($request);
    }

    private function isProtectedRoute(string $uri, string $method): bool
    {
        foreach ($this->protectedRoutes as $route) {
            if ($this->matchesRoute($uri, $method, $route)) {
                return true;
            }
        }
        return false;
    }

    private function isAdminRoute(string $uri, string $method): bool
    {
        foreach ($this->adminRoutes as $route) {
            if ($this->matchesRoute($uri, $method, $route)) {
                return true;
            }
        }
        return false;
    }

    private function matchesRoute(string $uri, string $method, array $route): bool
    {
        $routeUri = $route['uri'] ?? '';
        $routeMethod = $route['method'] ?? 'GET';

        // Simple pattern matching (can be enhanced with regex)
        $uriMatch = $uri === $routeUri || strpos($uri, $routeUri) === 0;
        $methodMatch = $method === $routeMethod;

        return $uriMatch && $methodMatch;
    }
}
