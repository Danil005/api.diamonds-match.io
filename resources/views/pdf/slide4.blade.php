<link rel="stylesheet" href="{{ asset('assets/pdf/main2.css') }}">

<div class="book">
    <div class="page background" id="hello2">
        <img src="{{ asset('assets/img/White.png') }}" class="transparent-background" alt="transparent"/>

        <div>
            <img src="{{ asset('assets/img/Logo.png') }}" class="logo-right" alt="Logo"/>
        </div>

        <div class="circle-title">
            <div class="circle-no-center"></div>
            <span class="circle-text">{{ __('pptx.slide4.t1.t') }}</span>
        </div>

        <div class="center" style="margin-left: 100px">
            <table class="info-table" style="table-layout: fixed; width: 450px">
                <tr>
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide4.t1.p1') }}</td>
                    <td class="right" style="width: 300px">{{ $q['zodiac_signs'] ?? __('pptx.empty') }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide4.t1.p2') }}</td>
                    <td class="right" style="width: 300px">{{ $q['height'] .'см' ?? __('pptx.empty') }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide4.t1.p3') }}</td>
                    <td class="right" style="width: 300px">{{ $q['weight'] .'кг' ?? __('pptx.empty') }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide4.t1.p4') }}</td>
                    <td class="right" style="width: 300px">{{ $q['body_type'] ?? __('pptx.empty') }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide4.t1.p5') }}</td>
                    <td class="right" style="width: 300px">{{ $q['hair_length'] ?? __('pptx.empty') }}</td>
                </tr>
            </table>
        </div>

        <div class="center" style="margin-left: 100px;margin-top: 50px">
            <table class="info-table" style="table-layout: fixed; width: 450px">
                <tr>
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide4.t1.p6') }}</td>
                    <td class="right" style="width: 300px">{{ $q['marital_status'] ?? __('pptx.empty') }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide4.t1.p7') }}</td>
                    <td class="right" style="width: 300px">{{ ($q['children'] ? 'Есть, '.$q['children_count'] : __('pptx.empty')) ?? __('pptx.empty') }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide4.t1.p8') }}</td>
                    <td class="right" style="width: 300px">{{ $q['children_desire'] ?? __('pptx.empty') }}</td>
                </tr>
            </table>
        </div>

        <div class="center" style="margin-left: 100px;margin-top: 50px">
            <table class="info-table" style="table-layout: fixed; width: 450px">
                <tr>
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide4.t1.p9') }}</td>
                    <td class="right" style="width: 300px">{{ $q['smoking'] ?? __('pptx.empty') }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide4.t1.p10') }}</td>
                    <td class="right" style="width: 300px">{{ $q['alcohol'] ?? __('pptx.empty') }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide4.t1.p11') }}</td>
                    <td class="right" style="width: 300px">{{ $q['religion'] ?? __('pptx.empty') }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide4.t1.p12') }}</td>
                    <td class="right" style="width: 300px">{{ ($q['languages'] == "" ? __('pptx.empty') : $q['languages']) ?? __('pptx.empty') }}</td>
                </tr>
            </table>
        </div>

        <div class="circle-title">
            <div class="circle-no-center"></div>
            <span class="circle-text">{{ __('pptx.slide4.t2.t') }}</span>
        </div>

        <div class="center" style="margin-left: 100px;margin-top: 30px">
            <table class="info-table" style="table-layout: fixed; width: 450px">
                <tr>
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide4.t2.p1') }}</td>
                    <td class="right" style="width: 300px">{{ $q['education'] ?? __('pptx.empty') }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide4.t2.p2') }}</td>
                    <td class="right" style="width: 300px">{{ ($q['work'] == 'Работаю' ? $q['work_name'] : $q['work']) ?? __('pptx.empty') }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide4.t2.p3') }}</td>
                    <td class="right" style="width: 300px">{{ $q['salary'] ?? __('pptx.empty') }}</td>
                </tr>
                <tr>
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide4.t2.p4') }}</td>
                    <td class="right" style="width: 300px">{{ $q['health_problems'] ?? __('pptx.empty') }}</td>
                </tr>

                <tr>
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide4.t2.p5') }}</td>
                    <td class="right" style="width: 300px">{{ $q['allergies'] ?? __('pptx.empty') }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
