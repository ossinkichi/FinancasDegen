<?php

readonly class AccordEntity implements JsonSerializable
{
    public function __construct(
        public int $id,
        public ?int $client,
        public float $price,
        public int $numberOfInstallments,
        public int $installmentsPaid,
        public string $status,
        public ?string $fees,
        public string $requests,
        public string $tickets,
        public bool $deleted = false
    ) {}

    /**
     * Retrieves the AccordEntity instance from the database.
     *
     * @return void
     */
    public static function make(mixed $model): self
    {
        return new self(
            id: $model['id'],
            client: $model['client'],
            price: (float) $model['price'],
            numberOfInstallments: (int) $model['numberofinstallments'],
            installmentsPaid: (int) $model['installmentspaid'],
            status: $model['status'],
            fees: $model['fees'],
            requests: $model['requests'],
            tickets: $model['tickets'],
            deleted: (bool) $model['deleted']
        );
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'client' => $this->client,
            'price' => $this->price,
            'numberOfInstallments' => $this->numberOfInstallments,
            'installmentsPaid' => $this->installmentsPaid,
            'status' => $this->status,
            'fees' => $this->fees,
            'requests' => json_decode($this->requests),
            'tickets' => json_decode($this->tickets),
            'deleted' => $this->deleted,
        ];
    }
}
