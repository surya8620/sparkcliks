@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="account py-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-7 col-xl-5">
                    <div class="account-form">
                        <a class="account-form__content mb-4 text-center" href="{{ route('home') }}"><img src="{{ asset(siteLogo()) }}" alt="SurfTraffic - Real Website Traffic Generator"></a>
                        <div class="account-form__content mb-4 text-center">
                            <h5 class="account-form__title mb-2">@lang('Forgot your password? No problem.')</h5>
                        </div>
                        <div class="mb-4">
                            <p>@lang('Just let us know your email address and we will email you a password reset code.')</p>
                        </div>
                        <form class="verify-gcaptcha" method="POST" action="{{ route('user.password.email') }}">
                            @csrf
                            <div class="form-group">
                                <label class="form--label">@lang('Email or Username')</label>
                                <input class="form--control" name="value" type="text" value="{{ old('value') }}" required autofocus="off">
                            </div>

                            <x-captcha />

                            <div class="form-group">
                                <button class="btn btn--base w-100" type="submit">@lang('Submit')</button>
                            </div>
                        </form>
                        <div class="col-sm-12">
                            <div class="have-account text-center">
                                <p class="have-account__text"><a class="have-account__link underline-with-text"
                                    href="{{ route('user.login') }}">@lang('Back to login')</a></p>
                                </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
