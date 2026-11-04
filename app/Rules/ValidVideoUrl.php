<?php

namespace App\Rules;

use App\Support\VideoUrl;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidVideoUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! VideoUrl::embed($value)) {
            $fail('Use a public HTTPS YouTube or Vimeo video URL. Embed HTML and other hosts are not accepted.');
        }
    }
}
