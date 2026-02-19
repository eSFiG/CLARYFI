<?php

declare(strict_types=1);

namespace App\Shipping\Strategy;

class TransCompanyStrategy implements CarrierPricingStrategyInterface
{
    private const string CARRIER_NAME = 'TransCompany';
    private const float PRICE_LOW_WEIGHT = 20.0;
    private const float PRICE_HIGH_WEIGHT = 100.0;
    private const float WEIGHT_THRESHOLD = 10.0;

    public function calculate(float $weight): string
    {
        $price = $weight <= self::WEIGHT_THRESHOLD ? self::PRICE_LOW_WEIGHT : self::PRICE_HIGH_WEIGHT;

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
