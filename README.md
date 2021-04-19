# API документация

### Оглавление
1. [Get Starting](#getStart)
2. [Авторизация](#auth)
3. [Методы](#methods)
    1. [Auth](#m-auth)
        1. [auth.login (Авторизация)](#auth.login)
        2. [auth.create (Создать пользователя)](#auth.create)


## 1. С чего начать? <a name="getStart"></a>
Запросы для API отправляются по адресу: 
``http://domain.ru/api/v1/<method>``

---

## 2. Авторизация <a name="auth"></a>
Авторизация несет в себе Bearer токен. В headers необходимо отправлять:

```
Authroization: Bearer <Token>
```

Он приходит после успешной авторизации пользователя.

---

### 3. Методы <a name="methods"></a>
Все доступные методы

---
#### 3.1. Auth <a name="m-auth"></a>
Пример: ``http://domain.ru/api/v1/auth/<method>``

---
##### 3.1.1. auth.login <a name="auth.login"></a>
Авторизация: ``Нет``

###### Тип: ``POST``

###### Параметры

|Параметр|Тип|Описание|Обязательный|
|--|--|--|--|
|email|string|Электронный адрес|Да|
|password|string|Пароль|Да|

###### Body:
```json
{
    "email": "danilsidorenko00@gmail.com",
    "password": "<password>"
}
```

###### Ответ:
```json
{
    "success": true,
    "message": "Вы успешно вошли в учетную запись",
    "data": {
        "token": "<token>",
        "name": "Данил Сидоренко"
    }
}
```

###### Ошибка:
```json
{
    "success": false,
    "message": "Неверный логин или пароль",
    "data": []
}
```

---
##### 3.1.1. auth.create <a name="auth.create"></a>
Авторизация: ``Да``

###### Тип: ``PUT``

Уровень доступа: ``Admin``

###### Параметры:

|Параметр|Тип|Описание|Обязательный|
|--|--|--|--|
|email|string|Электронный адрес|Да|
|name|string|Имя сотрудника|Да
|role|integer|Уровень доступа|Да
|phone|string|Номер телефона|Да

###### Доступные роли:
|Роль|Значение|
|--|--|
|client|0|
|admin|1|
|manager|2|

### ! После создания пользователя отправляется письмо с паролем для сотрудника

###### Body:
```json
{
    "name": "Данил Сидоренко",
    "role": 1,
    "email": "danilsidorenko00@yandex.ru",
    "phone": "+79181321819"
}
```

###### Ответ:
```json
{
    "success": true,
    "message": "Сотрудник был создан",
    "data": []
}
```

###### Ошибка:
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": [
            "The email has already been taken."
        ]
    }
}
```
