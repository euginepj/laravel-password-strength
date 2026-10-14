# Laravel Password Strength

[![Latest Version on Packagist](https://img.shields.io/packagist/v/euginepj/laravel-password-strength.svg?style=flat-square)](https://packagist.org/packages/euginepj/laravel-password-strength)
[![Run Tests](https://github.com/euginepj/laravel-password-strength/actions/workflows/run-tests.yml/badge.svg)](https://github.com/euginepj/laravel-password-strength/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/euginepj/laravel-password-strength.svg?style=flat-square)](https://packagist.org/packages/euginepj/laravel-password-strength)
[![License](https://img.shields.io/packagist/l/euginepj/laravel-password-strength.svg?style=flat-square)](https://github.com/euginepj/laravel-password-strength/blob/main/LICENSE)

A comprehensive password strength analyzer, scoring engine, and validation rule package for Laravel applications.

---

## ✨ Features

- **Entropy & Complexity Evaluator**: Analyzes character sets, length, repeated characters, and sequential keyboard patterns.
- **Common Password Detection**: Rejects commonly breached and guessed passwords (`123456`, `password`, `qwerty`, etc.).
- **Customizable Validation Rule**: Fluent and configurable validation rule with custom thresholds.
- **Score & Feedback Output**: Returns percentage (0–100%), score (0–4), security level (`weak`, `medium`, `strong`, `very_strong`), and user-facing suggestions.

---

## 📦 Installation

Install via Composer:

```bash
composer require euginepj/laravel-password-strength:^1.0
```

Publish configuration (optional):

```bash
php artisan vendor:publish --tag=password-strength-config
```

Publish translation messages (optional):

```bash
php artisan vendor:publish --tag=password-strength-lang
```

---

## 🚀 Usage

### 1. In Form Requests / Controllers

```php
use Euginepj\PasswordStrength\Rules\PasswordStrengthRule;

$request->validate([
    'password' => ['required', new PasswordStrengthRule()],
]);

// Or with fluent configuration:
$request->validate([
    'password' => [
        'required',
        (new PasswordStrengthRule())
            ->min(10)
            ->requireSymbols()
            ->minScore(3),
    ],
]);
```

### 2. Evaluator via Facade

```php
use Euginepj\PasswordStrength\Facades\PasswordStrength;

$result = PasswordStrength::evaluate('MySecret#2026');

$result->score;      // 4 (0 to 4)
$result->percentage; // 100 (%)
$result->level;      // "very_strong"
$result->passed;     // true
$result->feedback;   // []
```

### 3. Facade Rule Shortcut

```php
use Euginepj\PasswordStrength\Facades\PasswordStrength;

$request->validate([
    'password' => [
        'required',
        PasswordStrength::rule()->min(10)->minScore(3),
    ],
]);
```

---

## 🧪 Testing

```bash
composer test
```

---

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
