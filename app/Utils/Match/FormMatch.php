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
        $this->simpleMatch($percent, 'my_information', [
            "education", "work", "salary", "pets", "films_or_books", "relax", "countries_was", "countries_dream", "sleep", "clubs",
            "smoking", "alcohol", "religion", "sport", "children", "children_desire", "age"
        ]);

        # Добавляем в коллекцию результат
        $this->matchResult = $this->matchResult->put('form', $percent);
    }
}
