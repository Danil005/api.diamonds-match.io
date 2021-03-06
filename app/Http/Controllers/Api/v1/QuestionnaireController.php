<?php

namespace App\Http\Controllers\Api\v1;

use App\Events\NotifyPushed;
use App\Http\Requests\Employee\Update;
use App\Http\Requests\Questionnaire\Create;
use App\Http\Requests\Questionnaire\DeleteFilesQuestionnaire;
use App\Http\Requests\Questionnaire\DeletePhotoQuestionnaire;
use App\Http\Requests\Questionnaire\FilesQuestionnaire;
use App\Http\Requests\Questionnaire\ForceDeleteQuestionnaire;
use App\Http\Requests\Questionnaire\GetQuestionnaire;
use App\Http\Requests\Questionnaire\MakeDateQuestionnaire;
use App\Http\Requests\Questionnaire\OpenFilesQuestionnaire;
use App\Http\Requests\Questionnaire\UploadPhotoQuestionnaire;
use App\Http\Requests\Questionnaire\View;
use App\Models\Applications;
use App\Models\Cities;
use App\Models\Countries;
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
use App\Utils\Match\TestMatch;
use App\Utils\QuestionnaireUtils;
use App\Utils\TranslateFields;
use Carbon\Carbon;
use Hash;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Jenssegers\Date\Date;
use SoareCostin\FileVault\Facades\FileVault;
use Str;

class QuestionnaireController extends QuestionnaireUtils
{
    use TranslateFields, TestMatch;

    public function create(Create $request)
    {
        # ?????????????????? ?????? ????????????
        $data = [];
        if (!$request->has('sign') || $request->sign == null)
            $this->response()->error()->setMessage('SIGN ???????????? ???????? ????????????')->send();


        # ???????????? ???????????????? ???? ?????? ????????
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
                if (isset($information)) {
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
                } else {
                    $partnerInformation[$key] = null;
                }
            }

            $liveCountry = '';
            if ($key == 'live_place') {
                if (isset($information)) {
                    foreach ($information as $country) {
                        $liveCountry .= $country . ',';
                    }
                    $partnerInformation['city'] = trim($liveCountry, ',');
                } else {
                    $partnerInformation['city'] = '';
                }
            }

            if ($key == 'place_birth') {
                $place_birth = '';
                if (isset($partnerInformation[$key][0])) {
                    foreach ($partnerInformation[$key] as $item) {
                        $place_birth .= $item . ',';
                    }

                    $partnerInformation[$key] = trim($place_birth, ',');
                } else {
                    $this->response()->error()->setMessage('???????? `place_birth` ???????????? ???????? ??????????????????')->send();
                }
            }
        }

        foreach ($myInformation as $key => $information) {
            if ($key == 'birthday') {
                $birthday = Carbon::createFromTimeString($information . ' 0:0');
                $now = Carbon::now();

                $myInformation['age'] = $birthday->diffInYears($now);
            }

            if ($key == 'countries_was' || $key == 'countries_dream') {
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
                if (is_array($myInformation['live_country'])) {
                    $myInformation['city'] = $myInformation['live_country']['label'] . ', ' . $information;
                } else {
                    $myInformation['city'] = $myInformation['live_country'] . ', ' . $information;
                }
            }

            if ($key == 'place_birth') {
                if (is_string($information)) {
                    $myInformation[$key] = $information;
                } else {
                    $myInformation[$key] = implode(',', $information);
                }
            }

            if ($key == 'height' || $key == 'weight') {
                $myInformation[$key] = (int)$myInformation[$key];
            }

            if ($key == 'countries_was') {
                $myInformation[$key] = implode(',', $information);
            }

            if ($key == 'countries_dream') {
                $myInformation[$key] = implode(',', $information);
            }
        }


        # ?????????????? ?????? ?? ???????? ????????????
        $partnerAppearance = QuestionnairePartnerAppearance::create($partnerAppearance);
        $personalQualitiesPartner = QuestionnairePersonalQualitiesPartner::create($personalQualitiesPartner);
        $partnerInformation = QuestionnairePartnerInformation::create($partnerInformation);
        $test = QuestionnaireTest::create($test);
        $myAppearance = QuestionnaireMyAppearance::create($myAppearance);
        $myPersonalQualities = QuestionnaireMyPersonalQualities::create($myPersonalQualities);
        $myInformation = QuestionnaireMyInformation::create($myInformation);

        Storage::disk('public')->append('logs/questionnaire.txt', json_encode([
            '$partnerAppearance' => $partnerAppearance,
            '$personalQualitiesPartner' => $personalQualitiesPartner,
            '$partnerInformation' => $partnerInformation,
            '$test' => $test,
            '$myAppearance' => $myAppearance,
            '$myPersonalQualities' => $myPersonalQualities,
            '$myInformation' => $myInformation,
            'time' => Carbon::now()->format('H:i:s'),
            'date' => Carbon::now()->format('d.m.Y')
        ]));

        # ???????????????????? ???????????? ?? ?????????? ????????
        Questionnaire::where('sign', $request->sign)->update([
            'partner_appearance_id' => $partnerAppearance->id,
            'personal_qualities_partner_id' => $personalQualitiesPartner->id,
            'partner_information_id' => $partnerInformation->id,
            'test_id' => $test->id,
            'my_appearance_id' => $myAppearance->id,
            'my_personal_qualities_id' => $myPersonalQualities->id,
            'my_information_id' => $myInformation->id,
            'lang' => $request->lang ?? 'ru'
        ]);

        $q = Questionnaire::where('sign', $request->sign)->first();

        $resp = Applications::where('questionnaire_id', $q->id)->first(['responsibility']);

        if ($request->has('temp_photo_id')) {
            $files = Storage::files('public/questionnaire/temp/photo_' . $request->temp_photo_id);
            foreach ($files as $item) {
                Storage::move($item, str_replace(
                    'public/questionnaire/temp/photo_' . $request->temp_photo_id,
                    'public/questionnaire/photos/sign_' . $request->sign,
                    $item));

                QuestionnaireUploadPhoto::create([
                    'questionnaire_id' => $q->id,
                    'path' => str_replace(
                        'public/questionnaire/temp/photo_' . $request->temp_photo_id,
                        'storage/questionnaire/photos/sign_' . $request->sign,
                        $item
                    )
                ]);
            }
            Storage::deleteDirectory('public/questionnaire/temp/photo_' . $request->temp_photo_id);
        }

        $this->createNotify('questionnaire', '?????? ???????????? ' . $myInformation->name . ' ???????????????? ????????????.', [
            'questionnaire_id' => $q->id,
            'employee' => $resp != null ? explode(',', $resp['responsibility'])[0] : null
        ]);

        if( $myAppearance->sex == 'male' ) {
            Mail::to($request->email)->send(new \App\Mail\SendPrice(
                name: $myInformation->name,
                lang: $request->lang ?? 'ru',
                country: explode(',', $myInformation['city'])[0],
                app_id: Applications::where('questionnaire_id', $q->id)->first()->id
            ));
        }

        $this->response()->success()->setMessage('???? ?????????????? ?????????????? ?? ???????????? ???????????????? ???????????? ?????? ??????.')->send();
    }

    public function createFromSite(Create $request)
    {
        # ?????????????????? ?????? ????????????
        $data = [];

        # ???????????? ???????????????? ???? ?????? ????????
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
                if (isset($information)) {
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
                } else {
                    $partnerInformation[$key] = null;
                }
            }

            $liveCountry = '';
            if ($key == 'live_place') {
                if (isset($information)) {
                    foreach ($information as $country) {
                        $liveCountry .= $country . ',';
                    }
                    $partnerInformation['city'] = trim($liveCountry, ',');
                } else {
                    $partnerInformation['city'] = '';
                }
            }

            if ($key == 'place_birth') {
                $place_birth = '';
                if (isset($partnerInformation[$key][0])) {
                    foreach ($partnerInformation[$key] as $item) {
                        $place_birth .= $item . ',';
                    }

                    $partnerInformation[$key] = trim($place_birth, ',');
                } else {
                    $this->response()->error()->setMessage('???????? `place_birth` ???????????? ???????? ??????????????????')->send();
                }
            }
        }

        foreach ($myInformation as $key => $information) {
            if ($key == 'birthday') {
                $birthday = Carbon::createFromTimeString($information . ' 0:0');
                $now = Carbon::now();

                $myInformation['age'] = $birthday->diffInYears($now);
            }

            if ($key == 'countries_was' || $key == 'countries_dream') {
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
                if (is_array($myInformation['live_country'])) {
                    $myInformation['city'] = $myInformation['live_country']['label'] . ', ' . $information;
                } else {
                    $myInformation['city'] = $myInformation['live_country'] . ', ' . $information;
                }
            }

            if ($key == 'place_birth') {
                if (is_string($information)) {
                    $myInformation[$key] = $information;
                } else {
                    $myInformation[$key] = implode(',', $information);
                }
            }

            if ($key == 'height' || $key == 'weight') {
                $myInformation[$key] = (int)$myInformation[$key];
            }

            if ($key == 'countries_was') {
                $myInformation[$key] = implode(',', $information);
            }

            if ($key == 'countries_dream') {
                $myInformation[$key] = implode(',', $information);
            }
        }

        if ($request->has('temp_id')) {

        }

        # ?????????????? ?????? ?? ???????? ????????????
        $partnerAppearance = QuestionnairePartnerAppearance::create($partnerAppearance);
        $personalQualitiesPartner = QuestionnairePersonalQualitiesPartner::create($personalQualitiesPartner);
        $partnerInformation = QuestionnairePartnerInformation::create($partnerInformation);
        $test = QuestionnaireTest::create($test);
        $myAppearance = QuestionnaireMyAppearance::create($myAppearance);
        $myPersonalQualities = QuestionnaireMyPersonalQualities::create($myPersonalQualities);
        $myInformation = QuestionnaireMyInformation::create($myInformation);

        Storage::disk('public')->append('logs/questionnaire.txt', json_encode([
            '$partnerAppearance' => $partnerAppearance,
            '$personalQualitiesPartner' => $personalQualitiesPartner,
            '$partnerInformation' => $partnerInformation,
            '$test' => $test,
            '$myAppearance' => $myAppearance,
            '$myPersonalQualities' => $myPersonalQualities,
            '$myInformation' => $myInformation,
            'time' => Carbon::now()->format('H:i:s'),
            'date' => Carbon::now()->format('d.m.Y')
        ]));

        $application = Applications::create([
            'client_name' => $myInformation->name,
            'service_type' => 'free',
            'status' => 0,
            'questionnaire_id' => null,
            'responsibility' => null,
            'link' => null,
            'link_active' => true,
            'from' => '????????, ????????????',
            'email' => $request->has('email') ? $request->email : null,
            'phone' => $request->has('phone') ? $request->phone : null
        ]);

        if ($request->has('email')) {
            if( $myAppearance->sex == 'male' ) {
                Mail::to($request->email)->send(new \App\Mail\SendPrice(
                    name: $myInformation->name,
                    lang: $request->lang ?? 'ru',
                    country: explode(',', $myInformation['city'])[0],
                    app_id: $application->id
                ));}
        }

