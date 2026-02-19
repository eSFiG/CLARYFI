<?php

declare(strict_types=1);

namespace App\Tests\Shipping\Strategy;

use App\Shipping\Strategy\PackGroupStrategy;
use PHPUnit\Framework\TestCase;

final class PackGroupStrategyTest extends TestCase
{
    private PackGroupStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new PackGroupStrategy();
    }

    public function testCalculateOneKilogram(): void
    {
        $price = $this->strategy->calculate(1.0);

        $this->assertSame('1.00', $price);
    }

    public function testCalculateDecimalWeight(): void
    {
        $price = $this->strategy->calculate(12.5);

        $this->assertSame('12.50', $price);
    }

    public function testCalculateZeroPointOne(): void
    {
        $price = $this->strategy->calculate(0.1);

        $this->assertSame('0.10', $price);
    }

    public function testGetCarrierName(): void
    {
        $this->assertSame('PackGroup', $this->strategy->getCarrierName());
    }

    public function testSupportsPackgroup(): void
    {
        $this->assertTrue($this->strategy->supports('PackGroup'));
    }

    public function testDoesNotSupportOtherCarrier(): void
    {
        $this->assertFalse($this->strategy->supports('TransCompany'));
    }
}
