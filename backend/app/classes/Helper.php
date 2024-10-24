<?php

namespace App\Classes;

class Helper
{
    public function verifyMethod(string $method)
    {
        if ($_SERVER['REQUEST_METHOD'] != $method) {
            header('Content-Type: application/json');
            $this->message(['error' => 'Método não permitido'], 405);
        }
    }

    public function message(array $message, int $code = 200)
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($message);
    }
}
