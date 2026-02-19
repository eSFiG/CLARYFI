<?php

declare(strict_types=1);

namespace App\Shipping\Dto;

class CalculateShippingResponse
{
    public function __construct(
        private readonly string $carrier,
        private readonly float $weightKg,
        private readonly int|float $price,
        private readonly string $currency = 'EUR'
    ) {
    }

    public function getCarrier(): string
    {
        return $this->carrier;
    }

    public function getWeightKg(): float
    {
        return $this->weightKg;
    }

    public function getPrice(): int|float
    {
        return $this->price;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'carrier' => $this->carrier,
            'weightKg' => $this->weightKg,
            'price' => $this->price,
            'currency' => $this->currency,
        ];
    }
}
