<?php

namespace app\classes;

class Helper
{
    public function verifyMethod(string $method)
    {
        cors($method);
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== $method) {
            $this->message(['error' => 'Método não permitido'], 405);
            die();
        }
    }

    public function message(array $message, int $code = 200)
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($message);
    }

    public function sanitizeArray(array $data)
    {
        foreach ($data as $key => $value) {
            $data[$key] = htmlspecialchars($value);
        }
        return $data;
    }

    public function getData(string $input)
    {
        return get_object_vars(json_decode($input));
    }
}
