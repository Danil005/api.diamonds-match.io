<?php

namespace App\Services;

use App\Models\Questionnaire;
use App\Models\QuestionnaireMyAppearance;
use App\Models\QuestionnairePartnerAppearance;
use Illuminate\Support\Collection;

class MatchProcessor
{
    private Questionnaire $questionnaire;
    private Collection $collection;

    /**
     * Уже проверенные анкеты для пропуска
     * и оптимизации кода
     *
     * Как выглядит массив: [[id - проверенный, id - проверенный с чем]]
     *
     * @var array
     */
    private array $matched = [];

    private function matchPartnerAppearance(int $partnerAppearanceId, int $myAppearanceId)
    {
        # Получаем внешность партнера
        $partnerAppearance = QuestionnairePartnerAppearance::where('id', $partnerAppearanceId)->first();

        # Получаем внешность свою
        $myAppearance = QuestionnaireMyAppearance::where('id', $myAppearanceId)->first();

        if( $partnerAppearance->sex != $myAppearance->sex )
            return false;

        # Получаем параметры для сравнения
        $matchParams = array_keys(config('app.questionnaire.value.partner_appearance'));
    }

    /**
     * Получить все анкеты
     */
    private function getQuestionnaire(): void
    {
        $this->collection = $this->questionnaire->get();
    }

    private function make()
    {
        # Начинаем Match
        foreach ($this->collection as $currentKey => $currentItem) {
            # Проходимся по всем остальным анкетам
            foreach ($this->collection as $mathKey => $match) {
                # Если эта анкета совпадает с текущей, то пропускаем
                if( $currentKey == $mathKey ) continue;

                $matched = false;

                # Проверяем, что уже не проверяли пару анкет
                foreach ($this->matched as $matchedKeys) {
                    if( ($matchedKeys[0] == $mathKey && $matchedKeys[1] == $currentKey) ||
                        ($matchedKeys[0] == $currentKey && $matchedKeys[1] == $mathKey)) $matched = true;
                }

                # Если анкету уже сравнивали, то пропускаем
                if( $matched ) continue;

                # Добавляем в проверенные, чтобы не было повторных проверок
                $this->matched[] = [$currentKey, $mathKey];

                # Сравниваем внешность
                $matchAppearance = $this->matchPartnerAppearance($currentItem->partner_appearance_id, $match->my_appearance_id);

                # Если мы сразу говорим нет, то идем к следующей итерации
                if( $matchAppearance === false ) continue;
            }
        }
    }

    public function start(Questionnaire $questionnaire): void
    {
        $this->questionnaire = $questionnaire;

        $this->getQuestionnaire();
        $this->make();
    }
}
