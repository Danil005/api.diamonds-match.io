<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applications\ChangeApplications;
use App\Http\Requests\Applications\Create;
use App\Http\Requests\Applications\StartWorkApplications;
use App\Http\Requests\Applications\UpdateApplications;
use App\Models\Applications;
use App\Models\User;
use App\Utils\Response;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ApplicationsController extends Controller
{
    use Response;


    public function create(Create $create)
    {
        $service_type = $create->service_type;

        $service_type = match ($service_type) {
            'free' => 'Бесплатно',
            'pay' => 'Платные услуги',
            default => 'Услуги VIP'
        };
        $data = $create->all();

        $data['service_type'] = $service_type;

        if (Auth::check()) {
            $data['responsibility'] = Auth::user()->id . ',' . Auth::user()->name;
        }

        Applications::create($data);

        $this->response()->success()->setMessage('Заявка успешно создана')->send();
    }

    public function get(Request $request)
    {
        $applications = new Applications();

        if ($request->has('search')) {
            $search = $request->search;
            $applications = $applications->where(function (Builder $query) use ($search) {
                $query->where('responsibility', 'LIKE', '%' . $search . '%')
                    ->orWhere('client_name', 'LIKE', '%' . $search . '%');
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
            function declOfNum($number, $titles)
            {
                $cases = array(2, 0, 1, 1, 1, 2);
                $format = $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
                return sprintf($format, $number);
            }

            if ($diff->days == 0) {
                if( $diff->h == 0 ) {
                    $time = declOfNum($diff->i, $titles_min);
                } else {
                    $time = declOfNum($diff->h, $titles_hours);
                }
            } else if ($diff->days == 1) {
                $time = 'вчера';
            } else if ($diff->days == 2) {
                $time = 'позавчера';
            }


            $result[] = [
                'status' => $application['status'],
                'client_name' => $application['client_name'],
                'responsibility' => User::where('id', explode(',', $application['responsibility']))->first(['id', 'name', 'avatar', 'role']),
                'service_type' => $application['service_type'],
                'created_at' => $time
            ];
        }

        $this->response()->setMessage('Данные анкет получены')->setData($result)->send();
    }

    public function change(ChangeApplications $request)
    {
        Applications::where('id', $request->id)->update([
            'status' => $request->status
        ]);


        $this->response()->setMessage('Статус изменен')->send();
    }

    public function startWork(StartWorkApplications $applications)
    {
        Applications::where('id', $applications->id)->update([
            'status' => 1,
            'responsibility' => Auth::user()->id.','.Auth::user()->name,
        ]);

        $this->response()->setMessage('Статус изменен')->send();
    }

    public function update(UpdateApplications $request)
    {
        Applications::where('id', $request->id)->update($request->all());

        $this->response()->setMessage('Настройки сохранены')->send();
    }
}
