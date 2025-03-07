<?php

use app\controllers\CompanyController;

$company = new CompanyController;

$klein->respond('GET', '/company', [$company, 'index']);
$klein->respond('GET', '/company/[i:company]', [$company, 'get']);
$klein->respond('POST', '/company/register', [$company, 'register']);
$klein->respond('GET', '/company/delete/[i:company]', [$company, 'delete']);
$klein->respond('PATCH', '/company/update/plan', [$company, 'plan']);
