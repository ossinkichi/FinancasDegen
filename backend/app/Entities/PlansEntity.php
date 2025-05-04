<?php

namespace App\Entities;

class PlansEntity
{

    public function __construct(
        public int $id,
        public string $name,
        public string|null $describe,
        public int $numberofusers,
        public int $numberofclients,
        public string $price,
        public string $type,
        public bool $status
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
            numberofclients: $model['numberofclients'],
            status: $model['status']
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
            'numberofclients' => $this->numberofclients,
            'status' => $this->status
        ];
    }
}
