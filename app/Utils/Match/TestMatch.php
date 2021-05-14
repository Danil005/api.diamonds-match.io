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

        # Делаем простой матч
        $this->simpleMatch($percent, 'test');

        # Добавляем в коллекцию результат
        $this->matchResult = $this->matchResult->put('test', $percent);
    }
}
