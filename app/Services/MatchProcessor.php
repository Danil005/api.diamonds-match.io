<?php

namespace App\Services;

use App\Models\Questionnaire;
use App\Models\QuestionnaireMyAppearance;
use App\Models\QuestionnaireMyPersonalQualities;
use App\Models\QuestionnairePartnerAppearance;
use App\Models\QuestionnairePersonalQualitiesPartner;
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

    /**
     * ID текущей анкеты
     *
     * @var int
     */
    private int $currentQuestionnaire = 0;

    /**
     * ID проверяющей анкеты
     *
     * @var int
     */
    private int $matchingQuestionnaire = 0;

    /**
     * Временный файл для матча
     *
     * @var array
     */
    private array $matchTemp = [];

    private function matchPartnerAppearance(int $partnerAppearanceId, int $myAppearanceId): bool
    {
        $this->matchTemp = [
            'current' => $this->currentQuestionnaire,
            'matching' => $this->matchingQuestionnaire
        ];

        # Получаем внешность партнера
        $partnerAppearance = collect(QuestionnairePartnerAppearance::where('id', $partnerAppearanceId)->first())->except(['id', 'created_at', 'updated_at']);

        # Получаем внешность свою
        $myAppearance = collect(QuestionnaireMyAppearance::where('id', $myAppearanceId)->first())->except(['id', 'created_at', 'updated_at']);


        if( $partnerAppearance->get('sex') != $myAppearance->get('sex') )
            return false;

        # Получаем параметры для сравнения
        $matchParams = array_keys(config('app.questionnaire.value.partner_appearance'));

        $matchVector = [];

        # Строим вектор
        foreach ($matchParams as $param) {
            if($partnerAppearance->get($param) !== null && $myAppearance->get($param) !== null) {
                if( $partnerAppearance->get($param) === $myAppearance->get($param))
                    $matchVector[] = 1;
                else
                    $matchVector[] = 0;
            } else {
                $matchVector[] = 1;
            }
        }

        # Кол-во параметров всего
        $countParams = count($matchParams);
        $matchVectorTrue = collect($matchVector)->filter(function($item) {
            return $item;
        })->count();

        $percent = $matchVectorTrue * 100 / $countParams;

        $this->matchTemp['appearance'] = $percent;

        return true;
    }

    private function matchPersonalQualitiesPartner(int $personalQualitiesPartnerId, int $myPersonalQualitiesId)
    {
        # Получаем качества партнера
        $personalQualitiesPartner = collect(QuestionnairePersonalQualitiesPartner::where('id', $personalQualitiesPartnerId)->first())->except(['id', 'created_at', 'updated_at']);

        # Получаем качества свои
        $myPersonalQualities = collect(QuestionnaireMyPersonalQualities::where('id', $myPersonalQualitiesId)->first())->except(['id', 'created_at', 'updated_at']);

        # Получаем параметры для сравнения
        $matchParams = array_keys(config('app.questionnaire.value.my_personal_qualities'));

        $matchVector = [];

        # Строим вектор
        foreach ($myPersonalQualities as $param => $value) {
            if( $value ) {
                $matchVector[] =  in_array($param, $personalQualitiesPartner->toArray());
            } else {
                $matchVector[] = 0;
            }
        }

        dd($matchVector, $matchParams);

        # Кол-во параметров всего
        $countParams = count($matchParams);
        $matchVectorTrue = collect($matchVector)->filter(function($item) {
            return $item;
        })->count();

        $percent = $matchVectorTrue * 100 / $countParams;

        $this->matchTemp['personal_qualities'] = $percent;
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
            # Текущая анкетка, которая проверяется с остальными
            $this->currentQuestionnaire = $currentItem->id;

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
                $this->matchingQuestionnaire = $match->id;

                # Добавляем в проверенные, чтобы не было повторных проверок
                $this->matched[] = [$currentKey, $mathKey];

                # Сравниваем внешность
                $matchAppearance = $this->matchPartnerAppearance($currentItem->partner_appearance_id, $match->my_appearance_id);

                # Если мы сразу говорим нет, то идем к следующей итерации
                if( $matchAppearance === false ) continue;

                # Сравниваем личные качества
                $this->matchPersonalQualitiesPartner($currentItem->personal_qualities_partner_id, $match->my_personal_qualities_id);


                dd($this->matchTemp);
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
