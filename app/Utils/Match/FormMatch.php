<?php

namespace App\Utils\Match;

trait FormMatch
{
    /**
     * Получить отношение внешности
     */
    private function matchForm()
    {
        # Устанавливаем нулевое значение процента
        $percent = 0;

        # Делаем простой матч
        $this->simpleMatch($percent, 'partner_information', [
            'smoking', 'alcohol', 'religion', 'sport'
        ]);

        # Добавляем в коллекцию результат
        $this->matchResult = $this->matchResult->put('form', $percent);
    }
}
