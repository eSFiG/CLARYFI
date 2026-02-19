# Shipping Cost Calculator

A Symfony application to calculate shipping costs across multiple carriers using the Strategy Pattern, with a Vue 3 frontend.

## Project Overview

This project demonstrates clean architecture principles for a shipping cost calculator supporting multiple carriers with different pricing formulas.

## Tech Stack

- **Symfony 7** - PHP framework
- **Vue 3** - Frontend framework via Vite
- **Axios** - HTTP client for API calls
- **PHPUnit** - Testing framework
- **Docker** - Containerization (optional)

## Architecture

The application follows clean architecture with clear separation of concerns:

```
src/
├── Controller/              # HTTP layer (no business logic)
│   └── ShippingController.php
└── Shipping/
    ├── Strategy/            # Strategy Pattern for carrier pricing
    │   ├── CarrierPricingStrategyInterface.php
    │   ├── TransCompanyStrategy.php
    │   └── PackGroupStrategy.php
    ├── Service/             # Business logic orchestration
    │   └── ShippingCalculator.php
    ├── Exception/           # Domain exceptions
    │   └── CarrierNotFoundException.php
    └── Dto/                 # Data Transfer Objects
        ├── CalculateShippingRequest.php
        ├── CalculateShippingResponse.php
        ├── CarriersListResponse.php
        └── ErrorResponse.php
```

### Strategy Pattern

Each carrier implements `CarrierPricingStrategyInterface`:

```php
interface CarrierPricingStrategyInterface
{
    public function calculate(float $weight): string;  // Returns formatted price
    public function getCarrierName(): string;
    public function supports(string $carrier): bool;
}
```

The `ShippingCalculator` service automatically discovers and uses tagged strategy services through Symfony's dependency injection container.

## Setup Instructions

### Option 1: Local Development (Symfony CLI)

```bash
# Install PHP dependencies
composer install

# Install frontend dependencies
npm install

# Start Symfony server (runs at http://127.0.0.1:8000)
symfony server:start

# For development with hot-reload (in another terminal)
npm run dev
```

### Option 2: Docker

```bash
# Build and start containers
docker-compose up --build

# The application will be available at http://localhost:8080
```

## Frontend Build

The Vue 3 frontend is built with Vite:

```bash
# Development server (with hot-reload)
npm run dev

# Production build (outputs to public/)
npm run build

# Preview production build
npm run preview
```

## API Endpoints

### GET /api/carriers

List all available shipping carriers.

**Response (200):**
```json
{
  "carriers": ["PackGroup", "TransCompany"]
}
```

### POST /api/shipping/calculate

Calculate shipping cost for a carrier and weight.

**Request:**
```json
{
  "carrier": "TransCompany",
  "weightKg": 12.5
}
```

**Success Response (200):**
```json
{
  "carrier": "TransCompany",
  "weightKg": 12.5,
  "price": "100.00",
  "currency": "EUR"
}
```

**Error Response (400):**
```json
{
  "error": "Carrier \"InvalidCarrier\" not found."
}
```

## Carrier Pricing Rules

### TransCompany
- Weight <= 10 kg: 20.00 EUR
- Weight > 10 kg: 100.00 EUR

### PackGroup
- 1.00 EUR per kg (e.g., 5 kg = 5.00 EUR)

## Running Tests

```bash
# Run all tests
php bin/phpunit

# Run specific test suite
php bin/phpunit tests/Shipping/Strategy
php bin/phpunit tests/Shipping/Service
php bin/phpunit tests/Shipping/Controller
```

### Test Coverage

- Unit tests for each pricing strategy (`PackGroupStrategyTest`, `TransCompanyStrategyTest`)
- Unit test for ShippingCalculator service (`ShippingCalculatorTest`)
- Integration tests for API endpoints (`ShippingCalculatorControllerTest`)

## Access the Application

| Method | URL |
|--------|-----|
| Symfony CLI | http://127.0.0.1:8000 |
| Docker | http://localhost:8080 |
| Frontend | `/` (index.html) |
| Carriers API | `/api/carriers` |
| Calculate API | `/api/shipping/calculate` |

## How to Add a New Carrier

1. Create a new strategy class in `src/Shipping/Strategy/`:

```php
<?php

declare(strict_types=1);

namespace App\Shipping\Strategy;

class NewCarrierStrategy implements CarrierPricingStrategyInterface
{
    private const string CARRIER_NAME = 'NewCarrier';

    public function calculate(float $weight): string
    {
        // Implement your pricing logic
        $price = /* calculation */;

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
```

2. Clear cache: `symfony console cache:clear`

**No modification of existing code is required.** Symfony's autoconfigure feature automatically registers the new strategy through tagged service configuration in `config/services.yaml`.
