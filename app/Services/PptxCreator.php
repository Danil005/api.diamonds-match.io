<?php

namespace App\Services;

use App\Models\Countries;
use App\Models\Questionnaire;
use App\Models\QuestionnaireFiles;
use App\Models\QuestionnaireUploadPhoto;
use App\Utils\TranslateFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PptxCreator
{
    use TranslateFields;

    public function create(Request $request)
    {
        $id = $request->questionnaire_id;
        Storage::makeDirectory('public/pptx/generate/'.$id);
        sleep(2);

        $connection = ssh2_connect('45.141.79.57', 22);
        ssh2_auth_password($connection, env('SSH_U'), env('SSH_P'));


        for ($i = 1; $i <= 5; $i++) {
            $stream = ssh2_exec($connection, 'wkhtmltoimage https://api.diamondsmatch.org/getSlide/' . $i . '/' . $id . ' /var/www/html/storage/app/public/pptx/generate/'.$id.'/s' . $i . '.jpg');
            stream_set_blocking($stream, true);
            stream_get_contents($stream);
        }
        for ($i = 1; $i <= 5; $i++) {
            $stream = ssh2_exec(
                $connection,
                'convert /var/www/html/storage/app/public/pptx/generate/'.$id.'/s' . $i . '.jpg -crop 784x1119+0+0 /var/www/html/storage/app/public/pptx/generate/'.$id.'/s' . $i . '.jpg'
            );
            stream_set_blocking($stream, true);
            stream_get_contents($stream);
        }

        $slides = '';
        for($i = 1; $i <= 5; $i++) $slides .= '/var/www/html/storage/app/public/pptx/generate/'.$id.'/s'.$i . ' ';
        $slides = trim($slides);

        $stream = ssh2_exec($connection, 'convert ' . $slides . ' /var/www/html/storage/app/public/pptx/generate/'.$id.'/presentation.pdf');
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);

        $path = Storage::disk('public')->path('/pptx/generate/'.$id.'/presentation.pdf');
    }

    public function getSlide($slide, $questionnaireId)
    {
        $questionnaire = new Questionnaire();
        $questionnaire = $questionnaire->my()->where('questionnaires.id', $questionnaireId)->first()?->toArray();

        if ($questionnaire == null)
            return 'Презентация не найдена';

        $result = $questionnaire;
        $zodiac = $this->zodiacSigns();

        $result['zodiac_signs'] = $zodiac[$result['zodiac_signs']];

        $result['age'] = $this->years($result['age']);
        $city = explode(',', $result['city']);
        $c = $city;
        $city = Countries::where('title_en', 'ILIKE', $city[0])->first();
        if ($city != null) {
            $result['city'] = $city['title_ru'] . (isset($c[1]) ? ', ' . $c[1] : '');
        }

        if (isset($result['place_birth'])) {
            $place = explode(',', $result['place_birth']);
            $place2 = explode(',', $result['place_birth']);
            if (isset($place[1])) {
                $place = $place[1];
            } else {
                $place = $place[0];
            }
            $place = Countries::where('title_en', 'ILIKE', $place)->first();
            if ($place != null)
                $result['place_birth'] = $place['title_ru'];
            else
                $result['place_birth'] = $place2[1] ?? $place2[0];
        }

        if (isset($result['countries_was'])) {
            $country_was = explode(',', $result['countries_was']);
            $place = new Countries();
            foreach ($country_was as $item) {
                $place = $place->orWhere('title_en', 'ILIKE', $item);
            }
            $place = $place->get(['title_ru'])->toArray();
            $res = '';
            if ($place != null)
                foreach ($place as $item) $res .= ', ' . $item['title_ru'];
            $result['countries_was'] = trim($res, ', ');
        }

        if (isset($result['countries_dream'])) {
            $country_was = explode(',', $result['countries_dream']);
            $place = new Countries();
            foreach ($country_was as $item) {
                $place = $place->orWhere('title_en', 'ILIKE', $item);
            }
            $place = $place->get(['title_ru'])->toArray();
            $res = '';
            if ($place != null)
                foreach ($place as $item) $res .= ', ' . $item['title_ru'];
            $result['countries_dream'] = trim($res, ', ');
        }

        $result['ethnicity'] = $this->ethnicity($result['ethnicity']);
        $result['body_type'] = $this->bodyType($result['body_type']);

        if (isset($result['chest']) && $result['chest'] !== null) {
            $result['chest'] = $this->chestOrBooty($result['chest']);
        }

        if (isset($result['booty']) && $result['booty'] !== null) {
            $result['booty'] = $this->chestOrBooty($result['booty']);
        }

        if (isset($result['hair_length']) && $result['hair_length'] !== null) {
            $result['hair_length'] = $this->hairLength($result['hair_length']);
        }

        $result['hair_color'] = $this->hairColor($result['hair_color']);
        $result['eye_color'] = $this->colorEye($result['eye_color']);
        $result['sex'] = $result['sex'] === 'female' ? 'Женщина' : 'Мужчина';


        foreach ($result as $key => $item) {
            if ($key == 'smoking') {
                $result['smoking'] = $this->smoking($result['smoking'], 'male');
            }

            if ($key == 'alcohol') {
                $result['alcohol'] = $this->alcohol($result['alcohol'], 'male');
            }

            if ($key == 'religion') {
                $result['religion'] = $this->religion($result['religion'], 'male');
            }

            if ($key == 'sport') {
                $result['sport'] = $this->sport($result['sport'], 'male');
            }

            if ($key == 'education') {
                $result['education'] = $this->education($result['education'], 'male');
            }

            if ($key == 'work') {
                $result['work'] = $this->work($result['work'], 'male');
            }

            if ($key == 'pets') {
                $result['pets'] = $this->pets($result['pets'], 'male');
            }

            if ($key == 'films_or_books') {
                $result['films_or_books'] = $this->fm($result['films_or_books'], 'male');
            }

            if ($key == 'relax') {
                $result['relax'] = $this->relax($result['relax'], 'male');
            }

            if ($key == 'sleep') {
                $result['sleep'] = $this->sleep($result['sleep'], 'male');
            }

            if ($key == 'clubs') {
                $result['clubs'] = $this->clubs($result['clubs'], 'male');
            }

            if ($key == 'salary') {
                $result['salary'] = $this->salary($result['salary']);
            }

            if ($key == 'marital_status') {
                $result['marital_status'] = $this->maritalStatus($result['marital_status'], $result['sex']);
            }

            if ($key == 'children_desire') {
                $result['children_desire'] = $this->childrenDesire($result['children_desire'], $result['sex']);
            }
        }

        $moving = [];

        if ($questionnaire['moving_country']) {
            $moving[] = 'В другую страну';
        }

        if ($questionnaire['moving_city']) {
            $moving[] = 'В другой город';
        }

        $result['moving'] = empty($moving) ? 'Все равно' : implode(', ', $moving);

        $photos = QuestionnaireUploadPhoto::where('questionnaire_id', $questionnaireId)->get(['id', 'path'])?->toArray();
        $result['photos'] = $photos;
        dd($photos);

        echo view('pdf.slide' . $slide, ['q' => $result, 'class' => $this]);
    }
}
