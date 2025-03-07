<?php

namespace app\classes;

use app\Classes\JwtHelper;

class Helper
{
    private JwtHelper $jwt;

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

    public function message(array $message, int $code = 200): void
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

    public function arrayValidate(array|string $arrayForValidate, array $keys): void
    {
        is_string($arrayForValidate) ? $arrayForValidate = $this->getData($arrayForValidate) : null;
        foreach ($keys as $key) {
            if (!array_key_exists($key, $arrayForValidate)) {
                $this->message(['message' => 'Dados não informados'], 400);
                die();
            }
            $this->arrayValueNotNull($key);
        }
        return;
    }

    private function arrayValueNotNull(array|string $dataForValidate): void
    {
        if (empty($dataForValidate)) {
            $this->message(['message' => 'Dados não informados'], 400);
            die();
        }
    }
}
