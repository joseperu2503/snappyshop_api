<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ArrayOfHexColorsRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail('Colors must be an array.');
        }

        foreach ($value as $color) {
            if (!preg_match('/^#(?:[0-9a-fA-F]{3}){1,2}$/', $color)) {
                 // Cada elemento debe ser un valor hexadecimal válido
                $fail('Each color must be a valid hexadecimal color value.');
            }
        }
    }
}
