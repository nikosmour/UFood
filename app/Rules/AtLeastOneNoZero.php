<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\DataAwareRule;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AtLeastOneNoZero implements ValidationRule, DataAwareRule
{
    /**
     * All of the data under validation.
     *
     * @var array
     */
    protected $data = [];

    private array $params = [];

    public function __construct(...$params)
    {
        $this->params = $params;
    }

    /**
     * Create a new rule instance.
     *
     * @param $param
     * @return AtLeastOneNoZero
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach ($this->params as $param)
            if (0 < $this->data[$param])
                return;
        $fail('All values are 0');
    }
}
