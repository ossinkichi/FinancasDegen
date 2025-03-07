<?php

use app\controllers\UserController;

$users = new UserController;

$klein->respond('GET', '/user', [$users, 'index']);
$klein->respond('GET', '/user/[h:hash]', [$users, 'get']);
$klein->respond('POST', '/user/login', [$users, 'login']);
$klein->respond('POST', '/user/register', [$users, 'register']);
$klein->respond('PUT', '/user/update', [$users, 'update']);
$klein->respond('DELETE', '/user/delete/[h:hash]', [$users, 'delete']);
$klein->respond('PATCH', '/user/active/[h:hash]', [$users, 'active']);
$klein->respond('PATCH', '/user/forgoat', [$users, 'forgotPassword']);
$klein->respond('PATCH', '/user/join/[i:company]/[h:user]', [$users, 'join']); // testar rota após teste de rotas das companmias
// Adicionar rota de convite para a compania
