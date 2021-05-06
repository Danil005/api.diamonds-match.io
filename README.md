# API документация

### Оглавление
1. [Get Starting](#getStart)
2. [Авторизация](#auth)
3. [Методы](#methods)
    1. [Auth](#m-auth)
        1. [POST auth.login (Авторизация)](#auth.login)
        2. [PUT auth.create (Создать пользователя)](#auth.create)
    2. [Employee](#m-employee)
        1. [GET employee.get (Получить сотрудников)](#employee.get)
        2. [POST employee.update (Изменить сотрудника)](#employee.update)
        3. [POST employee.newPassword (Выслать новый пароль)](#employee.newPassword)
        4. [DELETE employee.archive (Архивировать)](#employee.archive)
        5. [POST employee.unarchive (Разархивировать)](#employee.unarchive)
    3. [Questionnaire](#m-questionnaire)
        1. [PUT questionnaire.create (Создать анкетку)](#questionnaire.create)


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
##### 3.1.2. auth.create <a name="auth.create"></a>
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

---
#### 3.2. Employee <a name="m-employee"></a>
Пример: ``http://domain.ru/api/v1/employee/<method>``


---
##### 3.2.1. employee.get <a name="employee.get"></a>
Авторизация: ``Да``

###### Тип: ``GET``

Уровень доступа: ``Manager``

###### Параметры:

|Параметр|Тип|Описание|Обязательный|
|--|--|--|--|
|limit|integer|Кол-во записей на странице|Нет|
|offset|integer|Кол-во пропусков записей|Нет|
|fields|string|Получить определенные поля (через запятую)|Нет|
|only_archive|boolean|Вывести только архивных сотрудников|Нет|
|search|string|Поиск по ключевым словам (Email, Имя, Номер телефона)|Нет|


###### Body:
```json
{}
```

###### Ответ:
```json
{
    "success": true,
    "message": "Сотрудники получены",
    "data": {
        "count": 3,
        "data": [
            {
                "id": 1,
                "name": "Данил Сидоренко",
                "email": "danilsidorenko00@gmail.com",
                "email_verified_at": null,
                "avatar": null,
                "phone": "918-132-1819",
                "role": 1,
                "deleted_at": null,
                "created_at": "2021-04-19T13:21:07.000000Z",
                "updated_at": "2021-04-19T13:21:07.000000Z"
            },
            {
                "id": 2,
                "name": "Данил Test",
                "email": "danilsidorenko00@yandex.ru",
                "email_verified_at": null,
                "avatar": null,
                "phone": "412-314-9212",
                "role": 1,
                "deleted_at": null,
                "created_at": "2021-04-19T13:21:43.000000Z",
                "updated_at": "2021-04-19T13:40:48.000000Z"
            },
            {
                "id": 3,
                "name": "Данил Сидоренко",
                "email": "danilsidorenko010@yandex.ru",
                "email_verified_at": null,
                "avatar": null,
                "phone": "918-132-1819",
                "role": 1,
                "deleted_at": null,
                "created_at": "2021-04-19T14:17:36.000000Z",
                "updated_at": "2021-04-19T14:17:36.000000Z"
            }
        ]
    }
}
```

###### Ошибка:
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "search": [
            "The search must be a string."
        ]
    }
}
```

---
##### 3.2.2. employee.update <a name="employee.update"></a>
Авторизация: ``Да``

###### Тип: ``POST``

Уровень доступа: ``Admin``

###### Параметры:

|Параметр|Тип|Описание|Обязательный|
|--|--|--|--|
|user_id|integer|ID-пользователя|Да|
|name|string|Имя сотрудника|Нет*|
|phone|string|Номер телефона|Нет*|
|role|integer|Роль сотрудника|Нет*|
|email|string|Электронный адрес|Нет*|

``* необязательно, если один из параметров был уже передан.``

###### Доступные роли:
|Роль|Значение|
|--|--|
|client|0|
|admin|1|
|manager|2|

###### Body:
```json
{
    "user_id": 2,
    "name": "Данил Сидоренко"
}
```

###### Ответ:
```json
{
    "success": true,
    "message": "Настройки сотрудника сохранены",
    "data": []
}
```

###### Ошибка:
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "name": [
            "The name field is required when none of phone / role / email are present."
        ],
        "phone": [
            "The phone field is required when none of name / role / email are present."
        ],
        "role": [
            "The role field is required when none of phone / name / email are present."
        ],
        "email": [
            "The email field is required when none of phone / role / name are present."
        ]
    }
}
```

---
##### 3.2.3. employee.newPassword <a name="employee.newPassword"></a>
Авторизация: ``Да``

###### Тип: ``POST``

Уровень доступа: ``Admin``

###### Параметры:

|Параметр|Тип|Описание|Обязательный|
|--|--|--|--|
|user_id|integer|ID-пользователя|Да|

### ! Отправляет письмо на почту с новым паролем

###### Body:
```json
{
    "user_id": 2
}
```

###### Ответ:
```json
{
    "success": true,
    "message": "Новый пароль был выслан на почту",
    "data": []
}
```

###### Ошибка:
```json
{
    "success": false,
    "message": "Пользователя не существует",
    "data": []
}
```

---
##### 3.2.3. employee.archive <a name="employee.archive"></a>
Авторизация: ``Да``

###### Тип: ``DELETE``

Уровень доступа: ``Admin``

###### Параметры:

|Параметр|Тип|Описание|Обязательный|
|--|--|--|--|
|user_id|integer|ID-пользователя|Да|


###### Body:
```json
{
    "user_id": 2
}
```

###### Ответ:
```json
{
    "success": true,
    "message": "Сотрудник был архивирован",
    "data": {
        "id": 2,
        "name": "Данил Сидоренко",
        "email": "danilsidorenko00@yandex.ru",
        "email_verified_at": null,
        "avatar": null,
        "phone": "412-314-9212",
        "role": 1,
        "deleted_at": null,
        "created_at": "2021-04-19T13:21:43.000000Z",
        "updated_at": "2021-04-19T14:42:09.000000Z"
    }
}
```

###### Ошибка:
```json
{
    "success": false,
    "message": "Пользователя не существует",
    "data": []
}
```

---
##### 3.2.3. employee.unarchive <a name="employee.unarchive"></a>
Авторизация: ``Да``

###### Тип: ``POST``

Уровень доступа: ``Admin``

###### Параметры:

|Параметр|Тип|Описание|Обязательный|
|--|--|--|--|
|user_id|integer|ID-пользователя|Да|


###### Body:
```json
{
    "user_id": 2
}
```

###### Ответ:
```json
{
    "success": true,
    "message": "Сотрудник был разархивирован",
    "data": {
        "id": 2,
        "name": "Данил Сидоренко",
        "email": "danilsidorenko00@yandex.ru",
        "email_verified_at": null,
        "avatar": null,
        "phone": "412-314-9212",
        "role": 1,
        "deleted_at": "2021-04-19T14:42:14.000000Z",
        "created_at": "2021-04-19T13:21:43.000000Z",
        "updated_at": "2021-04-19T14:42:14.000000Z"
    }
}
```

###### Ошибка:
```json
{
    "success": false,
    "message": "Пользователя не существует",
    "data": []
}
```

---

#### 3.2. Questionnaire <a name="m-questionnaire"></a>

### Что-ж, самая сложная часть! Удачи в подключении <3

Пример: ``http://domain.ru/api/v1/questionnaire/<method>``



---
##### 3.1.1. questionnaire.create <a name="questionnaire.create"></a>
Авторизация: ``Нет``

###### Тип: ``PUT``

###### Переименования (Не обязательно)

##### ! Если не удобные ГЛОБАЛЬНЫЕ переменные, можно их изменить!
##### ВНИМАНИЕ! Это необходимо указывать в HEADERS (Заголовке) запроса!
`Как только вы переименуете глобальные переменные, вам необходимо будет указывать те, 
которые вы указали в заголовке запроса!`

|Переменная|Что заменяет|
|--|--|
|X-PARTNER-APPEARANCE|partner_appearance
|X-PERSONAL-QUALITIES-PARTNER|personal_qualities_partner
|X-PARTNER-INFORMATION|partner_information
|X-TEST|test
|X-MY-APPEARANCE|my_appearance
|X-MY-PERSONAL-QUALITIES|my_personal_qualities
|X-MY-INFORMATION|my_information

###### Параметры

|Параметр|Тип|Описание|Обязательный|
|--|--|--|--|
|partner_appearance|Array|Глобальная переменная, внешний вид партнера|Да
|personal_qualities_partner|Array|Глобальная переменная, качества партнера|Да
|partner_information|Array|Глобальная переменная, информация по партнеру|Да
|test|Array|Глобальная переменная, тест|Да
|my_appearance|Array|Глобальная переменная, моя внешность|Да
|my_personal_qualities|Array|Глобальная переменная, мои личные качества|Да
|my_information|Array|Глобальная переменная, информация обо мне|Да

### А теперь по порядку, что в глобальных :)

###### partner_appearance

|Параметр|Тип|Описание|Обязательный|
|--|--|--|--|
|sex| female \| male | Пол партнера | Да
|ethnicity| no_matter(Не важно) \| caucasoid(Европеоид) \| asian(Азиат) \| dark_skinned(Темно-кожый) \| hispanic (Латиноамериканец) \| indian(Индиец) \| native_middle_east(Выходец из стран Ближнего Востока) \| mestizo(Метис, родители принадлежат к разным расам) \| native_american(Представитель коренного населения Америки) \| islands(Представитель коренного населения островов
Тихого Океана / Австралии / абориген) \| other(Иная этническая принадлежность) | Энтно-принадлежность | Да
|body_type| any(Любое) \| athletic(Атлетичное) \| slim(Стройное) \| hourglass(Песочные часы) \| full(Полный) | Да
|chest| any(Любая) \| big(Большая) \| middle(Средняя) \| small(маленькая) | Грудь | Нет
|booty| any(Любая) \| big(Большая) \| middle(Средняя) \| small(маленькая) | Попа | Нет
|hair_color| any(Любой) \| brunette(Брюнет) \| blonde(Блондин) \| redhead(Рыжий) \| brown-haired(Шатенка) | Цвет волос| Да
|hair_length| any(Любая) \| short(Короткая) \| long(Длинная) | Длина волос | Нет
|eye_color| any(Любой) \| blue(Голубой) \| gray(Серый) \| green(Зеленый) \| brown(Карий) | Цвет глаз | Да

---
