<?php

use app\classes\Ultils;
use Klein\Klein;
use Klein\Request;
use Klein\Response;

$klein = new Klein();

$klein->respond('GET', '/', function ($request,  $response) {
    $ultils = new Ultils();
    $ultils->createTables();
    return $response->code(201)->header('Content-Type', 'aplication/json')->body();
});

require __DIR__ . '/../router/clientRouter.php';
require __DIR__ . '/../router/companyRouter.php';
require __DIR__ . '/../router/plansRouter.php';
require __DIR__ . '/../router/requestRouter.php';
require __DIR__ . '/../router/userRouter.php';

try {
    $klein->dispatch();
} catch (Exception $e) {
    error_log("Erro na rota: " . $e->getMessage()); // Log do erro
    echo json_encode(["error" => "Ocorreu um erro interno"]); // Retorno amigável
}
