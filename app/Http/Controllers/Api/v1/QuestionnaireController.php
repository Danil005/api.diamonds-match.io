<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\Questionnaire\Create;
use App\Models\Questionnaire;
use App\Models\QuestionnaireMyAppearance;
use App\Models\QuestionnaireMyInformation;
use App\Models\QuestionnaireMyPersonalQualities;
use App\Models\QuestionnairePartnerAppearance;
use App\Models\QuestionnairePartnerInformation;
use App\Models\QuestionnairePersonalQualitiesPartner;
use App\Models\QuestionnaireTest;
use App\Utils\QuestionnaireUtils;
use Illuminate\Http\Request;

class QuestionnaireController extends QuestionnaireUtils
{
    public function create(Create $request)
    {
        # Сохраняем все данные
        $data = [];

        # Делаем проверки на все поля
        $this->partnerAppearance();
        $this->personalQualitiesPartner();
        $this->partnerInformation();
        $this->test();
        $this->myAppearance();
        $this->myPersonalQualities();
        $this->myInformation();


        $partnerAppearance = $request->{config('app.questionnaire.fields.partner_appearance')};
        $personalQualitiesPartner = array_flip($request->{config('app.questionnaire.fields.personal_qualities_partner')});
        $partnerInformation = $request->{config('app.questionnaire.fields.partner_information')};
        $test = $request->{config('app.questionnaire.fields.test')};
        $myAppearance = $request->{config('app.questionnaire.fields.my_appearance')};
        $myPersonalQualities = $request->{config('app.questionnaire.fields.my_personal_qualities')};
        $myInformation = $request->{config('app.questionnaire.fields.my_information')};

        foreach ($personalQualitiesPartner as $key=>$item) {
            $personalQualitiesPartner[$key] = true;
        }

        foreach ($partnerInformation as $key=>$information) {
            if( $key == 'age' || $key == 'height' || $key == 'weight' || $key == 'languages' ) {
                $partnerInformation[$key] = implode(',', $information);
            }
        }

        foreach ($myInformation as $key=>$information) {
            if( $key == 'languages' ) {
                $myInformation[$key] = implode(',', $information);
            }
        }


        # Заносим все в базу данных
        $partnerAppearance = QuestionnairePartnerAppearance::create($partnerAppearance);
        $personalQualitiesPartner = QuestionnairePersonalQualitiesPartner::create($personalQualitiesPartner);
        $partnerInformation = QuestionnairePartnerInformation::create($partnerInformation);
        $test = QuestionnaireTest::create($test);
        $myAppearance = QuestionnaireMyAppearance::create($myAppearance);
        $myPersonalQualities = QuestionnaireMyPersonalQualities::create($myPersonalQualities);
        $myInformation = QuestionnaireMyInformation::create($myInformation);

        # Объединяем ответы в общую базу
        Questionnaire::create([
            'partner_appearance_id' => $partnerAppearance->id,
            'personal_qualities_partner_id' => $personalQualitiesPartner->id,
            'partner_information_id' => $partnerInformation->id,
            'test_id' => $test->id,
            'my_appearance_id' => $myAppearance->id,
            'my_personal_qualities_id' => $myPersonalQualities->id,
            'my_information_id' => $myInformation->id
        ]);

        $this->response()->success()->setMessage('Мы создали анкетку и теперь начинаем подбор для вас.')->send();
    }
}
