@php
    $registrationDisabled = getContent('register_disable.content', true);
@endphp
<div class="register-disable text-center">
    <div class="container">
        <div class="register-disable-image">
            <img class="fit-image" src="{{ frontendImage('register_disable', @$registrationDisabled->data_values->image, '280x280') }}" alt="">
        </div>
        <h5 class="register-disable-title">{{ __(@$registrationDisabled->data_values->heading) }}</h5>
        <p class="register-disable-desc">
            {{ __(@$registrationDisabled->data_values->subheading) }}
        </p>
        <div class="text-center mt-3">
            <a href="{{ @$registrationDisabled->data_values->button_url }}" class="btn btn--base">{{ __(@$registrationDisabled->data_values->button_name) }}</a>
        </div>
    </div>
</div>

@push('style')
    <style>
        header,footer,.header-top{
            display: none;
        }
        .register-disable {
            height: 100vh;
            width: 100%;
            background-color: #fff;
            color: black;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-disable-image {
            max-width: 300px;
            width: 100%;
            margin: 0 auto 32px;
        }

        .register-disable-title {
            color: rgb(0 0 0 / 80%);
            font-size: 42px;
            margin-bottom: 18px;
            text-align: center
        }

    </style>
@endpush
