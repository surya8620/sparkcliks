@extends($activeTemplate . 'layouts.frontend')
@section('content')
@php
    $contactContent = getContent('contact.content', true);
    $contactElements = getContent('contact.element', false);
@endphp
    <div class="contact pt-120 pb-60">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-6">
                    <div class="contact-content">
                        <div class="section-heading">
                            <h2 class="section-heading__title" s-break="2" s-color="bg--green">{{ __($contactContent->data_values->heading) }}</h2>
                            <p class="section-heading__desc">
                                {{ __($contactContent->data_values->subheading) }}
                            </p>
                        </div>
                        <ul class="contact-content__list">
                            @foreach ($contactElements as $contactElemen)
                               @php
                                 $type=@$contactElemen->data_values->information_type;
                               @endphp
                                <li>
                                    @if ($type=='mailto' ||$type=='tel' )
                                    <a href="{{ $type }}:{{ $contactElemen->data_values->title}}">
                                        <img src="{{ frontendImage('contact' , $contactElemen->data_values->icon_image, '25x25') }}">
                                        {{ __($contactElemen->data_values->title) }}
                                    </a>
                                    @else
                                    <span>
                                        <img src="{{ frontendImage('contact' , $contactElemen->data_values->icon_image, '25x25') }}">
                                        {{ __($contactElemen->data_values->title) }}
                                    </span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="account-form">
                        <form class="verify-gcaptcha" method="post" >
                            @csrf
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form--label" for="name">@lang('Name')</label>
                                        <input class="form--control" name="name" type="text" value="{{ old('name', @$user->fullname) }}" @if ($user && $user->profile_complete) readonly @endif required>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form--label">@lang('Email')</label>
                                        <input class="form--control" name="email" type="email" value="{{ old('email', @$user->email) }}" @if ($user) readonly @endif required>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form--label">@lang('Subject')</label>
                                        <input class="form--control" name="subject" type="text" value="{{ old('subject') }}" required>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form--label">@lang('Message')</label>
                                        <textarea class="form--control" name="message" wrap="off" required>{{ old('message') }}</textarea>
                                    </div>
                                </div>
                                <x-captcha isCustom="true"/>
                                <div class="col-sm-12">
                                    <button class="btn btn--base mt-4" type="submit">@lang('Submit')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (@$sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif

@endsection
