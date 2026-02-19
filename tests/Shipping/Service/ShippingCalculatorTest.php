<?php

declare(strict_types=1);

namespace App\Tests\Shipping\Service;

use App\Shipping\Exception\CarrierNotFoundException;
use App\Shipping\Service\ShippingCalculator;
use App\Shipping\Strategy\PackGroupStrategy;
use App\Shipping\Strategy\TransCompanyStrategy;
use PHPUnit\Framework\TestCase;

final class ShippingCalculatorTest extends TestCase
{
    private ShippingCalculator $calculator;

    protected function setUp(): void
    {
        $strategies = [
            new TransCompanyStrategy(),
            new PackGroupStrategy(),
        ];
        $this->calculator = new ShippingCalculator($strategies);
    }

    public function testCalculateTranscompanyLowWeight(): void
    {
        $price = $this->calculator->calculate('TransCompany', 5.0);

        $this->assertSame('20.00', $price);
    }

    public function testCalculateTranscompanyHighWeight(): void
    {
        $price = $this->calculator->calculate('TransCompany', 15.0);

        $this->assertSame('100.00', $price);
    }

    public function testCalculatePackgroup(): void
    {
        $price = $this->calculator->calculate('PackGroup', 7.5);

        $this->assertSame('7.50', $price);
    }

    public function testThrowsExceptionForUnsupportedCarrier(): void
    {
        $this->expectException(CarrierNotFoundException::class);
        $this->expectExceptionMessage('Carrier "unknown" not found.');

        $this->calculator->calculate('unknown', 10.0);
    }

    public function testGetSupportedCarriers(): void
    {
        $carriers = $this->calculator->getSupportedCarriers();

        $this->assertCount(2, $carriers);
        $this->assertContains('TransCompany', $carriers);
        $this->assertContains('PackGroup', $carriers);
    }
}
