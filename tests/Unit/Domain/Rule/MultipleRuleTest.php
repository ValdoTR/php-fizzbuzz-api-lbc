<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Rule;

use App\Domain\Rule\MultipleRule;
use PHPUnit\Framework\TestCase;

final class MultipleRuleTest extends TestCase
{
    public function testApplyReturnsReplacementForMultiple(): void
    {
        $rule = new MultipleRule(3, 'fizz');

        $this->assertEquals('fizz', $rule->apply(3));
        $this->assertEquals('fizz', $rule->apply(6));
        $this->assertEquals('fizz', $rule->apply(9));
    }

    public function testApplyReturnsEmptyStringForNonMultiple(): void
    {
        $rule = new MultipleRule(3, 'fizz');

        $this->assertEquals('', $rule->apply(1));
        $this->assertEquals('', $rule->apply(2));
        $this->assertEquals('', $rule->apply(4));
    }
}
