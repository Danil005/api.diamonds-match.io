<link rel="stylesheet" href="{{ asset('assets/pdf/main.css') }}">

<div class="book">
{{--    <div class="page first-page" id="title">--}}

{{--        <div>--}}
{{--            <img src="{{ asset('assets/img/transparent_background.png') }}" class="transparent-background"--}}
{{--                 alt="transparent"/>--}}
{{--        </div>--}}
{{--        <div>--}}
{{--            <img src="{{ asset('assets/img/Logo.png') }}" class="logo-full" alt="Logo"/>--}}
{{--        </div>--}}

{{--        <div class="header-first">--}}
{{--            ПРЕДСТАВЬТЕ СЕБЕ БЕЗУПРЕЧНЫЕ ОТНОШЕНИЯ--}}
{{--        </div>--}}
{{--        <div class="header-last">--}}
{{--            Позвольте нам создать их!--}}
{{--        </div>--}}
{{--    </div>--}}
    <div class="page background" id="hello">
        <img src="{{ asset('assets/img/White.png') }}" class="transparent-background" alt="transparent"/>

        <div>
            <img src="{{ asset('assets/img/Logo.png') }}" class="logo-right" alt="Logo"/>
        </div>

        <div class="circle" style="height: 350px;width: 350px;"></div>
        <div class="photo">
            <img class="personPhoto" alt="" src="{{ asset('assets/img/photo.jpg') }}">
        </div>

        <div class="name">Берденникова Мария Алексеевна</div>
        <div class="age">18 лет</div>
        <div class="circle" style="height: 20px;width: 20px;"></div>
        <div class="hello">Hello!</div>

        <div class="center">
            <table class="info-table">
                <tr>
                    <td class="left" style="margin-top: 50px">Страна:</td>
                    <td class="right">{{ $country ?? '----' }}</td>
                </tr>
                <tr>
                    <td class="left">Этичность:</td>
                    <td class="right">{{ $ethnicity ?? '----' }}</td>
                </tr>
                <tr>
                    <td class="left">Место проживание:</td>
                    <td class="right">{{ $live ?? '----' }}</td>
                </tr>
                <tr>
                    <td class="left">Город рождения:</td>
                    <td class="right">{{ $birth_place ?? '----' }}</td>
                </tr>
                <tr>
                    <td class="left">Рассматриваете ли приезд:</td>
                    <td class="right">{{ $moving ?? '----' }}</td>
                </tr>
            </table>
        </div>
    </div>

{{--    <div class="page background" id="hello">--}}
{{--        <img src="{{ asset('assets/img/White.png') }}" class="transparent-background" alt="transparent"/>--}}

{{--        <div style="position: relative;margin-top: -40px">--}}
{{--            <div class="img-rectangle"--}}
{{--                 style="background: url({{ asset('assets/img/photo.jpg') }}) 50% 50% no-repeat;"></div>--}}
{{--        </div>--}}
{{--        <div class="photo-square-list m-t-25">--}}
{{--            <img src="{{ asset('assets/img/photo.jpg') }}" class="img-square" alt="1"/>--}}
{{--            <img src="{{ asset('assets/img/photo.jpg') }}" class="img-square m-l-25" alt="2"/>--}}

{{--            <img src="{{ asset('assets/img/photo.jpg') }}" class="img-square m-t-25" alt="3"/>--}}
{{--            <img src="{{ asset('assets/img/photo.jpg') }}" class="img-square m-l-25" alt="4"/>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <div class="page background" id="hello2">--}}
{{--        <img src="{{ asset('assets/img/White.png') }}" class="transparent-background" alt="transparent"/>--}}

{{--        <div>--}}
{{--            <img src="{{ asset('assets/img/Logo.png') }}" class="logo-right" alt="Logo"/>--}}
{{--        </div>--}}

{{--        <div class="circle-title">--}}
{{--            <div class="circle-no-center"></div>--}}
{{--            <span class="circle-text">ОБЩИЕ ДАННЫЕ</span>--}}
{{--        </div>--}}

