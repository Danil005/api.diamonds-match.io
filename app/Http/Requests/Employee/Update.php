<?php

namespace App\Http\Requests\Employee;

use App\Utils\Permissions;
use App\Utils\Response;
use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class Update extends FormRequest
{
    use Permissions, Response;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|integer',
            'name' => 'required_without_all:phone,role,email|string',
            'phone' => 'required_without_all:name,role,email|string',
            'role' => 'required_without_all:phone,name,email|integer',
            'email' => 'required_without_all:phone,role,name|string|email'
        ];
    }

    protected function failedAuthorization()
    {
        $this->response()->error()->setMessage('Access is denied')->send();
    }
}
