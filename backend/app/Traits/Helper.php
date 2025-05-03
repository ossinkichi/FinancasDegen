<?php

namespace App\Traits;

use InvalidArgumentException;

trait Helper
{

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

    public function arrayValidate(array|string $arrayForValidate, array|null $keys = [0]): void
    {

        if (\is_string($arrayForValidate)) {
            empty($arrayForValidate) ? $this->message(['message' => 'Dados não informados'], 400) : null;
            throw new \Exception('Dados não informados');
        }

        \array_walk($keys, function ($key) use ($arrayForValidate) {
            if (!array_key_exists($key, $arrayForValidate)) {
                $this->message(['message' => 'Dados não informados'], 400);
                throw new \Exception('Dados não informados');
            }
            $this->arrayValueNotNull($arrayForValidate[$key]);
        });
        return;
    }

    public function convertType(array $datas, array $types)
    {
        if (count($datas) > count($types)) {
            throw new InvalidArgumentException("O número de valores e tipos não corresponde.");
        }

        return \array_combine(\array_keys($datas), array_map(function ($value, $type) {
            return match ($type) {
                'int' => (int) $value,
                'float' => (float) $value,
                'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                'string' => (string) $value,
                'array' => (array) $value,
                'object' => (object) $value,
                'decimals' => \bcdiv((float) $value, '1', 2),
                default => throw new InvalidArgumentException("Tipo inválido: $type"),
            };
        }, $datas, $types));
    }


    private function arrayValueNotNull(array|string $dataForValidate): void
    {
        if (empty($dataForValidate)) {
            $this->message(['message' => 'Dados não informados'], 400);
            die();
        }
    }
}
