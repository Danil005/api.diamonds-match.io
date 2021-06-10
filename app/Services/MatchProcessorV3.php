<?php

namespace App\Services;

use App\Models\Countries;
use App\Models\Questionnaire;
use App\Models\QuestionnaireMatch;
use App\Utils\Match\AboutMeMatch;
use App\Utils\Match\AppearancesMatch;
use App\Utils\Match\FormMatch;
use App\Utils\Match\ProcessCore;
use App\Utils\Match\QualitiesMatch;
use App\Utils\Match\TestMatch;
use EZAMA\similar_text;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as Query;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use function PHPUnit\Framework\callback;

class MatchProcessorV3
{

    /**
     * Получить Query для меня
     *
     * @var Query|null
     */
    private ?Query $my = null;

    /**
     * Получить Query для партнера
     *
     * @var Query|null
     */
    private ?Query $partner = null;

    /**
     * Получить текущего пользователя для меня
     * в виде коллекции
     *
     * @var Collection
     */
    protected Collection $currentMy;

    /**
     * Получить текущего пользователя для партнера
     * в виде коллекции
     *
     * @var Collection
     */
    protected Collection $currentPartner;

    /**
     * ID-партнера
     *
     * @var int
     */
    protected int $currentPartnerId;

    /**
     * ID-мой
     *
     * @var int
     */
    protected int $currentMyId;

    /**
     * Добавленные анкеты
     *
     * @var array
     */
    protected array $added = [];

    /**
     * Функции для матча
     *
     * @var array
     */
    protected array $matchFunctions = [
        'matchAppearances',
        'matchQualities',
        'matchTest',
        'matchForm',
        'matchAboutMe'
    ];

    /**
     * Удалить стандартные поля
     *
     * @var array|string[]
     */
    private array $defaultExcept = [
        'sign', 'partner_appearance_id', 'personal_qualities_partner_id', 'partner_information_id',
        'test_id', 'my_appearance_id', 'my_personal_qualities_id', 'my_information_id',
        'manager_id', 'status_pay', 'deleted_at', 'created_at', 'updated_at',
        'application_id'
    ];

