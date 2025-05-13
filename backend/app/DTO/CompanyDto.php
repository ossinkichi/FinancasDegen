<?php

namespace App\DTO;

class CompanyDto
{

    public function __construct(
        public string $name,
        public string $cnpj,
        public string $describe,
        public ?string $phone,
        public ?string $email,
        public bool $status,
        public int $plan
    ) {}

    public static function make(mixed $model): self
    {
        return new self(
            name: $model['name'],
            describe: $model['describe'],
            cnpj: $model['cnpj'],
            phone: $model['phone'],
            email: $model['email'],
            status: (bool)$model['status'],
            plan: (int)$model['plan']
        );
    }
}
