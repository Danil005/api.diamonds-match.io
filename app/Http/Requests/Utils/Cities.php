<?php

namespace App\Http\Requests\Utils;

use Illuminate\Foundation\Http\FormRequest;

class Cities extends FormRequest
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
            'title' => 'required|string',
            'country_id' => 'integer|nullable'
        ];
    }
}
