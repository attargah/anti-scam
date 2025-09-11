<?php

namespace Attargah\AntiScam\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Xss implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value !== strip_tags($value)) {
            $fail("The {$attribute} contains invalid HTML or script.");
        }
    }
}
