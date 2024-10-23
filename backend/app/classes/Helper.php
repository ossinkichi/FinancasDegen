<?php

namespace App\Classes;

class Helper
{
    public function verifyMethod(string $method)
    {
        if ($_SERVER['REQUEST_METHOD'] != $method) {
            header('Content-Type: application/json');
            $this->message(['error' => 'metodo não permitido'], 405);
        }
    }

    public function handlerError(array $message, array $fields, int $code)
    {
        http_response_code($code);
        echo json_encode(['error' => $message, 'filds' => $fields]);
    }

    public function message(array $message, int $code = 200)
    {
        http_response_code($code);
        echo json_encode($message);
    }
}
