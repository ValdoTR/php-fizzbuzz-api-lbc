<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

final class FizzBuzzResult
{
    /** @param string[] $items */
    public function __construct(private readonly array $items)
    {
    }

    /** @return string[] */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getCount(): int
    {
        return count($this->items);
    }
}
