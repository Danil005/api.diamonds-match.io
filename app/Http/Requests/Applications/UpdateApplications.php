<?php

namespace App\Http\Requests\Applications;

use App\Utils\Permissions;
use Illuminate\Foundation\Http\FormRequest;

class UpdateApplications extends FormRequest
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
            'id' => 'required|integer',
            'client_name' => 'required_without_all:service_type,responsibility,status|string',
            'service_type' => 'required_without_all:client_name,responsibility,status|string',
            'responsibility' => 'required_without_all:service_type,client_name,status|string',
            'status' => 'required_without_all:service_type,client_name,responsibility|integer',
        ];
    }
}
