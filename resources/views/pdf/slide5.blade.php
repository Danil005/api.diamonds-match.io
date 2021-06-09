<link rel="stylesheet" href="{{ asset('assets/pdf/main2.css') }}">

<div class="book">
    <div class="page background" id="hello">
        <img src="{{ asset('assets/img/White.png') }}" class="transparent-background" alt="transparent"/>

        <div>
            <img src="{{ asset('assets/img/Logo.png') }}" class="logo-right" alt="Logo"/>
        </div>

        <div class="circle-title">
            <div class="circle-no-center"></div>
            <span class="circle-text">{{ __('pptx.slide5.t1.t') }}</span>
        </div>

        <div class="center" style="margin-left: 100px">
            <table class="info-table" style="table-layout: fixed; width: 450px">
                <tr style="margin-top: 20px">
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide5.t1.p1') }}</td>
                    <td class="right" style="width: 300px">{{ $q['pets'] ?? __('pptx.empty') }}</td>
                </tr>
                <tr style="margin-top: 20px">
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide5.t1.p2') }}</td>
                    <td class="right" style="width: 300px">{{ $q['have_pets'] ?? __('pptx.empty') }}</td>
                </tr>
                <tr style="margin-top: 20px">
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide5.t1.p3') }}</td>
                    <td class="right" style="width: 300px">{{ $q['films_or_books'] ?? __('pptx.empty') }}</td>
                </tr>
                <tr style="margin-top: 20px">
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide5.t1.p4') }}</td>
                    <td class="right" style="width: 300px">{{ $q['relax'] ?? __('pptx.empty') }}</td>
                </tr>
                <tr style="margin-top: 20px">
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide5.t1.p5') }}</td>
                    <td class="right" style="width: 300px">{{ $q['countries_was'] ?? __('pptx.empty') }}</td>
                </tr>
                <tr style="margin-top: 20px">
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide5.t1.p6') }}</td>
                    <td class="right" style="width: 300px">{{ $q['countries_dream'] ?? __('pptx.empty') }}</td>
                </tr>
                <tr style="margin-top: 20px">
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide5.t1.p7') }}</td>
                    <td class="right" style="width: 300px">{{ $q['best_gift'] ?? __('pptx.empty') }}</td>
                </tr>
                <tr style="margin-top: 20px">
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide5.t1.p8') }}</td>
                    <td class="right" style="width: 300px">{{ $q['hobbies'] ?? __('pptx.empty') }}</td>
                </tr>
                <tr style="margin-top: 20px">
                    <td class="left" style="margin-top: 50px">{{ __('pptx.slide5.t1.p9') }}</td>
                    <td class="right" style="width: 300px">{{ $q['kredo'] ?? __('pptx.empty') }}</td>
                </tr>
            </table>
        </div>


        <div class="circle-title">
            <div class="circle-no-center"></div>
            <span class="circle-text">{{ __('pptx.slide5.t2.t') }}</span>
        </div>

        <div class="answers">
            <div class="question">{{ __('pptx.slide5.t2.p1') }}</div>
            <div class="answer">{{ $q['features_repel'] ?? __('pptx.empty') }}</div>

            <div class="question">{{ __('pptx.slide5.t2.p2') }}</div>
            <div class="answer">{{ $q['age_difference'] ?? __('pptx.empty') }}</div>

            <div class="question">{{ __('pptx.slide5.t2.p3') }}</div>
            <div class="answer">{{ $q['talents'] ?? __('pptx.empty') }}</div>
        </div>
    </div>
</div>
