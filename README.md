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
|ethnicity| no_matter(Не важно) \| caucasoid(Европеоид) \| asian(Азиат) \| dark_skinned(Темно-кожый) \| hispanic (Латиноамериканец) \| indian(Индиец) \| native_middle_east(Выходец из стран Ближнего Востока) \| mestizo(Метис, родители принадлежат к разным расам) \| native_american(Представитель коренного населения Америки) \| islands(Представитель коренного населения островов \| Тихого Океана / Австралии / абориген) \| other(Иная этническая принадлежность) | Энтно-принадлежность | Да
|body_type| any(Любое) \| athletic(Атлетичное) \| slim(Стройное) \| hourglass(Песочные часы) \| full(Полный) | Телосложение | Да
|chest| any(Любая) \| big(Большая) \| middle(Средняя) \| small(маленькая) | Размер груди | Нет
|booty| any(Любая) \| big(Большая) \| middle(Средняя) \| small(маленькая) | Размер попы | Нет
|hair_color| any(Любой) \| brunette(Брюнет) \| blonde(Блондин) \| redhead(Рыжий) \| brown-haired(Шатенка) | Цвет волос| Да
|hair_length| any(Любая) \| short(Короткая) \| long(Длинная) | Длина волос | Нет
|eye_color| any(Любой) \| blue(Голубой) \| gray(Серый) \| green(Зеленый) \| brown(Карий) | Цвет глаз | Да

Пример конечного варианта:
```json
{
    "partner_appearance": {
        "sex": "female",
        "ethnicity": "no_matter",
        "body_type": "slim",
        "chest": "any",
        "hair_color": "blonde",
        "hair_length": "long",
        "eye_color": "blue"
    },
}
```

###### personal_qualities_partner

Тут необходимо вводим массивом один из двух параметров. То есть по индексам.

Нужно будет выбрать какой-то один из параметров. Например:

Нам доступно calm или energetic, то выбираем что-то одно.

`personal_qualities_partner: [calm, modest, purposeful]`

Как видите, параметры были выбраны и внесены таким образом.

##### А теперь доступные параметры:

|Параметр|Тип|Описание|Обязательный|
|--|--|--|--|
|calm \| energetic|string|Спокойная или энергичный|Да
|happy \| modest|string|Веселушка или Скромная|Да
|purposeful \| weak-willed|string|Целеустремленная или Безвольная|Да
|self \| dependent|string|Самостоятельная или Зависящая|Да
|feminine \| courageous \| confident|string|Женственная или Мужественный или Уверенная в себе|Да
|delicate \| live_here_now |string|Нежная или Умеющая жить здесь и сейчас|Да
|pragmatic \| graceful |string|Прагматичная или Грациозная|Да
|sociable \| smiling |string|Общительная или Улыбчивая|Да
|housewifely \| ambitious |string|Хозяйственная или Амбициозная|Да
|artistic \| good |string|Артистичная или Добрая|Да
|aristocratic \| stylish |string|Аристократическая или Стильная|Да
|economical \| business |string|Экономная или Деловая|Да
|sports \| fearless |string|Спортивная или Бесстрашная|Да
|shy \| playful |string|Застенчивая или Игривая|Да

Пример конечного варианта:
```json
{
    "personal_qualities_partner": [
        "calm", "happy", "purposeful", "self",
        "feminine", "live_here_now", "graceful", "sociable",
        "housewifely", "artistic", "stylish", "business",
        "sports", "playful"
    ],
}
```

###### partner_information

Информация о партнерше

