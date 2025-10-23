<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\Rule\FizzBuzzRuleInterface;

final class FizzBuzzAlgorithm
{
    /** @var FizzBuzzRuleInterface[] */
    private array $rules;

    public function __construct(FizzBuzzRuleInterface ...$rules)
    {
        $this->rules = $rules;
    }

    /**
     * Generate FizzBuzz sequence from 1 to limit.
     *
     * @return string[]
     */
    public function generate(int $limit): array
    {
        $result = [];
        for ($i = 1; $i <= $limit; ++$i) {
            $result[] = $this->applyRules($i);
        }

        return $result;
    }

    private function applyRules(int $number): string
    {
        $output = '';
        foreach ($this->rules as $rule) {
            $output .= $rule->apply($number);
        }

        return '' !== $output ? $output : (string) $number;
    }
}
