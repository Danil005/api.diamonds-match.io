<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\Questionnaire\Create;
use App\Http\Requests\Questionnaire\DeletePhotoQuestionnaire;
use App\Http\Requests\Questionnaire\UploadPhotoQuestionnaire;
use App\Http\Requests\Questionnaire\View;
use App\Models\Applications;
use App\Models\Questionnaire;
use App\Models\QuestionnaireMyAppearance;
use App\Models\QuestionnaireMyInformation;
use App\Models\QuestionnaireMyPersonalQualities;
use App\Models\QuestionnairePartnerAppearance;
use App\Models\QuestionnairePartnerInformation;
use App\Models\QuestionnairePersonalQualitiesPartner;
use App\Models\QuestionnaireTest;
use App\Models\QuestionnaireUploadPhoto;
use App\Utils\QuestionnaireUtils;
use App\Utils\TranslateFields;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Str;

class QuestionnaireController extends QuestionnaireUtils
{
    use TranslateFields;

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

        foreach ($personalQualitiesPartner as $key => $item) {
            $personalQualitiesPartner[$key] = true;
        }

        foreach ($partnerInformation as $key => $information) {
            if ($key == 'age' || $key == 'height' || $key == 'weight' || $key == 'languages') {
                $partnerInformation[$key] = implode(',', $information);
            }

            if ($key == 'live_country') {
                $partnerInformation[$key] = $information;
            }

            if ($key == 'live_city') {
                $partnerInformation['city'] = $myInformation['live_country'] . ', ' . $information;
            }
        }

