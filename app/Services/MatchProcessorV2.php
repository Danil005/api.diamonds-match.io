<?php

namespace App\Services;

use App\Models\Questionnaire;
use App\Models\QuestionnaireMatch;
use App\Utils\Match\AboutMeMatch;
use App\Utils\Match\AppearancesMatch;
use App\Utils\Match\FormMatch;
use App\Utils\Match\ProcessCore;
use App\Utils\Match\QualitiesMatch;
use App\Utils\Match\TestMatch;
use Illuminate\Database\Eloquent\Builder as Query;
use Illuminate\Support\Collection;

class MatchProcessorV2
{
    use ProcessCore;

    # Подключаем модуль проверки внешности
    use AppearancesMatch;

    # Подключаем модуль проверки личных качеств
    use QualitiesMatch;

    # Подключаем модуль проверки теста
    use TestMatch;

    # Подключаем модуль проверки анкеты
    use FormMatch;

    # Подключаем модуль проверки о себе
    use AboutMeMatch;

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
        })->exists();
    }

    /**
     * Обработчик
     *
     */
    private function handler()
    {
        $this->added = [];
        # Получаем все данные по партнеру
        $partner = $this->partner->get();

        # Получаем все данные по моим данным
        $me = $this->my->get();

        # Начинаем искать сходства
        foreach ($me as $keyMe => $meItem) {
            # Устанавливаем текущего
            $this->currentMy = collect($meItem);
            # Очищаем лишние поля
            $this->except($this->currentMy);

            $this->currentMyId = $this->currentMy['questionnaire_id'];

            foreach ($partner as $keyPartner => $partnerItem) {
                # Устанавливаем текущего
                $this->currentPartner = collect($partnerItem);
                # Очищаем лишние поля
                $this->except($this->currentPartner);
                # Пропускаем первый элемент, чтобы не проверяли одну и ту же анкету.
                if ($keyPartner == $keyMe) continue;

                # Проверяем, подходит ли нам данный пол или нет
                if (!$this->isSex()) continue;

                $this->currentPartnerId = $this->currentPartner['questionnaire_id'];

                $this->matchResult = collect([
                    'currentMeId' => $this->currentMyId,
                    'currentPartnerId' => $this->currentPartnerId
                ]);

                # Проверяем, что эта связка уже не была проверена
                if (!$this->validNotMatch($this->currentMyId, $this->currentPartnerId)) continue;

                # Выполнить все матчи
                $result = $this->doMatch($this->matchFunctions);

                # Добавляем в базу матча
                $id = QuestionnaireMatch::create([
                    'questionnaire_id' => $result['currentMeId'],
                    'with_questionnaire_id' => $result['currentPartnerId'],
                    'about_me' => $result['about_me'],
                    'appearance' => $result['appearances'],
                    'test' => $result['test'],
                    'information' => $result['form'],
                    'personal_qualities' => $result['qualities'],
                    'total' => $result['total']
                ]);

                $this->added[] = $id;
            }
            $this->makeTotal();
        }
    }

    private function makeTotal()
    {
        $data = [];
        foreach ($this->added as $item) {
            $data[] = QuestionnaireMatch::where('id', $item)->first();
            QuestionnaireMatch::where('id', $item)->delete();
        }

        QuestionnaireMatch::create([
            'questionnaire_id' => $data[0]['questionnaire_id'],
            'with_questionnaire_id' => $data[0]['with_questionnaire_id'],
            'about_me' => round(($data[0]['about_me'] + $data[1]['about_me']) / 2),
            'appearance' => round(($data[0]['appearance'] + $data[1]['appearance']) / 2),
            'test' => round(($data[0]['test'] + $data[1]['test']) / 2),
            'information' => round(($data[0]['information'] + $data[1]['information']) / 2),
            'personal_qualities' => round(($data[0]['personal_qualities'] + $data[1]['personal_qualities']) / 2),
            'total' => round(($data[0]['total'] + $data[1]['total']) / 2)
        ]);
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
