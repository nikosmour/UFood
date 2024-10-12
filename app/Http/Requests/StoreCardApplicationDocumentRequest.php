<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCardApplicationDocumentRequest extends FormRequest
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
            'file' => [
                'required',
                'file',
                'mimes:pdf',
                'mimetypes:application/pdf',
                'extension:pdf',
                'max:2048', // Maximum file size in kilobytes (2MB)
            ],
            'description' => [
                'required',
                'string',
                'max:27',
            ],
//            'id' => '' //if i want to also give the option to update
        ];
    }
}
