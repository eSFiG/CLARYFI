<?php

declare(strict_types=1);

namespace App\Tests\Shipping\Strategy;

use App\Shipping\Strategy\TransCompanyStrategy;
use PHPUnit\Framework\TestCase;

final class TransCompanyStrategyTest extends TestCase
{
    private TransCompanyStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new TransCompanyStrategy();
    }

    public function testCalculateLowWeight(): void
    {
        $price = $this->strategy->calculate(5.0);

        $this->assertSame('20.00', $price);
    }

    public function testCalculateExactThresholdWeight(): void
    {
        $price = $this->strategy->calculate(10.0);

        $this->assertSame('20.00', $price);
    }

    public function testCalculateHighWeight(): void
    {
        $price = $this->strategy->calculate(15.5);

        $this->assertSame('100.00', $price);
    }

    public function testGetCarrierName(): void
    {
        $this->assertSame('TransCompany', $this->strategy->getCarrierName());
    }

    public function testSupportsTranscompany(): void
    {
        $this->assertTrue($this->strategy->supports('TransCompany'));
    }

    public function testDoesNotSupportOtherCarrier(): void
    {
        $this->assertFalse($this->strategy->supports('PackGroup'));
    }
}
