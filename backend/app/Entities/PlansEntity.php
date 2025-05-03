<?php

namespace App\Entities;

class PlansEntity
{

    public function __construct(
        public int $id,
        public string $name,
        public string $describe,
        public int $numberofusers,
        public int $numberofclients,
        public float $price,
        public string $type
    ) {}

    public static function make(mixed $model): self
    {
        return new self(
            id: $model['id'],
            name: $model['name'],
            describe: $model['describe'],
            price: $model['price'],
            type: $model['type'],
            numberofusers: $model['numberofusers'],
            numberofclients: $model['numberofclients']
        );
    }

    public function JsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'describe' => $this->describe,
            'price' => $this->price,
            'type' => $this->type,
            'numberofusers' => $this->numberofusers,
            'numberofclients' => $this->numberofclients
        ];
    }
}
