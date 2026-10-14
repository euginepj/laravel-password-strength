<?php

declare(strict_types=1);

namespace Euginepj\PasswordStrength\Rules;

use Closure;
use Euginepj\PasswordStrength\Services\PasswordStrengthEvaluator;
use Illuminate\Contracts\Validation\ValidationRule;

class PasswordStrengthRule implements ValidationRule
{
    protected int $min;

    protected bool $requireLetters;

    protected bool $requireMixedCase;

    protected bool $requireNumbers;

    protected bool $requireSymbols;

    protected bool $disallowCommon;

    protected bool $disallowSequential;

    protected int $minScore;

    public function __construct()
    {
        $this->min = (int) config('password-strength.min_length', 8);
        $this->requireLetters = (bool) config('password-strength.require_letters', true);
        $this->requireMixedCase = (bool) config('password-strength.require_mixed_case', true);
        $this->requireNumbers = (bool) config('password-strength.require_numbers', true);
        $this->requireSymbols = (bool) config('password-strength.require_symbols', true);
        $this->disallowCommon = (bool) config('password-strength.disallow_common', true);
        $this->disallowSequential = (bool) config('password-strength.disallow_sequential', true);
        $this->minScore = (int) config('password-strength.min_score', 3);
    }

    public function min(int $length): self
    {
        $this->min = $length;

        return $this;
    }

    public function requireLetters(bool $require = true): self
    {
        $this->requireLetters = $require;

        return $this;
    }

    public function requireMixedCase(bool $require = true): self
    {
        $this->requireMixedCase = $require;

        return $this;
    }

    public function requireNumbers(bool $require = true): self
    {
        $this->requireNumbers = $require;

        return $this;
    }

    public function requireSymbols(bool $require = true): self
    {
        $this->requireSymbols = $require;

        return $this;
    }

    public function minScore(int $score): self
    {
        $this->minScore = $score;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $password = (string) $value;

        // 1. Length Check
        if (mb_strlen($password) < $this->min) {
            $fail(__('password-strength::messages.min_length', ['min' => $this->min]));

            return;
        }

        // 2. Letters Check
        if ($this->requireLetters && ! preg_match('/[a-zA-Z]/', $password)) {
            $fail(__('password-strength::messages.letters'));

            return;
        }

        // 3. Mixed Case Check
        if ($this->requireMixedCase && (! preg_match('/[a-z]/', $password) || ! preg_match('/[A-Z]/', $password))) {
            $fail(__('password-strength::messages.mixed_case'));

            return;
        }

        // 4. Numbers Check
        if ($this->requireNumbers && ! preg_match('/[0-9]/', $password)) {
            $fail(__('password-strength::messages.numbers'));

            return;
        }

        // 5. Symbols Check
        if ($this->requireSymbols && ! preg_match('/[^a-zA-Z0-9]/', $password)) {
            $fail(__('password-strength::messages.symbols'));

            return;
        }

        // 6. Overall Evaluation & Score Check
        $evaluator = new PasswordStrengthEvaluator;
        $result = $evaluator->evaluate($password, $this->minScore);

        if ($this->disallowCommon && ! $result->checks['not_common']) {
            $fail(__('password-strength::messages.common'));

            return;
        }

        if ($this->disallowSequential && ! $result->checks['not_sequential']) {
            $fail(__('password-strength::messages.sequential'));

            return;
        }

        if ($result->score < $this->minScore) {
            $levels = config('password-strength.levels', [
                2 => 'Medium',
                3 => 'Strong',
                4 => 'Very Strong',
            ]);
            $levelName = $levels[$this->minScore] ?? 'Strong';
            $fail(__('password-strength::messages.score', ['level' => $levelName]));
        }
    }
}
