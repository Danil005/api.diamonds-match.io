<?php

namespace App\Utils;

use App\Models\Notification;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use JetBrains\PhpStorm\Pure;

trait Response
{
    /**
     * @return Response\Response
     */
    #[Pure] public function response(): Response\Response
    {
        return new \App\Utils\Response\Response();
    }

    protected function failedValidation(Validator|\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = [
            "success" => false,
            "message" => 'Переданные данные не верны',
            "errors" => $validator->errors(),
        ];

        throw new HttpResponseException(response()->json($response, 422));
    }

    /**
     * Создать уведомление
     *
     * @param string $type
     * @param string $message
     * @param array $payload
     */
    public function createNotify(string $type, string $message, array $payload)
    {
        Notification::create([
            'type' => $type,
            'message' => $message,
            'payload' => json_encode($payload)
        ]);
    }
}
