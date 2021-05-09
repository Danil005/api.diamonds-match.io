<?php

namespace App\Utils\Response;

use Illuminate\Http\JsonResponse;

class Response
{
    private array $answer = [
        'success' => true,
        'message' => '',
        'data' => []
    ];

    private ?bool $success = null;
    private ?string $message = null;
    private mixed $data = [];
    private ?string $field = 'data';
    private mixed $status = 200;
    private mixed $additional = [];

    /**
     * @return void
     */
    private function createRequest(): void
    {
        $input = [
            'success' => $this->success,
            'message' => $this->message,
            $this->field => $this->data
        ];
        if( !empty($this->additional) ) {
            $input['additional'] = $this->additional;
        }
        response()->json($input, $this->status)->throwResponse();
    }

    /**
     * @return $this
     */
    public function success(): Response
    {
        $this->success = true;

        return $this;
    }

    public function setStatus(mixed $status): Response
    {
        $this->status = $status;

        return $this;
    }

    public function setAdditional(mixed $additional): Response
    {
        $this->additional = $additional;

        return $this;
    }

    /**
     * @return $this
     */
    public function error(): Response
    {
        $this->success = false;

        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): Response
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setData(mixed $data, string $field = 'data'): Response
    {
        $this->data = $data;
        $this->field = $field;

        return $this;
    }

    /**
     * Создать ответ
     */
    public function send(): void
    {
        if ($this->message == null) {
            $this->message = 'Сообщение не установлено';
            $this->success = false;
            $this->data = [];
        }

        if ($this->success != false && $this->success != true) {
            $this->message = 'Тип ответа не был задан';
            $this->success = false;
            $this->data = [];
        }

        $this->createRequest();
    }
}