|Параметр|Тип|Описание|Обязательный|
|--|--|--|--|
|age|array[integer, integer]|Возраст от и до|Да
|place_birth|string|Место рождения|Да
|city|string|Город проживания|Да
|zodiac_signs|string: aries(Овен) \| calf(Телец) \| twins(Близнецы) \| cancer(Рак) \| lion(Лев) \| virgo(Дева) \| libra(Весы) \| scorpio(Скорпион) \| sagittarius(Стрелец) \| capricorn(Козерог) \| aquarius(Водолей) \| fish(Рыба) |Знак зодиака|Да
|height|array[integer\|double, integer|\double]|Рост от и до|Да
|weight|array[integer\|double, integer|\double]|Вес от и до|Да
|marital_status|string: one(Один) \| divorced(Разведен) \| widow(Вдова) |Семейное положение(Статус)|Да
|languages|array[...string]|На каком языке говорит|Да
|moving_country|boolean|Согласен ли переезжать в другую страну|Да
|moving_city|boolean|Согласен ли переезжать в другой город|Да
|children|boolean|Есть ли дети|Да
|children_count|string|Кол-во детей|Нет
|children_desire|string: yes(Да) \| no(Нет) \| maybe(Возможно)|Хотят ли детей|Да
|smoking|string|Отношение к курение|Да
|alcohol|string|Отношение к алкоголю|Да
|religion|string|Религия и ее позиция|Да
|sport|string|Отношение к спорту|Да

Пример конечного варианта:
```json
{
    "partner_information": {
        "age": [18, 25],
        "place_birth": "Ейск",
        "city": "Ростов-на-Дону",
        "zodiac_signs": "aries",
        "height": [150, 190.00],
        "weight": [45, 60],
        "marital_status": "one",
        "languages": ["Русский"],
        "moving_country": true,
        "moving_city": true,
        "children": true,
        "children_count": "1",
        "children_desire": "yes",
        "smoking": "Иногда курю",
        "alcohol": "Могу иногда выпить",
        "religion": "Православный",
        "sport": "Занимаюсь"
    },
}
```

###### test

Выполнение теста. Все ключи идут по порядку как в фигме.

Нужно выбрать ответ от 0 до `(кол-во ответов - 1)`: lies имеет 4 ответ, можно выбрать от 0 до 3 (0,1,2,3)

|Параметр|Описание по первому ответу|Кол-во ответов
|--|--|--|
|lies|Считаю необходимым говорить партнеру практически обо всем|4
|intervention|Я не потреплю излишнего вмешательства партнера в мое личное пространство|3
|value|Достижение, успех, богатство, статус|7
|life|Удовольствие и праздник|3
|motive_marriage|Дети, родительство, следование традициям, продолжение рода|3
|family_atmosphere|Комфорт, спокойствие, понимание, «тихая гавань»|3
|position_sex|Мне нравится находить то, что доставляет сексуальное удовольствие моему партнеру|4
|books|Классику, философию, научную литературу|4
|friends|Я считаю что с моим/моей избранником/ей наши друзья должны стать общими и встречаться с ними мы должны вместе|3
|leisure|Шумные вечеринки, светские рауты, роскошные рестораны|3
|discussion_feelings|Я считаю необходимым открыто проявлять и обсуждать с партнером свои и его/ее чувства|3
|work_relationship|Муж полностью обеспечивает семью, а жена работать не должна|4
|family_decisions|Муж принимает основные важные решения, жена соглашается|4
|consent|Мне очень важно получать одобрение от моего партнера и согласие с моей точкой зрения|3
|interests_partner|Мне важно разделять с партнером интересы друг друга|4
|first_place_relationship|Прикосновения, объятия,  нежность, секс, ласка|5
|position_society|Я всегда нуждаюсь быть в центре внимания, эпатировать, выделяться, восхищать. Я коммуникабельный и энергичный человек|7
|conflicts|Я стараюсь уклоняться и избегать конфликтов|5
|cleanliness|Для меня принципиально важны чистота и порядок, расположение вещей на своих местах. Мой партнер должен этот порядок соблюдать|4
|clear_plan|Привлекает меня|2
|conflict_behavior|Меня легко вывести из себя и вовлечь в конфликт. Я бываю вспыльчив, не люблю уступать и быстро  «загораюсь», но также быстро «остываю»|4

Пример конечного варианта:
```json
{
    "test": {
        "lies": 2,
        "intervention": 0,
        "value": 1,
        "life": 2,
        "motive_marriage": 0,
        "family_atmosphere": 0,
        "position_sex": 0,
        "books": 0,
        "friends": 3,
        "leisure": 1,
        "discussion_feelings": 0,
        "work_relationship": 0,
        "family_decisions": 0,
        "consent": 1,
        "interests_partner": 0,
        "first_place_relationship": 0,
        "position_society": 0,
        "conflicts": 0,
        "cleanliness": 0,
        "clear_plan": 0,
        "conflict_behavior": 0
    },
}
```


