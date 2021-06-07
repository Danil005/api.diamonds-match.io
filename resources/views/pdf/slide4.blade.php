<link rel="stylesheet" href="{{ asset('assets/pdf/main2.css') }}">

<div class="book">
    <div class="page background" id="hello2">
        <img src="{{ asset('assets/img/White.png') }}" class="transparent-background" alt="transparent"/>

        <div>
            <img src="{{ asset('assets/img/Logo.png') }}" class="logo-right" alt="Logo"/>
        </div>

        <div class="circle-title">
            <div class="circle-no-center"></div>
            <span class="circle-text">ОБЩИЕ ДАННЫЕ</span>
        </div>

        <div class="center" style="margin-left: 100px">
            <table class="info-table" style="table-layout: fixed; width: 450px">
                <tr>
                    <td class="left" style="margin-top: 50px">Знак зодиака:</td>
                    <td class="right" style="width: 300px">{{ $q['zodiac_signs'] ?? 'Овен' }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">Рост:</td>
                    <td class="right" style="width: 300px">{{ $q['height'] .'см' ?? '189см' }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">Вес:</td>
                    <td class="right" style="width: 300px">{{ $q['weight'] .'кг' ?? '50кг' }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">Телосложение:</td>
                    <td class="right" style="width: 300px">{{ $q['body_type'] ?? 'Атлетическое' }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">Цвет волос:</td>
                    <td class="right" style="width: 300px">{{ $q['hair_length'] ?? 'Блонд' }}</td>
                </tr>
            </table>
        </div>

        <div class="center" style="margin-left: 100px;margin-top: 50px">
            <table class="info-table" style="table-layout: fixed; width: 450px">
                <tr>
                    <td class="left" style="margin-top: 50px">Статус:</td>
                    <td class="right" style="width: 300px">{{ $q['marital_status'] ?? 'Овен' }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">Дети:</td>
                    <td class="right" style="width: 300px">{{ ($q['children'] ? 'Есть, '.$q['children_count'] : 'Нет') ?? '189см' }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">Хотите ли детей:</td>
                    <td class="right" style="width: 300px">{{ $q['children_desire'] ?? '50кг' }}</td>
                </tr>
            </table>
        </div>

        <div class="center" style="margin-left: 100px;margin-top: 50px">
            <table class="info-table" style="table-layout: fixed; width: 450px">
                <tr>
                    <td class="left" style="margin-top: 50px">Курение:</td>
                    <td class="right" style="width: 300px">{{ $country ?? 'Овен' }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">Алкоголь:</td>
                    <td class="right" style="width: 300px">{{ $country ?? '189см' }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">Вероисповедание:</td>
                    <td class="right" style="width: 300px">{{ $country ?? '50кг' }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">Владение языками:</td>
                    <td class="right" style="width: 300px">{{ $country ?? '50кг' }}</td>
                </tr>
            </table>
        </div>

        <div class="circle-title">
            <div class="circle-no-center"></div>
            <span class="circle-text">ОБРАЗОВАНИЕ И ЗДОРОВЬЕ</span>
        </div>

        <div class="center" style="margin-left: 100px;margin-top: 30px">
            <table class="info-table" style="table-layout: fixed; width: 450px">
                <tr>
                    <td class="left" style="margin-top: 50px">Образование:</td>
                    <td class="right" style="width: 300px">{{ $country ?? 'Овен' }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">Работа:</td>
                    <td class="right" style="width: 300px">{{ $country ?? '189см' }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">Зарплата:</td>
                    <td class="right" style="width: 300px">{{ $country ?? '50кг' }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">Проблемы со здоровьем:</td>
                    <td class="right" style="width: 300px">{{ $country ?? '50кг' }}</td>
                </tr>

                <tr>
                    <td class="left" style="margin-top: 50px">Аллергия:</td>
                    <td class="right" style="width: 300px">{{ $country ?? '50кг' }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
