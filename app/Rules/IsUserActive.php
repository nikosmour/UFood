<?php

namespace App\Rules;

use App\Models\Academic;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class IsUserActive implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $isActive = Academic::whereAcademicId($value)->value('is_active');
        if (null === $isActive)
            $fail('validation.exists')->translate();
        elseif (!$isActive)
            $fail('validation.not_active')->translate();

    }
}
