<?php

declare(strict_types=1);

namespace Euginepj\PasswordStrength\Facades;

use Euginepj\PasswordStrength\Rules\PasswordStrengthRule;
use Euginepj\PasswordStrength\Services\PasswordStrengthEvaluator;
use Euginepj\PasswordStrength\Services\Result\StrengthResult;
use Illuminate\Support\Facades\Facade;

/**
 * @method static StrengthResult evaluate(?string $password, int $minScore = 3)
 * @method static StrengthResult check(?string $password, int $minScore = 3)
 * @method static PasswordStrengthRule rule()
 *
 * @see PasswordStrengthEvaluator
 */
class PasswordStrength extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'password-strength';
    }

    public static function rule(): PasswordStrengthRule
    {
        return new PasswordStrengthRule;
    }
}
