<?php

namespace App\Http\Requests\Employee;

use App\Utils\Permissions;
use App\Utils\Response;
use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class NewPassword extends FormRequest
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
            'user_id' => 'required|integer'
        ];
    }

    protected function failedAuthorization()
    {
        $this->response()->error()->setMessage('Access is denied')->send();
    }
}
