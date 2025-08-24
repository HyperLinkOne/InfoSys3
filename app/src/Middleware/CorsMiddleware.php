<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Http\Request;
use App\Http\Response;

class CorsMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        // Preflight sofort beantworten
        if ($request->method() === 'OPTIONS') {
            $pre = new Response('', 204);
            $this->applyCorsHeaders($pre);
            return $pre;
        }

        $response = $next($request);
        $this->applyCorsHeaders($response);
        return $response;
    }

    private function applyCorsHeaders(Response $response): void
    {
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        $response->setHeader('Access-Control-Max-Age', '86400');
    }
}
