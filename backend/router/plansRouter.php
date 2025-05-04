<?php

use App\Controllers\PlansController;

$plans = new PlansController;

$klein->respond('GET', '/plan', [$plans, 'plans']);
// $klein->respond('POST', '/plan/create', [$plans, 'register']);
// $klein->respond('PUT', '/plan/update', [$plans, 'update']);
// $klein->respond('PATCH', '/plan/enable', [$plans, 'enable']);
// $klein->respond('PATCH', '/plan/disable', [$plans, 'disable']);
