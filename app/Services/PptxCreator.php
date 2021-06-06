<?php

namespace App\Services;

use App\Models\Countries;
use App\Models\Questionnaire;
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

    public function getSlide($slide, $questionnaireId): string
    {
        $questionnaire = new Questionnaire();
        $questionnaire = $questionnaire->my()->where('questionnaires.id', $questionnaireId)->first()?->toArray();

        if ($questionnaire == null)
            return 'Презентация не найдена';

        $result = $questionnaire;
        $zodiac = $this->zodiacSigns();

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


        echo view('pdf.slide' . $slide, ['q' => $result, 'class' => $this]);
    }
}
