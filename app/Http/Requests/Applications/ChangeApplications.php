<?php

namespace App\Http\Requests\Applications;

use App\Utils\Permissions;
use App\Utils\Response;
use Illuminate\Foundation\Http\FormRequest;

class ChangeApplications extends FormRequest
{
    use Response, Permissions;

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
            'status' => 'required|integer'
        ];
    }
}
