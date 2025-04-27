<?php

class AccordDTO
{
    public function __construct(
        public string $client,
        public float $price,
        public int $numberOfInstallments,
        public int $installmentsPaid,
        public float $fees,
        public array $requests,
        public array $tickets
    ) {}

    public static function make(array $accordPayload): self
    {
        return new self(
            client: $accordPayload['client'],
            price: $accordPayload['price'],
            numberOfInstallments: $accordPayload['numberofinstallments'],
            installmentsPaid: $accordPayload['installmentspaid'],
            fees: $accordPayload['fees'],
            requests: $accordPayload['requests'],
            tickets: $accordPayload['tickets']
        );
    }
}
