<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Utils\Language;
use App\Models\Cities;
use App\Models\Countries;
use App\Models\Langs;
use App\Utils\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UtilsController extends Controller
{
    use Response;

    public function getCities(\App\Http\Requests\Utils\Cities $request)
    {
        $data = $request->all();

        $cities = Cities::where(function(Builder $query) use ($data){

            $query->where('title_ru', 'LIKE', '%'.$data['title'].'%')
                ->orWhere('title_en', 'LIKE', '%'.$data['title'].'%');

        })->limit(25)->get(['title_ru', 'title_en']);

        $result = [];

        foreach ($cities as $key=>$item) {
            $result[] = ['value_ru' => $item['title_ru'], 'value_en' => $item['title_en']];
        }

        $this->response()->success()->setMessage('Города получены')->setData($result)->send();
    }

    public function getCountry(\App\Http\Requests\Utils\Country $request)
    {
        $data = $request->all();

        if($request->has('title')) {
            $countries = Countries::where(function (Builder $query) use ($data) {

                $query->where('title_ru', 'LIKE', '%' . $data['title'] . '%')
                    ->orWhere('title_en', 'LIKE', '%' . $data['title'] . '%');

            })->limit(5)->get(['title_ru', 'title_en']);
        } else {
            $countries = Countries::get(['title_ru', 'title_en']);
        }
        $result = [];

        foreach ($countries as $key=>$item) {
            $result[] = ['value_ru' => $item['title_ru'], 'value_en' => $item['title_en']];
        }

        $this->response()->success()->setMessage('Страны получены')->setData($result)->send();
    }

    public function getLanguage(Language $request)
    {
        if(!function_exists('mb_ucfirst'))
        {
            function mb_ucfirst($string, $enc = 'UTF-8')
            {
                return mb_strtoupper(mb_substr($string, 0, 1, $enc), $enc) .
                    mb_substr($string, 1, mb_strlen($string, $enc), $enc);
            }
        }

        $data = $request->all();

        if($request->has('title')) {
            $data = mb_strtolower($data['title']);
            $langs = Langs::where(function (Builder $query) use ($data) {

                $query->where('nameRU', 'LIKE', '%' . $data . '%')
                    ->orWhere('nameEN', 'LIKE', '%' . $data . '%');

            })->limit(5)->get();
        } else {
            $langs = Langs::get();
        }

        $result = [];

        foreach ($langs as $item) {
            $result[] = [
                'code' => $item['code'],
                'nameRU' => mb_ucfirst($item['nameRU']),
                'nameEN' => mb_ucfirst($item['nameEN']),
                'nativeName' => $item['nativeName']
            ];
        }


        $this->response()->success()->setMessage('Языки получены')->setData($result)->send();
    }
}
