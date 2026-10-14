<?php

declare(strict_types=1);

namespace Euginepj\PasswordStrength\Tests;

use Euginepj\PasswordStrength\Facades\PasswordStrength;
use Euginepj\PasswordStrength\Rules\PasswordStrengthRule;
use Euginepj\PasswordStrength\Services\PasswordStrengthEvaluator;
use Illuminate\Support\Facades\Validator;

class PasswordStrengthTest extends TestCase
{
    public function test_empty_password_evaluates_to_very_weak(): void
    {
        $evaluator = new PasswordStrengthEvaluator();
        $result = $evaluator->evaluate('');

        $this->assertSame(0, $result->score);
        $this->assertSame(0, $result->percentage);
        $this->assertSame('very_weak', $result->level);
        $this->assertFalse($result->passed);
        $this->assertTrue($result->isWeak());
    }

    public function test_common_passwords_are_flagged_and_fail(): void
    {
        $evaluator = new PasswordStrengthEvaluator();
        $result = $evaluator->evaluate('password123');

        $this->assertSame(0, $result->score);
        $this->assertFalse($result->passed);
        $this->assertFalse($result->checks['not_common']);
    }

    public function test_sequential_patterns_are_flagged(): void
    {
        $evaluator = new PasswordStrengthEvaluator();
        $result = $evaluator->evaluate('Abcd12345678#');

        $this->assertFalse($result->checks['not_sequential']);
    }

    public function test_strong_password_passes(): void
    {
        $evaluator = new PasswordStrengthEvaluator();
        $result = $evaluator->evaluate('X7#k9P$mQ2@zL8!');

        $this->assertGreaterThanOrEqual(3, $result->score);
        $this->assertTrue($result->passed);
        $this->assertTrue($result->isStrong());
        $this->assertNotEmpty($result->toArray());
        $this->assertJson($result->toJson());
    }

    public function test_facade_evaluation(): void
    {
        $result = PasswordStrength::evaluate('Str0ngP@ssw0rd!');

        $this->assertTrue($result->passed);
        $this->assertGreaterThanOrEqual(3, $result->score);
    }

    public function test_validation_rule_fails_for_weak_password(): void
    {
        $validator = Validator::make(
            ['password' => 'short'],
            ['password' => [new PasswordStrengthRule()]]
        );

        $this->assertTrue($validator->fails());
    }

    public function test_validation_rule_passes_for_strong_password(): void
    {
        $validator = Validator::make(
            ['password' => 'Secure#P@ssw0rd2026'],
            ['password' => [new PasswordStrengthRule()]]
        );

        $this->assertTrue($validator->passes());
    }

    public function test_fluent_rule_configuration(): void
    {
        $rule = PasswordStrength::rule()
            ->min(12)
            ->requireSymbols()
            ->minScore(3);

        $validator = Validator::make(
            ['password' => 'Short1#'],
            ['password' => [$rule]]
        );

        $this->assertTrue($validator->fails());
    }
}
