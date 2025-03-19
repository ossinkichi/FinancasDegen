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

    public function sanitizeArray(array $datas)
    {
        return array_map('htmlspecialchars', $datas);
    }

    public function getData(string $input)
    {
        return get_object_vars(json_decode($input));
    }

    public function arrayValidate(array|string $arrayForValidate, array $keys): void
    {
        if (\is_string($arrayForValidate)) {
            $arrayForValidate = $this->getData($arrayForValidate);
        }

        \array_walk($keys, function ($key) use ($arrayForValidate) {
            if (!array_key_exists($key, $arrayForValidate)) {
                $this->message(['message' => 'Dados não informados'], 400);
                die();
            }
            $this->arrayValueNotNull($arrayForValidate[$key]);
        });
        return;
    }

    public function convertType(array $data, array $types)
    {
        $keys = array_keys($data); // Obtém as chaves originais

        return array_combine($keys, array_map(function ($value, $index) use ($types) {
            if (!isset($types[$index])) {
                return $value; // Mantém o valor original se não houver um tipo correspondente
            }

            return match ($types[$index]) {
                'int' => (int) $value,
                'float' => (float) $value,
                'string' => (string) $value,
                default => $value, // Se o tipo não for reconhecido
            };
        }, $data, array_keys($data)));
    }


    private function arrayValueNotNull(array|string $dataForValidate): void
    {
        if (empty($dataForValidate)) {
            $this->message(['message' => 'Dados não informados'], 400);
            die();
        }
    }
}