###### my_appearance

Моя внешность. То же самое, что что и `partner_appearance`

Пример конечного варианта:
```json
{
    "my_appearance": {
        "sex": "male",
        "ethnicity": "no_matter",
        "hair_color": "blonde",
        "eye_color": "blue",
        "body_type": "athletic"
    },
}
```

###### my_personal_qualities

Почти то же самое, что и `personal_qualities_partner`, за исключение того, 
что передаются все параметры, которые там указаны с ключем и значение boolean.

#### Кол-во значение `true` должно быть ровно 7!

Пример конечного варианта:
```json
{
    "my_personal_qualities": {
        "calm": true,
        "energetic": false,
        "happy": true,
        "modest": true,
        "purposeful": true,
        "weak-willed": true,
        "self": true,
        "dependent": true,
        "feminine": true,
        "courageous": false,
        "confident": false,
        "delicate": true,
        "live_here_now": false,
        "pragmatic": true,
        "graceful": false,
        "sociable": false,
        "smiling": true,
        "housewifely": false,
        "ambitious": false,
        "artistic": false,
        "good": false,
        "aristocratic": false,
        "stylish": false,
        "economical": false,
        "business": false,
        "sports": false,
        "fearless": false,
        "shy": false,
        "playful": false
    },
}
```

###### my_information

Информация обо мне включает то же самое, что и `partner_information`, но имеет еще доп.
обязательные параметры и некоторые изменения других параметров, таких как:
`height` и `weight`

|Параметр|Тип|Описание|Обязательный|
|--|--|--|--|
|name|string|ФИО|Да
|height|integer \| double | Мой Рост | Да
|weight|integer \| double | Мой Вес | Да
|education|string| Образование | Да
|work|string|Моя работа | Да
|salary|Зарплата|double /| integer| Да
|health_problems|string|Проблемы со здоровьем | Да
|allergies|string|Аллергии| Да
|pets|string|Отношение к животным| Да
|have_pets|string|Какие животные есть или нравятся| Да
|films_or_books|string|Фильмы или книги?|Да
|relax|string|Как отдыхаете|Да
|countries_was|string|В каких странах были|Да
|countries_dream|string|В какие страны бы хотели|Да
|best_gift|string|Лучший подарок для вас|Да
|hobbies|string|Ваши хобби|Да
|kredo|string|Ваше кредо|Да
|features_repel|string|Черты характера, которые отталкивают|Да
|age_difference|string|Разница в возрасте|Да
|films|string|Любимые фильмы|Да
|songs|string|Любимые песни|Да
|ideal_weekend|Идеальные выходные|Да
|sleep|string|Сова или жаворонок|Да
|doing_10|string|Занятия через 10 лет|Да
|signature_dish|string|Ваше любимое блюдо|Да
|clubs|string|Любите клубы?|Да
|best_gift_received|string|Лучший подарок, который вы получали|Да
|talents|string|Ваши таланты

Пример конечного варианта:
```json
{
    "my_information": {
        "name": "Данил",
        "place_birth": "Ейск",
        "city": "Ростов-на-Дону",
        "age": 21,
        "zodiac_signs": "aries",
        "height": 191.2,
        "weight": 76,
        "marital_status": "one",
        "languages": ["Русский"],
        "moving_city": true,
        "moving_country": true,
        "children": false,
        "children_desire": "yes",
        "smoking": "Иногда курю",
        "alcohol": "Могу иногда выпить",
        "religion": "Православный",
        "sport": "Занимаюсь",
        "education": "ЮФУ",
        "work": "ЮФУ",
        "salary": "100 000",
        "health_problems": "Нет",
        "allergies": "Нет",
        "pets": "Люблю",
        "have_pets": "Да",
        "films_or_books": "Фильмы",
        "relax": "гулять",
        "countries_was": "Россия",
        "countries_dream": "Все",
        "best_gift": "Я",
        "hobbies": "Бизнес",
        "kredo": "Какое-то",
        "features_repel": "Что-то отталкивает",
        "age_difference": 4,
        "films": "Какой-то фильм",
        "songs": "Резать океаны",
        "ideal_weekend": "Какой-то",
        "sleep": "Сова",
        "doing_10": "Что-то",
        "signature_dish": "Бутеры с сыром",
        "clubs": "Не люблю",
        "best_gift_received": "Какой-то",
        "talents": "Какие-то"
    }
}
```


