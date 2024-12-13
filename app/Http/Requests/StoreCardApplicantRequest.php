<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCardApplicantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
//            'cellphone' => ['required', 'phone:INTERNATIONAL,GR,MOBILE'],
            'first_year' => ['required', 'integer', 'between:2000,' . now()->format('Y')],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'addresses' => ['required', 'array', 'min:1', 'max:2'],
            'addresses.*.id' => ['integer', 'exists:addresses,id'],
            'addresses.*.location' => ['required', 'string', 'max:99'],
            'addresses.*.phone' => ['required', 'phone:INTERNATIONAL,GR'],
            'addresses.*.is_permanent' => ['required', 'boolean'],

        ];
    }
}
