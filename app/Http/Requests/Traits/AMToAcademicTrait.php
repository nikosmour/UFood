<?php

namespace App\Http\Requests\Traits;


use App\Models\Academic;

trait AMToAcademicTrait
{
    public function prepareForValidation()
    {
        $type = 'receiver_id';
        if ($this->academic_id)
            $type = 'academic_id';
        $academic_id = intval($this[$type]);
        if ($academic_id < 10 ** 11) {
            $academic_id = Academic::where('A_M', $academic_id)->value('academic_id');
            $this->merge([
                $type => $academic_id
            ]);
        }
    }
}