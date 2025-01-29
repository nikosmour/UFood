<?php

namespace App\Http\Requests;

use App\Enum\MealPlanPeriodEnum;
use App\Http\Requests\Traits\AMToAcademicTrait;
use App\Models\Academic;
use App\Rules\AtLeastOneNoZero;
use App\Rules\IsUserActive;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

abstract class TransactionRequest extends FormRequest
{
    use AMToAcademicTrait;
    /**
     * is storing the auth()->user() if it is academic for additional checks
     * @var Academic|null
     */
    protected Academic|null $user = null;
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
        if ($this->user) {
            $couponOwner = $this->user->couponOwner;
            $rules['receiver_id'][] = Rule::prohibitedIf($couponOwner->academic_id == $this->input('receiver_id'));
            foreach ($periods as $period)
                $rules[$period][] = 'max:' . $couponOwner[$period];
        }
        return $rules;
    }

    public function after(): array
    {
        //the user has store only if he is academic;
        return $this->user ? [
            function (Validator $validator) {
                if ($validator->errors()->isEmpty() && $this->hasTheSameCategory($this->input('receiver_id'))) {
                    $validator->errors()->add(
                        'receiver_id',
                        __("validation.differentCategoryCoupons")
                    );
                }
            }
        ] : [];
    }

    /**
     * Check if the user can send coupons to the receiver
     * @param $input
     * @return bool
     */
    private function hasTheSameCategory($input): bool
    {
        return !$this->user->isCouponCategory(
            Academic::whereAcademicId($input)->select('status')->first()->getCouponCategory()
        );
    }


}