        foreach ($myInformation as $key => $information) {
            if ($key == 'languages') {
                $myInformation[$key] = implode(',', $information);
            }

            if ($key == 'live_country') {
                $myInformation[$key] = $information;
            }

            if ($key == 'live_city') {
                $myInformation['city'] = $myInformation['live_country'] . ', ' . $information;
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
        Questionnaire::where('sign', $request->sign)->update([
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

    /**
     * @param View $request
     */
    public function view(View $request)
    {

        $questionnaire = new Questionnaire();
        $questionnaire = $questionnaire->where('id', $request->id)
            ->whereNotNUll('partner_appearance_id')->first();

        $result = [
            'partner_appearance' => collect(QuestionnairePartnerAppearance::where('id', $questionnaire->partner_appearance_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'personal_qualities_partner' => collect(QuestionnairePersonalQualitiesPartner::where('id', $questionnaire->personal_qualities_partner_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'partner_information' => collect(QuestionnairePartnerInformation::where('id', $questionnaire->partner_information_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'test' => collect(QuestionnaireTest::where('id', $questionnaire->test_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'my_appearance' => collect(QuestionnaireMyAppearance::where('id', $questionnaire->my_appearance_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'my_personal_qualities' => collect(QuestionnaireMyPersonalQualities::where('id', $questionnaire->my_personal_qualities_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'my_information' => collect(QuestionnaireMyInformation::where('id', $questionnaire->my_information_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray()
        ];

        $zodiac = $this->zodiacSigns();

        $result['partner_information']['zodiac_signs'] = $zodiac[$result['partner_information']['zodiac_signs']];
        $result['my_information']['zodiac_signs'] = $zodiac[$result['my_information']['zodiac_signs']];

        $result['my_information']['age'] = $this->years($result['my_information']['age']);
        $result['partner_information']['age'] = $this->years(explode(',', $result['partner_information']['age']));

        $temp = [];

        foreach ($result['personal_qualities_partner'] as $key => $item) {
            $temp[] = $this->personalQuality($key, $result['partner_appearance']['sex']);
        }
        $result['personal_qualities_partner'] = $temp;

        foreach ($result['my_personal_qualities'] as $key => $item) {
            $result['my_personal_qualities'][$this->personalQuality($key, $result['my_appearance']['sex'])] = $item;
            unset($result['my_personal_qualities'][$key]);
        }

        // Партнер

        $result['partner_appearance']['ethnicity'] = $this->ethnicity($result['partner_appearance']['ethnicity']);
        $result['partner_appearance']['body_type'] = $this->bodyType($result['partner_appearance']['body_type']);

        if (isset($result['partner_appearance']['chest']) && $result['partner_appearance']['chest'] !== null) {
            $result['partner_appearance']['chest'] = $this->chestOrBooty($result['partner_appearance']['chest']);
        }

        if (isset($result['partner_appearance']['booty']) && $result['partner_appearance']['booty'] !== null) {
            $result['partner_appearance']['booty'] = $this->chestOrBooty($result['partner_appearance']['booty']);
        }

        if (isset($result['partner_appearance']['hair_length']) && $result['partner_appearance']['hair_length'] !== null) {
            $result['partner_appearance']['hair_length'] = $this->hairLength($result['partner_appearance']['hair_length']);
        }

        $result['partner_appearance']['hair_color'] = $this->hairColor($result['partner_appearance']['hair_color']);
        $result['partner_appearance']['eye_color'] = $this->colorEye($result['partner_appearance']['eye_color']);
        $result['partner_appearance']['sex'] = $result['partner_appearance']['sex'] === 'female' ? 'Женщину' : 'Мужчину';


        // Мои

        $result['my_appearance']['ethnicity'] = $this->ethnicity($result['my_appearance']['ethnicity']);
        $result['my_appearance']['body_type'] = $this->bodyType($result['my_appearance']['body_type']);

        if (isset($result['my_appearance']['chest']) && $result['my_appearance']['chest'] !== null) {
            $result['my_appearance']['chest'] = $this->chestOrBooty($result['my_appearance']['chest']);
        }

        if (isset($result['my_appearance']['booty']) && $result['my_appearance']['booty'] !== null) {
            $result['my_appearance']['booty'] = $this->chestOrBooty($result['my_appearance']['booty']);
        }

        if (isset($result['my_appearance']['hair_length']) && $result['my_appearance']['hair_length'] !== null) {
            $result['my_appearance']['hair_length'] = $this->hairLength($result['my_appearance']['hair_length']);
        }

        $result['my_appearance']['hair_color'] = $this->hairColor($result['my_appearance']['hair_color']);
        $result['my_appearance']['eye_color'] = $this->colorEye($result['my_appearance']['eye_color']);
        $result['my_appearance']['sex'] = $result['my_appearance']['sex'] === 'female' ? 'Женщина' : 'Мужчина';

        $photos = QuestionnaireUploadPhoto::where('questionnaire_id', $questionnaire->id)->get(['id', 'path']);
        $result['files'] = [
            'photos' => $photos
        ];


        $this->response()->success()->setMessage('Анкета получена')->setData($result)->send();
    }

    public function uploadPhoto(UploadPhotoQuestionnaire $request)
    {
        $questionnaire = Questionnaire::where('id', $request->questionnaire_id)->first();
        if (empty($questionnaire))
            $this->response()->error()->setMessage('Анкета не найдена')->send();

        $file = $request->file('file');
        $path = 'public/questionnaire/photos/sign_' . $questionnaire->sign;

        $upload = $file->storePubliclyAs($path, md5(Str::random(16)).'.'.$file->getClientOriginalExtension());

        $path = str_replace('public/', 'storage/', $upload);

        QuestionnaireUploadPhoto::create([
            'path' => $path,
            'questionnaire_id' => $request->questionnaire_id
        ]);

        $this->response()->setMessage('Файл загружен')->setData([
            'path' => env('APP_URL').'/'. $path
        ])->send();
    }

    public function deletePhoto(DeletePhotoQuestionnaire $request)
    {
        $questionnaire = Questionnaire::where('id', $request->questionnaire_id)->first();
        if (empty($questionnaire))
            $this->response()->error()->setMessage('Анкета не найдена')->send();

        $photo = QuestionnaireUploadPhoto::where('id', $request->photo_id)->first();

        dd($photo['path']);
        Storage::disk('public')->delete($photo['path']);
    }
}
