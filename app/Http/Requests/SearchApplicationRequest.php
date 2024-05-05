<?php

namespace App\Http\Requests;

use App\Enum\CardStatusEnum;
use App\Enum\MealPlanPeriodEnum;
use App\Models\CardApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class SearchApplicationRequest extends FormRequest
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
        $array = ['id' => ['integer', "exists:card_application,id"],
            'academic_id' => ['integer', "exists:academics,academic_id"],
            'a_m' => ['integer', "exists:academics,a_m"],
            'email' => ['email', "exists:academics,email"],
            'status' => [new Enum(CardStatusEnum::class)]
        ];
        return $array;
    }
}
