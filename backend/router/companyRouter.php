<?php

use App\Controllers\CompanyController;

$company = new CompanyController;

$klein->respond('GET', '/company', [$company, 'index']);
$klein->respond('GET', '/company/[h:company]', [$company, 'get']);
$klein->respond('POST', '/company/register', [$company, 'register']);
$klein->respond('DELETE', '/company/delete/[h:company]', [$company, 'delete']);
$klein->respond('PATCH', '/company/update/plan', [$company, 'changeOfPlan']);
