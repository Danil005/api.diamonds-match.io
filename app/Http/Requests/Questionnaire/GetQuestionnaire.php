<?php

namespace App\Http\Requests\Questionnaire;

use App\Utils\Permissions;
use Illuminate\Foundation\Http\FormRequest;

class GetQuestionnaire extends FormRequest
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
            //
        ];
    }
}
