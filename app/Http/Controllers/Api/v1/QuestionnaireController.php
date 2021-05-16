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
            if ($key == 'age' ) {
                $partnerInformation[$key] = implode(',', $information);
            }

            if( $key == 'height' || $key == 'weight' ) {
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
            if( $key == 'birthday' ) {
                $birthday = Carbon::createFromTimeString($information . ' 0:0');
                $now = Carbon::now();

                $myInformation['age'] = $birthday->diffInYears($now);
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
                $myInformation[$key] = $information;
            }

            if( $key == 'height' || $key == 'weight' ) {
                $myInformation[$key] = (int) $myInformation[$key];
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
            if ($key == 'age' ) {
                $partnerInformation[$key] = implode(',', $information);
            }

            if( $key == 'height' || $key == 'weight' ) {
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
            if( $key == 'birthday' ) {
                $birthday = Carbon::createFromTimeString($information . ' 0:0');
                $now = Carbon::now();

                $myInformation['age'] = $birthday->diffInYears($now);
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
                $myInformation[$key] = $information;
            }

            if( $key == 'height' || $key == 'weight' ) {
                $myInformation[$key] = (int) $myInformation[$key];
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
            'service_type' => 'pay',
            'status' => 0,
            'questionnaire_id' => null,
            'responsibility' => null,
            'link' => null,
            'link_active' => true,
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

        $application = Applications::where('questionnaire_id', $request->id)->first();

        $result = [
            'partner_appearance' => collect(QuestionnairePartnerAppearance::where('id', $questionnaire->partner_appearance_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'personal_qualities_partner' => collect(QuestionnairePersonalQualitiesPartner::where('id', $questionnaire->personal_qualities_partner_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'partner_information' => collect(QuestionnairePartnerInformation::where('id', $questionnaire->partner_information_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'test' => collect(QuestionnaireTest::where('id', $questionnaire->test_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'my_appearance' => collect(QuestionnaireMyAppearance::where('id', $questionnaire->my_appearance_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'my_personal_qualities' => collect(QuestionnaireMyPersonalQualities::where('id', $questionnaire->my_personal_qualities_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'my_information' => collect(QuestionnaireMyInformation::where('id', $questionnaire->my_information_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'application' => $application,
            'appointed_data' => QuestionnaireAppointedDate::where('questionnaire_id', $request->id)->first(),
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

        $withQuestionnaire = Questionnaire::where('id', $request->with_questionnaire_id)->first();
        if (empty($withQuestionnaire))
            $this->response()->error()->setMessage('Анкета не найдена')->send();

        $dateValidation = explode('.', $request->date);
        $timeValidation = explode(':', $request->time);

        if (count($dateValidation) != 3 || strlen($dateValidation[0]) != 2 || strlen($dateValidation[1]) != 2 || strlen($dateValidation[2]) != 4)
            $this->response()->error()->setMessage('Неверный формат даты. Необходимо: dd.mm.YYYY')->send();

        if (count($timeValidation) != 2 || strlen($timeValidation[0]) != 2 || strlen($timeValidation[1]) != 2)
            $this->response()->error()->setMessage('Неверный формат времени. Необходимо: HH:MM')->send();

        QuestionnaireAppointedDate::create($request->all());

        $this->response()->success()->setMessage("Дата свидания была назначена на {$request->date} в {$request->time}. Удачи!")->send();
    }

    public function getMakeDate(Request $request)
    {
        $questionnaire = Questionnaire::where('id', $request->questionnaire_id)->first();
        if (empty($questionnaire))
            $this->response()->error()->setMessage('Анкета не найдена')->send();

        $matching = QuestionnaireMatch::where('questionnaire_id', $request->questionnaire_id)
            ->join('questionnaires as q', 'q.id', '=', 'questionnaire_matches.questionnaire_id')
            ->join('questionnaire_my_information as information', 'information.id', '=', 'questionnaire_matches.questionnaire_id')
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

        $myAppearance = $questionnaire->partner()->where('questionnaires.id', $request->questionnaire_id)->first(
            collect(array_keys(config('app.questionnaire.value.partner_appearance')))->except(['sex'])->toArray()
        )->toArray();

        $partnerAppearance = $questionnaire->partner()->where('questionnaires.id', $withQuestionnaire->id)->first(
            collect(array_keys(config('app.questionnaire.value.partner_appearance')))->except(['sex'])->toArray()
        )->toArray();

        $requirements = [];

        foreach ($myAppearance as $key=>$item) {
            if( $key == 'sex' ) continue;

            if( $item == $partnerAppearance[$key] || $item == null || $partnerAppearance[$key] == null )
                $requirements[$key] = true;
            else
                $requirements[$key] = false;
        }

        $result = [
            'matching_as' => $matching?->total,
            'partner_questionnaire_id' => $withQuestionnaire->id,
            'matching' => $matching->toArray(),
            'requirements' => $requirements,
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

    public function get(GetQuestionnaire $request)
    {
        $myQuestionnaire = new Questionnaire();

        $myQuestionnaire = $myQuestionnaire->my()
            ->join('applications as a', 'a.questionnaire_id', '=', 'questionnaires.id');

        $filter = false;
        if( $request->has('is_archive') ) {
            $myQuestionnaire = $myQuestionnaire->whereNotNull('questionnaires.deleted_at');
        }


        if ($request->has('sex')) {
            $filter = true;
            $myQuestionnaire = $myQuestionnaire->where('sex', $request->sex);
        }

        if ($request->has('to_age') && $request->has('from_age')) {
            $filter = true;
            $myQuestionnaire = $myQuestionnaire->whereBetween('age', [(int)$request->from_age, (int)$request->to_age]);
        }

        if ($request->has('country')) {
            $filter = true;
            $myQuestionnaire = $myQuestionnaire->where('city', 'LIKE', '%' . $request->country . '%');
        }

        if ($request->has('city')) {
            $filter = true;
            $myQuestionnaire = $myQuestionnaire->where('city', 'LIKE', '%' . $request->city . '%');
        }

        if ($request->has('service_type')) {
            $filter = true;

            $myQuestionnaire = $myQuestionnaire->where('service_type', 'LIKE', '%' . $request->get('service_type') . '%');
        }

        if ($request->has('responsibility')) {
            $filter = true;
            $myQuestionnaire = $myQuestionnaire->where('responsibility', 'LIKE', '%' . $request->responsibility . '%');
        }

        if ($request->has('search')) {
            $filter = true;
            $search = $request->search;
            $myQuestionnaire = $myQuestionnaire->where(function (Builder $query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%');
            });
        }

        if (!$filter) {
            $total = Questionnaire::whereNotNull('my_personal_qualities_id')->count();
        } else {
            $total = $myQuestionnaire->count();
        }
        $result = [];
        if ($request->has('page')) {
            $offset = (int)$request->page - 1;
            $offset = ($offset == 0) ? 0 : $offset + (int)$request->limit;
            $myQuestionnaire = $myQuestionnaire->offset($offset);
            $myQuestionnaire = $myQuestionnaire->limit((int)$request->limit);
            $result['pagination'] = [
                'total' => $total,
                'offset' => $offset + 1,
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


            };

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

        foreach ($history as $key=>$item) {
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
            $offset = (int)$request->page - 1;
            $offset = ($offset == 0) ? 0 : $offset + (int)$request->limit;
            $qm = $qm->offset($offset);
            $qm = $qm->limit((int)$request->limit);
            $total = $qm->count();
            $pagination = [
                'total' => $total,
                'offset' => $offset + 1,
                'limit' => (int)$request->limit,
                'page_available' => ceil($total / (int)$request->limit)
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
                    'total' => (float)$item->total,
                    'appearance' => (float)$item->appearance,
                    'personal_qualities' => (float)$item->personal_qualities,
                    'form' => (float)$item->information,
                    'about_me' => (float)$item->about_me,
                    'test' => (float)$item->test,
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

        if( !in_array($request->status, ['vip', 'pay', 'free', 'paid']) )
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
}
