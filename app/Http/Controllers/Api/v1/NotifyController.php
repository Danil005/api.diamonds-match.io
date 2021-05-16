<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationRead;
use App\Utils\Response;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotifyController extends Controller
{
    use Response;

    public function get(Request $request)
    {
        $notify = Notification::get()->toArray();

        $userId = \Auth::user()->id;

        foreach ($notify as $key => $item) {
            $exist = NotificationRead::where('notification_id', $item['id'])->where('user_id', $userId)->exists();

            if( $exist )
                unset($notify[$key]);
            else {
                $notify[$key]['payload'] = json_decode($item['payload'], true);
                $notify[$key]['created_at'] = Carbon::createFromTimeString($item['created_at'])->format('d.m.Y H:i');
            }
        }



        $notify = array_values($notify);

        $this->response()->success()->setMessage('Уведомления получены')->setData([
            'count' => count($notify),
            'notifications' => $notify
        ])->send();
    }

    public function clear()
    {
        $notify = Notification::get()->toArray();

        $userId = \Auth::user()->id;

        foreach ($notify as $key => $item) {
            $exist = NotificationRead::where('notification_id', $item['id'])->where('user_id', $userId)->exists();

            if( !$exist )
                NotificationRead::create([
                    'notification_id' => $item['id'],
                    'user_id' => $userId
                ]);
        }

        $this->response()->success()->setMessage('Уведомления очищены')->send();
    }
}