### В случае, если допустили ошибку, программа скажет сама, чего не хватает или то, что вы сделали неверно.


###### Пример полного Body:
```json
{
    "partner_appearance": {
       "sex": "female",
       "ethnicity": "no_matter",
       "body_type": "slim",
       "chest": "any",
       "hair_color": "blonde",
       "hair_length": "long",
       "eye_color": "blue"
    },
    "personal_qualities_partner": [
        "calm", "happy", "purposeful", "self",
        "feminine", "live_here_now", "graceful", "sociable",
        "housewifely", "artistic", "stylish", "business",
        "sports", "playful"
    ],
    "partner_information": {
        "age": [18, 25],
        "place_birth": "Ейск",
        "city": "Ростов-на-Дону",
        "zodiac_signs": "aries",
        "height": [150, 190.00],
        "weight": [45, 60],
        "marital_status": "one",
        "languages": ["Русский"],
        "moving_country": true,
        "moving_city": true,
        "children": true,
        "children_count": "1",
        "children_desire": "yes",
        "smoking": "Иногда курю",
        "alcohol": "Могу иногда выпить",
        "religion": "Православный",
        "sport": "Занимаюсь"

    },
    "test": {
        "lies": 0,
        "intervention": 0,
        "value": 0,
        "life": 0,
        "motive_marriage": 0,
        "family_atmosphere": 0,
        "position_sex": 0,
        "books": 0,
        "friends": 0,
        "leisure": 0,
        "discussion_feelings": 0,
        "work_relationship": 0,
        "family_decisions": 0,
        "consent": 0,
        "interests_partner": 0,
        "first_place_relationship": 0,
        "position_society": 0,
        "conflicts": 0,
        "cleanliness": 0,
        "clear_plan": 0,
        "conflict_behavior": 0
    },
    "my_appearance": {
       "sex": "male",
       "ethnicity": "no_matter",
       "hair_color": "blonde",
       "eye_color": "blue",
       "body_type": "athletic"
    },
    "my_personal_qualities": {
        "calm": true,
        "energetic": false,
        "happy": true,
        "modest": true,
        "purposeful": true,
        "weak-willed": true,
        "self": true,
        "dependent": true,
        "feminine": true,
        "courageous": false,
        "confident": false,
        "delicate": true,
        "live_here_now": false,
        "pragmatic": true,
        "graceful": false,
        "sociable": false,
        "smiling": true,
        "housewifely": false,
        "ambitious": false,
        "artistic": false,
        "good": false,
        "aristocratic": false,
        "stylish": false,
        "economical": false,
        "business": false,
        "sports": false,
        "fearless": false,
        "shy": false,
        "playful": false
    },
    "my_information": {
        "name": "Данил",
        "place_birth": "Ейск",
        "city": "Ростов-на-Дону",
        "age": 21,
        "zodiac_signs": "aries",
        "height": 191.2,
        "weight": 76,
        "marital_status": "one",
        "languages": ["Русский"],
        "moving_city": true,
        "moving_country": true,
        "children": false,
        "children_desire": "yes",
        "smoking": "Иногда курю",
        "alcohol": "Могу иногда выпить",
        "religion": "Православный",
        "sport": "Занимаюсь",
        "education": "ЮФУ",
        "work": "ЮФУ",
        "salary": "100 000",
        "health_problems": "Нет",
        "allergies": "Нет",
        "pets": "Люблю",
        "have_pets": "Да",
        "films_or_books": "Фильмы",
        "relax": "гулять",
        "countries_was": "Россия",
        "countries_dream": "Все",
        "best_gift": "Я",
        "hobbies": "Бизнес",
        "kredo": "Какое-то",
        "features_repel": "Что-то отталкивает",
        "age_difference": 4,
        "films": "Какой-то фильм",
        "songs": "Резать океаны",
        "ideal_weekend": "Какой-то",
        "sleep": "Сова",
        "doing_10": "Что-то",
        "signature_dish": "Бутеры с сыром",
        "clubs": "Не люблю",
        "best_gift_received": "Какой-то",
        "talents": "Какие-то"
    }
}
```

