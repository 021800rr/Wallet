<?php

namespace App\Controller;

use App\Handler\FeeHandler;
use App\Service\FixedFees\FixedFeesInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ApiFeeController extends AbstractController
{
    public function __invoke(FeeHandler $feeHandler, FixedFeesInterface $fixedFees): JsonResponse
    {
        $feeHandler->handle($fixedFees);

        return new JsonResponse(['status' => 'success']);
    }
}