# ???????????????????? ???????????? ?? ?????????? ????????
        $questionnaire = Questionnaire::create([
            'partner_appearance_id' => $partnerAppearance->id,
            'personal_qualities_partner_id' => $personalQualitiesPartner->id,
            'partner_information_id' => $partnerInformation->id,
            'test_id' => $test->id,
            'my_appearance_id' => $myAppearance->id,
            'my_personal_qualities_id' => $myPersonalQualities->id,
            'my_information_id' => $myInformation->id,
            'lang' => $request->lang ?? 'ru'
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

        if ($request->has('temp_photo_id')) {
            $files = Storage::files('public/questionnaire/temp/photo_' . $request->temp_photo_id);
            foreach ($files as $item) {
                Storage::move($item, str_replace(
                    'public/questionnaire/temp/photo_' . $request->temp_photo_id,
                    'public/questionnaire/photos/sign_' . $sign,
                    $item));

                QuestionnaireUploadPhoto::create([
                    'questionnaire_id' => $questionnaire->id,
                    'path' => str_replace(
                        'public/questionnaire/temp/photo_' . $request->temp_photo_id,
                        'storage/questionnaire/photos/sign_' . $sign,
                        $item
                    )
                ]);
            }
            Storage::deleteDirectory('public/questionnaire/temp/photo_' . $request->temp_photo_id);
        }

        $this->createNotify('application', '?????????????????? ?????????? ????????????.', [
            'application_id' => $application->id,
        ]);

        $this->createNotify('questionnaire', '?????????????????? ?????????? ????????????.', [
            'questionnaire_id' => $questionnaire->id,
        ]);


        event(new NotifyPushed('?????????????????? ?????????? ????????????', [
            'application_id' => $application->id,
        ]));

        event(new NotifyPushed('?????????????????? ?????????? ????????????', [
            'questionnaire_id' => $questionnaire->id,
        ]));

        $this->response()->success()->setMessage('???? ?????????????? ?????????????? ?? ???????????? ???????????????? ???????????? ?????? ??????.')->setData([
            'link_questionnaire' => $link
        ])->send();
    }

    public function uploadClientPhoto(Request $request)
    {
        if (!$request->has('temp_id'))
            $this->response()->error()->setMessage('?????????????????? ID ???????????? ????????????????????????')->send();

        if (!$request->has('photo'))
            $this->response()->error()->setMessage('???? ???????????? ?????????????? ???????????? ???????????????????? ?????? ???????? ??????????????')->send();

        $temp_id = $request->temp_id;
        $photo = $request->photo;


        if (!is_array($photo)) {
            $path = 'public/questionnaire/temp/photo_' . $temp_id;

            $upload = $photo->storePubliclyAs($path, md5(Str::random(16)) . '.' . $photo->getClientOriginalExtension());

            $path = str_replace('public/', 'storage/', $upload);

            $this->response()->success()->setMessage('???????? ????????????????')->setData([
                'path' => env('APP_URL') . '/' . $path,
            ])->send();
        } else {
            $paths = [];
            foreach ($photo as $item) {
                $path = 'public/questionnaire/temp/photo_' . $temp_id;

                $upload = $item->storePubliclyAs($path, md5(Str::random(16)) . '.' . $item->getClientOriginalExtension());

                $paths[] = str_replace('public/', 'storage/', $upload);
            }

            $this->response()->success()->setMessage('???????? ????????????????')->setData([
                'path' => $paths,
            ])->send();
        }
    }

    public function removeClientPhoto(Request $request)
    {
        if (!$request->has('path'))
            $this->response()->error()->setMessage('???? ???????????? ?????????????? path ????????????????????')->send();

        $path = str_replace(env('APP_URL') . '/', '', str_replace('storage/', '', $request->path));
        Storage::disk('public')->delete($path);

        $this->response()->success()->setMessage('???????????????????? ???????? ??????????????')->send();
    }

    /**
     * @param View $request
     */
    public
    function view(View $request)
    {

        $questionnaire = new Questionnaire();
        $questionnaire = $questionnaire::withTrashed()->where('id', $request->id)
            ->whereNotNUll('partner_appearance_id')->first();

        if (empty($questionnaire))
            $this->response()->error()->setMessage("???????????? ???? ????????????????????")->setData(["error" => 404])->send();

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

        $countMatch = QuestionnaireMatch::where('questionnaire_id', $request->id)->where('with_questionnaire_id', '!=', $request->id)->count();
        if ($countMatch >= 8)
            $countMatch = 8;

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
            'matched_count' => $countMatch,
            'deleted_at' => $questionnaire->deleted_at
        ];

        $zodiac = $this->zodiacSigns();

        $result['partner_information']['zodiac_signs'] = $zodiac[$result['partner_information']['zodiac_signs']];
        $result['my_information']['zodiac_signs'] = $zodiac[$result['my_information']['zodiac_signs']];

        $result['my_information']['age'] = $this->years($result['my_information']['age']);
        $city = explode(',', $result['my_information']['city']);
        $c = $city;
        $city = Countries::where('title_en', 'ILIKE', $city[0])->first();
        if ($city != null) {
            $result['my_information']['city'] = $city['title_ru'] . (isset($c[1]) ? ', ' . $c[1] : '');
        }

        if (isset($result['my_information']['place_birth'])) {
            $place = explode(',', $result['my_information']['place_birth']);
            $place2 = explode(',', $result['my_information']['place_birth']);
            if (isset($place[1])) {
                $place = $place[1];
            } else {
                $place = $place[0];
            }
            $place = Countries::where('title_en', 'ILIKE', $place)->first();
            if ($place != null)
                $result['my_information']['place_birth'] = $place['title_ru'];
            else
                $result['my_information']['place_birth'] = $place2[1] ?? $place2[0];
        }

        if (isset($result['my_information']['countries_was'])) {
            $country_was = explode(',', $result['my_information']['countries_was']);
            $place = new Countries();
            foreach ($country_was as $item) {
                $place = $place->orWhere('title_en', 'ILIKE', $item);
            }
            $place = $place->get(['title_ru'])->toArray();
            $res = '';
            if ($place != null)
                foreach ($place as $item) $res .= ', ' . $item['title_ru'];
            $result['my_information']['countries_was'] = trim($res, ', ');
        }

        if (isset($result['my_information']['countries_dream'])) {
            $country_was = explode(',', $result['my_information']['countries_dream']);
            $place = new Countries();
            foreach ($country_was as $item) {
                $place = $place->orWhere('title_en', 'ILIKE', $item);
            }
            $place = $place->get(['title_ru'])->toArray();
            $res = '';
            if ($place != null)
                foreach ($place as $item) $res .= ', ' . $item['title_ru'];
            $result['my_information']['countries_dream'] = trim($res, ', ');
        }
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

        // ??????????????

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
        $result['partner_appearance']['sex'] = $result['partner_appearance']['sex'] === 'female' ? '??????????????' : '??????????????';


        // ??????

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
        $result['my_appearance']['sex'] = $result['my_appearance']['sex'] === 'female' ? '??????????????' : '??????????????';


        foreach ($result['my_information'] as $key => $item) {
            if ($key == 'smoking') {
                $result['my_information']['smoking'] = $this->smoking($result['my_information']['smoking'], 'male');
            }

            if ($key == 'alcohol') {
                $result['my_information']['alcohol'] = $this->alcohol($result['my_information']['alcohol'], 'male');
            }

            if ($key == 'religion') {
                $result['my_information']['religion'] = $this->religion($result['my_information']['religion'], 'male');
            }

            if ($key == 'sport') {
                $result['my_information']['sport'] = $this->sport($result['my_information']['sport'], 'male');
            }

            if ($key == 'education') {
                $result['my_information']['education'] = $this->education($result['my_information']['education'], 'male');
            }

            if ($key == 'work') {
                $result['my_information']['work'] = $this->work($result['my_information']['work'], 'male');
            }

            if ($key == 'pets') {
                $result['my_information']['pets'] = $this->pets($result['my_information']['pets'], 'male');
            }

            if ($key == 'films_or_books') {
                $result['my_information']['films_or_books'] = $this->fm($result['my_information']['films_or_books'], 'male');
            }

            if ($key == 'relax') {
                $result['my_information']['relax'] = $this->relax($result['my_information']['relax'], 'male');
            }

            if ($key == 'sleep') {
                $result['my_information']['sleep'] = $this->sleep($result['my_information']['sleep'], 'male');
            }

            if ($key == 'clubs') {
                $result['my_information']['clubs'] = $this->clubs($result['my_information']['clubs'], 'male');
            }

            if ($key == 'salary') {
                $result['my_information']['salary'] = $this->salary($result['my_information']['salary']);
            }

            if ($key == 'marital_status') {
                $result['my_information']['marital_status'] = $this->maritalStatus($result['my_information']['marital_status'], $result['my_appearance']['sex']);
            }

            if ($key == 'children_desire') {
                $result['my_information']['children_desire'] = $this->childrenDesire($result['my_information']['children_desire'], $result['my_appearance']['sex']);
            }
        }

        foreach ($result['partner_information'] as $key => $item) {
            if ($key == 'smoking') {
                $result['partner_information']['smoking'] = $this->smoking($result['partner_information']['smoking'], 'male');
            }

            if ($key == 'alcohol') {
                $result['partner_information']['alcohol'] = $this->alcohol($result['partner_information']['alcohol'], 'male');
            }

            if ($key == 'religion') {
                $result['partner_information']['religion'] = $this->religion($result['partner_information']['religion'], 'male');
            }

            if ($key == 'sport') {
                $result['partner_information']['sport'] = $this->sport($result['partner_information']['sport'], 'male');
            }

            if ($key == 'education') {
                $result['partner_information']['education'] = $this->education($result['partner_information']['education'], 'male');
            }

            if ($key == 'work') {
                $result['partner_information']['work'] = $this->work($result['partner_information']['work'], 'male');
            }

            if ($key == 'pets') {
                $result['partner_information']['pets'] = $this->pets($result['partner_information']['pets'], 'male');
            }

            if ($key == 'films_or_books') {
                $result['partner_information']['films_or_books'] = $this->fm($result['partner_information']['films_or_books'], 'male');
            }

            if ($key == 'relax') {
                $result['partner_information']['relax'] = $this->relax($result['partner_information']['relax'], 'male');
            }

            if ($key == 'sleep') {
                $result['partner_information']['sleep'] = $this->sleep($result['partner_information']['sleep'], 'male');
            }

            if ($key == 'clubs') {
                $result['partner_information']['clubs'] = $this->clubs($result['partner_information']['clubs'], 'male');
            }

            if ($key == 'salary') {
                $result['partner_information']['salary'] = $this->salary($result['partner_information']['salary']);
            }

            if ($key == 'marital_status') {
                $result['partner_information']['marital_status'] = $this->maritalStatus($result['partner_information']['marital_status'], $result['my_appearance']['sex']);
            }

            if ($key == 'children_desire') {
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


        $this->response()->success()->setMessage('???????????? ????????????????')->setData($result)->send();
    }

    public
    function uploadPhoto(UploadPhotoQuestionnaire $request)
    {
        $questionnaire = Questionnaire::where('id', $request->questionnaire_id)->first();
        if (empty($questionnaire))
            $this->response()->error()->setMessage('???????????? ???? ??????????????')->send();

        $file = $request->file('file');
        $path = 'public/questionnaire/photos/sign_' . $questionnaire->sign;

        $upload = $file->storePubliclyAs($path, md5(Str::random(16)) . '.' . $file->getClientOriginalExtension());

        $path = str_replace('public/', 'storage/', $upload);

        $q = QuestionnaireUploadPhoto::create([
            'path' => $path,
            'questionnaire_id' => $request->questionnaire_id
        ]);

        $this->response()->success()->setMessage('???????? ????????????????')->setData([
            'path' => env('APP_URL') . '/' . $path,
            'id' => $q->id
        ])->send();
    }

    public
    function deletePhoto(DeletePhotoQuestionnaire $request)
    {
        $questionnaire = Questionnaire::where('id', $request->questionnaire_id)->first();
        if (empty($questionnaire))
            $this->response()->error()->setMessage('???????????? ???? ??????????????')->send();

        $photo = QuestionnaireUploadPhoto::where('id', $request->photo_id)->first();

        Storage::disk('public')->delete(str_replace('storage/', '', $photo['path']));

        QuestionnaireUploadPhoto::where('id', $request->photo_id)->delete();

        $this->response()->success()->setMessage('???????????????????? ???????? ??????????????')->send();
    }

    public
    function uploadFile(FilesQuestionnaire $request)
    {
        $questionnaire = Questionnaire::where('id', $request->questionnaire_id)->first();
        if (empty($questionnaire))
            $this->response()->error()->setMessage('???????????? ???? ??????????????')->send();

        if (!in_array($request->type, ['passport', 'agree', 'offer', 'founder']))
            $this->response()->error()->setMessage('???????????????? ?????? ???????????????? ??????????')->send();

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

        $this->response()->success()->setMessage('???????? ????????????????')->setData([
            'path' => env('APP_URL') . '/' . $path,
            'encrypted' => true
        ])->send();
    }

    public
    function openFile(OpenFilesQuestionnaire $request)
    {
        $questionnaire = Questionnaire::where('id', $request->questionnaire_id)->first();
        if (empty($questionnaire))
            $this->response()->error()->setMessage('???????????? ???? ??????????????')->send();

        $file = QuestionnaireFiles::where('id', $request->file_id)->first();

        if (empty($file))
            $this->response()->setMessage('???????????? ???????? ???? ?????? ????????????')->send();

        $path = str_replace('{hidden}', '{' . $file['key'] . '}', $file['path']) . '.enc';

        return response()->streamDownload(function () use ($path) {
            FileVault::streamDecrypt($path);
        }, $file['name']);
    }

    public
    function deleteFile(DeleteFilesQuestionnaire $request)
    {
        $questionnaire = Questionnaire::where('id', $request->questionnaire_id)->first();
        if (empty($questionnaire))
            $this->response()->error()->setMessage('???????????? ???? ??????????????')->send();

        $file = QuestionnaireFiles::where('id', $request->file_id)->first();

        Storage::disk('public')->delete(
            str_replace('public/', '', str_replace('{hidden}', '{' . $file['key'] . '}', $file['path'])) . '.enc'
        );

        QuestionnaireFiles::where('id', $request->file_id)->delete();

        $this->response()->success()->setMessage('???????? ?????? ??????????????')->send();
    }

    public
    function makeDate(MakeDateQuestionnaire $request)
    {
        $questionnaire = Questionnaire::where('id', $request->questionnaire_id)->first();
        if (empty($questionnaire))
            $this->response()->error()->setMessage('???????????? ???? ??????????????')->send();

        $withQuestionnaire = (new Questionnaire)->my()->where('questionnaires.id', $request->with_questionnaire_id)->first();
        if (empty($withQuestionnaire))
            $this->response()->error()->setMessage('???????????? ???? ??????????????')->send();

        $dateValidation = explode('.', $request->date);
        $timeValidation = explode(':', $request->time);

        if (count($dateValidation) != 3 || strlen($dateValidation[0]) != 2 || strlen($dateValidation[1]) != 2 || strlen($dateValidation[2]) != 4)
            $this->response()->error()->setMessage('???????????????? ???????????? ????????. ????????????????????: dd.mm.YYYY')->send();

        if (count($timeValidation) != 2 || strlen($timeValidation[0]) != 2 || strlen($timeValidation[1]) != 2)
            $this->response()->error()->setMessage('???????????????? ???????????? ??????????????. ????????????????????: HH:MM')->send();

        QuestionnaireAppointedDate::create($request->all());

        QuestionnaireHistory::create([
            'user' => auth()->user()->id,
            'from' => 'appointment',
            'comment' => '???????????????? ?? ' . $withQuestionnaire->name . ' ???????? ?????????????????? ???? ' . $request->date . ' ?? ' . $request->time . '.',
            'questionnaire_id' => $request->questionnaire_id
        ]);

        $this->response()->success()->setMessage("???????? ???????????????? ???????? ?????????????????? ???? {$request->date} ?? {$request->time}. ??????????!")->send();
    }

    public
    function getMakeDate(Request $request)
    {
        $questionnaire = Questionnaire::where('id', $request->questionnaire_id)->first();
        if (empty($questionnaire))
            $this->response()->error()->setMessage('???????????? ???? ??????????????')->send();

        $matching = QuestionnaireMatch::where('questionnaire_id', $request->questionnaire_id)
            ->join('questionnaires as q', 'q.id', '=', 'questionnaire_matches.with_questionnaire_id')
            ->join('questionnaire_my_information as information', 'information.id', '=', 'q.my_information_id')
            ->get(['questionnaire_id', 'with_questionnaire_id', 'name']);

        $this->response()->success()->setMessage('?????????????????? ????????????????')->setData($matching)->send();
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

    private function snakeToCamel($input)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $input))));
    }

    public
    function viewMatch(Request $request)
    {
        $headers = request()->headers;

        if (Cache::get('lang') == null) {
            Cache::add('lang', 'ru');
        }

        if ($headers->has('x-lang')) {
            Cache::set('lang', $headers->get('x-lang'));
        } else {
            Cache::set('lang', 'ru');
        }

        $questionnaire = Questionnaire::where('id', $request->questionnaire_id)->first();
        if (empty($questionnaire))
            $this->response()->error()->setMessage('???????????? ???? ??????????????')->send();

        $withQuestionnaire = Questionnaire::where('id', $request->with_questionnaire_id)->first();
        if (empty($questionnaire))
            $this->response()->error()->setMessage('???????????? ???? ??????????????')->send();

        $matching = QuestionnaireMatch::where('questionnaire_id', $request->questionnaire_id)
            ->where('with_questionnaire_id', $request->with_questionnaire_id)
            ->join('questionnaires as q', 'q.id', '=', 'questionnaire_matches.questionnaire_id')
            ->join('questionnaire_my_information as information', 'information.id', '=', 'q.my_information_id')
            ->first(['questionnaire_id', 'with_questionnaire_id', 'name', 'total', 'appearance', 'information', 'about_me', 'test', 'personal_qualities']);

        $partner = QuestionnaireMatch::where('with_questionnaire_id', $withQuestionnaire->id)
            ->join('questionnaires as q', 'q.id', '=', 'questionnaire_matches.with_questionnaire_id')
            ->join('questionnaire_my_information as information', 'information.id', '=', 'q.my_information_id')
            ->first(['name']);

        $questionnaire = new Questionnaire();

        $temp_q1 = [
            'my' => $questionnaire->my()->where('questionnaires.id', $request->questionnaire_id)->first()?->toArray(),
            'partner' => $questionnaire->partner()->where('questionnaires.id', $request->questionnaire_id)->first()?->toArray(),
        ];
        $temp_q2 = [
            'my' => $questionnaire->my()->where('questionnaires.id', $withQuestionnaire->id)->first()?->toArray(),
            'partner' => $questionnaire->partner()->where('questionnaires.id', $withQuestionnaire->id)->first()?->toArray(),
        ];

        # ???????? ??????????????????
        $fields = array_keys(config('app.questionnaire.value.partner_appearance'));

        $appearancesWant1 = collect($temp_q1['partner'])->only($fields);
        $appearancesMy1 = collect($temp_q2['my'])->only($fields);

        $appearancesWant2 = collect($temp_q2['partner'])->only($fields);
        $appearancesMy2 = collect($temp_q1['my'])->only($fields);

        $requirements = [
            'my' => [],
            'partner' => []
        ];

        foreach ($appearancesWant1 as $key => $item) {
            if ($key == 'sex') continue;

            if ($item === 'no_matter' || $appearancesMy1[$key] === 'no_matter') {
                $requirements['my'][$key] = true;
                continue;
            }

            if ($item === 'any' || $appearancesMy1[$key] === 'any') {
                $requirements['my'][$key] = true;
                continue;
            }

            $requirements['my'][$key] = $item == $appearancesMy1[$key];
        }

        foreach ($appearancesWant2 as $key => $item) {
            if ($key == 'sex') continue;

            if ($item === 'no_matter' || $appearancesMy2[$key] === 'no_matter') {
                $requirements['partner'][$key] = true;
                continue;
            }

            if ($item === 'any' || $appearancesMy2[$key] === 'any') {
                $requirements['partner'][$key] = true;
                continue;
            }

            $requirements['partner'][$key] = $item == $appearancesMy2[$key];
        }

        $collection = collect(array_keys(config('app.questionnaire.value.my_personal_qualities')));

        $fields = $collection->map(function ($value) {
            return 'personal_qualities.' . $value;
        })->toArray();

        $fields = array_keys(config('app.questionnaire.value.my_personal_qualities'));

        $_pqWant1 = collect($temp_q1['partner'])->only($fields);
        $_pqMy1 = collect($temp_q2['my'])->only($fields);

        $_pqWant2 = collect($temp_q2['partner'])->only($fields);
        $_pqMy2 = collect($temp_q1['my'])->only($fields);

        $pqWant1 = $_pqWant1->filter(function ($item, $key) {
            return $item === true;
        });

        $pqWant1 = $pqWant1->filter(function ($item, $key) use ($_pqMy1) {
            return $item === $_pqMy1[$key];
        });


        $pqWant2 = $_pqWant2->filter(function ($item, $key) {
            return $item === true;
        });

        $pqWant2 = $pqWant2->filter(function ($item, $key) use ($_pqMy2) {
            return $item === $_pqMy2[$key];
        });


        $_pqWant1 = $_pqWant2->filter(function ($item, $key) {
            return $item != true;
        });
        $pqWant1_False = $_pqWant1->filter(function ($item, $key) use ($_pqMy1) {
            return $item != $_pqMy1[$key];
        });
        $_pqWant2 = $_pqWant2->filter(function ($item, $key) {
            return $item != true;
        });
        $pqWant2_False = $_pqWant2->filter(function ($item, $key) use ($_pqMy2) {
            return $item != $_pqMy2[$key];
        });


//
//        $myAppearance = $questionnaire->my()->where('questionnaires.id', $request->questionnaire_id)->first($fields);
//        $partnerAppearance = $questionnaire->partner()->where('questionnaires.id', $withQuestionnaire->id)->first($fields);
//
//        $myAppearanceTrue = collect($myAppearance)->filter(function ($item, $key) {
//            return $item === true;
//        });
//
//        $res1 = [];
//        foreach ($myAppearanceTrue as $key => $item) {
//            $res1[$key] = $item === $partnerAppearance[$key];
//        }
//
//        $collection = collect(array_keys(config('app.questionnaire.value.my_personal_qualities')));
//
//        $fields = $collection->map(function ($value) {
//            return 'personal_qualities.' . $value;
//        })->toArray();
//        $myAppearance = $questionnaire->partner()->where('questionnaires.id', $request->questionnaire_id)->first($fields);
//        $partnerAppearance = $questionnaire->my()->where('questionnaires.id', $withQuestionnaire->id)->first($fields);
//
//        $myAppearanceTrue = collect($myAppearance)->filter(function ($item, $key) {
//            return $item === true;
//        });
//
//        $myAppearanceTrue->filter(function ($item, $key) use ($partnerAppearance) {
//            return $item === $partnerAppearance[$key];
//        })->count();
//
//        $res2 = [];
//        foreach ($myAppearanceTrue as $key => $item) {
//            $res2[$key] = $item === $partnerAppearance[$key];
//        }
//        dd($res2, $myAppearanceTrue);
//        $r3 = [];
//        $field1 = collect(array_keys(config('app.questionnaire.value.personal_qualities_partner')));
//        foreach ($field1 as $item) {
//
//        }

        $collection = collect(array_keys(config('app.questionnaire.value.personal_qualities_partner')));

        $res1 = [];
        $res2 = [];

//        dd($pqWant1->toArray(), $pqWant1_False->toArray(), $pqWant2->toArray(), $pqWant2_False->toArray());

        $pqWant1 = $pqWant1->toArray();
        $pqWant2 = $pqWant2->toArray();
//        dd($pqWant2_False,$pqWant2);

        foreach ($pqWant1_False->toArray() as $key => $item) {
            if (!in_array($key, array_keys($pqWant1)))
                $pqWant1[$key] = $item;
        }

        foreach ($pqWant2_False->toArray() as $key => $item) {
            if (!in_array($key, array_keys($pqWant2)))
                $pqWant2[$key] = $item;
        }

//        foreach ($pqWant2 as $key=>$item) {
//            dd($pqWant2);
//        }

        $qualities = [
            'my' => $pqWant1,
            'partner' => $pqWant2
        ];

        foreach ($qualities['my'] as $key => $item) {
            $qualities['my'][$key] = [
                'label' => $this->personalQuality($key, $appearancesMy2['sex']),
                'value' => $item
            ];
        }

        foreach ($qualities['partner'] as $key => $item) {
            $qualities['partner'][$key] = [
                'label' => $this->personalQuality($key, $appearancesMy1['sex']),
                'value' => $item
            ];
        }

        $myTest = $questionnaire->my(true)->where('questionnaires.id', $request->questionnaire_id)->first(
            collect(array_keys(config('app.questionnaire.value.test')))->except([])->toArray()
        )->toArray();

        $partnerTest = $questionnaire->partner(true)->where('questionnaires.id', $withQuestionnaire->id)->first(
            collect(array_keys(config('app.questionnaire.value.test')))->except([])->toArray()
        )->toArray();

        $c = 0;
        $testResult = [];
        foreach ($this->matchGraph as $key => $question) {
            $c++;
            $obj = [array_values($myTest)[$key] + 1, array_values($partnerTest)[$key] + 1];
            foreach ($question as $p => $percent) {
                foreach ($percent as $value) {
                    if (($value[0] === $obj[0] && $value[1] === $obj[1]) || ($value[1] === $obj[0] && $value[0] === $obj[1]))
                        $testResult[$key] = (float)$p;
                }
            }
        }


        $c = 0;
        foreach ($testResult as $key => $item) {
            if ($key != $c) $testResult[$c] = 0;
            $c++;
        }
        $testResult = array_values($testResult);

        $keys = array_keys(config('app.questionnaire.value.test'));
        foreach ($testResult as $key => $item) {
            $testResult[$keys[$key]] = $item;
            unset($testResult[$key]);
        }
        $keys = [
            '??????????????????', '??????????????', '????????????????', '?????????? ??????????', '??????????', '??????????????????', '????????',
            '??????????', '????????????', '??????????', '????????. ????????????????', '???????????????? ????????', '??????????????????????????',
            '?????????????????????? ???? ????????????', '????????????????', '???????? ??????????', '??????????????????', '?????????????? ????????????????????',
            '?????????????? ?? ????????', '????????????????????????', '?????????? ?? ??????????????????????'
        ];
        $c = 0;
        foreach ($testResult as $key=>$item) {
            $testResult[$keys[$c]] = $item;
            unset($testResult[$key]);
            $c++;
        }

        # ?????????????????? ???????? ???????????????????? ???? ????????????
        $fields = [
            'sport', 'children', 'children_desire', 'smoking', 'alcohol', 'religion',
            'age', 'zodiac_signs', 'height', 'weight', 'marital_status', 'moving_country',
            'moving_city', 'children_desire'
        ];

        $formWant1 = collect($temp_q1['partner'])->only($fields);
        $formMy1 = collect($temp_q2['my'])->only($fields);

        $formWant2 = collect($temp_q2['partner'])->only($fields);
        $formMy2 = collect($temp_q1['my'])->only($fields);

        $except = ['age'];
//        $r1 = ($this->simpleMatch($formWant1, $formMy1, $except) + $age) * 100 / (count($fields) - count($except) + 1);


//        $r2 = ($this->simpleMatch($formWant2, $formMy2, $except) + $age) * 100 / (count($fields) - count($except) + 1);

        $forms = [
            'my' => [],
            'partner' => []
        ];

        foreach ($formWant1 as $key => $item) {
            if ($key == 'age') {
                $between = explode(',', $item);
                $forms['my'][$key] = $formMy1[$key] >= $between[0] && $formMy1[$key] <= $between[1];
                continue;
            }

            if ($key == 'height') {
                $between = explode(',', $item);

                $forms['my'][$key] = $formMy1[$key] >= $between[0] && $formMy1[$key] <= $between[1];
                continue;
            }

            if ($key == 'weight') {
                $between = explode(',', $item);

                $forms['my'][$key] = $formMy1[$key] >= $between[0] && $formMy1[$key] <= $between[1];
                continue;
            }

            $ch1[] = $item;
            if ($item === 'no_matter' || $formMy1[$key] === 'no_matter') {
                $forms['my'][$key] = true;
                continue;
            }

            if ($item === 'any' || $formMy1[$key] === 'any') {
                $forms['my'][$key] = true;
                continue;
            }

            if ($item === null || $formMy1[$key] === null) {
                $forms['my'][$key] = true;
                continue;
            }

            if ($key == 'children') {
                $forms['my'][$key] = $item === $formMy1[$key];
                continue;
            }

            $forms['my'][$key] = $item == $formMy1[$key];
        }

        foreach ($formWant2 as $key => $item) {
            if ($key == 'age') {
                $between = explode(',', $item);
                $forms['partner'][$key] = $formMy2[$key] >= (int)$between[0] && $formMy2[$key] <= (int)$between[1];
                continue;
            }

            if ($key == 'height') {
                $between = explode(',', $item);
                $forms['partner'][$key] = $formMy2[$key] >= (int)$between[0] && $formMy2[$key] <= (int)$between[1];
                continue;
            }

            if ($key == 'weight') {
                $between = explode(',', $item);
                $forms['partner'][$key] = $formMy2[$key] >= (int)$between[0] && $formMy2[$key] <= (int)$between[1];
                continue;
            }

            $ch2[] = $item;
            if ($item === 'no_matter' || $formMy2[$key] === 'no_matter') {
                $forms['partner'][$key] = true;
                continue;
            }

            if ($item === 'any' || $formMy2[$key] === 'any') {
                $forms['partner'][$key] = true;
                continue;
            }

            if ($item === null || $formMy2[$key] === null) {
                $forms['partner'][$key] = true;
                continue;
            }

            if ($key == 'children') {
                $forms['partner'][$key] = $item === $formMy2[$key];
                continue;
            }

            $forms['partner'][$key] = $item == $formMy2[$key];
        }

        $fields1 = [
            "education", "work", "salary", "pets", "films_or_books", "relax", "countries_was", "countries_dream", "sleep", "clubs",
        ];
        $fields2 = [
            'education_name', 'work_name', 'health_problems',
            'allergies', 'have_pets', 'best_gift', 'hobbies',
            'kredo', 'features_repel', 'age_difference', 'films',
            'songs', 'ideal_weekend', 'sleep', 'doing_10', 'signature_dish',
            'clubs', 'best_gift_received', 'talents'
        ];
        $fields = [...$fields1, ...$fields2];
        $aboutMy2 = collect($temp_q1['my'])->only($fields);

        foreach ($aboutMy2 as $key => $item) {
            if (in_array($key, $fields1)) {
                if ($key == 'films_or_books') {
                    $aboutMy2[$key] = $this->fm($item);
                } elseif ($key == 'countries_was') {
                    $country_was = explode(',', $aboutMy2['countries_was']);
                    $place = new Countries();
                    foreach ($country_was as $item1) {
                        $place = $place->orWhere('title_en', 'ILIKE', $item1);
                    }
                    $place = $place->get(['title_ru'])->toArray();
                    $res = '';
                    if ($place != null)
                        foreach ($place as $item1) $res .= ', ' . $item1['title_ru'];
                    $aboutMy2['countries_was'] = trim($res, ', ');
                } elseif ($key == 'countries_dream') {
                    $country_was = explode(',', $aboutMy2['countries_dream']);
                    $place = new Countries();
                    foreach ($country_was as $item1) {
                        $place = $place->orWhere('title_en', 'ILIKE', $item1);
                    }
                    $place = $place->get(['title_ru'])->toArray();
                    $res = '';
                    if ($place != null)
                        foreach ($place as $item1) $res .= ', ' . $item1['title_ru'];
                    $aboutMy2['countries_dream'] = trim($res, ', ');

                } else {
                    $aboutMy2[$key] = $this->{$this->snakeToCamel($key)}($item);
                }
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
            'test' => $testResult,
            'partnerInformation' => $forms,
            'aboutMe' => $aboutMy2,
            'names' => [
                'me' => $matching->name,
                'partner' => $partner->name
            ]
        ];

        $this->response()->success()->setMessage('?????????????????? ????????????????')->setData($result)->send();
    }

    private
    function declOfNum($number, $titles)
    {
        $cases = array(2, 0, 1, 1, 1, 2);
        $format = $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
        return sprintf($format, $number);
    }

    private
    function mbUcfirst($str, $encoding = 'UTF-8')
    {
        $str = mb_ereg_replace('^[\ ]+', '', $str);
        $str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding) .
            mb_substr($str, 1, mb_strlen($str), $encoding);
        return $str;
    }

    public
    function get(GetQuestionnaire $request)
    {
        $myQuestionnaire = new Questionnaire();

        $myQuestionnaire = $myQuestionnaire->my()
            ->join('applications as a', 'a.questionnaire_id', '=', 'questionnaires.id');


        $filter = false;
        if ($request->has('is_archive')) {
            $myQuestionnaire = $myQuestionnaire->withTrashed()->whereNotNull('questionnaires.deleted_at');
        }


        if ($request->has('sex')) {
            $filter = true;
            $myQuestionnaire = $myQuestionnaire->where('sex', $request->sex);
        }

        if ($request->has('to_age') || $request->has('from_age')) {
            $filter = true;
            $myQuestionnaire = $myQuestionnaire->whereBetween('age', [(int)$request->from_age == null ? -100 : (int)$request->from_age, (int)$request->to_age == null ? 1000 : (int)$request->to_age]);
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
                    'no_matter' => '???? ??????????',
                    'caucasoid' => '??????????????????',
                    'asian' => '??????????',
                    'dark_skinned' => '????????????????????',
                    'hispanic' => '????????????????????????????????',
                    'indian' => '????????????',
                    'native_middle_east' => '?????????????? ???? ?????????? ???????????????? ??????????????',
                    'mestizo' => '??????????, ???????????????? ?????????????????????? ?? ???????????? ??????????',
                    'native_american' => '?????????????????????????? ?????????????????? ?????????????????? ??????????????',
                    'islands' => '?????????????????????????? ?????????????????? ?????????????????? ???????????????? | ???????????? ???????????? / ?????????????????? / ????????????????',
                    'other' => '????????????'
                ];

                $smoking = [
                    'dont_smoking' => '???? ????????',
                    'rarely' => '??????????',
                    'smoking' => '????????',
                    'no_matter' => '???? ??????????'
                ];

                $findZodiac = collect($zodiac)->filter(function ($item) use ($search) {
                    return false !== stristr($item, $search);
                });

                $findEthnicity = collect($ethnicity)->filter(function ($item) use ($search) {
                    return false !== stristr($item, $search);
                });

                $findSmoking = collect($smoking)->filter(function ($item) use ($search) {
                    return false !== stristr($item, $search);
                });

                if ($findZodiac->isNotEmpty()) {
                    $findZodiac = array_flip($findZodiac->toArray());
                    foreach ($findZodiac as $item) {
                        $q->orWhere('zodiac_signs', 'ILIKE', '%' . $item . '%');
                    }
                }

                if ($findEthnicity->isNotEmpty()) {
                    $findEthnicity = array_flip($findEthnicity->toArray());
                    foreach ($findEthnicity as $item) {
                        $q->orWhere('ethnicity', 'ILIKE', '%' . $item . '%');
                    }
                }

                if ($findSmoking->isNotEmpty()) {
                    $findSmoking = array_flip($findSmoking->toArray());
                    foreach ($findSmoking as $item) {
                        $q->orWhere('smoking', 'ILIKE', '%' . $item . '%');
                    }
                }
            });
        }

        if ($request->has('sort')) {
            $myQuestionnaire = $myQuestionnaire->orderBy('questionnaires.id', $request->sort == 1 ? 'DESC' : 'ASC');
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

            $titles_hours = ['%d ?????? ??????????', '%d ???????? ??????????', '%d ?????????? ??????????'];
            $titles_min = ['%d ???????????? ??????????', '%d ???????????? ??????????', '%d ?????????? ??????????'];


            if ($diff->days == 0) {
                if ($diff->h == 0) {
                    $time = $this->declOfNum($diff->i, $titles_min);
                } else {
                    $time = $this->declOfNum($diff->h, $titles_hours);
                }
            } else if ($diff->days == 1) {
                $time = '??????????';
            } else if ($diff->days == 2) {
                $time = '??????????????????';
            } else {
                $time = Carbon::createFromTimeString($item['created_at'])->format('d.m.Y');
            }


            $questionnaires[$key]['time'] = $time;
            $questionnaires[$key]['timestamp'] = $timestamp;
            $questionnaires[$key]['ethnicity'] = $this->ethnicity($questionnaires[$key]['ethnicity']);
            if (isset($questionnaires[$key]['city'])) {
                $city = explode(',', $questionnaires[$key]['city']);
                $c = $city;
                $city = Countries::where('title_en', 'ILIKE', $city[0])->first();
                if ($city != null) {
                    $questionnaires[$key]['city'] = $city['title_ru'] . (isset($c[1]) ? ',' . $c[1] : '');
                }
            }
        }

        $result['questionnaires'] = $questionnaires->toArray();


        $this->response()->success()->setMessage('???????????? ????????????????')->setData($result)->send();
    }

    public
    function getHistory(Request $request)
    {
        if (!$request->has('questionnaire_id'))
            $this->response()->setMessage('ID ???????????? ???? ????????????')->error()->send();

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

        $this->response()->success()->setMessage('?????????????? ??????????????')->setData($history)->send();
    }

    public
    function addHistory(Request $request)
    {
        if (!$request->has('questionnaire_id'))
            $this->response()->setMessage('ID ???????????? ???? ????????????')->error()->send();

        if (!$request->has('comment'))
            $this->response()->setMessage('?????????????????????? ???? ????????????')->error()->send();

        $history = QuestionnaireHistory::create([
            'questionnaire_id' => $request->questionnaire_id,
            'comment' => $request->comment,
            'from' => 'message',
            'user' => \Auth::user()->id
        ]);


        $this->response()->success()->setMessage('?????????????? ??????????????????')->setData($history)->send();
    }

    public
    function removeHistory(Request $request)
    {
        if (!$request->has('questionnaire_id'))
            $this->response()->setMessage('ID ???????????? ???? ????????????')->error()->send();

        if (!$request->has('history_id'))
            $this->response()->setMessage('ID-?????????????? ???? ????????????')->error()->send();


        $history = QuestionnaireHistory::where('id', $request->history_id)->delete();

        $this->response()->success()->setMessage('?????????????? ??????????????')->setData($history)->send();
    }

    public
    function getMatch(Request $request)
    {
        $result = [];

        if (!$request->has('questionnaire_id'))
            $this->response()->setMessage('ID ???????????? ???? ????????????')->error()->send();

        $qm = QuestionnaireMatch::where('questionnaire_id', $request->questionnaire_id);
        $total = $qm->count();


        $qm = $qm->orderBy('total', 'DESC')->get();


        $with_questionnaire = null;
        foreach ($qm as $item) {
            $with_questionnaire = Questionnaire::where('id', $item->with_questionnaire_id)->first();
            $photos = QuestionnaireUploadPhoto::where('questionnaire_id', $item->with_questionnaire_id)->first();
            $myInformation = QuestionnaireMyInformation::where('id', $with_questionnaire->my_information_id)->first();
            $q = QuestionnaireMailing::where('questionnaire_id', $item->questionnaire_id)->where('added_questionnaire_id', $with_questionnaire->id)->exists();
            $qms = QuestionnaireMatch::where('questionnaire_id', $item->questionnaire_id)->where('with_questionnaire_id', $item->with_questionnaire_id)->first();


            $result[] = [
                'questionnaire_id' => (int)$request->questionnaire_id,
                'with_questionnaire_id' => $with_questionnaire->id,
                'name' => $myInformation->name,
                'city' => $myInformation->city,
                'photo' => (isset($photos['path'])) ? $photos['path'] : null,
                'match' => [
                    'total' => round((float)$qms->total),
                    'appearance' => round((float)$qms->appearance),
                    'personal_qualities' => round((float)$qms->personal_qualities),
                    'form' => round((float)$qms->information),
                    'about_me' => round((float)$qms->about_me),
                    'test' => round((float)$qms->test),
                ],
                'in_mailing' => $q
            ];
        }

        $this->response()->success()->setMessage('???????????????????? ????????????')->setData($result)->setAdditional($pagination ?? [])->send();
    }

    public
    function addQuestionnaireMalling(Request $request)
    {
        if (!$request->has('questionnaire_id'))
            $this->response()->setMessage('ID ???????????? ???? ????????????')->error()->send();

        if (!$request->has('add_questionnaire_id'))
            $this->response()->setMessage('ID ???????????? ???? ????????????')->error()->send();

        QuestionnaireMailing::create([
            'questionnaire_id' => $request->questionnaire_id,
            'added_questionnaire_id' => $request->add_questionnaire_id
        ]);

        $this->response()->success()->setMessage('???????????? ?????????????????? ?? ????????????????')->send();
    }

    public
    function removeQuestionnaireMalling(Request $request)
    {
        if (!$request->has('questionnaire_id'))
            $this->response()->setMessage('ID ???????????? ???? ????????????')->error()->send();

        if (!$request->has('added_questionnaire_id'))
            $this->response()->setMessage('ID ???????????? ???? ????????????')->error()->send();

        QuestionnaireMailing::where('questionnaire_id', $request->questionnaire_id)->where('added_questionnaire_id', $request->added_questionnaire_id)->delete();

        $this->response()->success()->setMessage('???????????? ???????? ?????????????? ???? ????????????????')->send();
    }

    public
    function setStatus(Request $request)
    {
        if (!$request->has('questionnaire_id'))
            $this->response()->setMessage('ID ???????????? ???? ????????????')->error()->send();


        if (!$request->has('status'))
            $this->response()->setMessage('???????????? ???? ????????????')->error()->send();

        if (!in_array($request->status, ['vip', 'pay', 'free', 'paid']))
            $this->response()->setMessage('???????????? ?????????????? ???? ????????????????????. ??????????????????: vip, pay, free, paid')->error()->send();

        $questionnaire = Questionnaire::where('id', $request->questionnaire_id)->first();
        if (empty($questionnaire))
            $this->response()->error()->setMessage('???????????? ???? ??????????????')->send();

        Questionnaire::where('id', $request->questionnaire_id)->update([
            'status_pay' => $request->status
        ]);

        Applications::where('questionnaire_id', $request->questionnaire_id)->update([
            'service_type' => $request->status
        ]);

        $this->response()->success()->setMessage('???????????? ??????????????')->send();
    }

    public
    function createPresentation(Request $request)
    {
        if (!$request->has('questionnaire_id'))
            $this->response()->setMessage('ID ???????????? ???? ????????????')->error()->send();

        (new PptxCreator())->create($request);

        $this->response()->success()->setMessage('?????????????????????? ???????? ??????????????')->setData([
            'download_link' => env('APP_URL') . '/storage/pptx/generate/' . $request->questionnaire_id . '/presentation.pdf'
        ])->send();
    }

    public
    function getSlide(Request $request, $slide, $questionnaireId)
    {
        (new PptxCreator())->getSlide($slide, $questionnaireId);
    }

    public
    function sign(Request $request)
    {
        if (!$request->has('sign'))
            $this->response()->error()->setMessage('???? ???? ?????????????? SIGN ??????.')->send();

        $exist = SignQuestionnaire::where('sign', $request->sign)->exists();

        $this->response()->success()->setMessage('??????????????????')->setData(['exist' => $exist])->send();
    }

    public
    function archive(Request $request)
    {
        if (!$request->has('questionnaire_id'))
            $this->response()->error()->setMessage('???? ???? ?????????????? ID ????????????')->send();

        Questionnaire::where('id', $request->questionnaire_id)->delete();
        # ?????????????????? ?? ?????????? ??????????
        QuestionnaireMatch::where('questionnaire_id', $request->questionnaire_id)
            ->orWhere('with_questionnaire_id', $request->questionnaire_id)->delete();

        $this->response()->success()->setMessage('???????????? ???????? ???????????????????? ?? ??????????')->send();
    }

    public
    function deleteForce(ForceDeleteQuestionnaire $request)
    {
        Questionnaire::where('id', $request->questionnaire_id)->forceDelete();
        # ?????????????????? ?? ?????????? ??????????
        QuestionnaireMatch::where('questionnaire_id', $request->questionnaire_id)
            ->orWhere('with_questionnaire_id', $request->questionnaire_id)->forceDelete();

        $this->response()->success()->setMessage('???????????? ???????? ?????????????? ????????????????')->send();
    }

    public
    function unarchive(Request $request)
    {
        if (!$request->has('questionnaire_id'))
            $this->response()->error()->setMessage('???? ???? ?????????????? ID ????????????')->send();

        Questionnaire::where('id', $request->questionnaire_id)->restore();
        # ?????????????????? ?? ?????????? ??????????
        QuestionnaireMatch::where('questionnaire_id', $request->questionnaire_id)
            ->orWhere('with_questionnaire_id', $request->questionnaire_id)->restore();

        $this->response()->success()->setMessage('???????????? ???????? ???????????????????? ???? ????????????')->send();
    }

}
