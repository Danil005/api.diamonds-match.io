<?php

namespace App\Http\Requests\Questionnaire;

use App\Utils\Permissions;
use App\Utils\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;

class View extends FormRequest
{
    use Permissions, Response;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $headers = request()->headers;

        if( Cache::get('lang') == null ) {
            Cache::add('lang', 'ru');
        }

        if( $headers->has('x-lang') ) {
            Cache::set('lang', $headers->get('x-lang'));
        } else {
            Cache::set('lang', 'ru');
        }

        return $this->isManager();
    }

    /**
     * View the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|integer',
            'fields' => 'string'
        ];
    }

    protected function failedAuthorization()
    {
        $this->response()->error()->setMessage('Access is denied')->send();
    }
}
