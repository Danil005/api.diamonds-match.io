<?php

namespace App\Http\Controllers\Api\v1;

use App\Events\NotifyPushed;
use App\Http\Requests\Employee\Update;
use App\Http\Requests\Questionnaire\Create;
use App\Http\Requests\Questionnaire\DeleteFilesQuestionnaire;
use App\Http\Requests\Questionnaire\DeletePhotoQuestionnaire;
use App\Http\Requests\Questionnaire\FilesQuestionnaire;
use App\Http\Requests\Questionnaire\GetQuestionnaire;
use App\Http\Requests\Questionnaire\MakeDateQuestionnaire;
use App\Http\Requests\Questionnaire\OpenFilesQuestionnaire;
use App\Http\Requests\Questionnaire\UploadPhotoQuestionnaire;
use App\Http\Requests\Questionnaire\View;
use App\Models\Applications;
use App\Models\Langs;
use App\Models\Questionnaire;
use App\Models\QuestionnaireAppointedDate;
use App\Models\QuestionnaireFiles;
use App\Models\QuestionnaireHistory;
use App\Models\QuestionnaireMailing;
use App\Models\QuestionnaireMatch;
use App\Models\QuestionnaireMyAppearance;
use App\Models\QuestionnaireMyInformation;
use App\Models\QuestionnaireMyPersonalQualities;
use App\Models\QuestionnairePartnerAppearance;
use App\Models\QuestionnairePartnerInformation;
use App\Models\QuestionnairePersonalQualitiesPartner;
use App\Models\QuestionnaireTest;
use App\Models\QuestionnaireUploadPhoto;
use App\Models\SignQuestionnaire;
use App\Models\User;
use App\Services\PptxCreator;
use App\Utils\QuestionnaireUtils;
use App\Utils\TranslateFields;
use Carbon\Carbon;
use Hash;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Jenssegers\Date\Date;
use SoareCostin\FileVault\Facades\FileVault;
use Str;

class QuestionnaireController extends QuestionnaireUtils
{
    use TranslateFields;

