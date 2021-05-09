<?php

namespace App\Http\Requests\Questionnaire;

use App\Utils\Permissions;
use Illuminate\Foundation\Http\FormRequest;

class MakeDateQuestionnaire extends FormRequest
{
    use Permissions;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->isManager();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'questionnaire_id' => 'required|integer',
            'with_questionnaire_id' => 'required|integer',
            'date' => 'required|string',
            'time' => 'required|string'
        ];
    }
}
