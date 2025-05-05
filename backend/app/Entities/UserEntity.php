<?php

namespace App\Entities;

class UserEntity
{

    public function __construct(
        public string $userhash,
        public string $name,
        public string $email,
        public string $password,
        public string $cpf,
        public string $dateofbirth,
        public string $gender,
        public string $phone,
        public ?string $company = null,
        public ?string $position = 'funcionario'
    ) {}

    public static function make(array $model): self
    {
        return new self(
            userhash: $model['userhash'],
            name: $model['name'],
            email: $model['email'],
            password: $model['password'],
            cpf: $model['cpf'],
            dateofbirth: $model['dateofbirth'],
            gender: $model['gender'],
            phone: $model['phone'],
            company: $model['company'] ?? null,
            position: $model['position'] ?? 'funcionario'
        );
    }

    public function JsonSerialize(): array
    {
        return [
            'userhash' => $this->userhash,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'cpf' => $this->cpf,
            'dateofbirth' => $this->dateofbirth,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'company' => $this->company,
            'position' => $this->position
        ];
    }
}
