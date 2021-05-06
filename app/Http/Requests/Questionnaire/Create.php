<?php

namespace App\Http\Requests\Questionnaire;

use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
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
            config('app.questionnaire.fields.partner_appearance') => 'required|array',
            config('app.questionnaire.fields.personal_qualities_partner') => 'required|array',
            config('app.questionnaire.fields.partner_information') => 'required|array'
        ];
    }
}
