<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeWebUrl implements ValidationRule
{
    public static function allows(string $value): bool
    {
        if (preg_match('/[\x00-\x20\x7f]/', $value) || str_contains($value, chr(92))) {
            return false;
        }
        $parts = parse_url($value);

        return filter_var($value, FILTER_VALIDATE_URL) !== false && is_array($parts)
            && in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)
            && ! empty($parts['host']) && ! isset($parts['user']) && ! isset($parts['pass']);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! static::allows($value)) {
            $fail('Use a complete http:// or https:// URL without credentials, whitespace or backslashes.');
        }
    }
}