{{--        <div class="center" style="margin-left: 100px">--}}
{{--            <table class="info-table" style="table-layout: fixed; width: 450px">--}}
{{--                <tr>--}}
{{--                    <td class="left" style="margin-top: 50px">Знак зодиака:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? 'Овен' }}</td>--}}
{{--                </tr>--}}
{{--                <tr>--}}
{{--                    <td class="left" style="margin-top: 50px">Рост:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? '189см' }}</td>--}}
{{--                </tr>--}}
{{--                <tr>--}}
{{--                    <td class="left" style="margin-top: 50px">Вес:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? '50кг' }}</td>--}}
{{--                </tr>--}}
{{--                <tr>--}}
{{--                    <td class="left" style="margin-top: 50px">Телосложение:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? 'Атлетическое' }}</td>--}}
{{--                </tr>--}}
{{--                <tr>--}}
{{--                    <td class="left" style="margin-top: 50px">Цвет волос:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? 'Блонд' }}</td>--}}
{{--                </tr>--}}
{{--            </table>--}}
{{--        </div>--}}

{{--        <div class="center" style="margin-left: 100px;margin-top: 50px">--}}
{{--            <table class="info-table" style="table-layout: fixed; width: 450px">--}}
{{--                <tr>--}}
{{--                    <td class="left" style="margin-top: 50px">Статус:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? 'Овен' }}</td>--}}
{{--                </tr>--}}
{{--                <tr>--}}
{{--                    <td class="left" style="margin-top: 50px">Дети:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? '189см' }}</td>--}}
{{--                </tr>--}}
{{--                <tr>--}}
{{--                    <td class="left" style="margin-top: 50px">Хотите ли детей:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? '50кг' }}</td>--}}
{{--                </tr>--}}
{{--            </table>--}}
{{--        </div>--}}

{{--        <div class="center" style="margin-left: 100px;margin-top: 50px">--}}
{{--            <table class="info-table" style="table-layout: fixed; width: 450px">--}}
{{--                <tr>--}}
{{--                    <td class="left" style="margin-top: 50px">Курение:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? 'Овен' }}</td>--}}
{{--                </tr>--}}
{{--                <tr>--}}
{{--                    <td class="left" style="margin-top: 50px">Алкоголь:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? '189см' }}</td>--}}
{{--                </tr>--}}
{{--                <tr>--}}
{{--                    <td class="left" style="margin-top: 50px">Вероисповедание:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? '50кг' }}</td>--}}
{{--                </tr>--}}
{{--                <tr>--}}
{{--                    <td class="left" style="margin-top: 50px">Владение языками:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? '50кг' }}</td>--}}
{{--                </tr>--}}
{{--            </table>--}}
{{--        </div>--}}

{{--        <div class="circle-title">--}}
{{--            <div class="circle-no-center"></div>--}}
{{--            <span class="circle-text">ОБРАЗОВАНИЕ И ЗДОРОВЬЕ</span>--}}
{{--        </div>--}}

{{--        <div class="center" style="margin-left: 100px;margin-top: 30px">--}}
{{--            <table class="info-table" style="table-layout: fixed; width: 450px">--}}
{{--                <tr>--}}
{{--                    <td class="left" style="margin-top: 50px">Образование:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? 'Овен' }}</td>--}}
{{--                </tr>--}}
{{--                <tr>--}}
{{--                    <td class="left" style="margin-top: 50px">Работа:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? '189см' }}</td>--}}
{{--                </tr>--}}
{{--                <tr>--}}
{{--                    <td class="left" style="margin-top: 50px">Зарплата:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? '50кг' }}</td>--}}
{{--                </tr>--}}
{{--                <tr>--}}
{{--                    <td class="left" style="margin-top: 50px">Проблемы со здоровьем:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? '50кг' }}</td>--}}
{{--                </tr>--}}

{{--                <tr>--}}
{{--                    <td class="left" style="margin-top: 50px">Аллергия:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? '50кг' }}</td>--}}
{{--                </tr>--}}
{{--            </table>--}}
{{--        </div>--}}
{{--    </div>--}}


