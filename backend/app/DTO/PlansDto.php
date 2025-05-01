<?php

namespace App\DTO\m;

class PlansDto
{

    public function __construct(
        public string $name,
        public string $description,
        public string $price,
        public string $type,
        public int $numberofusers,
        public int $numberofclients
    ) {}

    public static function make(array $planPayload): self
    {
        return new self(
            name: $planPayload['name'] ?? '',
            description: $planPayload['description'] ?? '',
            price: $planPayload['price'] ?? '',
            type: $planPayload['type'] ?? '',
            numberofusers: $planPayload['numberofusers'] ?? 0,
            numberofclients: $planPayload['numberofclients'] ?? 0
        );
    }
}
