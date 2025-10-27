<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\FizzBuzzAlgorithm;
use App\Domain\Rule\MultipleRule;
use App\Domain\ValueObject\FizzBuzzResult;

/**
 * Main business service for FizzBuzz operations
 *
 * Orchestrates domain logic and statistics tracking
 */
final readonly class FizzBuzzService
{
    public function __construct(
        private StatisticsService $statisticsService
    ) {
    }

    /**
     * Process FizzBuzz request with custom parameters
     *
     * @param int $int1 First divisor
     * @param int $int2 Second divisor
     * @param int $limit Upper bound of sequence
     * @param string $str1 Replacement for int1 multiples
     * @param string $str2 Replacement for int2 multiples
     */
    public function process(
        int $int1,
        int $int2,
        int $limit,
        string $str1,
        string $str2
    ): FizzBuzzResult {
        // Create rules using Strategy pattern
        $rules = [
            new MultipleRule($int1, $str1),
            new MultipleRule($int2, $str2),
        ];

        // Execute core algorithm
        $algorithm = new FizzBuzzAlgorithm(...$rules);
        $items = $algorithm->generate($limit);

        // Track request for statistics
        $this->statisticsService->recordRequest([
            'int1' => $int1,
            'int2' => $int2,
            'limit' => $limit,
            'str1' => $str1,
            'str2' => $str2,
        ]);

        return new FizzBuzzResult($items);
    }
}
