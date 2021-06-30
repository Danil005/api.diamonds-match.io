<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Utils\Language;
use App\Models\Cities;
use App\Models\Countries;
use App\Models\Langs;
use App\Utils\Response;
use Fomvasss\Dadata\Facades\DadataSuggest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;


class UtilsController extends Controller
{
    use Response;

    public function getCities(\App\Http\Requests\Utils\Cities $request)
    {
        $data = $request->all();

        $cities = Cities::where(function(Builder $query) use ($data) {
            $query->where('title_ru', 'ILIKE', '%' . $data['title'] . '%');
        })->limit(25);

        if($request->has('country_id')) {
            $cities->where('country_id', (int)$request->country_id);
        }

        $cities = $cities->get([
            'title_ru',
            'title_en'
        ]);

        $result = [];
        $dup = [];

        foreach($cities as $key => $item) {
            if(!in_array($item['title_ru'], $dup)) {
                $result[] = [
                    'value_ru' => $item['title_ru'],
                    'value_en' => $item['title_en']
                ];
                $dup[] = $item['title_ru'];
            }
        }

        $this->response()->success()->setMessage('Города получены')->setData($result)->send();
    }

    public function getCountry(\App\Http\Requests\Utils\Country $request)
    {
        $data = $request->all();

        if($request->has('title')) {
            $countries = Countries::where(function(Builder $query) use ($data) {

                $query->where('title_ru', 'ILIKE', '%' . $data['title'] . '%')->orWhere('title_en', 'ILIKE', '%' . $data['title'] . '%');

            })->limit(5)->get([
                'title_ru',
                'title_en',
                'country_id'
            ]);
        } else {
            $countries = Countries::get([
                'title_ru',
                'title_en',
                'country_id'
            ]);
        }
        $result = [];

        foreach($countries as $key => $item) {
            $result[] = [
                'value_ru'   => $item['title_ru'],
                'value_en'   => $item['title_en'],
                'country_id' => $item['country_id']
            ];
        }

        $this->response()->success()->setMessage('Страны получены')->setData($result)->send();
    }

    public function getLanguage(Language $request)
    {
        if(!function_exists('mb_ucfirst')) {
            function mb_ucfirst($string, $enc = 'UTF-8')
            {
                return mb_strtoupper(mb_substr($string, 0, 1, $enc), $enc) . mb_substr($string, 1, mb_strlen($string, $enc), $enc);
            }
        }

        $data = $request->all();

        if($request->has('title')) {
            $data = mb_strtolower($data['title']);
            $langs = Langs::where(function(Builder $query) use ($data) {

                $query->where('nameRU', 'LIKE', '%' . $data . '%')->orWhere('nameEN', 'LIKE', '%' . $data . '%');

            })->limit(5)->get();
        } else {
            $langs = Langs::get();
        }

        $result = [];

        foreach($langs as $item) {
            $result[] = [
                'code'       => $item['code'],
                'nameRU'     => mb_ucfirst($item['nameRU']),
                'nameEN'     => mb_ucfirst($item['nameEN']),
                'nativeName' => $item['nativeName']
            ];
        }


        $this->response()->success()->setMessage('Языки получены')->setData($result)->send();
    }

    public function daDataCity(Request $request)
    {
        $result = DadataSuggest::suggest("address", [
            "query"    => $request->city,
            "count"    => 5,
            'language' => $request->lang ?? 'ru',
            'locations' => [
                'city_type_full' => 'город', 
                'country" => "*" 
            ]
        ]);

        $rs = [];
        foreach($result as $item) {
            $rs[] = [
                'value_ru'   => $item['data']['city'],
                'value_en'   => $item['data']['city'],
                'country_id' => Countries::where('title_ru', $item['data']['country'])->orWhere('title_en', $item['data']['country'])->first()?->country_id
            ];
        }

        $rs = array_values(collect($rs)->unique('value_ru')->toArray());

        $this->response()->success()->setMessage('Города')->setData($rs)->send();
    }
}
