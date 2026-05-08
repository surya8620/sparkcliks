@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="dashboard-table__header d-flex justify-content-end pt-0 px-0">
        <div class="dashboard-table__btn">
            <a href="#" class="btn btn-outline--base btn--sm">
                <i class="las la-list"></i> @lang('My Advertisements')
            </a>
        </div>
    </div>
    <div class="card custom--card">
        <div class="card-body">
            <form class="dashboard-form" role="form" method="POST" action="{{ route('user.web.update', $order->id) }}{{ route('user.ptc.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="form-group col-md-12">
                        <label class="form--label">@lang('Title of the Ad')</label>
                        <input type="text" name="title" class="form-control form--control form-control form--control-lg" value="{{ old('title') }}"
                            required>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="ads_type" class="form--label">@lang('Advertisement Type')</label>
                        <select class="form--control select2 form-control" id="ads_type" name="ads_type" required data-minimum-results-for-search="-1">
                            <option value="1" {{ old('ads_type') == 1 ? 'selected' : '' }}>@lang('Link / URL')</option>
                            <option value="2" {{ old('ads_type') == 2 ? 'selected' : '' }}>@lang('Banner / Image')</option>
                            <option value="3" {{ old('ads_type') == 3 ? 'selected' : '' }}>@lang('Script / Code')</option>
                            <option value="4" {{ old('ads_type') == 4 ? 'selected' : '' }}>@lang('Youtube Embeded Link')</option>
                        </select>
                        <pre class="text--danger">@lang('Price per ad') <span class="price-per-ad"></span> {{ __(gs('cur_text')) }}</pre>
                    </div>
                    <div class="form-group col-md-8" id="websiteLink">
                        <label class="form--label">@lang('Link')</label>
                        <input type="text" name="website_link" class="form-control form--control" value="{{ old('website_link') }}"
                            placeholder="@lang('http://example.com')">
                    </div>
                    <div class="form-group col-md-8" id="youtube">
                        <label class="form--label">@lang('Youtube Embeded Link')</label>
                        <input type="text" name="youtube" class="form-control form--control" value="{{ old('youtube') }}"
                            placeholder="@lang('https://www.youtube.com/embed/your_code')">
                    </div>
                    <div class="form-group col-md-8 d-none" id="bannerImage">
                        <label class="form--label">@lang('Banner')</label>
                        <input type="file" class="form-control form--control" name="banner_image">
                    </div>
                    <div class="form-group col-md-8 d-none" id="script">
                        <label class="form--label">@lang('Script')</label>
                        <textarea name="script" class="form-control form--control" rows="5">{{ old('script') }}</textarea>
                    </div>

                    <div class="form-group col-md-6">
                        <label class="form--label">@lang('Duration')</label>
                        <div class="input-group">
                            <input type="number" name="duration" class="form-control form--control" value="{{ old('duration') }}" required>
                            <div class="input-group-text">@lang('SECONDS')</div>
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label class="form--label">@lang('Maximum Show')</label>
                        <div class="input-group">
                            <input type="number" name="max_show" class="form-control form--control" value="{{ old('max_show') }}" required>
                            <div class="input-group-text">@lang('Times')</div>
                        </div>
                        <pre class="text--danger"><span class="total-price"></span> {{ __(gs('cur_text')) }} @lang(' will cut from your balance')</pre>
                    </div>
                </div>

                <div class="pt-5 mt-5 border-top">

                    <div class="d-flex justify-content-between mb-4 align-items-center">
                        <h5>@lang('Ad Sechule')</h5>
                        <button type="button" class="btn btn-outline--base btn--sm scheduleBtn"><i class="la la-plus"> </i>@lang('Add')</button>
                    </div>

                    <div id="rowFields"></div>

                </div>

                <div class="form-group col-md-12">
                    <button type="submit" class="btn btn--base btn--lg w-100">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
@endsection


@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";


            var price = 0
            $('#ads_type').on("change", function() {
                var adType = $(this).val();
                if (adType == 1) {
                    $("#websiteLink").removeClass('d-none');
                    $("#bannerImage").addClass('d-none');
                    $("#script").addClass('d-none');
                    $("#youtube").addClass('d-none');
                    price = {{ @gs('ads_setting')->ad_price->url ?? 0 }}
                } else if (adType == 2) {
                    $("#bannerImage").removeClass('d-none');
                    $("#websiteLink").addClass('d-none');
                    $("#script").addClass('d-none');
                    $("#youtube").addClass('d-none');
                    price = {{ @gs('ads_setting')->ad_price->image ?? 0 }}
                } else if (adType == 3) {
                    $("#bannerImage").addClass('d-none');
                    $("#websiteLink").addClass('d-none');
                    $("#script").removeClass('d-none');
                    $("#youtube").addClass('d-none');
                    price = {{ @gs('ads_setting')->ad_price->script ?? 0 }}
                } else {
                    $("#bannerImage").addClass('d-none');
                    $("#websiteLink").addClass('d-none');
                    $("#script").addClass('d-none');
                    $("#youtube").removeClass('d-none');
                    price = {{ @gs('ads_setting')->ad_price->youtube ?? 0 }}
                }
                $('.price-per-ad').text(price);
                $('[name=max_show]').trigger('input');
            }).change();

            $('[name=max_show]').on('input', function() {
                var maxShow = $(this).val();
                var totalPrice = price * maxShow;
                $('.total-price').text(totalPrice);
            });

            var item = 0;

            $('.scheduleBtn').on('click', function() {
                var html = `
                <div class="form-group">
                    <div class="row gy-4">
                        <div class="col-md-4">
                            <select name="schedule[${item}][day]" class="form--control" required>
                                <option value="sunday">@lang('Sunday')</option>
                                <option value="monday">@lang('Monday')</option>
                                <option value="tuesday">@lang('Tuesday')</option>
                                <option value="wednesday">@lang('Wednesday')</option>
                                <option value="thursday">@lang('Thursday')</option>
                                <option value="friday">@lang('Friday')</option>
                                <option value="saturday">@lang('Saturday')</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="time" name="schedule[${item}][start]" class="form-control form--control" required>
                        </div>
                        <div class="col-md-3">
                            <input type="time" name="schedule[${item}][end]" class="form-control form--control" required>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline--danger btn--lg w-100 h-45 scheduleClose"><i class="la la-times"></i></button>
                        </div>
                    </div>
                </div>
            `;

                item += 1;

                $('#rowFields').append(html);
            });

            $(document).on('click', '.scheduleClose', function() {
                $(this).closest('.form-group').remove();
            });

        })(jQuery);
    </script>
@endpush
