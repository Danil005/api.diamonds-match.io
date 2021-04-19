<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Create;
use App\Mail\CreateEmployee;
use App\Models\User;
use App\Utils\Phone;
use App\Utils\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Str;

class AuthController extends Controller
{
    use Response, Phone;

    /**
     * @param Create $request
     */
    public function create(Create $request)
    {
        $password = Str::random(8);

        $input = [
            'name' => trim($request->name),
            'role' => $request->role,
            'email' => $request->email,
            'phone' => $this->phoneFormatting($request->phone),
            'password' => Hash::make($password)
        ];

        $role = $request->role;

        $role = match ($role) {
            1 => 'Администратор',
            2 => 'Менеджер',
            default => 'Клиент',
        };

        Mail::to($request->email)->send(new CreateEmployee(
            email: $request->email,
            password: $password,
            role: $role
        ));

        User::create($input);

        $this->response()->success()->setMessage('Сотрудник был создан')->send();
    }

    /**
     * @param Request $request
     */
    public function login(Request $request)
    {
        if( !$request->has('email') || !$request->has('password') ) {
            $this->response()->error()->setMessage('Email и Password обязательные поля')->setData([])->send();
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('Laravel Personal Access Client')->accessToken;
            $success['name'] =  $user->name;

            $this->response()->success()->setMessage('Вы успешно вошли в учетную запись')->setData($success)->send();
        } else {
            $this->response()->error()->setMessage('Неверный логин или пароль')->setData([])->send();
        }
    }
}
