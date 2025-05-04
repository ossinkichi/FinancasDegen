<?php

use App\Classes\Ultils;
use Klein\Klein;
use Klein\Request;
use Klein\Response;

$klein = new Klein();

// Middleware para adicionar headers CORS
$klein->respond(function (Request $request, Response $response) {
    $origin = $request->headers()->get('Origin');

    // Permite qualquer origem (ou troque por um domínio específico)
    $response->header('Access-Control-Allow-Origin', $origin ?: '*');
    $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    $response->header('Access-Control-Allow-Credentials', 'true');

    // Trata requisições OPTIONS diretamente
    if ($request->method() === 'OPTIONS') {
        $response->code(200);
        return $response;
    }
});

$klein->respond('GET', '/', function ($request,  $response) {
    $ultils = new Ultils();
    $ultils->createTables();
    return $response->code(201)->header('Content-Type', 'aplication/json')->body();
});

require __DIR__ . '/../router/plansRouter.php';
/*
require __DIR__ . '/../router/userRouter.php';
require __DIR__ . '/../router/companyRouter.php';
require __DIR__ . '/../router/clientRouter.php';
require __DIR__ . '/../router/requestRouter.php';
require __DIR__ . '/../router/ticketRouter.php';
*/
try {
    $klein->dispatch();
} catch (Exception $e) {
    error_log("Erro na rota: " . $e->getMessage() . ', no arquivo: ' . $e->getFile() . ', na linha: ' . $e->getLine()); // Log do erro
    echo json_encode(["error" => "Ocorreu um erro interno"]); // Retorno amigável
}
