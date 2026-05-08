@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $loginContent = getContent('login.content', true);
    @endphp
    <section class="account py-120" style="background-color: #41C1BA;">
        <div class="container">
            <div class="row justify-content-center gy-4">
                <div class="col-lg-6">
                    <div class="account-form">
                        <div class="account-form__content mb-4 text-center">
                            <a class="account-form__content mb-4 text-center" href="https://www.sparkcliks.com"><img
                                    src="{{ asset(siteLogo()) }}"
                                    alt="SparkCliks - Real Website Traffic Generator, Automated Website Traffic, Traffic Bot and Best CTR Solution To Rank Your Site Higher On Google."
                                    style="height: 100px;"></a>
                            <h6 class="account-form__title mb-2"> @lang('Log in to your account')</h6>
                        </div>
                        <form class="verify-gcaptcha" method="POST" action="{{ route('user.login') }}">
                            @csrf
                            <div class="row">
                                @include($activeTemplate . 'partials.social_login')
                                <div class="col-sm-12 form-group">
                                    <label class="form--label " style="font-size: 14px;"
                                        for="email">@lang('Username or Email')</label>
                                    <input class="form--control" name="username" type="text"
                                        value="{{ old('username') }}" placeholder="Enter your Username or Email" required>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="form--label" style="font-size: 14px;">@lang('Password')</label>
                                    <div class="position-relative">
                                        <input class="form--control" id="password" name="password" type="password" required
                                            placeholder="Enter your password">
                                        <span class="password-show-hide fas fa-eye toggle-password fa-eye-slash"
                                            id="#password"></span>
                                    </div>
                                </div>
                                <div class="col-sm-12 pb-3">
                                    <div class="have-account text-left">
                                        <p class="have-account__text " style="font-size: 14px;"> <a
                                                class="have-account__link underline-with-text"
                                                href="{{ route('user.password.request') }}">@lang('Forgot your Password?')</a></p>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <x-captcha />
                                </div>
                                <div class="form-group col-12">
                                    <button class="btn btn--base w-100 " style="font-size: 14px;"
                                        type="submit">@lang('Sign In')</button>
                                </div>
                                <div class="col-sm-12">
                                    <div class="have-account text-center">
                                        <p class="have-account__text " style="font-size: 14px;"> @lang('Don\'t have an account') <a
                                                class="have-account__link underline-with-text"
                                                href="{{ route('user.register') }}">@lang('Sign Up')</a></p>
                                    </div>
                                </div>
                            </div>
                        </form><br>
                        <h6 class="text-center " style="font-size: 14px;">By signing in, you agree to the SparkCliks <br> <a
                                href="https://www.sparkcliks.com/privacy-policy/"> Privacy Policy </a>, <a
                                href="https://www.sparkcliks.com/disclaimer/"> Disclaimer </a> and <a
                                href="https://www.sparkcliks.com/terms-of-use/"> Terms of Use </a>.</h6>
                        <h6 class="text-center " style="font-size: 14px;">This site is protected by reCAPTCHA and the Google
                            <br><a href="https://policies.google.com/privacy"> Privacy Policy </a> and <a
                                href="https://policies.google.com/terms"> Terms of Service </a> apply.</h6>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
