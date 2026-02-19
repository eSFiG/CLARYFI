<?php

declare(strict_types=1);

namespace App\Shipping\Dto;

class CarriersListResponse
{
    /**
     * @param list<string> $carriers
     */
    public function __construct(private readonly array $carriers) {
    }

    /**
     * @return list<string>
     */
    public function getCarriers(): array
    {
        return $this->carriers;
    }

    /**
     * @return array{carriers: list<string>}
     */
    public function toArray(): array
    {
        return [
            'carriers' => $this->carriers,
        ];
    }
}
