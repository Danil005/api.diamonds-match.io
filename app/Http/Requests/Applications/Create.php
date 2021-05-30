<?php

namespace App\Http\Requests\Applications;

use App\Utils\Permissions;
use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
{
    use Permissions;
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
            'client_name' => 'required|string',
            'service_type' => 'required|string|in:pay,free,vip,paid',
            'responsibility' => 'string|nullable',
            'questionnaire_id' => 'integer|nullable',
            'email' => 'required|email',
            'phone' => 'required|nullable',
        ];
    }
}
