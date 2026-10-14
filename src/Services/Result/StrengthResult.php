<?php

declare(strict_types=1);

namespace Euginepj\PasswordStrength\Services\Result;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class StrengthResult implements Arrayable, Jsonable
{
    public function __construct(
        public readonly int $score,
        public readonly int $percentage,
        public readonly string $level,
        public readonly bool $passed,
        public readonly array $checks = [],
        public readonly array $feedback = []
    ) {}

    public function isWeak(): bool
    {
        return $this->score <= 1;
    }

    public function isMedium(): bool
    {
        return $this->score === 2;
    }

    public function isStrong(): bool
    {
        return $this->score >= 3;
    }

    public function isVeryStrong(): bool
    {
        return $this->score === 4;
    }

    public function toArray(): array
    {
        return [
            'score' => $this->score,
            'percentage' => $this->percentage,
            'level' => $this->level,
            'passed' => $this->passed,
            'checks' => $this->checks,
            'feedback' => $this->feedback,
        ];
    }

    public function toJson($options = 0): string
    {
        return (string) json_encode($this->toArray(), $options);
    }
}
