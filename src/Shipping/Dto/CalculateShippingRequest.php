<?php

declare(strict_types=1);

namespace App\Shipping\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CalculateShippingRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Carrier is required.')]
        private readonly string $carrier,

        #[Assert\NotNull(message: 'Weight is required.')]
        #[Assert\Positive(message: 'Weight must be positive.')]
        private readonly float $weightKg
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
}
