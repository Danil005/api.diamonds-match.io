<?php

namespace App\Services;

use App\Models\Questionnaire;
use App\Models\QuestionnaireMatch;
use App\Utils\Match\AppearancesMatch;
use App\Utils\Match\ProcessCore;
use Illuminate\Database\Eloquent\Builder as Query;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\ArrayShape;

class MatchProcessorV2
{
    use ProcessCore;

    # Подключаем модуль проверки внешности
    use AppearancesMatch;

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
     * Удалить стандартные поля
     *
     * @var array|string[]
     */
    private array $defaultExcept = [
        'sign', 'partner_appearance_id', 'personal_qualities_partner_id', 'partner_information_id',
        'test_id', 'my_appearance_id', 'my_personal_qualities_id', 'my_information_id',
        'manager_id', 'status_pay', 'deleted_at', 'created_at', 'updated_at'
    ];

    /**
     * Результат матча
     *
     * @var Collection
     */
    private Collection $matchResult;


    /**
     * MatchProcessorV2 constructor.
     * @param Questionnaire|null $questionnaire
     */
    public function __construct(
        private ?Questionnaire $questionnaire = null
    ) {}


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
        return !QuestionnaireMatch::where(function(Query $query) use($meId, $partnerId) {
            $query->where('questionnaire_id', $meId)->where('with_questionnaire_id', $partnerId);
        })->orWhere(function(Query $query) use ($meId, $partnerId) {
            $query->where('questionnaire_id', $partnerId)->where('with_questionnaire_id', $meId);
        })->exists();
    }

    /**
     * Обработчик
     *
     */
    private function handler()
    {
        # Получаем все данные по партнеру
        $partner = $this->partner->get();

        # Получаем все данные по моим данным
        $me = $this->my->get();

        # Начинаем искать сходства
        foreach ($me as $keyMe=>$meItem) {
            # Устанавливаем текущего
            $this->currentMy = collect($meItem);
            # Очищаем лишние поля
            $this->except($this->currentMy);

            foreach ($partner as $keyPartner=>$partnerItem) {
                # Устанавливаем текущего
                $this->currentPartner = collect($partnerItem);
                $this->except($this->currentPartner);
                # Пропускаем первый элемент, чтобы не проверяли одну и ту же анкету.
                if( $keyPartner == $keyMe ) continue;

                # Проверяем, подходит ли нам данный пол или нет
                if( !$this->isSex() ) continue;

                # Очищаем лишние поля

                # Проверяем, что эта связка уже не была проверена
                if( !$this->validNotMatch($meItem->id, $partnerItem->id) ) continue;

                $this->matchAppearances();
            }
        }
    }

    /**
     * Запустить
     *
     * @param Questionnaire $questionnaire
     */
    public function start(Questionnaire $questionnaire): void
    {
        $this->questionnaire = $questionnaire;

        # Получаем модель запроса для моих данных
        $this->my = $this->questionnaire->my(true);

        # Получаем модель запроса для партнеров
        $this->partner = $this->questionnaire->partner(true);

        # Выполнить обработчик
        $this->handler();
    }
}
