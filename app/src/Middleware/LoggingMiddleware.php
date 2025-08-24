<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Http\Request;
use App\Http\Response;

class LoggingMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        $start = microtime(true);

        $response = $next($request);

        $duration = number_format((microtime(true) - $start) * 1000, 2);
        error_log(sprintf(
            "[%s] %s %s - %dms",
            date('Y-m-d H:i:s'),
            $request->method(),
            $request->uri(),
            $duration
        ));

        return $response;
    }
}
