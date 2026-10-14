<?php

declare(strict_types=1);

namespace Euginepj\PasswordStrength\Services;

use Euginepj\PasswordStrength\Services\Result\StrengthResult;

class PasswordStrengthEvaluator
{
    /**
     * Top common compromised passwords list.
     *
     * @var array<string>
     */
    protected array $commonPasswords = [
        'password', '123456', '12345678', '123456789', '12345', 'qwerty',
        '111111', '1234567', 'dragon', 'welcome', 'admin', 'password123',
        'iloveyou', 'sunshine', 'princess', 'football', 'monkey', 'charlie',
        'aa123456', 'letmein', 'shadow', 'master', '666666', 'trustno1',
        'google', 'internet', 'secret', 'pass123', 'qwertyuiop', 'admin123',
    ];

    public function evaluate(?string $password, int $minScore = 3): StrengthResult
    {
        $password = (string) $password;
        $length = mb_strlen($password);

        if ($length === 0) {
            return new StrengthResult(
                score: 0,
                percentage: 0,
                level: 'very_weak',
                passed: false,
                checks: [
                    'min_length' => false,
                    'has_lowercase' => false,
                    'has_uppercase' => false,
                    'has_numbers' => false,
                    'has_symbols' => false,
                    'not_common' => true,
                    'not_sequential' => true,
                ],
                feedback: ['Password cannot be empty.']
            );
        }

        $hasLower = (bool) preg_match('/[a-z]/', $password);
        $hasUpper = (bool) preg_match('/[A-Z]/', $password);
        $hasNumbers = (bool) preg_match('/[0-9]/', $password);
        $hasSymbols = (bool) preg_match('/[^a-zA-Z0-9]/', $password);
        $isCommon = $this->isCommonPassword($password);
        $isSequential = $this->hasSequentialOrRepeatedPatterns($password);

        $checks = [
            'min_length' => $length >= config('password-strength.min_length', 8),
            'optimal_length' => $length >= config('password-strength.optimal_length', 12),
            'has_lowercase' => $hasLower,
            'has_uppercase' => $hasUpper,
            'has_numbers' => $hasNumbers,
            'has_symbols' => $hasSymbols,
            'not_common' => ! $isCommon,
            'not_sequential' => ! $isSequential,
        ];

        // Scoring algorithm (0 to 4)
        $scorePoints = 0;
        $feedback = [];

        // Length contribution
        if ($length >= 8) {
            $scorePoints += 1;
        } else {
            $feedback[] = 'Make your password at least 8 characters long.';
        }

        if ($length >= 12) {
            $scorePoints += 1;
        }

        // Diversity contribution
        $diversityCount = ($hasLower ? 1 : 0) + ($hasUpper ? 1 : 0) + ($hasNumbers ? 1 : 0) + ($hasSymbols ? 1 : 0);
        if ($diversityCount >= 3) {
            $scorePoints += 1;
        }
        if ($diversityCount === 4) {
            $scorePoints += 1;
        }

        // Suggestions for feedback
        if (! $hasUpper || ! $hasLower) {
            $feedback[] = 'Use both uppercase and lowercase letters.';
        }
        if (! $hasNumbers) {
            $feedback[] = 'Add at least one number.';
        }
        if (! $hasSymbols) {
            $feedback[] = 'Add special characters (e.g. !@#$%).';
        }

        // Penalties
        if ($isCommon) {
            $scorePoints = 0;
            $feedback[] = 'This password is too common and easily guessed.';
        } elseif ($isSequential) {
            $scorePoints = max(0, $scorePoints - 1);
            $feedback[] = 'Avoid repeated characters or obvious keyboard sequences (e.g. 1234, qwerty).';
        }

        $finalScore = min(4, max(0, $scorePoints));
        $levels = [
            0 => 'very_weak',
            1 => 'weak',
            2 => 'medium',
            3 => 'strong',
            4 => 'very_strong',
        ];

        $percentage = (int) round(($finalScore / 4) * 100);
        $level = $levels[$finalScore];
        $passed = $finalScore >= $minScore && ! $isCommon;

        return new StrengthResult(
            score: $finalScore,
            percentage: $percentage,
            level: $level,
            passed: $passed,
            checks: $checks,
            feedback: array_values(array_unique($feedback))
        );
    }

    protected function isCommonPassword(string $password): bool
    {
        return in_array(strtolower($password), $this->commonPasswords, true);
    }

    protected function hasSequentialOrRepeatedPatterns(string $password): bool
    {
        // Check 4 or more repeated identical characters: aaaa, 1111
        if (preg_match('/(.)\1{3,}/', $password)) {
            return true;
        }

        // Check obvious sequential numbers: 1234, 4321
        $lower = strtolower($password);
        $sequences = ['0123', '1234', '2345', '3456', '4567', '5678', '6789', '4321', '5432', '6543', '7654', '8765', '9876', 'abcd', 'qwerty'];
        foreach ($sequences as $seq) {
            if (str_contains($lower, $seq)) {
                return true;
            }
        }

        return false;
    }
}
