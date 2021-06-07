<link rel="stylesheet" href="{{ asset('assets/pdf/main2.css') }}">

<div class="book">
    <div class="page background" id="hello">
        <img src="{{ asset('assets/img/White.png') }}" class="transparent-background" alt="transparent"/>

        <div style="position: relative;margin-top: -40px">
            <div class="img-rectangle"
                 style="background: url({{ $q['photos'][0] }}) 30% 30% no-repeat;"></div>
        </div>
        <div class="photo-square-list m-t-25">
            @if(isset($q['photos'][1]))
                <img src="{{ $q['photos'][1] }}" class="img-square" alt="1"/>
            @endif
            @if(isset($q['photos'][2]))
                <img src="{{ $q['photos'][2] }}" class="img-square m-l-25" alt="2"/>
            @endif
            @if(isset($q['photos'][3]))
                <img src="{{ $q['photos'][3] }}" class="img-square m-t-25" alt="3"/>
            @endif
            @if(isset($q['photos'][4]))
                <img src="{{ $q['photos'][4] }}" class="img-square m-l-25" alt="4"/>
            @endif
        </div>
    </div>
</div>
