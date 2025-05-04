<?php

namespace App\DTO;

class PlansDto
{

    public function __construct(
        public int|null $id,
        public string $name,
        public string $describe,
        public string $price,
        public string $type,
        public int $numberofusers,
        public int $numberofclients
    ) {}

    public static function make(array $planPayload): self
    {
        return new self(
            id: $planPayload['id'],
            name: $planPayload['name'],
            describe: $planPayload['describe'],
            price: $planPayload['price'],
            type: $planPayload['type'],
            numberofusers: $planPayload['numberofusers'],
            numberofclients: $planPayload['numberofclients']
        );
    }
}
