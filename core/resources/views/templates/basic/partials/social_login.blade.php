@if (
    @gs('socialite_credentials')->linkedin->status ||
        @gs('socialite_credentials')->facebook->status == Status::ENABLE ||
        @gs('socialite_credentials')->google->status == Status::ENABLE)
    <div class="col-sm-12">


        <div class="d-flex gap-2 form-group flex-wrap">
            @if (@gs('socialite_credentials')->google->status == Status::ENABLE)
                <a class="btn btn-outline--base signup-btn googleSignupBtn  flex-fill" type="submit" href="{{ route('user.social.login', 'google') }}">
                    <img src="{{ asset($activeTemplateTrue . 'images/thumbs/google.png') }}" alt="">
                    @lang('Google')
                </a>
            @endif

            @if (@gs('socialite_credentials')->facebook->status == Status::ENABLE)
                <a class="btn btn-outline--base signup-btn flex-fill" type="submit" href="{{ route('user.social.login', 'facebook') }}">
                    <img src="{{ asset($activeTemplateTrue . 'images/thumbs/facebook.png') }}" alt="">
                    @lang('Facebook')
                </a>
            @endif

            @if (@gs('socialite_credentials')->linkedin->status == Status::ENABLE)
                <a class="btn btn-outline--base signup-btn flex-fill" type="submit" href="{{ route('user.social.login', 'linkedin') }}">
                    <img src="{{ asset($activeTemplateTrue . 'images/thumbs/linkedin.png') }}" alt="">
                    @lang('Linkdin')
                </a>
            @endif
        </div>
        <div class="form-group">
            <div class="other-option">
                <span class="other-option__text">@lang('OR')</span>
            </div>
        </div>
    </div>
@endif

@push('style')
    <style>
        .social-login-btn {
            border: 1px solid #cbc4c4;
        }
    </style>
@endpush
