<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class ProfanityFree implements ValidationRule
{
    /**
     * @var list<string>
     */
    private array $blockedFragments = [
        'chuj',
        'huj',
        'cipa',
        'cipka',
        'dziwka',
        'jeb',
        'kutas',
        'kurw',
        'pierd',
        'pizd',
        'pojeb',
        'skurw',
        'spierd',
        'wyjeb',
        'zajeb',
        'zjeb',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $normalized = Str::of((string) $value)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '')
            ->toString();

        foreach ($this->blockedFragments as $fragment) {
            if (str_contains($normalized, $fragment)) {
                $fail('Ta nazwa jest wulgarna. Zmień ją na mniej wulgarną.');

                return;
            }
        }
    }
}
