<?php

namespace App\Entities;

class CompaniesEntity
{

    public function __construct(
        public int $id,
        public string $name,
        public string $cnpj,
        public string $describe,
        public ?string $phone,
        public ?string $email,
        public bool $status
    ) {}

    public static function make(mixed $model): self
    {
        return new self(
            id: $model['id'],
            name: $model['name'],
            cnpj: $model['cnpj'],
            describe: $model['describe'],
            phone: $model['phone'],
            email: $model['email'],
            status: (bool)$model['status']
        );
    }

    public function JsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'cnpj' => $this->cnpj,
            'describe' => $this->describe,
            'phone' => $this->phone,
            'email' => $this->email,
            'status' => (bool)$this->status
        ];
    }
}
