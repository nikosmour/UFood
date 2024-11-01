<?php

namespace App\Http\Requests;

use App\Enum\MealPlanPeriodEnum;
use App\Models\Academic;
use App\Rules\AtLeastOneNoZero;
use App\Rules\IsUserActive;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class TransactionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string,array>
     */
    public function rules(): array
    {
        $rules = [];
        $rules['receiver_id'] = ["required", "integer",
            new IsUserActive(), "exists:coupon_owners,academic_id"];
        $periods = MealPlanPeriodEnum::names();
        foreach ($periods as $period) {
            $rules[$period] = ['required',
                'integer', 'min:0'
            ];
        }
        // Ensure that at least one of the period values is non-zero
        $rules["meals"] = new AtLeastOneNoZero(...$periods);
        $user = auth()->user();
        if ($user instanceof Academic) {
            $couponOwner = $user->couponOwner;
            $rules['receiver_id'][] = Rule::prohibitedIf($couponOwner->academic_id == $this->input('receiver_id'));
            foreach ($periods as $period)
                $rules[$period][] = 'max:' . $couponOwner[$period];
        }
        return $rules;
    }


}
