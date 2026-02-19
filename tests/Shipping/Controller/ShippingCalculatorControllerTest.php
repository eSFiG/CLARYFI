<?php

declare(strict_types=1);

namespace App\Tests\Shipping\Controller;

use App\Tests\WebTestCase;

final class ShippingCalculatorControllerTest extends WebTestCase
{
    public function testCalculateSuccess(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/shipping/calculate',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['carrier' => 'TransCompany', 'weightKg' => 15.5])
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame('TransCompany', $response['carrier']);
        $this->assertSame(15.5, $response['weightKg']);
        $this->assertEquals(100.0, $response['price'], 'Price should be 100', 0.01);
        $this->assertSame('EUR', $response['currency']);
    }

    public function testCalculatePackgroup(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/shipping/calculate',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['carrier' => 'PackGroup', 'weightKg' => 5.5])
        );

        $this->assertResponseIsSuccessful();

        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame(5.5, $response['price']);
    }

    public function testCalculateInvalidCarrier(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/shipping/calculate',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['carrier' => 'invalid', 'weightKg' => 10.0])
        );

        // Invalid carrier returns 404 (carrier not found)
        $this->assertResponseStatusCodeSame(404);

        $content = $client->getResponse()->getContent();
        $this->assertNotEmpty($content);

        $response = json_decode($content, true);
        $this->assertIsArray($response);
        $this->assertArrayHasKey('error', $response);
    }

    public function testCalculateMissingCarrier(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/shipping/calculate',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['weightKg' => 10.0])
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testCalculateNegativeWeight(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/shipping/calculate',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['carrier' => 'TransCompany', 'weightKg' => -5.0])
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testListCarriers(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/carriers');

        $this->assertResponseIsSuccessful();

        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('carriers', $response);
        $this->assertContains('TransCompany', $response['carriers']);
        $this->assertContains('PackGroup', $response['carriers']);
    }
}
