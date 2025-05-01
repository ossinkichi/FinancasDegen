<?php

namespace App\DTO;

class UserDto
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
        public ?string $position = null
    ) {}

    public static function make(array $userPayload): self
    {
        return new self(
            userhash: $userPayload['userhash'],
            name: $userPayload['name'],
            email: $userPayload['email'],
            password: $userPayload['password'],
            cpf: $userPayload['cpf'],
            gender: $userPayload['gender'],
            position: $userPayload['position'] ?? null,
            phone: $userPayload['phone'],
            dateofbirth: $userPayload['dateofbirth'] ?? date('Y-m-d', strtotime($userPayload['dateofbirth'])),
        );
    }
}