    private array $testMatchArray = [
        [ // 1
            "0.9" => [[1, 1], [2, 2], [3, 3], [4, 4]],
            "0.6" => [[1, 4], [2, 4]],
            "0.3" => [[2, 4], [3, 4]],
            "0.0" => [[1, 2], [1, 3]]
        ],
        [ // 2
            "0.9" => [[1, 1], [2, 2], [3, 3]],
            "0.6" => [[1, 3]],
            "0.3" => [[2, 3]],
            "0.0" => [[1, 2]]
        ],
        [ // 3
            "0.9" => [[1, 1], [2, 2], [3, 3], [4, 4], [5, 5], [6, 6], [2, 4]],
            "0.6" => [[1, 6], [2, 6], [3, 6], [2, 5], [2, 3], [3, 6]],
            "0.3" => [[5, 3], [4, 6], [1, 3], [3, 5], [1, 4]],
            "0.0" => [[1, 2], [1, 5], [4, 5], [5, 6]]
        ],
        [ // 4
            "0.9" => [[1, 1], [2, 2], [3, 3]],
            "0.6" => [[2, 3]],
            "0.3" => [[1, 3]],
            "0.0" => [[1, 2]]
        ],
        [ // 5
            "0.9" => [[1, 1], [2, 2], [3, 3]],
            "0.6" => [[1, 3]],
            "0.3" => [[1, 2]],
            "0.0" => [[2, 3]]
        ],
        [ // 6
            "0.9" => [[1, 1], [2, 2], [3, 3]],
            "0.6" => [[1, 3]],
            "0.3" => [[2, 3]],
            "0.0" => [[1, 2]]
        ],
        [ // 7
            "0.9" => [[1, 1], [2, 2], [3, 3], [4, 4]],
            "0.6" => [[2, 3], [1, 3]],
            "0.3" => [[2, 4], [1, 4]],
            "0.0" => [[1, 2], [3, 4]]
        ],
        [ // 8
            "0.9" => [[1, 1], [2, 2], [3, 3], [4, 4]],
            "0.6" => [[1, 2], [3, 4]],
            "0.3" => [[2, 3], [1, 3]],
            "0.0" => [[1, 4], [2, 4]]
        ],
        [ // 9
            "0.9" => [[1, 1], [2, 2], [3, 3]],
            "0.6" => [[1, 3]],
            "0.3" => [[2, 3]],
            "0.0" => [[1, 2]]
        ],
        [ // 10
            "0.9" => [[1, 1], [2, 2], [3, 3]],
            "0.6" => [[2, 3]],
            "0.3" => [[1, 3]],
            "0.0" => [[1, 2]]
        ],
        [ // 11
            "0.9" => [[1, 1], [2, 2], [3, 3]],
            "0.6" => [[1, 3]],
            "0.3" => [[2, 3]],
            "0.0" => [[1, 2]]
        ],
        [ // 12
            "0.9" => [[1, 1], [2, 2], [3, 3], [4, 4]],
            "0.6" => [[3, 4], [1, 2]],
            "0.3" => [[2, 3]],
            "0.0" => [[1, 3], [1, 4], [2, 4]]
        ],
        [ // 13
            "0.9" => [[1, 1], [2, 2], [3, 3], [4, 4]],
            "0.6" => [[2, 4], [2, 3]],
            "0.3" => [[1, 2]],
            "0.0" => [[1, 3], [1, 4], [3, 4]]
        ],
        [ // 14
            "0.9" => [[1, 1], [2, 2], [3, 3]],
            "0.6" => [[2, 3]],
            "0.3" => [[1, 2]],
            "0.0" => [[1, 3]]
        ],
        [ // 15
            "0.9" => [[1, 1], [2, 2], [3, 3], [4, 4]],
            "0.6" => [[1, 4], [2, 3]],
            "0.3" => [[1, 2], [2, 4]],
            "0.0" => [[1, 3], [3, 4]]
        ],
        [ // 16
            "0.9" => [[1, 1], [2, 2], [3, 3], [4, 4], [5, 5]],
            "0.6" => [[1, 3], [1, 4], [2, 5], [4, 5]],
            "0.3" => [[1, 5], [3, 4], [1, 5]],
            "0.0" => [[2, 3], [2, 4], [3, 5]]
        ],
        [ // 17
            "0.9" => [[1, 3], [2, 4], [2, 6], [3, 7], [4, 6], [5, 5], [5, 6], [6, 6]],
            "0.6" => [[1, 4], [1, 7], [2, 2], [3, 3], [4, 4], [2, 5], [4, 5], [7, 7]],
            "0.3" => [[1, 5], [1, 1], [2, 3], [4, 7], [3, 5]],
            "0.0" => [[1, 2], [1, 6], [2, 7], [3, 4], [3, 7], [5, 7], [6, 7]]
        ],
        [ // 18
            "0.9" => [[2, 2], [4, 4], [5, 5], [5, 6]],
            "0.6" => [[1, 2], [2, 4], [2, 3]],
            "0.3" => [[1, 4], [1, 5], [3, 4], [1, 3]],
            "0.0" => [[1, 1], [3, 3], [3, 5]]
        ],
        [ // 19
            "0.9" => [[1, 1], [2, 2], [3, 3], [2, 3], [4, 4], [4, 2]],
            "0.6" => [[3, 4]],
            "0.3" => [[1, 3], [1, 4]],
            "0.0" => [[1, 2]]
        ],
        [ // 20
            "0.9" => [[1, 1], [2, 2]],
            "0.6" => [[-1, -1]],
            "0.3" => [[-1, -1]],
            "0.0" => [[1, 2]]
        ],
        [ // 21
            "0.9" => [[1, 4], [2, 3]],
            "0.6" => [[3, 4], [4, 4], [2, 2]],
            "0.3" => [[2, 4], [1, 2], [3, 3]],
            "0.0" => [[1, 1], [1, 3]],
        ]
    ];

    /**
     * Результат матча
     *
     * @var Collection
     */
    private Collection $matchResult;

