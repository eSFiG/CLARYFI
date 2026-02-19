<?php

declare(strict_types=1);

namespace App\Shipping\Service;

use App\Shipping\Exception\CarrierNotFoundException;
use App\Shipping\Strategy\CarrierPricingStrategyInterface;

class ShippingCalculator
{
    /**
     * @param iterable<CarrierPricingStrategyInterface> $strategies
     */
    public function __construct(
        private readonly iterable $strategies
    ) {
    }

    public function calculate(string $carrier, float $weight): string
    {
        $strategy = $this->findStrategy($carrier);

        return $strategy->calculate($weight);
    }

    /**
     * @return array<string>
     */
    public function getSupportedCarriers(): array
    {
        $carriers = [];
        foreach ($this->strategies as $strategy) {
            $carriers[] = $strategy->getCarrierName();
        }

        return $carriers;
    }

    private function findStrategy(string $carrier): CarrierPricingStrategyInterface
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($carrier)) {
                return $strategy;
            }
        }

        throw new CarrierNotFoundException(sprintf('Carrier "%s" not found.', $carrier));
    }
}
