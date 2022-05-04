<?php

namespace App\Http\Requests;

use App\Enum\MealPlanPeriodEnum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDailyMealPlanRequest extends FormRequest
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
        $array = [];
        foreach (MealPlanPeriodEnum::names() as $period) {
            $array[$period] = ["sometimes", "array", "min:1"];
            $array[$period . '.*'] = ["sometimes", "string", "exists:meals,description"];
        }
        return $array;
    }
}
