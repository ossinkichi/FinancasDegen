<?php


require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/functions/config.php';
include __DIR__ . '/../app/functions/helpers.php';
include __DIR__ . '/../core/route.php';

use Klein\Request;
use Klein\Response;




$klein->respond('GET', '/', function (Request $request, Response $response) {
    dd(['message' => $request->id()]);
});