    /**
     * Финальный результат с инвертированием
     *
     * @var array
     */
    private array $matchResultFinal;


    /**
     * MatchProcessorV2 constructor.
     * @param Questionnaire|null $questionnaire
     */
    public function __construct(
        private ?Questionnaire $questionnaire = null
    )
    {
    }


    /**
     * Удалить исключения. По умолчанию:
     * @array defaultExcept
     *
     * @param Collection $collection
     * @param array|string[] $except
     */
    private function except(Collection &$collection, array $except = null)
    {
        $except = $except ?? $this->defaultExcept;

        $collection = $collection->except($except);
    }

    /**
     * Проверяем на существование уже
     * проверенных анкет между собой.
     *
     * Return (boolean):
     * true - не существуют
     * false - существует
     *
     * @param int $meId
     * @param int $partnerId
     * @return bool
     */
    private function validNotMatch(int $meId, int $partnerId): bool
    {
        return !QuestionnaireMatch::where(function (Query $query) use ($meId, $partnerId) {
            $query->where('questionnaire_id', $meId)->where('with_questionnaire_id', $partnerId);
        })->orWhere(function (Query $query) use ($meId, $partnerId) {
            $query->where('with_questionnaire_id', $meId)->where('questionnaire_id', $partnerId);
        })->exists();
    }


    private function simpleMatch(Collection $first, Collection $second, array $except = [], ?callable $function = null): int
    {
        return $first->except($except)->filter(function ($item, $key) use ($second, $except, $function) {
            if ($function !== null) {
                $res = $function($key, $item, $second);
                if( $res != null )
                    return $res;
            }
            if ($item === 'no_matter' || $second[$key] === 'no_matter')
                return true;

            if ($item === 'any' || $second[$key] === 'any')
                return true;

            return $item === $second[$key];
        })->count();
    }

    private function pqMatch(Collection $my, Collection $partner): int
    {
        $my = $my->filter(function ($item, $key) {
            return $item === true;
        });

        return $my->filter(function ($item, $key) use ($partner) {
            return $item === $partner[$key];
        })->count();
    }

    public function similarMatch(float &$percent, Collection $first, Collection $second, callable $callback = null, array $except = [])
    {
        $first = $first->except($except);
        $second = $second->except($except);

        foreach ($first as $key => $value) {
            if ($value == "0")
                $first[$key] = "zero";

            if (isset($second[$key]) && $second[$key] == "0")
                $second->put($key, "zero");
        }

        # Вычисляем процент схожести
        $result = $first->map(fn($item, $key) => round(similar_text::similarText($item, ($second[$key] ?? 'undefined')) / 100, 2));
        if ($callback !== null) {
            [$key, $value] = $callback();
            $result[$key] = $value;
        }

        # Получаем сумму процентов
        $sum = 0;
        foreach ($result as $value) {
            $sum += $value;
        }
        # Вычисляем процент схожести
        $sum *= 100 / count($result);

        # Выдаем процент
        $percent = round($sum, 2);
    }

    private function country(string $countryWas): array
    {
        $country_was = explode(',', $countryWas);
        $place = new Countries();
        foreach ($country_was as $item) {
            $place = $place->orWhere('title_en', 'ILIKE', $item)->orWhere('title_ru', 'ILIKE', $item);
        }
        $place = $place->get(['title_ru'])->toArray();
        $res = '';
        if ($place != null)
            foreach ($place as $item) $res .= ', ' . $item['title_ru'];
        return explode(', ', trim($res, ', '));
    }


