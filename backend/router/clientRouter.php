<?php

use App\controllers\ClientController;

$client = new ClientController;

$klein->respond('GET', '/client/[i:company]', [$client, 'get']);
$klein->respond('POST', '/client/register', [$client, 'register']);
$klein->respond('DELETE', '/client/delete', [$client, 'delete']);
$klein->respond('PUT', '/client/update', [$client, 'update']);
$klein->respond('GET', '/client/[i:company]/[i:id]', [$client, 'searchClient']);
