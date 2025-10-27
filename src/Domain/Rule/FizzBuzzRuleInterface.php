<?php

declare(strict_types=1);

namespace App\Domain\Rule;

/**
 * Strategy interface for FizzBuzz rules
 *
 * Implementations define when and how to replace numbers with strings.
 * This follows the Strategy and Open/Closed principles.
 */
interface FizzBuzzRuleInterface
{
    /**
     * Apply rule to a number
     *
     * @param int $number Number to check
     *
     * @return string Replacement string if rule applies, empty string otherwise
     */
    public function apply(int $number): string;
}
