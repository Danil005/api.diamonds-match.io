<?php

namespace App\Utils\Match;

trait TestMatch
{
    /**
     * Получить отношение внешности
     */
    private function matchTest()
    {
        # Устанавливаем нулевое значение процента
        $percent = 0;

        $fields = collect(array_keys(config("app.questionnaire.value.test")));

        # Моя внешность
        $my = $this->currentMy->only($fields);

        # Внешность партнера
        $partner = $this->currentPartner->only($fields);

        dd($my);

//        # Получаем кол-во элементов, которые сошлись
//        $result = $my->filter(fn($item, $key) => $item === $partner[$key])->count();

        # Добавляем в коллекцию результат
        $this->matchResult = $this->matchResult->put('test', $percent);
    }
}
