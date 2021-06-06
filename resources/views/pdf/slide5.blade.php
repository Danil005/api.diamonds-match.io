<link rel="stylesheet" href="{{ asset('assets/pdf/main2.css') }}">

<div class="book">
    <div class="page background" id="hello">
        <img src="{{ asset('assets/img/White.png') }}" class="transparent-background" alt="transparent"/>

        <div>
            <img src="{{ asset('assets/img/Logo.png') }}" class="logo-right" alt="Logo"/>
        </div>

        <div class="circle-title">
            <div class="circle-no-center"></div>
            <span class="circle-text">ИНТЕРЕСЫ</span>
        </div>

        <div class="center" style="margin-left: 100px">
            <table class="info-table" style="table-layout: fixed; width: 450px">
                <tr style="margin-top: 20px">
                    <td class="left" style="margin-top: 50px">Любите ли вы домашних животных:</td>
                    <td class="right" style="width: 300px">{{ $country ?? 'Овен' }}</td>
                </tr>
                <tr style="margin-top: 20px">
                    <td class="left" style="margin-top: 50px">Есть ли домашние животные и какие:</td>
                    <td class="right" style="width: 300px">{{ $country ?? '189см' }}</td>
                </tr>
                <tr style="margin-top: 20px">
                    <td class="left" style="margin-top: 50px">Книги или фильмы:</td>
                    <td class="right" style="width: 300px">{{ $country ?? '50кг' }}</td>
                </tr>
                <tr style="margin-top: 20px">
                    <td class="left" style="margin-top: 50px">Отдых:</td>
                    <td class="right" style="width: 300px">{{ $country ?? 'Атлетическое' }}</td>
                </tr>
                <tr style="margin-top: 20px">
                    <td class="left" style="margin-top: 50px">Страны, в которых были:</td>
                    <td class="right" style="width: 300px">{{ $country ?? 'Блонд' }}</td>
                </tr>
                <tr style="margin-top: 20px">
                    <td class="left" style="margin-top: 50px">Страны, в которых мечтает побывать:</td>
                    <td class="right" style="width: 300px">{{ $country ?? 'Блонд' }}</td>
                </tr>
                <tr style="margin-top: 20px">
                    <td class="left" style="margin-top: 50px">Лучший подарок для вас:</td>
                    <td class="right" style="width: 300px">{{ $country ?? 'Блонд' }}</td>
                </tr>
                <tr style="margin-top: 20px">
                    <td class="left" style="margin-top: 50px">Хобби:</td>
                    <td class="right" style="width: 300px">{{ $country ?? 'Блонд' }}</td>
                </tr>
                <tr style="margin-top: 20px">
                    <td class="left" style="margin-top: 50px">Жизненное кредо:</td>
                    <td class="right" style="width: 300px">{{ $country ?? 'Блонд' }}</td>
                </tr>
            </table>
        </div>


        <div class="circle-title">
            <div class="circle-no-center"></div>
            <span class="circle-text">ОТВЕТЫ НА ВОПРОСЫ</span>
        </div>

        <div class="answers">
            <div class="question">Какие черты тебя отталкивают в людях?</div>
            <div class="answer">Какой-то ответ очень большойКакой-то ответ очень большойКакой-то ответ очень
                большойКакой-то ответ очень большойКакой-то ответ очень большойКакой-то ответ очень большой
            </div>

            <div class="question">Какие черты тебя отталкивают в людях?</div>
            <div class="answer">Какой-то ответ очень большойКакой-то ответ очень большойКакой-то ответ очень
                большойКакой-то ответ очень большойКакой-то ответ очень большойКакой-то ответ очень большой
            </div>

            <div class="question">Какие черты тебя отталкивают в людях?</div>
            <div class="answer">Какой-то ответ очень большойКакой-то ответ очень большойКакой-то ответ очень
                большойКакой-то ответ очень большойКакой-то ответ очень большойКакой-то ответ очень большой
            </div>
        </div>
    </div>
</div>
