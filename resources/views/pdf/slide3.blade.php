<link rel="stylesheet" href="{{ asset('assets/pdf/main.css') }}">

<div class="book">
    <div class="page background" id="hello">
        <img src="{{ asset('assets/img/White.png') }}" class="transparent-background" alt="transparent"/>

        <div style="position: relative;margin-top: -40px">
            <div class="img-rectangle"
                 style="background: url({{ asset('assets/img/photo.jpg') }}) 50% 50% no-repeat;"></div>
        </div>
        <div class="photo-square-list m-t-25">
            <img src="{{ asset('assets/img/photo.jpg') }}" class="img-square" alt="1"/>
            <img src="{{ asset('assets/img/photo.jpg') }}" class="img-square m-l-25" alt="2"/>

            <img src="{{ asset('assets/img/photo.jpg') }}" class="img-square m-t-25" alt="3"/>
            <img src="{{ asset('assets/img/photo.jpg') }}" class="img-square m-l-25" alt="4"/>
        </div>
    </div>
</div>
