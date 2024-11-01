<?php

namespace App\Http\Requests;

use App\Enum\MealPlanPeriodEnum;
use App\Rules\AtLeastOneNoZero;
use App\Rules\IsUserActive;
use Illuminate\Foundation\Http\FormRequest;

class TransactionCouponConformationDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * If the user has the ability to sell coupons.
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array<string,array>
     */
    public function rules(): array
    {
        $rules = [];
        $rules['receiver_id'] = ["required", "integer", new IsUserActive];
        $periods = MealPlanPeriodEnum::names();
        foreach ($periods as $period) {
            $rules[$period] = ['required',
                'integer', 'min:0'
            ];
        }
        $rules[$periods[0]][] = new AtLeastOneNoZero(...$periods);
        return $rules;
    }


}
