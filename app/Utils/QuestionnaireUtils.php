<?php

namespace App\Utils;

use App\Http\Controllers\Controller;
use mysql_xdevapi\Exception;

class QuestionnaireUtils extends Controller
{
    use Response;

    private bool $typeVerified = false;


    private function validTypeSimple(array $fieldsRequired, mixed $data, string $fieldGlobal, string $field, mixed &$typeNeed, mixed $key, bool $isArray = true)
    {
        $typeNeed = getType($typeNeed) === 'array' ? implode('|', $typeNeed) : $typeNeed;
        if (str_contains($typeNeed, '|')) {

            $success = false;
            $typeNeed = explode('|', $typeNeed);
            foreach ($typeNeed as $valid) {
                if ($valid != getType($isArray ? $data[$key] : $data)) {
                    $success = false;
                } else {
                    $success = true;
                    break;
                }
            }

            if (!$success) {
                $this->response()->error()->setMessage("Неверный тип данных для '{$field}' в '{$fieldGlobal}'")->setData(
                    $isArray ? $fieldsRequired : $fieldsRequired[0], 'need_type'
                )->send();
            }
        } else {
            if ($typeNeed != getType($isArray ? $data[$key] : $data)) {
                $this->response()->error()->setMessage("Неверный тип данных для '{$field}' в '{$fieldGlobal}'")->setData(
                    $fieldsRequired, 'need_type'
                )->send();
            }
        }
    }


    private function parseAndValidType(array $fieldsRequired, string|array $data, string $fieldGlobal, string $field, bool $isArray = true)
    {

        foreach ($fieldsRequired as $key => $item) {
            if (str_contains($item, 'type:')) {
                $typeNeed = explode(':', $item)[1];


                if (str_contains($typeNeed, '(')) {
                    $typeNeed = explode('(', $typeNeed);

                    if ($typeNeed[0] != getType($data[$key])) {
                        $this->response()->error()->setMessage("Неверный тип данных для '{$field}' в '{$fieldGlobal}'")->setData(
                            $fieldsRequired, 'need_type'
                        )->send();
                    }

                    $typeNeed = explode(')', $typeNeed[1]);

                    foreach ($typeNeed as $key1 => $type) {
                        if (empty($type)) unset($typeNeed[$key1]);
                    }

                    $typeNeed = implode('|', array_values($typeNeed));

                    foreach ($data[0] as $datum) {
                        $this->validTypeSimple($fieldsRequired, $datum, $fieldGlobal, $field, $typeNeed, $key, $isArray);
                    }
                }
                if ($isArray) {
                    $this->validTypeSimple($fieldsRequired, $data, $fieldGlobal, $field, $typeNeed, $key, $isArray);
                }
            }
            $this->typeVerified = true;
        }

    }

    /**
     * @param array $request
     * @param string $field
     * @return bool
     */
    private function valid(array $request, string $field): bool
    {
        $field = config("app.questionnaire.fields.{$field}");
        $fieldsRequired = config("app.questionnaire.value.{$field}");
        $fieldsNeed = array_keys($fieldsRequired);

        if (!isset($request[$field]))
            $this->response()->error()->setMessage("Поле `{$field}` должно быть обязательно указано");

        $searchAt = array_keys($request[$field]);


        foreach ($fieldsNeed as $fieldValue => $item) {
            if (!in_array($item, $searchAt)
                && !in_array(
                    '!required',
                    !is_string($fieldsRequired[$item]) ? $fieldsRequired[$item] : ['type:' . $fieldsRequired[$item]]
                )) {

                if (getType($item) != 'integer') {
                    $notGiven = [];
                    foreach ($fieldsNeed as $need) {
                        if (!in_array($need, $searchAt) && !in_array(
                                '!required',
                                !is_string($fieldsRequired[$need]) ? $fieldsRequired[$need] : ['type:' . $fieldsRequired[$need]]
                            ))
                            $notGiven[] = $need;
                    }

                    $this->response()->error()->setMessage('Неверные данные для поля `' . $field . '`')
                        ->setData($notGiven, 'field_need')
                        ->send();
                } else {
                    $this->response()->error()->setMessage('Неверные данные для поля `' . $field . '`')
                        ->setData(implode(' | ', $fieldsRequired[$item]), 'value_need')
                        ->send();
                }
            }

            if (isset($request[$field][$item])) {
                if (getType($fieldsRequired[$item]) == 'array') {

                    if (getType($item) == 'integer') {
                        $this->typeVerified = true;
                        $fieldWrong = '\'' . $field . '\'. Index: ' . $fieldValue . '. Вы передали: ' . $request[$field][$item];
                    } else {
                        $this->parseAndValidType($fieldsRequired[$item], $request[$field][$item], $field, $item);

                        $fieldWrong = mb_strtoupper($item) . ' в \'' . $field . '\'';
                    }
                    if (!in_array($request[$field][$item], $fieldsRequired[$item])) {
                        foreach ($fieldsRequired[$item] as $r) {
                            if (!str_contains($r, 'type:')) {
                                $this->response()
                                    ->error()->setMessage("Вы должны указать верные данные для поля {$fieldWrong}")
                                    ->setData($fieldsRequired[$item], 'data_need')->send();
                            }
                        }
                    }
                } else {
                    $this->parseAndValidType(['type:' . $fieldsRequired[$item]], [$request[$field][$item]], $field, $item, false);
                }
            }
        }

        return true;
    }

    /**
     * Внешний вид партнера
     */
    protected function partnerAppearance(): bool
    {
        return $this->valid(request()->all(), 'partner_appearance');
    }

    /**
     * Личные качества партнера
     */
    protected function personalQualitiesPartner(): bool
    {
        return $this->valid(request()->all(), 'personal_qualities_partner');
    }

    /**
     * Информация о партнере
     *
     * @return bool
     */
    protected function partnerInformation(): bool
    {
        return $this->valid(request()->all(), 'partner_information');
    }

    /**
     * Психологический тест
     *
     * @return bool
     */
    protected function test(): bool
    {
        return $this->valid(request()->all(), 'test');
    }

    /**
     * Моя внешность
     *
     * @return bool
     */
    protected function myAppearance(): bool
    {
        return $this->valid(request()->all(), 'my_appearance');
    }

    /**
     * Мои личные качества
     *
     * @return bool
     */
    protected function myPersonalQualities(): bool
    {
        return $this->valid(request()->all(), 'my_personal_qualities');
    }

    /**
     * Моя информация
     *
     * @return bool
     */
    protected function myInformation(): bool
    {
        return $this->valid(request()->all(), 'my_information');
    }


}
