<?php

namespace App\Utils\Match;

trait ProcessCore
{
    /**
     * Проверяем, что этот пол нам подходит
     */
    public function isSex(): bool
    {
        return $this->currentMy['sex'] == $this->currentPartner['sex'];
    }

    /**
     * Выполнить все функции
     *
     * @param array $functions
     */
    public function doMatch(array $functions = [])
    {
        # Проходимся по всем функциям
        foreach ($functions as $item) {
            # Выполняем их
            $this->$item();
        }

        dd($this->matchResult);
    }

    /**
     * Выполнить простой матч
     *
     * @param float $percent
     * @param string|callable $field
     */
    public function simpleMatch(float &$percent, string|callable $field)
    {
        # Проверяем, что это строка
        if (gettype($field) == 'string') {
            # Получаем все поля для проверки внешности
            $fields = collect(array_keys(config("app.questionnaire.value.{$field}")));
        } else {
            # Получаем из функции
            dd($field);
            $fields = $field();
        }

        # Моя внешность
        $my = $this->currentMy->only($fields);

        # Внешность партнера
        $partner = $this->currentPartner->only($fields);

        # Получаем кол-во элементов, которые сошлись
        $result = $my->filter(fn($item, $key) => $item === $partner[$key])->count();

        # Вычисляем процент
        $percent = round($result * 100 / count($fields), 2);
    }
}