    public function create(Create $request)
    {
        # Сохраняем все данные
        $data = [];
        if( !$request->has('sign') || $request->sign == null )
            $this->response()->error()->setMessage('SIGN должна быть задана')->send();


        # Делаем проверки на все поля
        $this->partnerAppearance();
        $this->personalQualitiesPartner();
        $this->partnerInformation();
        $this->test();
        $this->myAppearance();
        $this->myPersonalQualities();
        $this->myInformation();


        $partnerAppearance = $request->{config('app.questionnaire.fields.partner_appearance')};
        $personalQualitiesPartner = $request->{config('app.questionnaire.fields.personal_qualities_partner')};
        $partnerInformation = $request->{config('app.questionnaire.fields.partner_information')};
        $test = $request->{config('app.questionnaire.fields.test')};
        $myAppearance = $request->{config('app.questionnaire.fields.my_appearance')};
        $myPersonalQualities = $request->{config('app.questionnaire.fields.my_personal_qualities')};
        $myInformation = $request->{config('app.questionnaire.fields.my_information')};

        foreach ($personalQualitiesPartner as $key => $item) {
            if ($item == null) {
                unset($personalQualitiesPartner[$key]);
            }
        }

        $personalQualitiesPartner = array_flip(array_values($personalQualitiesPartner));

        foreach ($personalQualitiesPartner as $key => $item) {
            $personalQualitiesPartner[$key] = true;
        }

        foreach ($partnerInformation as $key => $information) {
            if ($key == 'age') {
                $partnerInformation[$key] = implode(',', $information);
            }

            if ($key == 'height' || $key == 'weight') {
                $partnerInformation[$key][0] = (int)$information;
                $partnerInformation[$key][1] = (int)$information;
                $partnerInformation[$key] = implode(',', $information);
            }

            if ($key == 'languages') {
                $langs = new Langs();
                foreach ($information as $item) {
                    $langs = $langs->orWhere('code', $item);
                }
                $langs = $langs->get()->toArray();

                $temp = '';
                foreach ($langs as $item) {
                    $temp .= $item['nameRU'] . ',';
                }
                $partnerInformation[$key] = trim($temp, ',');
            }

            $liveCountry = '';
            if ($key == 'live_place') {
                foreach ($information as $country) {
                    $liveCountry .= $country . ',';
                }
                $partnerInformation['city'] = trim($liveCountry, ',');
            }

            if ($key == 'place_birth') {
                $place_birth = '';

                if (isset($partnerInformation[$key][0])) {
                    foreach ($partnerInformation[$key] as $item) {
                        $place_birth .= $item . ',';
                    }

                    $partnerInformation[$key] = trim($place_birth, ',');
                } else {
                    $this->response()->error()->setMessage('Поле `place_birth` должно быть заполнено')->send();
                }
            }
        }

        foreach ($myInformation as $key => $information) {
            if ($key == 'birthday') {
                $birthday = Carbon::createFromTimeString($information . ' 0:0');
                $now = Carbon::now();

                $myInformation['age'] = $birthday->diffInYears($now);
            }

            if( $key == 'countries_was' || $key == 'countries_dream') {
                $myInformation[$key] = implode(',', $information);
            }

            if ($key == 'languages') {
                $langs = new Langs();
                foreach ($information as $item) {
                    $langs = $langs->orWhere('code', $item);
                }
                $langs = $langs->get()->toArray();

                $temp = '';
                foreach ($langs as $item) {
                    $temp .= $item['nameRU'] . ',';
                }
                $myInformation[$key] = trim($temp, ',');
            }

            if ($key == 'live_country') {
                $myInformation[$key] = $information;
            }

            if ($key == 'live_city') {
                $myInformation['city'] = $myInformation['live_country'] . ', ' . $information;
            }

            if ($key == 'place_birth') {
                if( is_string($information) ) {
                    $myInformation[$key] = $information;
                } else {
                    $myInformation[$key] = implode(',', $information);
                }
            }

            if ($key == 'height' || $key == 'weight') {
                $myInformation[$key] = (int)$myInformation[$key];
            }

            if( $key == 'countries_was' ) {
                $myInformation[$key] = implode(',', $information);
            }

            if( $key == 'countries_dream' ) {
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
        Questionnaire::where('sign', $request->sign)->update([
            'partner_appearance_id' => $partnerAppearance->id,
            'personal_qualities_partner_id' => $personalQualitiesPartner->id,
            'partner_information_id' => $partnerInformation->id,
            'test_id' => $test->id,
            'my_appearance_id' => $myAppearance->id,
            'my_personal_qualities_id' => $myPersonalQualities->id,
            'my_information_id' => $myInformation->id
        ]);

        $q = Questionnaire::where('sign', $request->sign)->first();

        $resp = Applications::where('questionnaire_id', $q->id)->first(['responsibility']);

        $this->createNotify('questionnaire', 'Ваш клиент '.$myInformation->name.' заполнил анкету.', [
            'questionnaire_id' => $q->id,
            'employee' => $resp != null ? explode(',', $resp['responsibility'])[0] : null
        ]);

        $this->response()->success()->setMessage('Мы создали анкетку и теперь начинаем подбор для вас.')->send();
    }

    public function createFromSite(Create $request)
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
        $personalQualitiesPartner = $request->{config('app.questionnaire.fields.personal_qualities_partner')};
        $partnerInformation = $request->{config('app.questionnaire.fields.partner_information')};
        $test = $request->{config('app.questionnaire.fields.test')};
        $myAppearance = $request->{config('app.questionnaire.fields.my_appearance')};
        $myPersonalQualities = $request->{config('app.questionnaire.fields.my_personal_qualities')};
        $myInformation = $request->{config('app.questionnaire.fields.my_information')};

        foreach ($personalQualitiesPartner as $key => $item) {
            if ($item == null) {
                unset($personalQualitiesPartner[$key]);
            }
        }

        $personalQualitiesPartner = array_flip(array_values($personalQualitiesPartner));

        foreach ($personalQualitiesPartner as $key => $item) {
            $personalQualitiesPartner[$key] = true;
        }

        foreach ($partnerInformation as $key => $information) {
            if ($key == 'age') {
                $partnerInformation[$key] = implode(',', $information);
            }

            if ($key == 'height' || $key == 'weight') {
                $partnerInformation[$key][0] = (int)$information;
                $partnerInformation[$key][1] = (int)$information;
                $partnerInformation[$key] = implode(',', $information);
            }

            if ($key == 'languages') {
                $langs = new Langs();
                foreach ($information as $item) {
                    $langs = $langs->orWhere('code', $item);
                }
                $langs = $langs->get()->toArray();

                $temp = '';
                foreach ($langs as $item) {
                    $temp .= $item['nameRU'] . ',';
                }
                $partnerInformation[$key] = trim($temp, ',');
            }

            $liveCountry = '';
            if ($key == 'live_place') {
                foreach ($information as $country) {
                    $liveCountry .= $country . ',';
                }
                $partnerInformation['city'] = trim($liveCountry, ',');
            }

            if ($key == 'place_birth') {
                $place_birth = '';

                if (isset($partnerInformation[$key][0])) {
                    foreach ($partnerInformation[$key] as $item) {
                        $place_birth .= $item . ',';
                    }

                    $partnerInformation[$key] = trim($place_birth, ',');
                } else {
                    $this->response()->error()->setMessage('Поле `place_birth` должно быть заполнено')->send();
                }
            }
        }

        foreach ($myInformation as $key => $information) {
            if ($key == 'birthday') {
                $birthday = Carbon::createFromTimeString($information . ' 0:0');
                $now = Carbon::now();

                $myInformation['age'] = $birthday->diffInYears($now);
            }

            if( $key == 'countries_was' || $key == 'countries_dream') {
                $myInformation[$key] = implode(',', $information);
            }

            if ($key == 'languages') {
                $langs = new Langs();
                foreach ($information as $item) {
                    $langs = $langs->orWhere('code', $item);
                }
                $langs = $langs->get()->toArray();

                $temp = '';
                foreach ($langs as $item) {
                    $temp .= $item['nameRU'] . ',';
                }
                $myInformation[$key] = trim($temp, ',');
            }

            if ($key == 'live_country') {
                $myInformation[$key] = $information;
            }

            if ($key == 'live_city') {
                $myInformation['city'] = $myInformation['live_country'] . ', ' . $information;
            }

            if ($key == 'place_birth') {
                if( is_string($information) ) {
                    $myInformation[$key] = $information;
                } else {
                    $myInformation[$key] = implode(',', $information);
                }
            }

            if ($key == 'height' || $key == 'weight') {
                $myInformation[$key] = (int)$myInformation[$key];
            }

            if( $key == 'countries_was' ) {
                $myInformation[$key] = implode(',', $information);
            }

            if( $key == 'countries_dream' ) {
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

        $application = Applications::create([
            'client_name' => $myInformation->name,
            'service_type' => 'free',
            'status' => 0,
            'questionnaire_id' => null,
            'responsibility' => null,
            'link' => null,
            'link_active' => true,
            'from' => 'Сайт, кнопка',
            'email' => $request->has('email') ? $request->email : null,
            'phone' => $request->has('phone') ? $request->phone : null
        ]);

        # Объединяем ответы в общую базу
        $questionnaire = Questionnaire::create([
            'partner_appearance_id' => $partnerAppearance->id,
            'personal_qualities_partner_id' => $personalQualitiesPartner->id,
            'partner_information_id' => $partnerInformation->id,
            'test_id' => $test->id,
            'my_appearance_id' => $myAppearance->id,
            'my_personal_qualities_id' => $myPersonalQualities->id,
            'my_information_id' => $myInformation->id
        ]);

        $sign = md5(\Illuminate\Support\Str::random(16));

        SignQuestionnaire::create([
            'application_id' => $application->id,
            'questionnaire_id' => $questionnaire->id,
            'sign' => $sign,
            'active' => true
        ]);
        $link = env('APP_QUESTIONNAIRE_URL') . '/sign/' . $sign;
        Questionnaire::where('id', $questionnaire->id)->update(['sign' => $sign]);
        Applications::where('id', $application->id)->update(['link' => $link, 'questionnaire_id' => $questionnaire->id]);


        $this->createNotify('application', 'Появилась новая заявка.', [
            'application_id' => $application->id,
        ]);

        $this->createNotify('questionnaire', 'Появилась новая анкета.', [
            'questionnaire_id' => $questionnaire->id,
        ]);


        event(new NotifyPushed('Появилась новая заявка', [
            'application_id' => $application->id,
        ]));

        event(new NotifyPushed('Появилась новая анкета', [
            'questionnaire_id' => $questionnaire->id,
        ]));

        $this->response()->success()->setMessage('Мы создали анкетку и теперь начинаем подбор для вас.')->setData([
            'link_questionnaire' => $link
        ])->send();
    }

    /**
     * @param View $request
     */
    public function view(View $request)
    {

        $questionnaire = new Questionnaire();
        $questionnaire = $questionnaire->where('id', $request->id)
            ->whereNotNUll('partner_appearance_id')->first();

        if(empty($questionnaire))
           $this->response()->error()->setMessage("Анкета не существует")->setData(["error" => 404])->send();

        $application = Applications::withTrashed()->where('questionnaire_id', $request->id)->first();


        $history = QuestionnaireHistory::where('questionnaire_histories.questionnaire_id', (int)$questionnaire->id)
            ->join('users', 'users.id', '=', 'questionnaire_histories.user')
            ->get([
                'questionnaire_histories.id', 'from', 'comment', 'questionnaire_histories.created_at',
                'name'
            ]);
        Date::setlocale(config('app.locale'));
        Carbon::setLocale(config('app.locale'));

        $history = $history->toArray();

        foreach ($history as $key => $item) {
            $history[$key]['created_at'] = Carbon::createFromTimeString($item['created_at'])->format('j F Y');
        }

        $result = [
            'partner_appearance' => collect(QuestionnairePartnerAppearance::where('id', $questionnaire->partner_appearance_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'personal_qualities_partner' => collect(QuestionnairePersonalQualitiesPartner::where('id', $questionnaire->personal_qualities_partner_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'partner_information' => collect(QuestionnairePartnerInformation::where('id', $questionnaire->partner_information_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'test' => collect(QuestionnaireTest::where('id', $questionnaire->test_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'my_appearance' => collect(QuestionnaireMyAppearance::where('id', $questionnaire->my_appearance_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'my_personal_qualities' => collect(QuestionnaireMyPersonalQualities::where('id', $questionnaire->my_personal_qualities_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'my_information' => collect(QuestionnaireMyInformation::where('id', $questionnaire->my_information_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'application' => $application,
            'histories' => $history,
            'appointed_data' => QuestionnaireAppointedDate::where('questionnaire_id', $request->id)->first(),
            'matched_count' => QuestionnaireMatch::where('questionnaire_id', $request->id)->count()
        ];

        $zodiac = $this->zodiacSigns();

        $result['partner_information']['zodiac_signs'] = $zodiac[$result['partner_information']['zodiac_signs']];
        $result['my_information']['zodiac_signs'] = $zodiac[$result['my_information']['zodiac_signs']];

        $result['my_information']['age'] = $this->years($result['my_information']['age']);
        $result['partner_information']['age'] = $this->years(explode(',', $result['partner_information']['age']));

        $temp = [];

        foreach ($result['personal_qualities_partner'] as $key => $item) {
            if ($item)
                $temp[] = $this->personalQuality($key, $result['partner_appearance']['sex']);
        }
        $result['personal_qualities_partner'] = $temp;

        foreach ($result['my_personal_qualities'] as $key => $item) {
            try {
                $result['my_personal_qualities'][$this->personalQuality($key, $result['my_appearance']['sex'])] = $item;
                unset($result['my_personal_qualities'][$key]);
            } catch (\Exception) {

            }
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


        foreach ($result['my_information'] as $key=>$item) {
            if( $key == 'smoking' ) {
                $result['my_information']['smoking'] = $this->smoking($result['my_information']['smoking'], 'male');
            }

            if( $key == 'alcohol' ) {
                $result['my_information']['alcohol'] = $this->alcohol($result['my_information']['alcohol'], 'male');
            }

            if( $key == 'religion' ) {
                $result['my_information']['religion'] = $this->religion($result['my_information']['religion'], 'male');
            }

            if( $key == 'sport' ) {
                $result['my_information']['sport'] = $this->sport($result['my_information']['sport'], 'male');
            }

            if( $key == 'education' ) {
                $result['my_information']['education'] = $this->education($result['my_information']['education'], 'male');
            }

            if( $key == 'work' ) {
                $result['my_information']['work'] = $this->work($result['my_information']['work'], 'male');
            }

            if( $key == 'pets' ) {
                $result['my_information']['pets'] = $this->pets($result['my_information']['pets'], 'male');
            }

            if( $key == 'films_or_books' ) {
                $result['my_information']['films_or_books'] = $this->fm($result['my_information']['films_or_books'], 'male');
            }

            if( $key == 'relax' ) {
                $result['my_information']['relax'] = $this->relax($result['my_information']['relax'], 'male');
            }

            if( $key == 'sleep' ) {
                $result['my_information']['sleep'] = $this->sleep($result['my_information']['sleep'], 'male');
            }

            if( $key == 'clubs' ) {
                $result['my_information']['clubs'] = $this->clubs($result['my_information']['clubs'], 'male');
            }

            if( $key == 'salary' ) {
                $result['my_information']['salary'] = $this->salary($result['my_information']['salary']);
            }

            if( $key == 'marital_status' ) {
                $result['my_information']['marital_status'] = $this->maritalStatus($result['my_information']['marital_status'], $result['my_appearance']['sex']);
            }

            if( $key == 'children_desire' ) {
                $result['my_information']['children_desire'] = $this->childrenDesire($result['my_information']['children_desire'], $result['my_appearance']['sex']);
            }
        }

        foreach ($result['partner_information'] as $key=>$item) {
            if( $key == 'smoking' ) {
                $result['partner_information']['smoking'] = $this->smoking($result['partner_information']['smoking'], 'male');
            }

            if( $key == 'alcohol' ) {
                $result['partner_information']['alcohol'] = $this->alcohol($result['partner_information']['alcohol'], 'male');
            }

            if( $key == 'religion' ) {
                $result['partner_information']['religion'] = $this->religion($result['partner_information']['religion'], 'male');
            }

            if( $key == 'sport' ) {
                $result['partner_information']['sport'] = $this->sport($result['partner_information']['sport'], 'male');
            }

            if( $key == 'education' ) {
                $result['partner_information']['education'] = $this->education($result['partner_information']['education'], 'male');
            }

            if( $key == 'work' ) {
                $result['partner_information']['work'] = $this->work($result['partner_information']['work'], 'male');
            }

            if( $key == 'pets' ) {
                $result['partner_information']['pets'] = $this->pets($result['partner_information']['pets'], 'male');
            }

            if( $key == 'films_or_books' ) {
                $result['partner_information']['films_or_books'] = $this->fm($result['partner_information']['films_or_books'], 'male');
            }

            if( $key == 'relax' ) {
                $result['partner_information']['relax'] = $this->relax($result['partner_information']['relax'], 'male');
            }

            if( $key == 'sleep' ) {
                $result['partner_information']['sleep'] = $this->sleep($result['partner_information']['sleep'], 'male');
            }

            if( $key == 'clubs' ) {
                $result['partner_information']['clubs'] = $this->clubs($result['partner_information']['clubs'], 'male');
            }

            if( $key == 'salary' ) {
                $result['partner_information']['salary'] = $this->salary($result['partner_information']['salary']);
            }

            if( $key == 'marital_status' ) {
                $result['partner_information']['marital_status'] = $this->maritalStatus($result['partner_information']['marital_status'], $result['my_appearance']['sex']);
            }

            if( $key == 'children_desire' ) {
                $result['partner_information']['children_desire'] = $this->childrenDesire($result['partner_information']['children_desire'], $result['my_appearance']['sex']);
            }
        }

//        $result['application']['service_type'] = $serviceType;

        $photos = QuestionnaireUploadPhoto::where('questionnaire_id', $questionnaire->id)->get(['id', 'path']);
        $files = QuestionnaireFiles::where('questionnaire_id', $questionnaire->id)->get(['id', 'type', 'name', 'size']);
        $result['files'] = [
            'photos' => $photos,
            'files' => $files
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

        $upload = $file->storePubliclyAs($path, md5(Str::random(16)) . '.' . $file->getClientOriginalExtension());

        $path = str_replace('public/', 'storage/', $upload);

        $q = QuestionnaireUploadPhoto::create([
            'path' => $path,
            'questionnaire_id' => $request->questionnaire_id
        ]);

        $this->response()->success()->setMessage('Файл загружен')->setData([
            'path' => env('APP_URL') . '/' . $path,
            'id' => $q->id
        ])->send();
    }

    public function deletePhoto(DeletePhotoQuestionnaire $request)
    {
        $questionnaire = Questionnaire::where('id', $request->questionnaire_id)->first();
        if (empty($questionnaire))
            $this->response()->error()->setMessage('Анкета не найдена')->send();

        $photo = QuestionnaireUploadPhoto::where('id', $request->photo_id)->first();

        Storage::disk('public')->delete(str_replace('storage/', '', $photo['path']));

        QuestionnaireUploadPhoto::where('id', $request->photo_id)->delete();

        $this->response()->success()->setMessage('Фотография была удалена')->send();
    }

    public function uploadFile(FilesQuestionnaire $request)
    {
        $questionnaire = Questionnaire::where('id', $request->questionnaire_id)->first();
        if (empty($questionnaire))
            $this->response()->error()->setMessage('Анкета не найдена')->send();

        if (!in_array($request->type, ['passport', 'agree', 'offer', 'founder']))
            $this->response()->error()->setMessage('Неверный тип загрузки файла')->send();

        $file = $request->file('file');
        $path = 'public/questionnaire/files/' . $request->type . '/sign_' . $questionnaire->sign;

        $key = substr(md5($path), 6, 12);
        $name = $request->type . '-encrypted{' . $key . '}.' . $file->getClientOriginalExtension();
        $filename = Storage::putFileAs($path, $file, $name);

        if ($filename) {
            FileVault::encrypt($filename);
        }

        $path = $path . '/' . str_replace('{' . $key . '}', '{hidden}', $name);

        $name = match ($request->type) {
            'passport' => 'passport-' . $request->questionnaire_id . '.pdf',
            'agree' => 'consent-data-processing-' . $request->questionnaire_id . '.pdf',
            'offer' => 'contract-copy-' . $request->questionnaire_id . '.pdf',
            'founder' => 'contract-founder-' . $request->questionnaire_id . '.pdf'
        };

        QuestionnaireFiles::create([
            'path' => $path,
            'type' => $request->type,
            'questionnaire_id' => $request->questionnaire_id,
            'name' => $name,
            'size' => round($file->getSize() / 1024 / 1024, 2) . ' mb',
            'key' => $key
        ]);

        $this->response()->success()->setMessage('Файл загружен')->setData([
            'path' => env('APP_URL') . '/' . $path,
            'encrypted' => true
        ])->send();
    }

    public function openFile(OpenFilesQuestionnaire $request)
    {
        $questionnaire = Questionnaire::where('id', $request->questionnaire_id)->first();
        if (empty($questionnaire))
            $this->response()->error()->setMessage('Анкета не найдена')->send();

        $file = QuestionnaireFiles::where('id', $request->file_id)->first();

        if (empty($file))
            $this->response()->setMessage('Данный файл не был найден')->send();

        $path = str_replace('{hidden}', '{' . $file['key'] . '}', $file['path']) . '.enc';

        return response()->streamDownload(function () use ($path) {
            FileVault::streamDecrypt($path);
        }, $file['name']);
    }

    public function deleteFile(DeleteFilesQuestionnaire $request)
    {
        $questionnaire = Questionnaire::where('id', $request->questionnaire_id)->first();
        if (empty($questionnaire))
            $this->response()->error()->setMessage('Анкета не найдена')->send();

        $file = QuestionnaireFiles::where('id', $request->file_id)->first();

        Storage::disk('public')->delete(
            str_replace('public/', '', str_replace('{hidden}', '{' . $file['key'] . '}', $file['path'])) . '.enc'
        );

        QuestionnaireFiles::where('id', $request->file_id)->delete();

        $this->response()->success()->setMessage('Файл был удалена')->send();
    }

    public function makeDate(MakeDateQuestionnaire $request)
    {
        $questionnaire = Questionnaire::where('id', $request->questionnaire_id)->first();
        if (empty($questionnaire))
            $this->response()->error()->setMessage('Анкета не найдена')->send();

        $withQuestionnaire = (new Questionnaire)->my()->where('questionnaires.id', $request->with_questionnaire_id)->first();
        if (empty($withQuestionnaire))
            $this->response()->error()->setMessage('Анкета не найдена')->send();

        $dateValidation = explode('.', $request->date);
        $timeValidation = explode(':', $request->time);

        if (count($dateValidation) != 3 || strlen($dateValidation[0]) != 2 || strlen($dateValidation[1]) != 2 || strlen($dateValidation[2]) != 4)
            $this->response()->error()->setMessage('Неверный формат даты. Необходимо: dd.mm.YYYY')->send();

        if (count($timeValidation) != 2 || strlen($timeValidation[0]) != 2 || strlen($timeValidation[1]) != 2)
            $this->response()->error()->setMessage('Неверный формат времени. Необходимо: HH:MM')->send();

        QuestionnaireAppointedDate::create($request->all());

        QuestionnaireHistory::create([
            'user' => auth()->user()->id,
            'from' => 'appointment',
            'comment' => 'Свидание с ' . $withQuestionnaire->name . ' было назначено на ' . $request->date . ' в ' . $request->time .'.',
            'questionnaire_id' => $request->questionnaire_id
        ]);

        $this->response()->success()->setMessage("Дата свидания была назначена на {$request->date} в {$request->time}. Удачи!")->send();
    }

    public function getMakeDate(Request $request)
    {
        $questionnaire = Questionnaire::where('id', $request->questionnaire_id)->first();
        if (empty($questionnaire))
            $this->response()->error()->setMessage('Анкета не найдена')->send();

        $matching = QuestionnaireMatch::where('questionnaire_id', $request->questionnaire_id)
            ->join('questionnaires as q', 'q.id', '=', 'questionnaire_matches.with_questionnaire_id')
            ->join('questionnaire_my_information as information', 'information.id', '=', 'q.my_information_id')
            ->get(['questionnaire_id', 'with_questionnaire_id', 'name']);

        $this->response()->success()->setMessage('Доступные свидания')->setData($matching)->send();
    }

    public function viewMatch(Request $request)
    {
        $questionnaire = Questionnaire::where('id', $request->questionnaire_id)->first();
        if (empty($questionnaire))
            $this->response()->error()->setMessage('Анкета не найдена')->send();

        $withQuestionnaire = Questionnaire::where('id', $request->with_questionnaire_id)->first();
        if (empty($questionnaire))
            $this->response()->error()->setMessage('Анкета не найдена')->send();

        $matching = QuestionnaireMatch::where('questionnaire_id', $request->questionnaire_id)
            ->join('questionnaires as q', 'q.id', '=', 'questionnaire_matches.questionnaire_id')
            ->join('questionnaire_my_information as information', 'information.id', '=', 'q.my_information_id')
            ->first(['questionnaire_id', 'with_questionnaire_id', 'name', 'total', 'appearance', 'information', 'about_me', 'test', 'personal_qualities']);

        $partner = QuestionnaireMatch::where('with_questionnaire_id', $withQuestionnaire->id)
            ->join('questionnaires as q', 'q.id', '=', 'questionnaire_matches.with_questionnaire_id')
            ->join('questionnaire_my_information as information', 'information.id', '=', 'q.my_information_id')
            ->first(['name']);

        $questionnaire = new Questionnaire();

        $myAppearance = $questionnaire->my()->where('questionnaires.id', $request->questionnaire_id)->first(
            collect(array_keys(config('app.questionnaire.value.partner_appearance')))->except([])->toArray()
        )->toArray();

        $partnerAppearance = $questionnaire->partner()->where('questionnaires.id', $withQuestionnaire->id)->first(
            collect(array_keys(config('app.questionnaire.value.partner_appearance')))->except([])->toArray()
        )->toArray();

        $requirements = [
            'my' => [],
            'partner' => []
        ];

        foreach ($myAppearance as $key => $item) {
            if ($key == 'sex') continue;

            if(  $item == null ) continue;

            if ($item == $partnerAppearance[$key] && $item != null && $partnerAppearance[$key] != null) {
                $requirements['my'][$key] = true;
            } else {
                $requirements['my'][$key] = false;
            }
        }


        $myAppearance = $questionnaire->partner()->where('questionnaires.id', $request->questionnaire_id)->first(
            collect(array_keys(config('app.questionnaire.value.partner_appearance')))->except([])->toArray()
        )->toArray();

        $a = $myAppearance;
        $partnerAppearance = $questionnaire->my()->where('questionnaires.id', $withQuestionnaire->id)->first(
            collect(array_keys(config('app.questionnaire.value.partner_appearance')))->except([])->toArray()
        )->toArray();

        foreach ($partnerAppearance as $key => $item) {
            if ($key == 'sex') continue;

            if(  $item == null ) continue;

            if ($item == $myAppearance[$key]) {
                $requirements['partner'][$key] = true;
            } else {
                $requirements['partner'][$key] = false;
            }
        }


        $myAppearance = $questionnaire->my()->where('questionnaires.id', $request->questionnaire_id)->first(
            [
                ...collect(array_keys(config('app.questionnaire.value.my_personal_qualities')))->except([12])->toArray(),
                ...['personal_qualities.sport']
            ]
        )->toArray();

        $partnerAppearance = $questionnaire->partner()->where('questionnaires.id', $withQuestionnaire->id)->first([
            ...collect(array_keys(config('app.questionnaire.value.my_personal_qualities')))->except([12])->toArray(),
            ...['personal_qualities.sport']
        ])->toArray();

        $qualities = [
            'my' => [],
            'partner' => []
        ];

        foreach ($myAppearance as $key => $item) {
            if ($myAppearance[$key] == $partnerAppearance[$key]) {
                $qualities['my'][] = $this->personalQuality($key, $a['sex']);
            }
        }



        $myAppearance = $questionnaire->partner()->where('questionnaires.id', $request->questionnaire_id)->first(
            [
                ...collect(array_keys(config('app.questionnaire.value.my_personal_qualities')))->except([12])->toArray(),
                ...['personal_qualities.sport']
            ]
        )->toArray();

        $partnerAppearance = $questionnaire->my()->where('questionnaires.id', $withQuestionnaire->id)->first([
            ...collect(array_keys(config('app.questionnaire.value.my_personal_qualities')))->except([12])->toArray(),
            ...['personal_qualities.sport']
        ])->toArray();


        foreach ($partnerAppearance as $key => $item) {
            if ($partnerAppearance[$key] == $myAppearance[$key]) {
                $qualities['partner'][] = $this->personalQuality($key, $a['sex']);
            }
        }


        $rs = $matching?->toArray();

        $rs['total'] = round($rs['total']);
        $rs['appearance'] = round($rs['appearance']);
        $rs['information'] = round($rs['information']);
        $rs['about_me'] = round($rs['about_me']);
        $rs['test'] = round($rs['test']);
        $rs['personal_qualities'] = round($rs['personal_qualities']);

        $result = [
            'matching_as' => $matching?->total,
            'partner_questionnaire_id' => $withQuestionnaire->id,
            'matching' => $rs,
            'requirements' => $requirements,
            'qualities' => $qualities,
            'names' => [
                'me' => $matching->name,
                'partner' => $partner->name
            ]
        ];

        $this->response()->success()->setMessage('Доступные свидания')->setData($result)->send();
    }

    private function declOfNum($number, $titles)
    {
        $cases = array(2, 0, 1, 1, 1, 2);
        $format = $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
        return sprintf($format, $number);
    }

    private function mbUcfirst($str, $encoding='UTF-8')
    {
        $str = mb_ereg_replace('^[\ ]+', '', $str);
        $str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding).
            mb_substr($str, 1, mb_strlen($str), $encoding);
        return $str;
    }

    public function get(GetQuestionnaire $request)
    {
        $myQuestionnaire = new Questionnaire();

        $myQuestionnaire = $myQuestionnaire->my()
            ->join('applications as a', 'a.questionnaire_id', '=', 'questionnaires.id');


        $filter = false;
        if ($request->has('is_archive')) {
            $myQuestionnaire = $myQuestionnaire->whereNotNull('questionnaires.deleted_at');
        }


        if ($request->has('sex')) {
            $filter = true;
            $myQuestionnaire = $myQuestionnaire->where('sex', $request->sex);
        }

        if ($request->has('to_age') || $request->has('from_age')) {
            $filter = true;
            $myQuestionnaire = $myQuestionnaire->whereBetween('age', [(int)$request->from_age == null ? -100 : (int)$request->from_age, (int)$request->to_age == null ? 1000 : (int) $request->to_age]);
        }

        if ($request->has('country')) {
            $filter = true;
            $myQuestionnaire = $myQuestionnaire->where('city', 'ILIKE', '%' . $request->country . '%');
        }

        if ($request->has('city')) {
            $filter = true;
            $myQuestionnaire = $myQuestionnaire->where('city', 'ILIKE', '%' . $request->city . '%');
        }

        if ($request->has('service_type')) {
            $filter = true;

            $myQuestionnaire = $myQuestionnaire->where('service_type', 'ILIKE', '%' . $request->get('service_type') . '%');
        }

        if ($request->has('responsibility')) {
            $filter = true;
            $myQuestionnaire = $myQuestionnaire->where('responsibility', 'ILIKE', '%' . $request->responsibility . '%');
        }

        if ($request->has('search')) {
            $filter = true;
            $search = $request->search;
            $myQuestionnaire = $myQuestionnaire->where(function (Builder $query) use ($search) {
                $q = $query->where('name', 'ILIKE', '%' . $search . '%');

                $zodiac = $this->zodiacSigns();
                $ethnicity = [
                    'no_matter' => 'Не важно',
                    'caucasoid' => 'Европеоид',
                    'asian' => 'Азиат',
                    'dark_skinned' => 'Темнокожий',
                    'hispanic' => 'Латиноамериканец',
                    'indian' => 'Индиец',
                    'native_middle_east' => 'Выходец из стран Ближнего Востока',
                    'mestizo' => 'Метис, родители принадлежат к разным расам',
                    'native_american' => 'Представитель коренного населения Америки',
                    'islands' => 'Представитель коренного населения островов | Тихого Океана / Австралии / Абориген',
                    'other' => 'Другие'
                ];

                $smoking = [
                    'dont_smoking' => 'Не курю',
                    'rarely' => 'Редко',
                    'smoking' => 'Курю',
                    'no_matter' => 'Не важно'
                ];

                $findZodiac = collect($zodiac)->filter(function ($item) use ($search) {
                    return false !== stristr($item, $search);
                });

                $findEthnicity = collect($ethnicity)->filter(function ($item) use ($search) {
                    return false !== stristr($item, $search);
                });

                $findSmoking = collect($ethnicity)->filter(function ($item) use ($search) {
                    return false !== stristr($item, $search);
                });

                if( $findZodiac->isNotEmpty() ) {
                    $findZodiac = array_flip($findZodiac->toArray());
                    foreach ($findZodiac as $item) {
                        $q->orWhere('zodiac_signs', 'ILIKE', '%' . $item . '%');
                    }
                }

                if( $findEthnicity->isNotEmpty() ) {
                    $findEthnicity = array_flip($findEthnicity->toArray());
                    foreach ($findEthnicity as $item) {
                        $q->orWhere('ethnicity', 'ILIKE', '%' . $item . '%');
                    }
                }

                if( $findSmoking->isNotEmpty() ) {
                    $findSmoking = array_flip($findSmoking->toArray());
                    foreach ($findSmoking as $item) {
                        $q->orWhere('smoking', 'ILIKE', '%' . $item . '%');
                    }
                }
            });
        }

        if($request->has('sort')) {
            $myQuestionnaire = $myQuestionnaire->orderBy('questionnaires.id',  $request->sort == 1 ? 'DESC' : 'ASC');
        }


        if (!$filter) {
            $total = Questionnaire::whereNotNull('my_personal_qualities_id')->count();
        } else {
            $total = $myQuestionnaire->count();
        }
        $result = [];
        if ($request->has('page')) {
            $offset = (int)$request->page - 1;
            $offset = ($offset == 0) ? 0 : $offset + ((int)$request->limit - 1);
            $myQuestionnaire = $myQuestionnaire->offset($offset);
            $myQuestionnaire = $myQuestionnaire->limit((int)$request->limit);
            $result['pagination'] = [
                'total' => $total,
                'offset' => $offset,
                'limit' => (int)$request->limit,
                'page_available' => ceil($total / (int)$request->limit)
            ];
        }


        $questionnaires = $myQuestionnaire->get([
            'questionnaires.id', 'name', 'ethnicity', 'service_type', 'age', 'city', 'responsibility', 'questionnaires.created_at',
            'questionnaires.deleted_at'
        ]);


        foreach ($questionnaires as $key => $item) {
            $photo = QuestionnaireUploadPhoto::where('questionnaire_id', $item->id)->first(['path']);
            $questionnaires[$key]['photo'] = $photo == null ? null : $photo->path;

            $timestamp = Carbon::createFromTimeString($item['created_at'])->timestamp;
            $now = Carbon::now();
            $then = Carbon::createFromTimeString($item['created_at']);
            $diff = $now->diff($then);

            $titles_hours = ['%d час назад', '%d часа назад', '%d часов назад'];
            $titles_min = ['%d минуту назад', '%d минуты назад', '%d минут назад'];


            if ($diff->days == 0) {
                if ($diff->h == 0) {
                    $time = $this->declOfNum($diff->i, $titles_min);
                } else {
                    $time = $this->declOfNum($diff->h, $titles_hours);
                }
            } else if ($diff->days == 1) {
                $time = 'вчера';
            } else if ($diff->days == 2) {
                $time = 'позавчера';
            } else {
                $time = Carbon::createFromTimeString($item['created_at'])->format('d.m.Y');
            }


            $questionnaires[$key]['time'] = $time;
            $questionnaires[$key]['timestamp'] = $timestamp;
            $questionnaires[$key]['ethnicity'] = $this->ethnicity($questionnaires[$key]['ethnicity']);
        }

        $result['questionnaires'] = $questionnaires->toArray();

        $this->response()->success()->setMessage('Данные получены')->setData($result)->send();
    }

    public function getHistory(Request $request)
    {
        if (!$request->has('questionnaire_id'))
            $this->response()->setMessage('ID анкеты не указан')->error()->send();

        $history = QuestionnaireHistory::where('questionnaire_id', $request->questionnaire_id)
            ->join('users', 'users.id', '=', 'questionnaire_histories.user')
            ->get([
                'questionnaire_histories.id', 'from', 'comment', 'questionnaire_histories.created_at',
                'name'
            ]);
        Date::setlocale(config('app.locale'));
        Carbon::setLocale(config('app.locale'));

        $history = $history->toArray();

        foreach ($history as $key => $item) {
            $history[$key]['created_at'] = Carbon::createFromTimeString($item['created_at'])->format('j F Y');
        }

        $this->response()->success()->setMessage('Успешно найдено')->setData($history)->send();
    }

    public function addHistory(Request $request)
    {
        if (!$request->has('questionnaire_id'))
            $this->response()->setMessage('ID анкеты не указан')->error()->send();

        if (!$request->has('comment'))
            $this->response()->setMessage('Комментарий не указан')->error()->send();

        $history = QuestionnaireHistory::create([
            'questionnaire_id' => $request->questionnaire_id,
            'comment' => $request->comment,
            'from' => 'message',
            'user' => \Auth::user()->id
        ]);


        $this->response()->success()->setMessage('История обновлена')->setData($history)->send();
    }

    public function removeHistory(Request $request)
    {
        if (!$request->has('questionnaire_id'))
            $this->response()->setMessage('ID анкеты не указан')->error()->send();

        if (!$request->has('history_id'))
            $this->response()->setMessage('ID-истории не указан')->error()->send();


        $history = QuestionnaireHistory::where('id', $request->history_id)->delete();

        $this->response()->success()->setMessage('История удалена')->setData($history)->send();
    }

    public function getMatch(Request $request)
    {
        $result = [];

        if (!$request->has('questionnaire_id'))
            $this->response()->setMessage('ID анкеты не указан')->error()->send();

        $qm = QuestionnaireMatch::where('questionnaire_id', $request->questionnaire_id);
        $total = $qm->count();

        if ($request->has('page')) {
            $limit = (int)$request->limit == 0 ? 1 : (int)$request->limit;
            $offset = (int)$request->page - 1;
            $offset = ($offset == 0) ? 0 : $offset + $limit;
            $qm = $qm->offset($offset);
            $qm = $qm->limit((int)$request->limit);
            $total = $qm->count();
            $pagination = [
                'total' => $total,
                'offset' => $offset + 1,
                'limit' => (int)$request->limit,
                'page_available' => ceil($total / $limit)
            ];
        }

        $qm = $qm->orderBy('total', 'DESC')->get();


        $with_questionnaire = null;
        foreach ($qm as $item) {
            $with_questionnaire = Questionnaire::where('id', $item->with_questionnaire_id)->first();
            $photos = QuestionnaireUploadPhoto::where('questionnaire_id', $item->with_questionnaire_id)->first();
            $myInformation = QuestionnaireMyInformation::where('id', $with_questionnaire->my_information_id)->first();
            $q = QuestionnaireMailing::where('questionnaire_id', $item->questionnaire_id)->where('added_questionnaire_id', $with_questionnaire->id)->exists();

            $result[] = [
                'questionnaire_id' => (int)$request->questionnaire_id,
                'with_questionnaire_id' => $with_questionnaire->id,
                'name' => $myInformation->name,
                'city' => $myInformation->city,
                'photo' => (isset($photos['path'])) ? $photos['path'] : null,
                'match' => [
                    'total' => round((float)$item->total),
                    'appearance' => round((float)$item->appearance),
                    'personal_qualities' => round((float)$item->personal_qualities),
                    'form' => round((float)$item->information),
                    'about_me' => round((float)$item->about_me),
                    'test' => round((float)$item->test),
                ],
                'in_mailing' => $q
            ];
        }

        $this->response()->success()->setMessage('Подходящие анкеты')->setData($result)->setAdditional($pagination ?? [])->send();
    }

    public function addQuestionnaireMalling(Request $request)
    {
        if (!$request->has('questionnaire_id'))
            $this->response()->setMessage('ID анкеты не указан')->error()->send();

        if (!$request->has('add_questionnaire_id'))
            $this->response()->setMessage('ID анкеты не указан')->error()->send();

        QuestionnaireMailing::create([
            'questionnaire_id' => $request->questionnaire_id,
            'added_questionnaire_id' => $request->add_questionnaire_id
        ]);

        $this->response()->success()->setMessage('Анкета добавлена в рассылку')->send();
    }

    public function removeQuestionnaireMalling(Request $request)
    {
        if (!$request->has('questionnaire_id'))
            $this->response()->setMessage('ID анкеты не указан')->error()->send();

        if (!$request->has('added_questionnaire_id'))
            $this->response()->setMessage('ID анкеты не указан')->error()->send();

        QuestionnaireMailing::where('questionnaire_id', $request->questionnaire_id)->where('added_questionnaire_id', $request->added_questionnaire_id)->delete();

        $this->response()->success()->setMessage('Анкета была удалена из рассылки')->send();
    }

    public function setStatus(Request $request)
    {
        if (!$request->has('questionnaire_id'))
            $this->response()->setMessage('ID анкеты не указан')->error()->send();


        if (!$request->has('status'))
            $this->response()->setMessage('Статус не указан')->error()->send();

        if (!in_array($request->status, ['vip', 'pay', 'free', 'paid']))
            $this->response()->setMessage('Такого статуса не существует. Доступные: vip, pay, free, paid')->error()->send();

        $questionnaire = Questionnaire::where('id', $request->questionnaire_id)->first();
        if (empty($questionnaire))
            $this->response()->error()->setMessage('Анкета не найдена')->send();

        Questionnaire::where('id', $request->questionnaire_id)->update([
            'status_pay' => $request->status
        ]);

        Applications::where('questionnaire_id', $request->questionnaire_id)->update([
            'service_type' => $request->status
        ]);

        $this->response()->success()->setMessage('Статус изменен')->send();
    }

    public function createPresentation(Request $request)
    {
        if (!$request->has('questionnaire_id'))
            $this->response()->setMessage('ID анкеты не указан')->error()->send();
//
//        sleep(10);
//        $this->response()->setMessage('Презентация была создана')->setData([
//            'download_link' => 'https://google.com/',
//        ])->send();

        $create = (new PptxCreator())->create(new Questionnaire(), $request->questionnaire_id);

        $this->response()->success()->setMessage('Презентация была создана')->setData([
           'download_link' => env('APP_URL').'/storage/questionnaire'.explode('/questionnaire', $create)[1]
        ])->send();
    }

    public function sign(Request $request)
    {
        if( !$request->has('sign') )
            $this->response()->error()->setMessage('Вы не указали SIGN хеш.')->send();

        $exist = SignQuestionnaire::where('sign', $request->sign)->exists();

        $this->response()->success()->setMessage('Валидация')->setData(['exist' => $exist])->send();
    }
}
