<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\Rule\FizzBuzzRuleInterface;

/**
 * Core FizzBuzz algorithm using Strategy pattern
 *
 * This class is framework-agnostic and contains pure business logic.
 * Rules can be composed to create custom FizzBuzz variants.
 *
 * Complexity: O(n) where n is the limit
 */
final class FizzBuzzAlgorithm
{
    /** @var FizzBuzzRuleInterface[] */
    private array $rules;

    public function __construct(FizzBuzzRuleInterface ...$rules)
    {
        $this->rules = $rules;
    }

    /**
     * Generate FizzBuzz sequence from 1 to limit
     *
     * @param int $limit Upper bound (inclusive)
     *
     * @return string[] Array of strings representing the sequence
     */
    public function generate(int $limit): array
    {
        $result = [];
        for ($i = 1; $i <= $limit; ++$i) {
            $result[] = $this->applyRules($i);
        }

        return $result;
    }

    /**
     * Apply all rules to a number and return the result
     *
     * If no rules match, return the number itself as a string.
     * If multiple rules match, concatenate their replacements.
     *
     * @param int $number Number to process
     *
     * @return string The transformed value
     */
    private function applyRules(int $number): string
    {
        $output = '';
        foreach ($this->rules as $rule) {
            $output .= $rule->apply($number);
        }

        return '' !== $output ? $output : (string) $number;
    }
}
