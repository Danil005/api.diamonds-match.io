<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Applications;
use App\Models\Questionnaire;
use App\Models\User;
use App\Utils\Response;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    use Response;

    public function get(Request $request)
    {
        $questionnairesCountAll = Questionnaire::count();
        $questionnairesCountToday = Questionnaire::whereDate('created_at', Carbon::today())->count();
        $applicationsCountAll = Applications::count();
        $applicationsCountNew = Applications::whereNull('responsibility')->whereDate('created_at', Carbon::today())->count();
        $onlineCount = User::where('online', true)->count();
        $questionnairesCountAllWithout = Questionnaire::join('applications as a', 'a.questionnaire_id', '=', 'questionnaires.id')->count();

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
