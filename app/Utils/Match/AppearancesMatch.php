<?php

namespace App\Utils\Match;

trait AppearancesMatch
{

    /**
     * Поля которые относятся к внешности
     *
     * @var array|string[]
     */
    private array $appearancesFields = [
        'ethnicity',
        'body_type',
        'chest',
        'booty',
        'hair_color',
        'hair_length',
        'eye_color'
    ];

    /**
     *
     */
    private function matchAppearances()
    {
        # Получаем все поля для проверки внешности
        $fields = collect(array_keys(config('app.questionnaire.value.partner_appearance')));

        # Моя внешность
        $myAppearances = $this->currentMy->only($this->appearancesFields);

        # Внешность партнера
        $partnerAppearances = $this->currentPartner->only($this->appearancesFields);

//        $myAppearances->filter(fn($item) => );
    }
}
