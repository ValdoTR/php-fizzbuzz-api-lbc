<?php

declare(strict_types=1);

namespace App\Domain\Rule;

final class MultipleRule implements FizzBuzzRuleInterface
{
    public function __construct(
        private readonly int $divisor,
        private readonly string $replacement,
    ) {
    }

    public function apply(int $number): string
    {
        return (0 === $number % $this->divisor) ? $this->replacement : '';
    }
}
