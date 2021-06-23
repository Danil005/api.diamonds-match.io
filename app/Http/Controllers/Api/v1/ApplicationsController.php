<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applications\ChangeApplications;
use App\Http\Requests\Applications\Create;
use App\Http\Requests\Applications\DeleteApplication;
use App\Http\Requests\Applications\StartWorkApplications;
use App\Http\Requests\Applications\UnarchiveApplication;
use App\Http\Requests\Applications\UpdateApplications;
use App\Models\Applications;
use App\Models\Questionnaire;
use App\Models\SignQuestionnaire;
use App\Models\User;
use App\Utils\Response;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use YooKassa;

class ApplicationsController extends Controller
{
    use Response;


    public function create(Create $create)
    {
        $service_type = $create->service_type;

        $data = $create->all();

        $data['service_type'] = $service_type;

        if (Auth::check()) {
            $data['responsibility'] = Auth::user()->id . ',' . Auth::user()->name;
        }

        Applications::create($data);

        $this->response()->success()->setMessage('Заявка успешно создана')->send();
    }

    private function declOfNum($number, $titles)
    {
        $cases = array(2, 0, 1, 1, 1, 2);
        $format = $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
        return sprintf($format, $number);
    }

    public function get(Request $request)
    {
        $applications = new Applications();

        if( $request->has('archive_only') ) {
            $applications = $applications->withTrashed()->whereNotNull('applications.deleted_at');
        }

        if( $request->has('responsibility_id') ) {
            $user = User::where('id', $request->responsibility_id)->first();

            if( empty($user) )
                $this->response()->error()->setMessage('Сотрудник не найден')->send();


            $applications = $applications->where('responsibility', $user->id.','.$user->name);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $applications = $applications->where(function (Builder $query) use ($search) {
                $query->where('responsibility', 'ILIKE', '%' . $search . '%')
                    ->orWhere('client_name', 'ILIKE', '%' . $search . '%')
                    ->orWhere('phone', 'ILIKE', '%' . $search . '%')
                    ->orWhere('email', 'ILIKE', '%' . $search . '%');
            });
        }

        $applications = $applications->get();
        $result = [];

        foreach ($applications as $key => $application) {
            $time = Carbon::createFromTimeString($application['created_at']);

            $now = Carbon::now();
            $then = Carbon::createFromTimeString($application['created_at']);
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
            } else {
                $time = $time->format('d.m.Y');
            }

            $result[] = [
                'id' => $application['id'],
                'status' => $application['status'],
                'client_name' => $application['client_name'],
                'responsibility' => $application['responsibility'] == null ? null :
                    User::where('id', (int)explode(',', $application['responsibility'])[0])->first(['id', 'name', 'avatar', 'role']),
                'service_type' => $application['service_type'],
                'email' => $application['email'],
                'phone' => $application['phone'],
                'link' => $application['link'],
                'link_active' => $application['link_active'],
                'created_at' => $time,
                'created_at_timestamp' => Carbon::createFromTimeString($application['created_at'])->timestamp
            ];
        }

        $result = array_values(collect($result)->sortByDesc('created_at_timestamp')->toArray());

        $resp = $this->response()->success()->setMessage('Данные анкет получены')->setData($result);

        if( $request->has('archive_only') ) {
            $resp->setAdditional(['is_archived' => true])->send();
        } else {
            $resp->send();
        }
    }

    public function change(ChangeApplications $request)
    {
        $application = Applications::where('id', $request->id)->first();

        if( $request->status != 0) {
            $user = auth()->user();
            Applications::where('id', $request->id)->update([
                'responsibility' => $user->id .','.$user->name
            ]);
        } else {
            Applications::where('id', $request->id)->update([
                'responsibility' => null
            ]);
        }

        if( empty($application->link) ) {
            $sign = md5(Str::random(16));
            $questionnaire = Questionnaire::create([
                'sign' => $sign
            ]);

            SignQuestionnaire::create([
                'application_id' => $request->id,
                'questionnaire_id' => $questionnaire->id,
                'sign' => $sign,
                'active' => true
            ]);

            Applications::where('id', $request->id)->update([
                'link' => env('APP_QUESTIONNAIRE_URL').'/sign/'.$sign,
                'link_active' => true,
                'questionnaire_id' => $questionnaire->id
            ]);
        }

        Applications::where('id', $request->id)->update([
            'status' => $request->status
        ]);

        $this->response()->success()->setMessage('Статус изменен')->setData([
            'link' => $application->link ?? null
        ])->send();
    }

    public function startWork(StartWorkApplications $applications)
    {
        Applications::where('id', $applications->id)->orWhere('questionnaire_id', $applications->id)->update([
            'status' => 1,
            'responsibility' => Auth::user()->id.','.Auth::user()->name,
        ]);

        $this->response()->success()->setMessage('Статус изменен')->send();
    }

    public function update(UpdateApplications $request)
    {
        Applications::where('id', $request->id)->update($request->all());

        $this->response()->setMessage('Настройки сохранены')->send();
    }

    public function delete(DeleteApplication $request)
    {
        Applications::where('id', $request->id)->delete();

        $this->response()->setMessage('Анкета была архивирована')->send();
    }

    public function unarchive(UnarchiveApplication $request)
    {
        Applications::withTrashed()->where('id', $request->id)->update([
            'deleted_at' => null
        ]);

        $this->response()->setMessage('Анкета была разархивирована')->send();
    }

    public function view(Request $request)
    {
        if( !$request->has('id') )
            $this->response()->error()->setMessage('ID-не указан')->send();

        $application = Applications::where('id', (int)$request->id)->first();

        if( empty($application) )
            $this->response()->error()->setMessage('Анкета не найдена')->send();

        $this->response()->success()->setMessage('Данные анкеты')->setData($application)->send();
    }

    public function createPayment(Request $request)
    {
        $id = $request->id;
        $sum = $request->sum;
        $currency = $request->sum;

//        YooKassa::createPayment((float)$sum, )
    }
}
