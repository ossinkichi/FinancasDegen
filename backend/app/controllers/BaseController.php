<?php

namespace App\controllers;

use App\Traits\Helper;
use App\Classes\JwtHelper;
use Klein\Response;
use Throwable;

class BaseController
{
    use Helper;

    protected JwtHelper $jwt;

    public function __construct()
    {
        $this->jwt = new JwtHelper;
    }

    public function successRequest(Response $response, array $payload, int $statusCode = 200)
    {
        return $response
            ->code($statusCode)
            ->header('Content-Type', 'application/json')
            ->body(\json_encode($payload));
    }

    public function errorRequest(Response $response, Throwable $throwable, array $context = [])
    {
        return $response
            ->code($throwable->getCode())
            ->header('Content-Type', 'application/json')
            ->body(\json_encode([
                'error' => $throwable->getMessage(),
                'code' => $throwable->getCode(),
                'line' => $throwable->getLine(),
                'context' => $context,
            ]));
    }
}
