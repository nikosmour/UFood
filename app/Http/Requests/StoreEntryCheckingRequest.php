<?php

namespace App\Http\Requests;

use App\Enum\UserAbilityEnum;
use App\Rules\AtLeastOneNoZero;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreEntryCheckingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // if the user is staff coupon
        /** @noinspection PhpUndefinedFieldInspection */
        return Auth::user()->status->hasAbility(UserAbilityEnum::ENTRY_CHECK);
    }
    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
//        $validator->after(function ($validator) {
//            if (new AtLeastOneNoZero(...config('constants.meal.plan.period'))) {
//                $validator->errors()->add('meals', 'Something is wrong with this field!');
//            }
//        });
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return ['academic_id'=>'required|integer|min:1'
            ];
    }


}
