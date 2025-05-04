<?php

namespace App\Controllers;

use App\Traits\Helper;
use App\Classes\JwtHelper;
use Klein\Response;
use Throwable;

class BaseController
{
    use Helper;

    protected JwtHelper $jwtHelper;

    public function __construct()
    {
        $this->jwtHelper = new JwtHelper(); // Instancia o helper de JWT
    }

    public function successRequest(Response $response, array $payload, int $statusCode = 200)
    {
        return $response
            ->code($statusCode)
            ->header('Content-Type', 'application/json')
            ->body($payload);
    }

    public function errorRequest(Response $response, Throwable $throwable, array $context = [])
    {
        return $response
            ->code(500)
            ->header('Content-Type', 'application/json')
            ->body(\json_encode([
                'error' => $throwable->getMessage(),
                'code' => $throwable->getCode(),
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'context' => $context,
            ]));
    }
}
