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

        $this->currentPartner = collect(
            $this->questionnaire->partner(false, true)->where('questionnaires.id', $this->currentPartnerId)->first()
        );

        $this->except($this->currentPartner);

        # Делаем простой матч
        $this->similarMatch($percent, fn() => [
            'education', 'work', 'salary', 'health_problems',
            'allergies', 'pets', 'have_pets', 'films_or_books', 'relax',
            'countries_was', 'countries_dream', 'best_gift', 'hobbies',
            'kredo', 'features_repel', 'age_difference', 'films',
            'songs', 'ideal_weekend', 'sleep', 'doing_10', 'signature_dish',
            'clubs', 'best_gift_received', 'talents'
        ]);

        # Добавляем в коллекцию результат
        $this->matchResult = $this->matchResult->put('about_me', $percent);
    }
}
