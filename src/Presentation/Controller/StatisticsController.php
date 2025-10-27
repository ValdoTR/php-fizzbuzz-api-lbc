<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Service\StatisticsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StatisticsController extends AbstractController
{
    public function __construct(
        private readonly StatisticsService $statisticsService
    ) {
    }

    #[Route('/api/fizzbuzz/stats', name: 'fizzbuzz_stats', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $stats = $this->statisticsService->getMostFrequentRequest();

        if (null === $stats) {
            return $this->json(null, Response::HTTP_NO_CONTENT);
        }

        return $this->json($stats, Response::HTTP_OK);
    }
}
