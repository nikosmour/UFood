<?php

namespace App\Http\Requests;

use App\Enum\CardStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreCardApplicationCheckingRequest extends FormRequest
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
            'status' => ['required', new Enum(CardStatusEnum::class)],
            'card_application_id' => ['required', "exists:card_applications,id"],
            'expiration_date' => ['date',
                Rule::requiredIf($this->input('status') == CardStatusEnum::ACCEPTED->value),
                'after_or_equal:' . now()->format('Y-m-d'),

            ],
            'card_application_staff_comment' => ['string', Rule::requiredIf(!in_array($this->input('status'), [
                CardStatusEnum::ACCEPTED->value, CardStatusEnum::CHECKING->value, CardStatusEnum::TEMPORARY_CHECKED->value]))]


        ];
    }
}
