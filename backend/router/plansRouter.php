<?php

use app\controllers\PlansController;

$plans = new PlansController;

$klein->respond('GET', '/plan', [$plans, 'index']);
$klein->respond('POST', '/plan/create', [$plans, 'register']);
$klein->respond('PUT', '/plan/update', [$plans, 'update']);
