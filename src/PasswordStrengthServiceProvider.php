<?php

declare(strict_types=1);

namespace Euginepj\PasswordStrength;

use Euginepj\PasswordStrength\Services\PasswordStrengthEvaluator;
use Illuminate\Support\ServiceProvider;

class PasswordStrengthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/password-strength.php',
            'password-strength'
        );

        $this->app->singleton('password-strength', function () {
            return new PasswordStrengthEvaluator;
        });
    }

    public function boot(): void
    {
        $this->loadTranslationsFrom(
            __DIR__.'/../resources/lang',
            'password-strength'
        );

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/password-strength.php' => config_path('password-strength.php'),
            ], 'password-strength-config');

            $this->publishes([
                __DIR__.'/../resources/lang' => lang_path('vendor/password-strength'),
            ], 'password-strength-lang');
        }
    }
}
