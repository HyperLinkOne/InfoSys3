<?php

declare(strict_types=1);

namespace App\Http;

use App\Http\Response;

final class JsonResponse extends Response
{
    public function __construct(array $data, int $status = 200)
    {
        parent::__construct(json_encode($data), $status, [
            'Content-Type' => 'application/json'
        ]);
    }
}
