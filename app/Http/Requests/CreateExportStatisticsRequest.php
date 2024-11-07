<?php

namespace App\Http\Requests;

use App\Enum\MealPlanPeriodEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CreateExportStatisticsRequest extends FormRequest
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

        $array = (!$this->input('current')) ? [
            'from_date' => ["required", "date"],
            'to_date' => ["required", "date"],
            'meal_category' => ['required', 'array', 'min:1'],
            'meal_category.*' => ['required', new Enum(MealPlanPeriodEnum::class)]
        ] : [];
        return $array;
    }
}
