<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Infrastructure\Repository\StatisticsRepository;

/**
 * Service for tracking and retrieving request statistics
 */
final readonly class StatisticsService
{
    public function __construct(
        private StatisticsRepository $repository
    ) {
    }

    /**
     * Record a FizzBuzz request for statistics tracking
     *
     * @param array<string, int|string> $parameters
     */
    public function recordRequest(array $parameters): void
    {
        $this->repository->incrementRequestCount($parameters);
    }

    /**
     * Get the most frequently requested parameters
     *
     * @return array{parameters: array<string, int|string>, count: int}|null
     */
    public function getMostFrequentRequest(): ?array
    {
        return $this->repository->getMostFrequent();
    }
}
