<?php

declare(strict_types=1);

namespace App\Shipping\Strategy;

class PackGroupStrategy implements CarrierPricingStrategyInterface
{
    private const string CARRIER_NAME = 'PackGroup';
    private const float PRICE_PER_KG = 1.0;

    public function calculate(float $weight): string
    {
        $price = $weight * self::PRICE_PER_KG;

        return number_format($price, 2, '.', '');
    }

    public function getCarrierName(): string
    {
        return self::CARRIER_NAME;
    }

    public function supports(string $carrier): bool
    {
        return self::CARRIER_NAME === $carrier;
    }
}
