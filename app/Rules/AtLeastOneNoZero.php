<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;

class AtLeastOneNoZero implements Rule, DataAwareRule
{
    /**
     * All of the data under validation.
     *
     * @var array
     */
    protected $data = [];

    private array $params=[];
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
    public function __construct( ...$params)
    {
        $this->params = $params;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        foreach( $this->params as $param)
            if(0<$this->data[$param])
                return true;
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'All values are 0';
    }
}
