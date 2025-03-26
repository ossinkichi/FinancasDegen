<?php

use Klein\Klein;
use Klein\Request;
use Klein\Response;

$klein = new Klein();

$klein->respond('GET', '/', function (Request $request, Response $response): Response {
    return $response->code(201)->header('Content-Type', 'aplication/json')->body();
});

// require __DIR__ . '/../router/clientRouter.php';
// require __DIR__ . '/../router/companyRouter.php';
// require __DIR__ . '/../router/plansRouter.php';
// require __DIR__ . '/../router/requestRouter.php';
// require __DIR__ . '/../router/userRouter.php';