###### Возможные ошибки:

Переданы неверные данные:
```json
{
    "success": false,
    "message": "Вы должны указать верные данные для поля SEX в 'partner_appearance'",
    "data_need": [
        "female",
        "male"
    ]
}
```

Передан неверный тип:
```json
{
    "success": false,
    "message": "Неверный тип данных для 'age' в 'partner_information'",
    "need_type": [
        "type:integer",
        "type:integer"
    ]
}
```

Указаны не все данные:

```json
{
    "success": false,
    "message": "Неверные данные для поля `partner_information`",
    "field_need": [
        "marital_status"
    ]
}
```

###### Карта типов и проверки полей:

Адрес файла: `./config/app.php`

`!required` - не обязательно

```php
return [
    'questionnaire' => [
        'fields' => [
            'partner_appearance' => 'partner_appearance',
            'personal_qualities_partner' => 'personal_qualities_partner',
            'partner_information' => 'partner_information',
            'test' => 'test',
            'my_appearance' => 'my_appearance',
            'my_personal_qualities' => 'my_personal_qualities',
            'my_information' => 'my_information',
        ],
        'value' => [
            'partner_appearance' => [
                'sex' => ['female', 'male'],
                'ethnicity' => [
                    'no_matter', 'caucasoid', 'asian', 'dark_skinned',
                    'hispanic', 'indian', 'native_middle_east', 'mestizo',
                    'native_american', 'islands', 'other'
                ],
                'body_type' => ['any', 'athletic', 'slim', 'hourglass', 'full'],
                'chest' => ['!required', 'any', 'big', 'middle', 'small'],
                'booty' => ['!required', 'any', 'big', 'middle', 'small'],
                'hair_color' => ['any', 'brunette', 'blonde', 'redhead', 'brown-haired'],
                'hair_length' => ['!required', 'any', 'short', 'long'],
                'eye_color' => ['any', 'blue', 'gray', 'green', 'brown']
            ],
            'personal_qualities_partner' => [
                ['calm', 'energetic'],
                ['happy', 'modest'],
                ['purposeful', 'weak-willed'],
                ['self', 'dependent'],
                ['feminine', 'courageous', 'confident'],
                ['delicate', 'live_here_now'],
                ['pragmatic', 'graceful'],
                ['sociable', 'smiling'],
                ['housewifely', 'ambitious'],
                ['artistic', 'good'],
                ['aristocratic', 'stylish'],
                ['economical', 'business'],
                ['sports', 'fearless'],
                ['shy', 'playful'],
            ],
            'partner_information' => [
                'age' => ['type:integer', 'type:integer'],
                'place_birth' => 'string|integer',
                'city' => 'string|integer',
                'zodiac_signs' => [
                    'aries', 'calf', 'twins', 'cancer', 'lion', 'virgo',
                    'libra', 'scorpio', 'sagittarius', 'capricorn', 'aquarius',
                    'fish'
                ],
                'height' => ['type:integer|double', 'type:integer|double'],
                'weight' => ['type:integer|double', 'type:integer|double'],
                'marital_status' => ['one', 'divorced', 'widow'],
                'languages' => 'array(string|integer)',
                'moving_country' => 'boolean',
                'moving_city' => 'boolean',
                'children' => 'boolean',
                'children_count' => [...generateNumbers(), '!required'],
                'children_desire' => ['yes', 'no', 'maybe'],
                'smoking' => 'string',
                'alcohol' => 'string',
                'religion' => 'string',
                'sport' => 'string'
            ],
            'test' => [
                'lies' => [0, 1, 2, 3],
                'intervention' => [0, 1, 2],
                'value' => [0, 1, 2, 3, 4, 5, 6],
                'life' => [0, 1, 2],
                'motive_marriage' => [0, 1, 2],
                'family_atmosphere' => [0, 1, 2],
                'position_sex' => [0, 1, 2, 3],
                'books' => [0, 1, 2, 3],
                'friends' => [0, 1, 2],
                'leisure' => [0, 1, 2],
                'discussion_feelings' => [0, 1, 2],
                'work_relationship' => [0, 1, 2, 3],
                'family_decisions' => [0, 1, 2, 3],
                'consent' => [0, 1, 2],
                'interests_partner' => [0, 1, 2, 3],
                'first_place_relationship' => [0, 1, 2, 3, 4],
                'position_society' => [0, 1, 2, 3, 4, 5, 6],
                'conflicts' => [0, 1, 2, 3, 4],
                'cleanliness' => [0, 1, 2, 3],
                'clear_plan' => [0, 1],
                'conflict_behavior' => [0, 1, 2, 3],
            ],
            'my_appearance' => [
                'ethnicity' => [
                    'no_matter', 'caucasoid', 'asian', 'dark_skinned',
                    'hispanic', 'indian', 'native_middle_east', 'mestizo',
                    'native_american', 'islands', 'other'
                ],
                'body_type' => ['any', 'athletic', 'slim', 'hourglass', 'full'],
                'chest' => ['!required', 'any', 'big', 'middle', 'small'],
                'booty' => ['!required', 'any', 'big', 'middle', 'small'],
                'hair_color' => ['any', 'brunette', 'blonde', 'redhead', 'brown-haired'],
                'hair_length' => ['!required', 'any', 'short', 'long'],
                'eye_color' => ['any', 'blue', 'gray', 'green', 'brown']
            ],
            'my_personal_qualities' => [
                'calm' => 'boolean',
                'energetic' => 'boolean',
                'happy' => 'boolean',
                'modest' => 'boolean',
                'purposeful' => 'boolean',
                'weak-willed' => 'boolean',
                'self' => 'boolean',
                'dependent' => 'boolean',
                'feminine' => 'boolean',
                'courageous' => 'boolean',
                'confident' => 'boolean',
                'delicate' => 'boolean',
                'live_here_now' => 'boolean',
                'pragmatic' => 'boolean',
                'graceful' => 'boolean',
                'sociable' => 'boolean',
                'smiling' => 'boolean',
                'housewifely' => 'boolean',
                'ambitious' => 'boolean',
                'artistic' => 'boolean',
                'good' => 'boolean',
                'aristocratic' => 'boolean',
                'stylish' => 'boolean',
                'economical' => 'boolean',
                'business' => 'boolean',
                'sports' => 'boolean',
                'fearless' => 'boolean',
                'shy' => 'boolean',
                'playful' => 'boolean',
            ],
            'my_information' => [
                'name' => 'string',
                'place_birth' => 'string|integer',
                'city' => 'string|integer',
                'age' => 'integer',
                'zodiac_signs' => [
                    'aries', 'calf', 'twins', 'cancer', 'lion', 'virgo',
                    'libra', 'scorpio', 'sagittarius', 'capricorn', 'aquarius',
                    'fish'
                ],
                'height' => 'integer|double',
                'weight' => 'integer|double',
                'marital_status' => ['one', 'divorced', 'widow'],
                'languages' => 'array(string|integer)',
                'moving_country' => 'boolean',
                'moving_city' => 'boolean',
                'children' => 'boolean',
                'children_count' => [...generateNumbers(), '!required'],
                'children_desire' => ['yes', 'no', 'maybe'],
                'smoking' => 'string',
                'alcohol' => 'string',
                'religion' => 'string',
                'sport' => 'string',
                'education' => 'string',
                'work' => 'string',
                'salary' => 'string|double|integer',
                'health_problems' => 'string',
                'allergies' => 'string',
                'pets' => 'string',
                'have_pets' => 'string',
                'films_or_books' => 'string',
                'relax' => 'string',
                'countries_was' => 'string',
                'countries_dream' => 'string',
                'best_gift' => 'string',
                'hobbies' => 'string',
                'kredo' => 'string',
                'features_repel' => 'string',
                'age_difference' => 'string|integer|double',
                'films' => 'string',
                'songs' => 'string',
                'ideal_weekend' => 'string',
                'sleep' => 'string',
                'doing_10' => 'string',
                'signature_dish' => 'string',
                'clubs' => 'string',
                'best_gift_received' => 'string',
                'talents' => 'string'
            ]
        ]
    ],
];
```

---
