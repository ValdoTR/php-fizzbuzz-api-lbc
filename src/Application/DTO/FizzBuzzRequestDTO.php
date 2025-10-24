<?php
declare(strict_types=1);

namespace App\Application\DTO;

final class FizzBuzzRequestDTO
{
    public function __construct(
        public readonly int $int1,
        public readonly int $int2,
        public readonly int $limit,
        public readonly string $str1,
        public readonly string $str2
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            int1: (int) ($data['int1'] ?? 0),
            int2: (int) ($data['int2'] ?? 0),
            limit: (int) ($data['limit'] ?? 0),
            str1: (string) ($data['str1'] ?? ''),
            str2: (string) ($data['str2'] ?? '')
        );
    }

    public function toArray(): array
    {
        return [
            'int1' => $this->int1,
            'int2' => $this->int2,
            'limit' => $this->limit,
            'str1' => $this->str1,
            'str2' => $this->str2,
        ];
    }
}