    /**
     * Обработчик
     *
     */
    private function handler()
    {
        set_time_limit(3600);
//        $arrayMy = $this->my->get()?->toArray();
//        $arrayPartner = $this->partner->get()?->toArray();
//
//        foreach ($arrayMy as $itemMy) {
//            foreach ($arrayPartner as $itemPartner) {
//
//                    if ($itemMy['questionnaire_id'] != $itemPartner['questionnaire_id']) {
//
//                    }
//
//                    dd($itemMy['questionnaire_id'], $itemPartner['questionnaire_id']);
//
//            }
//        }

        $questionnaires = Questionnaire::whereNotNull('my_appearance_id')->get();
        $questionnaire = new Questionnaire();

        $temp_q1 = null;
        $temp_q2 = null;


        foreach ($questionnaires as $q1) {
            foreach ($questionnaires as $q2) {
                if ($q1->id == $q2->id) continue;

                if( !$this->validNotMatch($q1->id, $q2->id) )
                    continue;


                $temp_q1 = [
                    'my' => $questionnaire->my(true)->where('questionnaires.id', $q1->id)->first()?->toArray(),
                    'partner' => $questionnaire->partner(true)->where('questionnaires.id', $q1->id)->first()?->toArray(),
                ];
                $temp_q2 = [
                    'my' => $questionnaire->my(true)->where('questionnaires.id', $q2->id)->first()?->toArray(),
                    'partner' => $questionnaire->partner(true)->where('questionnaires.id', $q2->id)->first()?->toArray(),
                ];

                # Матч внешность
                $fields = array_keys(config('app.questionnaire.value.partner_appearance'));

                $appearancesWant1 = collect($temp_q1['partner'])->only($fields);
                $appearancesMy1 = collect($temp_q2['my'])->only($fields);

                if ($appearancesMy1['sex'] != $appearancesWant1['sex'])
                    continue;

                $appearancesWant2 = collect($temp_q2['partner'])->only($fields);
                $appearancesMy2 = collect($temp_q1['my'])->only($fields);

                if ($appearancesMy2['sex'] != $appearancesWant2['sex'])
                    continue;

                $r1 = $this->simpleMatch($appearancesMy1, $appearancesWant1) * 100 / count($fields);
                $r2 = $this->simpleMatch($appearancesMy2, $appearancesWant2) * 100 / count($fields);

                $appearancesResult = round(($r1 + $r2) / 2);
//
//                if( $q1->id == 69 && $q2->id == 41 ) {
//                    dd($appearancesResult, $r1, $r2, '1: ', $appearancesWant1, $appearancesMy1, '2: ', $appearancesWant2, $appearancesMy2, count($fields));
//                }


//
                # Сравнение качеств
                $fields = array_keys(config('app.questionnaire.value.my_personal_qualities'));

                $pqWant1 = collect($temp_q1['partner'])->only($fields);
                $pqMy1 = collect($temp_q2['my'])->only($fields);

                $pqWant2 = collect($temp_q2['partner'])->only($fields);
                $pqMy2 = collect($temp_q1['my'])->only($fields);

                $r1 = $this->pqMatch($pqMy1, $pqWant1) * 100 / 7;
                $r2 = $this->pqMatch($pqMy2, $pqWant2) * 100 / 7;

                $pqResult = round(($r1 + $r2) / 2);

                if( $q1->id == 25 && $q2->id == 37 ) {
                    dd($pqResult, $r1, $r2, '1: ', $pqWant1, $pqMy1, $this->pqMatch($pqMy1, $pqWant1),  '2: ', $pqWant2, $pqMy2, $this->pqMatch($pqMy2, $pqWant2));
                }

                # Сравнение тестов
                $fields = array_keys(config('app.questionnaire.value.test'));

                $testWant = collect($temp_q1['partner'])->only($fields);
                $testMy = collect($temp_q2['my'])->only($fields);

                $c = 0;
                $result = [];
                foreach ($this->testMatchArray as $key => $question) {
                    $c++;
                    $obj = [array_values($testWant->toArray())[$key] + 1, array_values($testMy->toArray())[$key] + 1];
                    foreach ($question as $p => $percent) {
                        foreach ($percent as $value) {
                            if (($value[0] === $obj[0] && $value[1] === $obj[1]) || ($value[1] === $obj[0] && $value[0] === $obj[1]))
                                $result[$key] = $p;

                        }
                    }
                }
                $sum = 0;
                foreach ($result as $item) {
                    $sum += (float)$item;
                }
                $testResult = round($sum / $c * 100, 2);

                # Сравнение моей информации по ключам
                $fields = [
                    'sport', 'children', 'children_desire', 'smoking', 'alcohol', 'religion', 'age'
                ];

                $formWant1 = collect($temp_q1['partner'])->only($fields);
                $formMy1 = collect($temp_q2['my'])->only($fields);

                $formWant2 = collect($temp_q2['partner'])->only($fields);
                $formMy2 = collect($temp_q1['my'])->only($fields);

                $except = ['age'];

                $between = explode(',', $formWant1['age']);
                $age = 0;
                if ($formMy1['age'] >= $between[0] && $formMy1['age'] <= $between[1]) {
                    $age = 1;
                }
                $r1 = ($this->simpleMatch($formWant1, $formMy1, $except) + $age) * 100 / (count($fields) - count($except) + 1);

                $between = explode(',', $formWant2['age']);
                $age = 0;
                if ($formMy2['age'] >= $between[0] && $formMy2['age'] <= $between[1]) {
                    $age = 1;
                }
                $r2 = ($this->simpleMatch($formWant2, $formMy2, $except) + $age) * 100 / (count($fields) - count($except) + 1);


                $formResult = round(($r1 + $r2) / 2);

                $fields = [
                    "education", "work", "salary", "pets", "films_or_books", "relax", "countries_was", "countries_dream", "sleep", "clubs",
                ];

                $formMy11 = collect($temp_q2['my'])->only($fields);
                $formMy21 = collect($temp_q1['my'])->only($fields);


                $p = 0;
                $p = $this->simpleMatch($formMy11, $formMy21, function: function ($key, $item, $second) {
                        $result = null;
                        if ($key == 'countries_was' || $key == 'countries_dream') {
                            $myCountries = $this->country($item);
                            $partnerCountries = $this->country($second[$key]);
                            $result = count(array_intersect_assoc($myCountries, $partnerCountries)) > 0;
                        }
                        return $result;
                    }) * 100 / count($fields);

                $formResult = ($formResult + $p) / 2;

                # About
                $fields = [
                    'education_name', 'work_name', 'health_problems',
                    'allergies', 'have_pets', 'best_gift', 'hobbies',
                    'kredo', 'features_repel', 'age_difference', 'films',
                    'songs', 'ideal_weekend', 'sleep', 'doing_10', 'signature_dish',
                    'clubs', 'best_gift_received', 'talents'
                ];

                $aboutMy1 = collect($temp_q2['my'])->only($fields);

                $aboutMy2 = collect($temp_q1['my'])->only($fields);

                $p1 = 0;
                $this->similarMatch($p1, $aboutMy1, $aboutMy2);

                $aboutResult = $p1;

                QuestionnaireMatch::create([
                    'questionnaire_id' => $q1->id,
                    'with_questionnaire_id' => $q2->id,
                    'about_me' => $aboutResult,
                    'appearance' => $appearancesResult,
                    'test' => $testResult,
                    'information' => $formResult,
                    'personal_qualities' => $pqResult,
                    'total' => round((($aboutResult+$appearancesResult+$testResult+$formResult+$pqResult)/5), 2)
                ]);

                QuestionnaireMatch::create([
                    'questionnaire_id' => $q2->id,
                    'with_questionnaire_id' => $q1->id,
                    'about_me' => $aboutResult,
                    'appearance' => $appearancesResult,
                    'test' => $testResult,
                    'information' => $formResult,
                    'personal_qualities' => $pqResult,
                    'total' => round((($aboutResult+$appearancesResult+$testResult+$formResult+$pqResult)/5), 2)
                ]);
            }
        }
    }

    /**
     *
     */
    /**
     * Запустить
     *
     * @param Questionnaire $questionnaire
     */
    public function start(Questionnaire $questionnaire): void
    {
        $this->questionnaire = $questionnaire;

        # Получаем модель запроса для моих данных
        $this->my = $this->questionnaire->my(true)->whereNotNull('questionnaires.partner_appearance_id');

        # Получаем модель запроса для партнеров
        $this->partner = $this->questionnaire->partner(true)->whereNotNull('questionnaires.partner_appearance_id');

        # Выполнить обработчик
        $this->handler();
    }
}
