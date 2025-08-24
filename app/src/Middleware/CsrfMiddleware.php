<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Http\Request;
use App\Http\Response;
use App\Http\JsonResponse;
use App\Security\CsrfTokenManager;
class CsrfMiddleware implements MiddlewareInterface
{
    public function __construct(private CsrfTokenManager $tokenManager) {}

    public function handle(Request $request, callable $next): Response
    {
        // Nur für state-changing Methoden prüfen
        if (in_array($request->method(), ['POST', 'PUT', 'DELETE'], true)) {
            $token = $request->input('_csrf_token');

            if (!$this->tokenManager->isTokenValid('default', $token)) {
                return new JsonResponse(['error' => 'Invalid CSRF token'], 403);
            }
        }

        return $next($request);
    }
}
