<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Questionnaire extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'partner_appearance_id', 'personal_qualities_partner_id',
        'partner_information_id', 'test_id', 'my_appearance_id', 'my_personal_qualities_id',
        'my_information_id', 'sign'
    ];


    /**
     * @param bool $withTest
     * @return Builder|\Illuminate\Database\Query\Builder
     */
    public function my(bool $withTest = false): Builder|\Illuminate\Database\Query\Builder
    {
        $builder = $this
            ->join('questionnaire_my_appearances as appearances', 'appearances.id', '=', 'questionnaires.my_appearance_id')
            ->join('questionnaire_my_personal_qualities as personal_qualities', 'personal_qualities.id', '=', 'questionnaires.my_personal_qualities_id')
            ->join('questionnaire_my_information as information', 'information.id', '=', 'questionnaires.my_information_id')
            ->join('sign_questionnaires as sign', 'sign.id', '=', 'questionnaires.id');

        return !$withTest ? $builder : $builder->join('questionnaire_tests as test', 'test.id', '=', 'questionnaires.test_id');

    }

    /**
     * @param bool $withTest
     * @param bool $myInformation
     * @return Builder|\Illuminate\Database\Query\Builder
     */
    public function partner(bool $withTest = false, bool $myInformation = false): Builder|\Illuminate\Database\Query\Builder
    {
        $builder = $this->join('questionnaire_partner_appearances as appearances', 'appearances.id', '=', 'questionnaires.partner_appearance_id')
            ->join('questionnaire_personal_qualities_partners as personal_qualities', 'personal_qualities.id', '=', 'questionnaires.personal_qualities_partner_id')
            ->join('sign_questionnaires as sign', 'sign.id', '=', 'questionnaires.id');

        if (!$myInformation) {
            $builder = $builder->join('questionnaire_partner_information as information', 'information.id', '=', 'questionnaires.partner_information_id');
        } else {
            $builder = $builder->join('questionnaire_my_information as information', 'information.id', '=', 'questionnaires.my_information_id');
        }

        return !$withTest ? $builder : $builder->join('questionnaire_tests as test', 'test.id', '=', 'questionnaires.test_id');
    }

    public function applications()
    {
//        return $this->join('')
    }


    /**
     * Получить все данные с JOIN
     *
     * @param Builder $partner
     * @param Builder $my
     * @return Collection
     */
    public function getFormatting(Builder $partner, Builder $my): Collection
    {
        $except = [
            'partner_appearance_id', 'personal_qualities_partner_id', 'partner_information_id', 'test_id',
            'my_appearance_id', 'my_personal_qualities_id', 'my_information_id', 'manager_id'
        ];

        $myQuestionnaire = collect($my->get())->except($except);
        $partnerQuestionnaire = collect($partner->get())->except($except);


        $migrationQuestionnaire = [];

        foreach ($myQuestionnaire as $item) {
            $migrationQuestionnaire[]['my'] = $item;
        }

        foreach ($partnerQuestionnaire as $key => $item) {
            $migrationQuestionnaire[$key]['partner'] = $item;
        }

        return collect($migrationQuestionnaire);
    }
}
