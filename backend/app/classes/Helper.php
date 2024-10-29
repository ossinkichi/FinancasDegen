<?php

namespace App\Classes;

class Helper
{
    public function verifyMethod(string $method)
    {
        if ($_SERVER['REQUEST_METHOD'] != strtoupper($method)) {
            $this->message(['error' => 'Método não permitido'], 405);
            die();
        }
    }

    public function message(array $message, int $code = 200)
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($message);
        return;
    }
}
