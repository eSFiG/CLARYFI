<?php

namespace App\Controller;

use App\Shipping\Service\ShippingCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ShippingController extends AbstractController
{
    public function __construct(
        private readonly ShippingCalculator $shippingCalculator
    ) {
    }

    #[Route('/api/carriers', name: 'api_carriers_list', methods: ['GET'])]
    public function listCarriers(): JsonResponse
    {
        $carriers = $this->shippingCalculator->getSupportedCarriers();

        return $this->json([
            'carriers' => $carriers
        ]);
    }

    #[Route('/api/shipping/calculate', name: 'api_shipping_calculate', methods: ['POST'])]
    public function calculate(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['carrier']) || !isset($data['weightKg'])) {
            return $this->json([
                'error' => 'Missing required fields: carrier and weightKg'
            ], 400);
        }

        try {
            $result = $this->shippingCalculator->calculate(
                $data['carrier'],
                (float) $data['weightKg']
            );

            return $this->json($result);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'An error occurred while calculating shipping'
            ], 500);
        }
    }
}
