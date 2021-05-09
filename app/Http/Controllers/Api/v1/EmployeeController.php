<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\Archive;
use App\Http\Requests\Employee\Got;
use App\Http\Requests\Employee\NewPassword;
use App\Http\Requests\Employee\Update;
use App\Mail\CreateEmployee;
use App\Models\User;
use App\Utils\Phone;
use App\Utils\Response;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    use Response, Phone;

    /**
     * Получить всех пользователей
     *
     * @param Got $request
     */
    public function get(Got $request)
    {
        $fields = ['*'];
        $model = new User();

        if ($request->has('only_archive') && $request->only_archive) {
            $model = $model::withTrashed();
            $model = $model->whereNotNull('deleted_at');
        }
        if( $request->has('role') ) {
            $model = $model->where('role', $request->role);
        } else {
            $model = $model->where(function(Builder $query) {
                $query->where('role', 1)->orWhere('role', 2);
            });
        }



        if ($request->has('fields')) {
            $fields = trim($request->fields);
            $fields = preg_replace('/\s+/', '', $fields);
            $fields = explode(',', $fields);
        }

        if( !$request->has('search') || empty($request->search) ) {
            if ($request->has('limit')) {
                $model = $model->limit($request->limit);
            }

            if ($request->has('offset')) {
                $model = $model->offset($request->offset);
            }
        } else {
            $search = $request->search;
            $model = $model->where(function(Builder $query) use ($search){
                $query
                    ->where('name', 'LIKE', '%'.$search.'%')
                    ->orWhere('phone', 'LIKE', '%'.$search.'%')
                ;
            });
        }

        $model = $model->get($fields)->toArray();

        foreach ($model as $key=>$item) {
            $model[$key]['created_at'] = Carbon::createFromTimeString($item['created_at'])->format('d.m.Y в H:i');
        }

        $model = collect($model);

        $this->response()->success()->setMessage('Сотрудники получены')->setData([
            'count' => $model->count(),
            'data' => $model
        ])->send();
    }

    /**
     * Редактировать сотрудника
     *
     * @param Update $request
     */
    public function update(Update $request)
    {
        $input = $request->except('user_id');
        if( isset($input['phone']) )
            $input['phone'] = $this->phoneFormatting($input['phone']);

        $updated = User::where('id', $request->user_id)->update($input);

        $this->response()->success()->setMessage('Настройки сотрудника сохранены')->send();
    }

    /**
     * Изменить пароль
     */
    public function newPassword(NewPassword $request)
    {
        $password = Str::random(8);
        $user = User::where('id', $request->user_id)->first();

        if( empty($user) )
            $this->response()->error()->setMessage('Пользователя не существует')->send();

        User::where('id', $request->user_id)->update([
            'password' => Hash::make($password)
        ]);

        $role = match ($user['role'] ) {
            1 => 'Администратор',
            2 => 'Менеджер',
            default => 'Клиент',
        };

//        Mail::to($user['email'])->send(new CreateEmployee(
//            email: $user['email'],
//            password: $password,
//            role: $role,
//            isNewPassword: true
//        ));

        $this->response()->success()->setMessage('Новый пароль был выслан на почту')->setData([
            'password' => $password,
            'email' => $user['email']
        ])->send();
    }

    /**
     * Архивировать сотрудника
     *
     * @param Archive $request
     */
    public function archive(Archive $request)
    {
        $user = User::where('id', $request->user_id)->first();

        if(empty($user))
            $this->response()->error()->setMessage('Пользователя не существует')->send();

        User::where('id', $request->user_id)->delete();

        $this->response()->success()->setMessage('Сотрудник был архивирован')->setData($user)->send();
    }

    /**
     * Разархивировать сотрудника
     *
     * @param Archive $request
     */
    public function unarchive(Archive $request)
    {
        $user = User::withTrashed()->where('id', $request->user_id)->first();

        if(empty($user))
            $this->response()->error()->setMessage('Пользователя не существует')->send();

        User::where('id', $request->user_id)->restore();

        $this->response()->success()->setMessage('Сотрудник был разархивирован')->setData($user)->send();
    }
}
