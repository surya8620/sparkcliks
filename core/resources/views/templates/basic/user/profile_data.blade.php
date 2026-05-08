@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="account py-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="account-form">
                        <div class="alert alert-primary mb-4 text-center" role="alert">
                            <h4 class="form-label font-weight-bold "><strong>@lang('Update Your Profile')</strong></h4>
                            <small>@lang('We need your details to process payments and invoices.')</small>
                        </div>
                        <form method="POST" action="{{ route('user.profile.data.submit') }}">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label class="form--label" style="font-size: 14px;">@lang('First Name')</label>
                                    <input class="form--control" name="firstname" type="text" placeholder="First Name"
                                        value="{{ old('firstname') }}" required>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="form--label" style="font-size: 14px;">@lang('Last Name')</label>
                                    <input class="form--control" name="lastname" type="text" placeholder="Last Name"
                                        value="{{ old('lastname') }}" required>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="form--label" style="font-size: 14px;">@lang('Address')</label>
                                    <input class="form--control" name="address" type="text" value="{{ old('address') }}" placeholder="Address">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="form--label" style="font-size: 14px;">@lang('State')</label>
                                    <input class="form--control" name="state" type="text" value="{{ old('state') }}" placeholder="State">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="form--label" style="font-size: 14px;">@lang('Zip Code')</label>
                                    <input class="form--control" name="zip" type="text" value="{{ old('zip') }}" placeholder="Zip Code">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="form--label" style="font-size: 14px;">@lang('City')</label>
                                    <input class="form--control" name="city" type="text" value="{{ old('city') }}" placeholder="City">
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form--label" style="font-size: 14px;">@lang('Country')</label>
                                        <select name="country" class="form-control form--control select2 " required>
                                            @foreach ($countries as $key => $country)
                                                <option data-mobile_code="{{ $country->dial_code }}"
                                                    value="{{ $country->country }}" data-code="{{ $key }}">
                                                    {{ __($country->country) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form--label" style="font-size: 14px;">@lang('Mobile')</label>
                                        <div class="input-group ">
                                            <span class="input-group-text mobile-code">

                                            </span>
                                            <input type="hidden" name="mobile_code">
                                            <input type="hidden" name="country_code">
                                            <input type="number" name="mobile" value="{{ old('mobile') }}"
                                                class="form-control form--control checkUser" required>
                                        </div>
                                        <small class="text--danger mobileExist"></small>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="form--label" style="font-size: 14px;">@lang('Company')</label>
                                    <input class="form--control" name="org" type="text"
                                        value="{{ old('org') }}" placeholder="Company/Org Name">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="form--label" style="font-size: 14px;">@lang('VAT/GST')</label>
                                    <input class="form--control" name="vat" type="text" placeholder="VAT/GST"
                                        value="{{ old('vat') }}">
                                </div>

                            </div>
                            <button class="btn btn--base w-100" type="submit">
                                @lang('Submit')
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('style')
    <style>
        .select2 .selection {
            width: 100% !important;
        }
    </style>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
@endpush



@push('script')
    <script>
        "use strict";
        (function($) {

            @if ($mobileCode)
                $(`option[data-code={{ $mobileCode }}]`).attr('selected', '');
            @endif
            $('.select2').select2();

            $('select[name=country]').on('change', function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
                var value = $('[name=mobile]').val();
                var name = 'mobile';
                checkUser(value, name);
            });

            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));


            $('.checkUser').on('focusout', function(e) {
                var value = $(this).val();
                var name = $(this).attr('name')
                checkUser(value, name);
            });

            function checkUser(value, name) {
                var url = '{{ route('user.checkUser') }}';
                var token = '{{ csrf_token() }}';

                if (name == 'mobile') {
                    var mobile = `${value}`;
                    var data = {
                        mobile: mobile,
                        mobile_code: $('.mobile-code').text().substr(1),
                        _token: token
                    }
                }
                if (name == 'username') {
                    var data = {
                        username: value,
                        _token: token
                    }
                }
                $.post(url, data, function(response) {
                    if (response.data != false) {
                        $(`.${response.type}Exist`).text(`${response.field} already exist`);
                    } else {
                        $(`.${response.type}Exist`).text('');
                    }
                });
            }
        })(jQuery);
    </script>
@endpush
