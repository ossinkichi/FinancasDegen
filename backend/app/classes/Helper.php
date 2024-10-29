<?php

namespace App\Classes;

class Helper
{
    public function verifyMethod(string $method = 'OPTIONS')
    {
        if ($_SERVER['REQUEST_METHOD'] !== $method) {
            $this->message(['error' => 'Método não permitido'], 405);
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->message(['error' => 'Método não permitido'], 405);
                die();
            }
            die();
        }
        dd($_SERVER['REQUEST_METHOD']);
    }

    public function message(array $message, int $code = 200)
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($message);
    }
}
