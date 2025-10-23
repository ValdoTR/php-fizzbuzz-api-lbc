<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain;

use App\Domain\FizzBuzzAlgorithm;
use App\Domain\Rule\MultipleRule;
use PHPUnit\Framework\TestCase;

final class FizzBuzzAlgorithmTest extends TestCase
{
    public function testClassicFizzBuzz(): void
    {
        $rules = [
            new MultipleRule(3, 'fizz'),
            new MultipleRule(5, 'buzz'),
        ];
        $algorithm = new FizzBuzzAlgorithm(...$rules);

        $result = $algorithm->generate(15);

        $expected = [
            '1', '2', 'fizz', '4', 'buzz', 'fizz', '7', '8',
            'fizz', 'buzz', '11', 'fizz', '13', '14', 'fizzbuzz',
        ];

        $this->assertEquals($expected, $result);
    }

    public function testCustomParameters(): void
    {
        $rules = [
            new MultipleRule(2, 'foo'),
            new MultipleRule(3, 'bar'),
        ];
        $algorithm = new FizzBuzzAlgorithm(...$rules);

        $result = $algorithm->generate(6);

        $expected = ['1', 'foo', 'bar', 'foo', '5', 'foobar'];

        $this->assertEquals($expected, $result);
    }

    public function testSingleRule(): void
    {
        $rules = [new MultipleRule(2, 'even')];
        $algorithm = new FizzBuzzAlgorithm(...$rules);

        $result = $algorithm->generate(5);

        $expected = ['1', 'even', '3', 'even', '5'];

        $this->assertEquals($expected, $result);
    }

    public function testLimitOne(): void
    {
        $rules = [new MultipleRule(1, 'all')];
        $algorithm = new FizzBuzzAlgorithm(...$rules);

        $result = $algorithm->generate(1);

        $this->assertEquals(['all'], $result);
    }
}
