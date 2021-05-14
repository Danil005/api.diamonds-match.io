<?php

namespace App\Utils\Match;

trait QualitiesMatch
{
    /**
     * Получить отношение внешности
     */
    private function matchQualities()
    {
        # Устанавливаем нулевое значение процента
        $percent = 0;

        # Делаем простой матч
        $this->simpleMatch($percent, 'my_personal_qualities');

        # Добавляем в коллекцию результат
        $this->matchResult = $this->matchResult->put('qualities', $percent);
    }
}
