<?php

namespace App\Services;

use App\Models\Questionnaire;
use App\Models\QuestionnaireMatch;
use App\Models\QuestionnaireMyAppearance;
use App\Models\QuestionnaireMyInformation;
use App\Models\QuestionnaireMyPersonalQualities;
use App\Models\QuestionnairePartnerAppearance;
use App\Models\QuestionnairePartnerInformation;
use App\Models\QuestionnairePersonalQualitiesPartner;
use App\Models\QuestionnaireTest;
use EZAMA\similar_text;
use Illuminate\Database\Eloquent\Builder;
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


        if ($partnerAppearance->get('sex') != $myAppearance->get('sex'))
            return false;

        # Получаем параметры для сравнения
        $matchParams = array_keys(config('app.questionnaire.value.partner_appearance'));

        $matchVector = [];

        # Строим вектор
        foreach ($matchParams as $param) {
            if ($partnerAppearance->get($param) !== null && $myAppearance->get($param) !== null) {
                if ($partnerAppearance->get($param) === $myAppearance->get($param))
                    $matchVector[] = 1;
                else
                    $matchVector[] = 0;
            } else {
                $matchVector[] = 1;
            }
        }

        # Кол-во параметров всего
        $countParams = count($matchParams);
        $matchVectorTrue = collect($matchVector)->filter(function ($item) {
            return $item;
        })->count();

        $percent = $matchVectorTrue * 100 / $countParams;

        $this->matchTemp['appearance'] = $percent;

        return true;
    }

    private function matchPersonalQualitiesPartner(int $personalQualitiesPartnerId, int $myPersonalQualitiesId): bool
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
            if ($value) {
                $matchVector[] = (int)in_array($param, $personalQualitiesPartner->toArray());
            } else {
                $matchVector[] = 0;
            }
        }


        # Кол-во параметров всего
        $countParams = count($matchParams);
        $matchVectorTrue = collect($matchVector)->filter(function ($item) {
            return $item;
        })->count();

        $percent = $matchVectorTrue * 100 / $countParams;

        $this->matchTemp['personal_qualities'] = round(($percent * 2 >= 100) ? $percent : $percent * 2, 2);

        return true;
    }

    private function matchInformation(int $partnerInformationId, int $myInformationId): bool
    {
        # Получаем информацию о партнере
        $partnerInformation = collect(QuestionnairePartnerInformation::where('id', $partnerInformationId)->first())->except(['id', 'created_at', 'updated_at']);

        # Получаем мою информацию
        $myInformation = collect(QuestionnaireMyInformation::where('id', $myInformationId)->first())->except(['id', 'created_at', 'updated_at']);

        $cities = 0;
        $ages = null;
        $zodiacSigns = 0;
        $height = null;
        $weight = null;
        $maritalStatus = 0;
        $languages = null;
        $movingCountry = 0;
        $movingCity = 0;
        $children = 0;
        $childrenDesire = 0;
        $smoking = 0;
        $alcohol = 0;
        $religion = 0;
        $sport = 0;

        foreach ($myInformation as $key => $information) {
            if (isset($partnerInformation[$key])) {
                $cities = similar_text::similarText($myInformation['city'], $partnerInformation['city']) / 100;
                $ages = explode(',', $partnerInformation['age']);

                if (isset($ages[0]) && isset($ages[1])) {
                    if ($myInformation['age'] >= $ages[0] && $myInformation['age'] <= $ages[1])
                        $ages = 1;
                    else
                        $ages = 0;
                }

                if ($myInformation['zodiac_signs'] == $partnerInformation['zodiac_signs']) $zodiacSigns = 1;
                if ($myInformation['marital_status'] == $partnerInformation['marital_status']) $maritalStatus = 1;
                if ($myInformation['moving_country'] == $partnerInformation['moving_country']) $movingCountry = 1;
                if ($myInformation['moving_city'] == $partnerInformation['moving_city']) $movingCity = 1;
                if ($myInformation['children'] == $partnerInformation['children']) $children = 1;
                if ($myInformation['children_desire'] == $partnerInformation['children_desire']) $childrenDesire = 1;

                $height = explode(',', $partnerInformation['height']);
                if (isset($height[0]) && isset($height[1])) {
                    if ($myInformation['height'] >= $height[0] && $myInformation['height'] <= $height[1])
                        $height = 1;
                    else
                        $height = 0;
                }
                $weight = explode(',', $partnerInformation['weight']);
                if (isset($weight[0]) && isset($weight[1])) {
                    if ($myInformation['weight'] >= $weight[0] && $myInformation['weight'] <= $weight[1])
                        $weight = 1;
                    else
                        $weight = 0;
                }

                $languagesPartner = mb_strtolower($partnerInformation['languages']);
                $languagesMy = mb_strtolower($myInformation['languages']);
                $languages = similar_text::similarText($languagesPartner, $languagesMy) / 100;
                $smoking = similar_text::similarText($myInformation['smoking'], $partnerInformation['smoking']) / 100;
                $alcohol = similar_text::similarText($myInformation['alcohol'], $partnerInformation['alcohol']) / 100;
                $religion = similar_text::similarText($myInformation['religion'], $partnerInformation['religion']) / 100;
                $sport = similar_text::similarText($myInformation['sport'], $partnerInformation['sport']) / 100;
            }
        }

        $matchVector = [
            $cities, $ages, $zodiacSigns, $height, $weight, $maritalStatus, $languages, $movingCountry, $movingCity, $children,
            $childrenDesire, $smoking, $alcohol, $religion, $sport
        ]; //15

        $sumValue = 0;

        foreach ($matchVector as $item) $sumValue += $item;
        $percent = $sumValue * 100 / 15;

        $this->matchTemp['information'] = round($percent, 2);

        return true;
    }

    private function test(int $partnerTest, int $myTest): bool
    {
        # Получаем тест партнера
        $partnerTest = collect(QuestionnaireTest::where('id', $partnerTest)->first())->except(['id', 'created_at', 'updated_at']);

        # Получаем мой тест
        $myTest = collect(QuestionnaireTest::where('id', $myTest)->first())->except(['id', 'created_at', 'updated_at']);

        # Получаем параметры для сравнения
        $matchParams = array_keys(config('app.questionnaire.value.test'));

        $matchVector = [];

        foreach ($matchParams as $param) {
            if ($partnerTest[$param] === $myTest[$param]) {
                $matchVector[] = 1;
            } else
                $matchVector[] = 0;
        }

        # Кол-во параметров всего
        $countParams = count($matchParams);
        $matchVectorTrue = collect($matchVector)->filter(function ($item) {
            return $item;
        })->count();

        $percent = $matchVectorTrue * 100 / $countParams;

        $this->matchTemp['test'] = round($percent, 2);

        return true;
    }

    /**
     * Получить все анкеты
     */
    private function getQuestionnaire(): void
    {
        $this->collection = $this->questionnaire->whereNotNull('partner_appearance_id')->get();
    }

    private function make()
    {
        # Начинаем Match
        foreach ($this->collection as $currentKey => $currentItem) {
            # Текущая анкетка, которая проверяется с остальными
            $this->currentQuestionnaire = $currentItem->id;

            # Проходимся по всем остальным анкетам
            foreach ($this->collection as $mathKey => $match) {
                $currentId = $currentItem->id;
                $matchId = $match->id;
                if( QuestionnaireMatch::where(function(Builder $query) use ($currentId, $matchId){
                    $query->where('questionnaire_id', $currentId)->where('with_questionnaire_id', $matchId);
                })->orWhere(function(Builder $query) use($currentId, $matchId) {
                    $query->where('questionnaire_id', $matchId)->where('with_questionnaire_id', $currentId);
                })->exists() ) continue;

                # Если эта анкета совпадает с текущей, то пропускаем
                if ($currentKey == $mathKey) continue;

                $matched = false;

                # Проверяем, что уже не проверяли пару анкет
                foreach ($this->matched as $matchedKeys) {
                    if (($matchedKeys[0] == $mathKey && $matchedKeys[1] == $currentKey) ||
                        ($matchedKeys[0] == $currentKey && $matchedKeys[1] == $mathKey)) $matched = true;
                }

                # Если анкету уже сравнивали, то пропускаем
                if ($matched) continue;
                $this->matchingQuestionnaire = $match->id;

                # Добавляем в проверенные, чтобы не было повторных проверок
                $this->matched[] = [$currentKey, $mathKey];

                # Сравниваем внешность
                $matchAppearance = $this->matchPartnerAppearance($currentItem->partner_appearance_id, $match->my_appearance_id);

                # Если мы сразу говорим нет, то идем к следующей итерации
                if ($matchAppearance === false) continue;

                # Сравниваем личные качества
                $this->matchPersonalQualitiesPartner($currentItem->personal_qualities_partner_id, $match->my_personal_qualities_id);
                $this->matchInformation($currentItem->partner_information_id, $match->my_information_id);
                $this->test($currentItem->test_id, $match->test_id);

                $total = round(($this->matchTemp['appearance'] + $this->matchTemp['personal_qualities'] + $this->matchTemp['information']
                        + $this->matchTemp['test']) / 4, 2);

                QuestionnaireMatch::create([
                    'questionnaire_id' => $this->matchTemp['current'],
                    'with_questionnaire_id' => $this->matchTemp['matching'],
                    'appearance' => $this->matchTemp['appearance'],
                    'personal_qualities' => $this->matchTemp['personal_qualities'],
                    'information' => $this->matchTemp['information'],
                    'test' => $this->matchTemp['test'],
                    'total' => $total
                ]);

                $this->matchTemp = [];
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
