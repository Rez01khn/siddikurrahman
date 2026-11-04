<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeHomepageLink implements ValidationRule
{
    public static function allows(string $value): bool
    {
        return preg_match('/\A(?:#[a-zA-Z][a-zA-Z0-9_-]*|\/(?!\/)[a-zA-Z0-9\/_-]*)\z/', $value) === 1;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! static::allows($value)) {
            $fail('Use a local path such as /contact or a section anchor such as #journey. External URLs, scripts, and encoded URLs are not allowed.');
        }
    }
}
