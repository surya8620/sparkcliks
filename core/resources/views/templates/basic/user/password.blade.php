@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="dashboard-card custom--card">
                <div class="dashboard-card-body">
                    <h5 class="form-label font-weight-bold mb-4 text-center"><strong>@lang('Change Password')</strong></h5><hr>
                    <form class="dashboard-form"  method="post">
                        @csrf
                        <div class="form-group">
                            <label class="form--label">@lang('Current Password')</label>
                            <div class="position-relative">
                                <input class="form--control exclude" id="current-password" name="current_password" type="password" required autocomplete="current-password">
                                <span class="password-show-hide fas fa-eye toggle-password fa-eye-slash" id="#current-password"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form--label">@lang('Password')</label>
                            <div class="position-relative">
                            <input id="password" class="form--control exclude @if (gs('secure_password')) secure-password @endif" name="password" type="password" required autocomplete="current-password">
                            <span class="password-show-hide fas fa-eye toggle-password fa-eye-slash" id="#password"></span>
                        </div>
                        </div>
                        <div class="form-group">
                            <label class="form--label">@lang('Confirm Password')</label>
                            <div class="position-relative">
                            <input class="form--control exclude" id="confirm" name="password_confirmation" type="password" required autocomplete="current-password">
                            <span class="password-show-hide fas fa-eye toggle-password fa-eye-slash" id="#confirm"></span>
                        </div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn--base w-100" type="submit">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
