<?php

namespace App\Http\Requests;

use App\Enum\MealPlanPeriodEnum;
use Illuminate\Foundation\Http\FormRequest;

class StoreDailyMealPlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $array=['date'=>["required", "date", "unique:meal_plans,date"] ];
        foreach (MealPlanPeriodEnum::names() as $period){
            $array[$period]=["sometimes", "array", "min:1"];
            $array[$period.'.*']=["sometimes", "string", "exists:meals,description"];
        }
        return $array ;
    }
}
