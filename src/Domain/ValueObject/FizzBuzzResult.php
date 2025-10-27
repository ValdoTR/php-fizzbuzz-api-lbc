<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

/**
 * Immutable value object representing FizzBuzz result
 *
 * This encapsulates the generated sequence and provides type safety.
 */
final readonly class FizzBuzzResult
{
    /**
     * @param string[] $items The generated FizzBuzz sequence
     */
    public function __construct(
        private array $items
    ) {
    }

    /**
     * Get the sequence items
     *
     * @return string[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Get the count of items in the sequence
     */
    public function getCount(): int
    {
        return \count($this->items);
    }
}
