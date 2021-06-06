<link rel="stylesheet" href="{{ asset('assets/pdf/main.css') }}">

<div class="book">
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
</div>