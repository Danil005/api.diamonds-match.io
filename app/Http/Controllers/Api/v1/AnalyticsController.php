<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Applications;
use App\Models\Questionnaire;
use App\Models\QuestionnaireUploadPhoto;
use App\Models\User;
use App\Utils\Response;
use App\Utils\TranslateFields;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    use Response, TranslateFields;

    private function declOfNum($number, $titles)
    {
        $cases = array(2, 0, 1, 1, 1, 2);
        $format = $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
        return sprintf($format, $number);
    }

    public function get(Request $request)
    {
        $questionnaire = new Questionnaire();
        $questionnairesCountAll = Questionnaire::count();
        $questionnairesCountToday = Questionnaire::whereDate('created_at', Carbon::today())->count();
        $applicationsCountAll = Applications::count();
        $applicationsCountNew = Applications::whereNull('responsibility')->whereDate('created_at', Carbon::today())->count();
        $onlineCount = User::where('online', true)->count();
        $questionnairesCountAllWithout = $questionnaire = my()->whereNull('responsibility')->join('applications as a', 'a.questionnaire_id', '=', 'questionnaires.id')->get();

        foreach ($questionnairesCountAllWithout as $key => $item) {
            $photo = QuestionnaireUploadPhoto::where('questionnaire_id', $item->id)->first(['path']);
            $questionnairesCountAllWithout[$key]['photo'] = $photo == null ? null : $photo->path;

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


            $questionnairesCountAllWithout[$key]['time'] = $time;
            $questionnairesCountAllWithout[$key]['timestamp'] = $timestamp;
            $questionnairesCountAllWithout[$key]['ethnicity'] = $this->ethnicity($questionnairesCountAllWithout[$key]['ethnicity']);
        }

        $lastApplications = Applications::orderBy('created_at', 'DESC')->whereNull('responsibility')->limit(5)->get();

        return $this->response()->success()->setMessage('Статистика получена')->setData([
            'online_count' => $onlineCount,
            'questionnaires_all_count' => $questionnairesCountAll,
            'applications_all_count' => $applicationsCountAll,
            'questionnaires_new_count' => $questionnairesCountToday,
            'applications_new_count' => $applicationsCountNew,
            'questionnaires_without_employee' => $questionnairesCountAllWithout,
            'last_applications' => $lastApplications
        ])->send();
    }
}
