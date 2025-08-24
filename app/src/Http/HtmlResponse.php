<?php

declare(strict_types=1);

namespace App\Http;

use App\Http\Response;

final class HtmlResponse extends Response
{
    public function __construct(string $data, int $status = 200)
    {
        parent::__construct($data, $status, [
            'Content-Type' => 'text/html',
            'charset' => 'utf-8',
        ]);
    }
}
