<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\Archive;
use App\Http\Requests\Employee\Got;
use App\Http\Requests\Employee\NewPassword;
use App\Http\Requests\Employee\Update;
use App\Mail\CreateEmployee;
use App\Models\Applications;
use App\Models\Questionnaire;
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
                $query->where('name', 'ILIKE', '%'.$search);
            });
        }

        $model = $model->get($fields)->toArray();

        foreach ($model as $key=>$item) {
            $model[$key]['created_at'] = Carbon::createFromTimeString($item['created_at'])->format('d.m.Y в H:i');
            $model[$key]['created_at_timestamp'] = Carbon::createFromTimeString($item['created_at'])->timestamp;
        }

        $model = collect($model);

        $this->response()->success()->setMessage('Сотрудники получены')->setData([
            'count' => $model->count(),
            'data' => $model
        ])->send();
    }

    /**
     * Получить всех пользователей
     *
     * @param Got $request
     */
    public function getV2(Got $request)
    {
        $fields = ['*'];
        $model = new User();
        $filter = false;

        if ($request->has('only_archive') && $request->only_archive) {
            $model = $model::withTrashed();
            $model = $model->whereNotNull('deleted_at');
        }
        if( $request->has('role') ) {
            $filter = true;
            $model = $model->where('role', $request->role);
        } else {
            $model = $model->where(function(Builder $query) {
                $query->where('role', 1)->orWhere('role', 2);
            });
        }

        if( $request->has('sort') ) {
            $model = $model->orderBy('created_at', $request->sort == 1 ? 'DESC' : 'ASC');
        }



        if ($request->has('fields')) {
            $fields = trim($request->fields);
            $fields = preg_replace('/\s+/', '', $fields);
            $fields = explode(',', $fields);
        }

        if( !$request->has('search') || empty($request->search) ) {

        } else {
            $filter = true;
            $search = $request->search;
            $model = $model->where(function(Builder $query) use ($search){
                $query
                    ->where('name', 'ILIKE', '%'.$search.'%')
                    ->orWhere('phone', 'ILIKE', '%'.$search.'%')
                ;
            });
        }

        if (!$filter) {
            $total = User::count();
        } else {
            $total = $model->count();
        }
        $result = [];

        if ($request->has('page')) {
            $offset = (int)$request->page - 1;
            $offset = ($offset == 0) ? 0 : $offset + ((int)$request->limit - 1);
            $model = $model->offset($offset);
            $model = $model->limit((int)$request->limit);
            $result['pagination'] = [
                'total' => $total,
                'offset' => $offset + 1,
                'limit' => (int)$request->limit,
                'page_available' => ceil($total / (int)$request->limit)
            ];
        }

        $model = $model->get($fields)->toArray();

        foreach ($model as $key=>$item) {
            $model[$key]['created_at'] = Carbon::createFromTimeString($item['created_at'])->format('d.m.Y в H:i');
            $model[$key]['created_at_timestamp'] = Carbon::createFromTimeString($item['created_at'])->timestamp;
        }

        $model = collect($model);
        $result['data'] = $model;


        $this->response()->success()->setMessage('Сотрудники получены')->setData($result)->send();
    }

    /**
     * Редактировать сотрудника
     *
     * @param Update $request
     */
    public function update(Update $request)
    {
        $input = $request->except('user_id');
        $user = User::where('id', $request->user_id)->first();
        $updated = User::where('id', $request->user_id)->update($input);

        if( $request->has('name') ) {
            Applications::where('responsibility', 'LIKE', '%' . $user->id . ',' . $user->name . '%')->update([
                'responsibility' => $user->id .','.$request->name
            ]);
        }

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

        Mail::to($user['email'])->send(new CreateEmployee(
            email: $user['email'],
            password: $password,
            role: $role,
            isNewPassword: true
        ));

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
        if( auth()->user()->id == $request->user_id )
            $this->response()->error()->setMessage('Невозможно удалить самого себя')->send();


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
