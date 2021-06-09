<link rel="stylesheet" href="{{ asset('assets/pdf/main2.css') }}">

<div class="book">
    <div class="page background" id="hello">
        <img src="{{ asset('assets/img/White.png') }}" class="transparent-background" alt="transparent"/>

        <div>
            <img src="{{ asset('assets/img/Logo.png') }}" class="logo-right" alt="Logo"/>
        </div>

        <div class="circle" style="height: 350px;width: 350px;"></div>
        <div class="photo">
            <img class="personPhoto" alt="" src="{{ $q['photos'][0] }}">
        </div>

        <div class="name">{{ $q['name'] }}</div>
        <div class="age">{{ $q['age'] }}</div>
        <div class="circle" style="height: 20px;width: 20px;"></div>
        <div class="hello"></div>

        <div class="center">
            <table class="info-table">
                <tr>
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide2.p1') }}</td>
                    <td class="right">{{ explode(',', $q['city'])[0] ?? '----' }}</td>
                </tr>
                <tr>
                    <td class="left">{{ __('pptx.slide2.p2') }}</td>
                    <td class="right">{{ $q['ethnicity'] ?? '----' }}</td>
                </tr>
                <tr>
                    <td class="left">{{ __('pptx.slide2.p3') }}</td>
                    <td class="right">{{ explode(',', $q['city'])[1] ?? '----' }}</td>
                </tr>
                <tr>
                    <td class="left">{{ __('pptx.slide2.p4') }}</td>
                    <td class="right">{{ $q['place_birth'] ?? '----' }}</td>
                </tr>
                <tr>
                    <td class="left">{{ __('pptx.slide2.p5') }}</td>
                    <td class="right">{{ ($q['moving']) ?? '----' }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
