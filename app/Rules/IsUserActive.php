<?php

namespace App\Rules;

use App\Models\Academic;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IsUserActive implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $User = Academic::whereAcademicId($value)->select('is_active')->first();
        if (!$User)
            $fail('there is not that user in the system');
        elseif (!$User->is_active)
            $fail('the user is not active');

    }
}
