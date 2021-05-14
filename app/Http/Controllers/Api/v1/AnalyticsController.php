<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Applications;
use App\Models\Questionnaire;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function get(Request $request)
    {
        $questionnairesCountAll = Questionnaire::count();
        $questionnairesCountToday = Questionnaire::whereDate('created_at', Carbon::today())->count();
        $applicationsCountAll = Applications::count();
    }
}
