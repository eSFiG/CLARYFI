<?php

declare(strict_types=1);

namespace App\Controller;

use App\Shipping\Dto\CalculateShippingRequest;
use App\Shipping\Dto\CalculateShippingResponse;
use App\Shipping\Exception\CarrierNotFoundException;
use App\Shipping\Service\ShippingCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ShippingCalculatorController extends AbstractController
{
    public function __construct(
        private readonly ShippingCalculator $calculator,
        private readonly ValidatorInterface $validator
    ) {
    }

    #[Route('/api/shipping/calculate', name: 'shipping_calculate', methods: ['POST'])]
    public function calculate(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, flags: JSON_THROW_ON_ERROR);

            $dto = new CalculateShippingRequest(
                carrier: $data['carrier'] ?? '',
                weightKg: $data['weightKg'] ?? 0
            );

            $violations = $this->validator->validate($dto);
            if (count($violations) > 0) {
                return $this->validationError($violations);
            }

            $price = $this->calculator->calculate($dto->getCarrier(), $dto->getWeightKg());

            $response = new CalculateShippingResponse(
                carrier: $dto->getCarrier(),
                weightKg: $dto->getWeightKg(),
                price: (float) $price
            );

            return $this->json($response->toArray());
        } catch (CarrierNotFoundException $e) {
            return $this->json(['error' => 'Unsupported carrier'], Response::HTTP_NOT_FOUND);
        } catch (\JsonException $e) {
            return $this->json(['error' => 'Invalid JSON payload'], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/carriers', name: 'carriers_list', methods: ['GET'])]
    public function listCarriers(): JsonResponse
    {
        $carriers = $this->calculator->getSupportedCarriers();

        return $this->json(['carriers' => $carriers]);
    }

    private function validationError($violations): JsonResponse
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = [
                'field' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
            ];
        }

        return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }
}
