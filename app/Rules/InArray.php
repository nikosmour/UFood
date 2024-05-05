<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Closure;

class InArray implements ValidationRule
{
    protected $allowedValues;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $allowedValues)
    {
        $this->allowedValues = $allowedValues;
    }

    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!in_array($value, $this->allowedValues))
            $fail('The :attribute must be one of ' . implode(', ', $this->allowedValues));
    }
}
