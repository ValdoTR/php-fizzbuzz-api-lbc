<?php

declare(strict_types=1);

namespace App\Domain\Rule;

interface FizzBuzzRuleInterface
{
    /**
     * Apply rule to number. Return replacement string or empty string if not applicable.
     */
    public function apply(int $number): string;
}
