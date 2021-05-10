<?php

namespace App\Http\Controllers\Api\v1;

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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
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
            if( $item == null ) {
                unset($personalQualitiesPartner[$key]);
            }
        }

        $personalQualitiesPartner = array_flip(array_values($personalQualitiesPartner));

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
            if( $item == null ) {
                unset($personalQualitiesPartner[$key]);
            }
        }

        $personalQualitiesPartner = array_flip(array_values($personalQualitiesPartner));

        foreach ($personalQualitiesPartner as $key => $item) {
            $personalQualitiesPartner[$key] = true;
        }

        foreach ($partnerInformation as $key => $information) {
            if ($key == 'age' || $key == 'height' || $key == 'weight') {
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

            if ($key == 'live_country') {
                $partnerInformation[$key] = $information;
            }

            if ($key == 'live_city') {
                $partnerInformation['city'] = $myInformation['live_country'] . ', ' . $information;
            }
        }

        foreach ($myInformation as $key => $information) {
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
        Applications::where('id', $application->id)->update(['link' => $link]);

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

        $application = Applications::where('link', env('APP_QUESTIONNAIRE_URL') .'/sign'.$questionnaire->sign)->first();

        $result = [
            'partner_appearance' => collect(QuestionnairePartnerAppearance::where('id', $questionnaire->partner_appearance_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'personal_qualities_partner' => collect(QuestionnairePersonalQualitiesPartner::where('id', $questionnaire->personal_qualities_partner_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'partner_information' => collect(QuestionnairePartnerInformation::where('id', $questionnaire->partner_information_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'test' => collect(QuestionnaireTest::where('id', $questionnaire->test_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'my_appearance' => collect(QuestionnaireMyAppearance::where('id', $questionnaire->my_appearance_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'my_personal_qualities' => collect(QuestionnaireMyPersonalQualities::where('id', $questionnaire->my_personal_qualities_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'my_information' => collect(QuestionnaireMyInformation::where('id', $questionnaire->my_information_id)->first())->except(['id', 'created_at', 'updated_at'])->toArray(),
            'application' => $application
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

        QuestionnaireUploadPhoto::create([
            'path' => $path,
            'questionnaire_id' => $request->questionnaire_id
        ]);

        $this->response()->setMessage('Файл загружен')->setData([
            'path' => env('APP_URL') . '/' . $path
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

        $this->response()->setMessage('Фотография была удалена')->send();
    }

    public function uploadFile(FilesQuestionnaire $request)
    {
        $questionnaire = Questionnaire::where('id', $request->questionnaire_id)->first();
        if (empty($questionnaire))
            $this->response()->error()->setMessage('Анкета не найдена')->send();

        if (!in_array($request->type, ['passport', 'agree', 'offer']))
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
            'offer' => 'contract-copy-' . $request->questionnaire_id . '.pdf'
        };

        QuestionnaireFiles::create([
            'path' => $path,
            'type' => $request->type,
            'questionnaire_id' => $request->questionnaire_id,
            'name' => $name,
            'size' => round($file->getSize() / 1024 / 1024, 2) . ' mb',
            'key' => $key
        ]);

        $this->response()->setMessage('Файл загружен')->setData([
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

    private function declOfNum($number, $titles)
    {
        $cases = array(2, 0, 1, 1, 1, 2);
        $format = $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
        return sprintf($format, $number);
    }

    public function get(GetQuestionnaire $request)
    {
        if( $request->has('to_age') && $request->has('from_age') ) {
            $qmi = QuestionnaireMyInformation::whereBetween('age', [(int)$request->from_age, (int)$request->to_age]);
        }

        if( $request->has('sex') && $request->sex != 'all' ) {
            $qma = QuestionnaireMyAppearance::where('sex', $request->sex);
        }

        if( $request->has('responsibility') ) {
            $qa = Applications::where('responsibility', $request->responsibility);
        }

        if( $request->has('status') ) {
//            $qa = $qa->where
        }


        $questionnaire = Questionnaire::whereNotNull('personal_qualities_partner_id')->get();

        $result = [];

        foreach ($questionnaire as $item) {
            $appearance = QuestionnaireMyAppearance::where('id', $item['my_appearance_id'])->first();
            $myInformation = QuestionnaireMyInformation::where('id', $item['my_information_id'])->first();

            $applications = collect(Applications::where('link', env('APP_QUESTIONNAIRE_URL') . '/sign/'.$item->sign)->first());

            $time = Carbon::createFromTimeString($item['created_at']);

            $now = Carbon::now();
            $then = Carbon::createFromTimeString($item['created_at']);
            $diff = $now->diff($then);

            $titles_hours = ['%d час назад', '%d часа назад', '%d часов назад'];
            $titles_min = ['%d минуту назад', '%d минуты назад', '%d минут назад'];


            if ($diff->days == 0) {
                if( $diff->h == 0 ) {
                    $time = $this->declOfNum($diff->i, $titles_min);
                } else {
                    $time = $this->declOfNum($diff->h, $titles_hours);
                }
            } else if ($diff->days == 1) {
                $time = 'вчера';
            } else if ($diff->days == 2) {
                $time = 'позавчера';
            }

            $city = explode(',', $myInformation['city']);
            $country = trim($city[0]);
            $city = trim($city[1]);

            $age = $myInformation['age'];

            $result[] = [
                'application' => $applications->except(['responsibility'])->toArray(),
                'city' => $city,
                'country' => $country,
                'age' => $this->years($age),
                'nationality' => $this->ethnicity($appearance['ethnicity']),
                'responsibility' => User::where('id', explode(',', $applications['responsibility'])[0])->first(),
                'status' => $item['status_pay'] == 'free' ? 'На оплате' : $applications['service_type'],
                'time' => $time,
                'timestamp' => Carbon::createFromTimeString($item['created_at'])->timestamp
            ];
        }

        $this->response()->setMessage('Данные получены')->setData($result)->send();
    }

    public function getHistory(Request $request)
    {
        if( !$request->has('questionnaire_id') )
            $this->response()->setMessage('ID анкеты не указан')->error()->send();

        $history = QuestionnaireHistory::where('questionnaire_id', $request->questionnaire_id)->get();


        $this->response()->success()->setMessage('Успешно найдено')->setData($history)->send();
    }

    public function addHistory(Request $request)
    {
        if( !$request->has('questionnaire_id') )
            $this->response()->setMessage('ID анкеты не указан')->error()->send();

        if( !$request->has('comment') )
            $this->response()->setMessage('ID анкеты не указан')->error()->send();

        $history = QuestionnaireHistory::create([
            'questionnaire_id' => $request->questionnaire_id,
            'comment' => $request->comment
        ]);


        $this->response()->success()->setMessage('История обновлена')->setData($history)->send();
    }
}
