<?php
declare(strict_types=1);

namespace App\Application\Service;

use App\Infrastructure\Repository\StatisticsRepository;

final class StatisticsService
{
    public function __construct(
        private readonly StatisticsRepository $repository
    ) {}

    public function recordRequest(array $parameters): void
    {
        $this->repository->incrementRequestCount($parameters);
    }

    public function getMostFrequentRequest(): ?array
    {
        return $this->repository->getMostFrequent();
    }
}
