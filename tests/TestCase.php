<?php

declare(strict_types=1);

namespace Euginepj\PasswordStrength\Tests;

use Euginepj\PasswordStrength\PasswordStrengthServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            PasswordStrengthServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('password-strength.min_length', 8);
        $app['config']->set('password-strength.optimal_length', 12);
        $app['config']->set('password-strength.min_score', 3);
        $app['config']->set('password-strength.require_letters', true);
        $app['config']->set('password-strength.require_mixed_case', true);
        $app['config']->set('password-strength.require_numbers', true);
        $app['config']->set('password-strength.require_symbols', true);
        $app['config']->set('password-strength.disallow_common', true);
        $app['config']->set('password-strength.disallow_sequential', true);
    }
}
