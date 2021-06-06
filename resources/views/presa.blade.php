<link rel="stylesheet" href="{{ asset('assets/pdf/main.css') }}">

<div class="book">
    {{--    <div class="page first-page" id="title">--}}

    {{--        <div>--}}
    {{--            <img src="{{ asset('assets/img/transparent_background.png') }}" class="transparent-background" alt="transparent"/>--}}
    {{--        </div>--}}
{{--            <div>--}}
{{--                <img src="{{ asset('assets/img/Logo.png') }}" class="logo-full" alt="Logo"/>--}}
{{--            </div>--}}

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
                    <td class="left">Страна: </td>
                    <td class="right">Россия</td>
                </tr>
            </table>
        </div>

    </div>

    <div class="page background" id="hello">
        <img src="{{ asset('assets/img/White.png') }}" class="transparent-background" alt="transparent"/>
    </div>
</div>
