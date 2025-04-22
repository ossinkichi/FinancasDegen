<?php

use app\Controllers\TicketController;

$ticket = new TicketController;
$klein->respond('GET', '/ticket/[i:account]', [$ticket, 'getTicketsForRequest']);
$klein->respond('POST', '/ticket/create', [$ticket, 'create']);
$klein->respond('PATCH', '/ticket/paid', [$ticket, 'paid']);
