<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotCommonPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        $password = $this->normalize($value);

        foreach (config('security.password.common_phrases', []) as $phrase) {
            if ($password === $this->normalize((string) $phrase)) {
                $fail('To hasło jest zbyt popularne. Wybierz dłuższą, unikalną frazę.');

                return;
            }
        }
    }

    private function normalize(string $value): string
    {
        return preg_replace('/[\s_-]+/u', '', mb_strtolower($value, 'UTF-8')) ?? '';
    }
}
