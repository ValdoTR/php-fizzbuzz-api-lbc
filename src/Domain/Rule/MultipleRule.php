<?php

declare(strict_types=1);

namespace App\Domain\Rule;

/**
 * Rule that replaces multiples of a divisor with a string
 *
 * Example: MultipleRule(3, 'fizz') replaces 3, 6, 9, ... with 'fizz'
 */
final readonly class MultipleRule implements FizzBuzzRuleInterface
{
    public function __construct(
        private int $divisor,
        private string $replacement
    ) {
    }

    public function apply(int $number): string
    {
        return (0 === $number % $this->divisor) ? $this->replacement : '';
    }
}
