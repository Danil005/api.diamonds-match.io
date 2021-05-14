<?php

namespace App\Utils\Match;

trait AboutMeMatch
{
    /**
     * Получить отношение внешности
     */
    private function matchAboutMe()
    {
        # Устанавливаем нулевое значение процента
        $percent = 0;

        dd($this->currentPartnerId);
        $this->currentPartner = collect(
            $this->questionnaire->partner(false, true)->first()
        );

        $this->except($this->currentPartner);
        dd($this->currentPartner);

        # Делаем простой матч
        $this->simpleMatch($percent, fn() => [
            ''
        ]);

        # Добавляем в коллекцию результат
        $this->matchResult = $this->matchResult->put('test', $percent);
    }
}
