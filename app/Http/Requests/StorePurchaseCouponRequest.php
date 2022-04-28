<?php

namespace App\Http\Requests;

use App\Enum\UserAbilityEnum;
use App\Rules\AtLeastOneNoZero;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePurchaseCouponRequest extends FormRequest
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
        return Auth::user()->status->hasAbility(UserAbilityEnum::COUPON_SELL);
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
        $rules=[];
        $rules['academic_id']='required|integer|exists:coupon_owners,academic_id';
        $periods=config('constants.meal.plan.period');
        foreach($periods as $period)
        {
            $rules[$period]= ['required',
                'integer','min:0'
                ];
        }
        $rules[$periods[0]][]=new AtLeastOneNoZero(...$periods);
        return $rules;
    }


}