{{--    <div class="page background" id="hello">--}}
{{--        <img src="{{ asset('assets/img/White.png') }}" class="transparent-background" alt="transparent"/>--}}

{{--        <div>--}}
{{--            <img src="{{ asset('assets/img/Logo.png') }}" class="logo-right" alt="Logo"/>--}}
{{--        </div>--}}

{{--        <div class="circle-title">--}}
{{--            <div class="circle-no-center"></div>--}}
{{--            <span class="circle-text">ИНТЕРЕСЫ</span>--}}
{{--        </div>--}}

{{--        <div class="center" style="margin-left: 100px">--}}
{{--            <table class="info-table" style="table-layout: fixed; width: 450px">--}}
{{--                <tr style="margin-top: 20px">--}}
{{--                    <td class="left" style="margin-top: 50px">Любите ли вы домашних животных:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? 'Овен' }}</td>--}}
{{--                </tr>--}}
{{--                <tr style="margin-top: 20px">--}}
{{--                    <td class="left" style="margin-top: 50px">Есть ли домашние животные и какие:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? '189см' }}</td>--}}
{{--                </tr>--}}
{{--                <tr style="margin-top: 20px">--}}
{{--                    <td class="left" style="margin-top: 50px">Книги или фильмы:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? '50кг' }}</td>--}}
{{--                </tr>--}}
{{--                <tr style="margin-top: 20px">--}}
{{--                    <td class="left" style="margin-top: 50px">Отдых:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? 'Атлетическое' }}</td>--}}
{{--                </tr>--}}
{{--                <tr style="margin-top: 20px">--}}
{{--                    <td class="left" style="margin-top: 50px">Страны, в которых были:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? 'Блонд' }}</td>--}}
{{--                </tr>--}}
{{--                <tr style="margin-top: 20px">--}}
{{--                    <td class="left" style="margin-top: 50px">Страны, в которых мечтает побывать:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? 'Блонд' }}</td>--}}
{{--                </tr>--}}
{{--                <tr style="margin-top: 20px">--}}
{{--                    <td class="left" style="margin-top: 50px">Лучший подарок для вас:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? 'Блонд' }}</td>--}}
{{--                </tr>--}}
{{--                <tr style="margin-top: 20px">--}}
{{--                    <td class="left" style="margin-top: 50px">Хобби:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? 'Блонд' }}</td>--}}
{{--                </tr>--}}
{{--                <tr style="margin-top: 20px">--}}
{{--                    <td class="left" style="margin-top: 50px">Жизненное кредо:</td>--}}
{{--                    <td class="right" style="width: 300px">{{ $country ?? 'Блонд' }}</td>--}}
{{--                </tr>--}}
{{--            </table>--}}
{{--        </div>--}}


{{--        <div class="circle-title">--}}
{{--            <div class="circle-no-center"></div>--}}
{{--            <span class="circle-text">ОТВЕТЫ НА ВОПРОСЫ</span>--}}
{{--        </div>--}}

{{--        <div class="answers">--}}
{{--            <div class="question">Какие черты тебя отталкивают в людях?</div>--}}
{{--            <div class="answer">Какой-то ответ очень большойКакой-то ответ очень большойКакой-то ответ очень--}}
{{--                большойКакой-то ответ очень большойКакой-то ответ очень большойКакой-то ответ очень большой--}}
{{--            </div>--}}

{{--            <div class="question">Какие черты тебя отталкивают в людях?</div>--}}
{{--            <div class="answer">Какой-то ответ очень большойКакой-то ответ очень большойКакой-то ответ очень--}}
{{--                большойКакой-то ответ очень большойКакой-то ответ очень большойКакой-то ответ очень большой--}}
{{--            </div>--}}

{{--            <div class="question">Какие черты тебя отталкивают в людях?</div>--}}
{{--            <div class="answer">Какой-то ответ очень большойКакой-то ответ очень большойКакой-то ответ очень--}}
{{--                большойКакой-то ответ очень большойКакой-то ответ очень большойКакой-то ответ очень большой--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
</div>
