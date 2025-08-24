<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Http\Request;
use App\Http\Response;
use App\Http\JsonResponse;

class ExceptionMiddleware implements MiddlewareInterface
{
    public function __construct(private bool $debug = false)
    {
    }
    public function handle(Request $request, callable $next): Response
    {
        try {
            return $next($request);
        } catch (\Throwable $e) {
            error_log("[ERROR] {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}");

            $status = 500;
            $data = [
                'error' => 'Internal Server Error',
                'message' => $this->debug ? $e->getMessage() : 'Something went wrong'
            ];

            if ($this->debug) {
                $data['trace'] = explode("\n", $e->getTraceAsString());
            }

            return new JsonResponse($data, $status);
        }
    }
}
