<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\HtmlResponse;
use App\Http\JsonResponse;
use App\Http\Response;

abstract class BaseController
{
    /** Gibt JSON-Daten mit Status-Code zurück */
    protected function json(array $data, int $status = 200): JsonResponse
    {
        return new JsonResponse($data, $status);
    }

    /** Gibt einfachen HTML-Text zurück */
    protected function html(string $html, int $status = 200): HtmlResponse
    {
        return new HtmlResponse($html, $status, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    /** Gibt Plaintext zurück */
    protected function text(string $text, int $status = 200): Response
    {
        return new Response($text, $status, ['Content-Type' => 'text/plain; charset=utf-8']);
    }
}
