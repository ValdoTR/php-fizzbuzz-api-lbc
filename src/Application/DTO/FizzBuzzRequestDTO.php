<?php

declare(strict_types=1);

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class FizzBuzzRequestDTO
{
    public function __construct(
        #[Assert\NotBlank(message: 'is required')]
        #[Assert\Type(type: 'integer', message: 'must be an integer')]
        #[Assert\Positive(message: 'must be positive')]
        #[Assert\LessThanOrEqual(value: 1000, message: 'must be less than or equal to 1000')]
        public int $int1,

        #[Assert\NotBlank(message: 'is required')]
        #[Assert\Type(type: 'integer', message: 'must be an integer')]
        #[Assert\Positive(message: 'must be positive')]
        #[Assert\LessThanOrEqual(value: 1000, message: 'must be less than or equal to 1000')]
        public int $int2,

        #[Assert\NotBlank(message: 'is required')]
        #[Assert\Type(type: 'integer', message: 'must be an integer')]
        #[Assert\Positive(message: 'must be positive')]
        #[Assert\LessThanOrEqual(value: 100000, message: 'must be less than or equal to 100000')]
        public int $limit,

        #[Assert\NotBlank(message: 'is required')]
        #[Assert\Type(type: 'string', message: 'must be a string')]
        #[Assert\Length(max: 50, maxMessage: 'must be at most 50 characters')]
        public string $str1,

        #[Assert\NotBlank(message: 'is required')]
        #[Assert\Type(type: 'string', message: 'must be a string')]
        #[Assert\Length(max: 50, maxMessage: 'must be at most 50 characters')]
        public string $str2
    ) {
    }

    /**
     * Create DTO from array data
     *
     * @param array<string, mixed> $data
     */
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

    /**
     * Convert DTO to array
     *
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        return \get_object_vars($this);
    }
}
