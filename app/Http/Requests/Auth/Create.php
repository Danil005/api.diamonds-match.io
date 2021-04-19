<?php

namespace App\Http\Requests\Auth;

use App\Utils\Permissions;
use App\Utils\Response;
use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class Create extends FormRequest
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
    #[ArrayShape(['name' => "string", 'role' => "string", 'email' => "string", 'phone' => "string"])] public function rules(): array
    {
        return [
            'name' => 'required|string',
            'role' => 'required|integer',
            'email' => 'required|email|unique:users',
            'phone' => 'required'
        ];
    }

    protected function failedAuthorization()
    {
        $this->response()->error()->setMessage('Access is denied')->send();
    }
}
