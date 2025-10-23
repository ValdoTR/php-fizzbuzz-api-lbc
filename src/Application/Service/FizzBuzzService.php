<?php
declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\FizzBuzzAlgorithm;
use App\Domain\Rule\MultipleRule;
use App\Domain\ValueObject\FizzBuzzResult;

final class FizzBuzzService
{
    public function process(
        int $int1,
        int $int2,
        int $limit,
        string $str1,
        string $str2
    ): FizzBuzzResult {
        // Create rules and algorithm
        $rules = [
            new MultipleRule($int1, $str1),
            new MultipleRule($int2, $str2),
        ];
        
        $algorithm = new FizzBuzzAlgorithm(...$rules);
        $items = $algorithm->generate($limit);
        
        return new FizzBuzzResult($items);
    }
}
