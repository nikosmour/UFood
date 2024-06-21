<?php

namespace App\Http\Requests;

use App\Enum\CardStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateCardApplicationRequest extends FormRequest
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
        return [
            'comment' => 'string',
            'status' => [Rule::in([CardStatusEnum::SUBMITTED, CardStatusEnum::TEMPORARY_SAVED]), new Enum(CardStatusEnum::class)]
        ];
    }
}
