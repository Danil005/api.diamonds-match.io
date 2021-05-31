<?php

namespace App\Utils\Match;

use EZAMA\similar_text;
use Illuminate\Support\Collection;

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
     * @return Collection
     */
    public function doMatch(array $functions = []): Collection
    {
        # Проходимся по всем функциям
        foreach ($functions as $item) {
            # Выполняем их
            $this->$item();
        }

        # Удаляем временные данные
        $excepted = $this->matchResult->except(['currentMeId', 'currentPartnerId']);
        # Считаем сколько всего переменных
        $count = $excepted->count();

        # Считаем сумму
        $sum = 0;
        foreach ($excepted as $value) {
            $sum+=$value;
        }

        # Считаем общий процент
        $total = round($sum/$count, 2);

        # Вносим в результат
        $this->matchResult->put('total', $total);

        return $this->matchResult;
    }

    /**
     * Выполнить простой матч
     *
     * @param float $percent
     * @param string|callable $field
     * @param array $similarFields
     */
    public function simpleMatch(float &$percent, string|callable $field, array $similarFields = [], ?int $count = null)
    {
        # Проверяем, что это строка
        if (gettype($field) == 'string') {
            # Получаем все поля для проверки внешности
            $fields = collect(array_keys(config("app.questionnaire.value.{$field}")));
        } else {
            # Получаем из функции
            $fields = $field();
        }

        # Моя внешность
        $my = $this->currentMy->only($fields);

        # Внешность партнера
        $partner = $this->currentPartner->only($fields);

        # Получаем кол-во элементов, которые сошлись
        $result = $my->filter(fn($item, $key) => $item === $partner[$key])->count();

        # Если есть поля, которые нужно проверить для совместимости
        if (!empty($similarFields)) {
            foreach ($similarFields as $key) {
                if( !isset($my[$key]) || !isset($partner[$key]) ) continue;

                if (similar_text::similarText($my[$key], $partner[$key]) > 40)
                    $result += 1;
            }
        }

        # Удаляем разницу, если будет такова ситуация, когда результат больше кол-ва
        if ($result > count($fields)) {
            # Вычисляем разницу по модулю
            $division = abs($result - count($fields));
            $result -= $division;
        }

        # Доп секции
        if( isset($partner['age']) ) {
            $agePartner = explode(',', $partner['age']);
            if( $my['age'] >= (int)$agePartner[0] && $my['age'] <= (int)$agePartner[1]  ) {
                $result+=1;
            }
        }

        if( isset($partner['height']) ) {
            $agePartner = explode(',', $partner['height']);
            if( $my['height'] >= (float)$agePartner[0] && $my['height'] <= (float)$agePartner[1]  ) {
                $result+=1;
            }
        }

        if( isset($partner['weight']) ) {
            $agePartner = explode(',', $partner['weight']);
            if( $my['weight'] >= (int)$agePartner[0] && $my['weight'] <= (int)$agePartner[1]  ) {
                $result+=1;
            }
        }

        if( $count == null ) {
            $r = count($fields);
        } else {
            $r = $count;
        }

        # Вычисляем процент
        $percent = round($result * 100 / $r, 2);
    }

    /**
     * @param float $percent
     * @param string|callable $field
     * @param bool $similar
     */
    public function similarMatch(float &$percent, string|callable $field, bool $similar = true)
    {
        if ($similar) {
            # Проверяем, что это строка
            if (gettype($field) == 'string') {
                # Получаем все поля для проверки внешности
                $fields = collect(array_keys(config("app.questionnaire.value.{$field}")));
            } else {
                # Получаем из функции
                $fields = $field();
            }

            # Моя внешность
            $my = $this->currentMy->only($fields);

            # Внешность партнера
            $partner = $this->currentPartner->only($fields);

            # Заменяем нули
            foreach ($my as $key => $value) {
                if ($value == "0")
                    $my[$key] = "zero";

                if ($partner[$key] == "0")
                    $partner->put($key, "zero");
            }

            # Вычисляем процент схожести
            $result = $my->map(fn($item, $key) => round(similar_text::similarText($item, ($partner[$key] ?? 'undefined')) / 100, 2));

            # Получаем сумму процентов
            $sum = 0;
            foreach ($result as $value) {
                $sum += $value;
            }
            # Вычисляем процент схожести
            $sum *= 100 / count($result);

            # Выдаем процент
            $percent = round($sum, 2);
        } else {
            $this->similarMatch($percent, $field);
        }
    }
}
