<?php

use App\controllers\RequestsController;

$request = new RequestsController;

$klein->respond('GET', '/request/[i:client]', [$request, 'get']);
$klein->respond('POST', '/request/create', [$request, 'register']);
$klein->respond('PUT', '/request/discard', [$request, 'discard']);
$klein->respond('PUT', '/request/accept', [$request, 'recive']);
$klein->respond('PUT', '/request/pay', [$request, 'payInInstallment']);
