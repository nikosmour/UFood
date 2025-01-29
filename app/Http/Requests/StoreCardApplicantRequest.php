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


    public function prepareForValidation()
    {
        /** @var Academic $user */
        $user = auth()->user();
        $this->isCreate = $user->cardApplicant === null;

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
//            'cellphone' => ['phone:INTERNATIONAL,GR,MOBILE'],
'academic_id' => [
    'integer',
    'min:' . 10 ** 11
],
            'first_year' => ['integer', 'between:2000,' . now()->format('Y')],
            'department' => ['string', 'exists:departments,name'],
            'addresses' => ['array:permanent,temporary', 'min:1', 'max:2'],
            'addresses.*.location' => ['string', 'max:99'],
            'addresses.*.phone' => ['phone:INTERNATIONAL,GR'],
            'addresses.*.is_permanent' => ['boolean'],

        ];
        if ($this->isCreate)
            foreach ($rules as $key => $value) {
                $rules[$key][] = 'required';
            }
        else
            $rules["delPermanent"] = ['boolean'];
        return $rules;
    }
}
