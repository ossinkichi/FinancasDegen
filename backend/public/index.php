<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/functions/config.php';
include __DIR__ . '/../app/functions/helpers.php';
require __DIR__ . '/../core/route.php';

use Klein\Request;
use Klein\Response;

$klein->respond('GET', '/dice', function (Request $request, Response $response) {
    return $response->header('Content-Type', 'application/json')
        ->body(json_encode(['message' => 'Hello World!!']));
});
