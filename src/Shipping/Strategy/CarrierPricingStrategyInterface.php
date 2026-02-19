<?php

declare(strict_types=1);

namespace App\Shipping\Strategy;

interface CarrierPricingStrategyInterface
{
    public function calculate(float $weight): string;

    public function getCarrierName(): string;

    public function supports(string $carrier): bool;
}
