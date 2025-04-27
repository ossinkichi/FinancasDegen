<?php

namespace App\controllers;

use App\Shared\JWT;
use Klein\AbstractResponse;
use Klein\Response;
use Throwable;

abstract class BaseController
{
    protected JWT $jwt;

    public function __construct()
    {
        $this->jwt = new JWT;
    }

    public function success(Response $response, array $payload, int $statusCode = 200): AbstractResponse|Response|int|string
    {
        return $response->code($statusCode)
            ->header('Content-Type', 'application/json')
            ->body(json_encode($payload));
    }

    public function error(Response $response, Throwable $throwable, array $context = []): AbstractResponse|Response|int|string
    {
        return $response->code($throwable->getCode())
            ->header('Content-Type', 'application/json')
            ->body(json_encode([
                'error' => $throwable->getMessage(),
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'context' => $context,
            ]));
    }
}
