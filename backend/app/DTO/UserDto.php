<?php

namespace App\DTO;

class UserDto
{

    public function __construct(
        public ?string $userhash,
        public string $name,
        public string $email,
        public ?string $password = null,
        public string $cpf,
        public string $dateofbirth,
        public string $gender,
        public string $phone,
        public ?string $company = null,
        public string $position = 'funcionario'
    ) {}

    public static function make(array $userPayload): self
    {
        return new self(
            userhash: $userPayload['userhash'] = null,
            name: $userPayload['name'],
            email: $userPayload['email'],
            password: $userPayload['password'] ?? null,
            cpf: $userPayload['cpf'],
            gender: $userPayload['gender'],
            phone: $userPayload['phone'],
            dateofbirth: $userPayload['dateofbirth'] ?? date('Y-m-d', strtotime($userPayload['dateofbirth'])),
            company: $userPayload['company'] ?? null,
            position: $userPayload['position'] ?? 'funcionario',
        );
    }
}
