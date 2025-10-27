<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\DTO;

use App\Application\DTO\FizzBuzzRequestDTO;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class FizzBuzzRequestDTOTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    public function testFromArrayCreatesValidDTO(): void
    {
        $data = [
            'int1' => 3,
            'int2' => 5,
            'limit' => 15,
            'str1' => 'fizz',
            'str2' => 'buzz',
        ];

        $dto = FizzBuzzRequestDTO::fromArray($data);

        $this->assertSame(3, $dto->int1);
        $this->assertSame(5, $dto->int2);
        $this->assertSame(15, $dto->limit);
        $this->assertSame('fizz', $dto->str1);
        $this->assertSame('buzz', $dto->str2);
    }

    public function testToArrayReturnsCorrectFormat(): void
    {
        $dto = new FizzBuzzRequestDTO(3, 5, 15, 'fizz', 'buzz');
        $array = $dto->toArray();

        $expected = [
            'int1' => 3,
            'int2' => 5,
            'limit' => 15,
            'str1' => 'fizz',
            'str2' => 'buzz',
        ];

        $this->assertEquals($expected, $array);
    }

    public function testValidationPassesForValidData(): void
    {
        $dto = new FizzBuzzRequestDTO(3, 5, 15, 'fizz', 'buzz');
        $violations = $this->validator->validate($dto);

        $this->assertCount(0, $violations);
    }

    public function testValidationFailsForInt1TooLarge(): void
    {
        $dto = new FizzBuzzRequestDTO(1001, 5, 15, 'fizz', 'buzz');
        $violations = $this->validator->validate($dto);

        $this->assertGreaterThan(0, \count($violations));
    }

    public function testValidationFailsForNegativeInt2(): void
    {
        $dto = new FizzBuzzRequestDTO(3, -5, 15, 'fizz', 'buzz');
        $violations = $this->validator->validate($dto);

        $this->assertGreaterThan(0, \count($violations));
    }

    public function testValidationFailsForLimitTooLarge(): void
    {
        $dto = new FizzBuzzRequestDTO(3, 5, 100001, 'fizz', 'buzz');
        $violations = $this->validator->validate($dto);

        $this->assertGreaterThan(0, \count($violations));
    }

    public function testValidationFailsForEmptyStr1(): void
    {
        $dto = new FizzBuzzRequestDTO(3, 5, 15, '', 'buzz');
        $violations = $this->validator->validate($dto);

        $this->assertGreaterThan(0, \count($violations));
    }

    public function testValidationFailsForStr2TooLong(): void
    {
        $dto = new FizzBuzzRequestDTO(
            3,
            5,
            15,
            'fizz',
            \str_repeat('a', 51)
        );
        $violations = $this->validator->validate($dto);

        $this->assertGreaterThan(0, \count($violations));
    }
}
