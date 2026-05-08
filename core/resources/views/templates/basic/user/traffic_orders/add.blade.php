@php
    $user = auth()->user();
    //WT
$creditColumnsWeb = [
101 => 'traffic_nano',
102 => 'traffic_mini',
103 => 'traffic_small',
104 => 'traffic_medium',
105 => 'traffic_large',
106 => 'traffic_ultimate',
];
$webServices = webTrafficServices();
$minWebServiceId = $webServices->min('id');
// Calculate the total available credits
$totalAvailableCreditsWeb = 0;
foreach ($webServices as $webService) {
$columnNameWeb = $creditColumnsWeb[$webService->id] ?? null;
$availableCreditsWeb = $columnNameWeb ? $user->$columnNameWeb : 0;
$totalAvailableCreditsWeb += $availableCreditsWeb;
}

@endphp
@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6 mb-4">
            @if($totalAvailableCreditsWeb > 0)
                    <form method="post" id="from-prevent-multiple-submits" action="{{ route('user.web.create', $webService->id) }}">
                        @csrf
                        <div class="modal-body">
                            <div class="form-row form-group">
                                <label for="plan" class="font-weight-bold"><strong>@lang('Campaign - Package')</strong></label>
                                <div class="col-sm-12">
                                    <select name="plan" id="plan" class="form--control form--control-lg">
                                        @foreach ($webServices as $webService)
                                                                @php
                                                                    $columnNameWeb = $creditColumnsWeb[$webService->id] ?? null;
                                                                    $availableCreditsWeb = $columnNameWeb ? $user->$columnNameWeb : 0;
                                                                @endphp

                                                                @if($availableCreditsWeb > 0)
                                                                    <option value="{{ $webService->id }}" class="font-weight-bold"
                                                                        data-title="{{ __($webService->name) }}"
                                                                        @selected($webService->id == $minWebServiceId)>
                                                                        {{ __($webService->name) }} (Available Credits - {{ $availableCreditsWeb }})
                                                                    </option>
                                                                @else
                                                                    <option value="{{ $webService->id }}" disabled
                                                                        data-title="{{ __($webService->name) }}">
                                                                        {{ __($webService->name) }} (Available Credits - {{ $availableCreditsWeb }})
                                                                    </option>
                                                                @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-row form-group">
                                <label for="title" class="font-weight-bold"><strong>@lang('Campaign Name')</strong></label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control bold" id="title" name="title"
                                        placeholder="Name of the Campaign" required>
                                </div>
                            </div>

                            <div class="form-row form-group">
                                <label for="link" class="font-weight-bold"><strong>@lang('Website URL')</strong></label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control bold" id="link" name="link"
                                        placeholder="URL of your website, eg: https://example.com" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <small
                                    class="font-weight-bold">@lang('You will have more settings after creating the campaign')</small><br>
                                <small
                                    class="font-weight-bold">@lang('The traffic will be delivered as per the Delivery Policy.')</small>
                            </div>

                            <div style="display: flex; align-items: flex-start; gap: 10px;">
                                <input type="checkbox" id="agreeCheck" name="agree" value="1" required style="margin-top: 4px;">
                                <label for="agreeCheck" style="margin: 0;">
                                    <strong>
                                        I have read and agree to the 
                                        <a href="https://www.exp.com/delivery-policy/" target="_blank">Delivery Policy</a>
                                    </strong>
                                </label>
                            </div>
                        </div>


                        <button type="submit" class="btn btn--base font-weight-bold" id="btn-save"
                            value="add">@lang('Create Campaign')</button>
                    </form>
                </div>
                <!-- Right Section () -->
                <div class="col-lg-4 mb-4">
                    <div class="col-12">
                        <div class="payment-system-list rounded shadow-sm p-4">
                            <h4 class="form-label font-weight-bold text-center">
                                <strong>@lang('Looking for Real User Engagement?')</strong></h4>
                            <div class="info-text pt-3">
                                <label class="card-text mb-2">
                                    @lang('Explore our Realistic Traffic Packs, equipped with advanced Google Analytics 4 features including User Engagement, Content Engagement, Natural Events, Scroll, Random Clicks, Engagement Metrics, and Session Time.')
                                </label>
                            </div>
                            <div class="button-container2">
                                <a href="{{ route('user.realistic.buy') }}" class="btn btn--base w-50 d-block mx-auto mb-4"
                                    data-original-title="@lang('Check Packs')">
                                    @lang('Check Packs')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                <div class="col-lg-12 col-md-3 mb-4">
                    <div class="card card-deposit text-center">
                        <div class="card-body card-body-deposit">
                            {{--<img class="card-img-top" src="#" alt="Card image cap">--}}
                            <h5 class="card-title font-weight-bold text--danger">You're Out of Credits!</h5>
                            <p class="card-text text--danger">Please purchase credits to create a new campaign.</p>
                        </div>
                        <div class="card-footer">
                            <div class="d-grid gap-3 col-6 mx-auto">
                                <a href="{{ route('user.web.buy') }}"
                                    class="btn  btn--base btn-block custom-success deposi orderBtn font-weight-bold"
                                    data-original-title="@lang('Buy Credits')">
                                    Buy Credits
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

    </div>


@endsection

@push('style')
    <style>
        .break_line {
            white-space: initial !important;
        }
    </style>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";
            $('.orderBtn').on('click', function () {
                var modal = $('#orderModal');
                var plan = $(this).data('plan');
                var url = $(this).data('url');
                var title = $(this).data('title');

                modal.find('form').attr('action', url);
                modal.find('input[name=plan]').val(plan);
                modal.find('input[name=title]').val(title);
                modal.modal('show');

            });

            //Scroll to paginate position
            var pathName = document.location.pathname;
            window.onbeforeunload = function () {
                var scrollPosition = $(document).scrollTop();
                sessionStorage.setItem("scrollPosition_" + pathName, scrollPosition.toString());
            }
            if (sessionStorage["scrollPosition_" + pathName]) {
                $(document).scrollTop(sessionStorage.getItem("scrollPosition_" + pathName));
            }
            $('#from-prevent-multiple-submits').on('submit', function () {
                $("#btn-save", this)
                    .html("Please wait...")
                    .attr('disabled', 'disabled');
                return true;
            })

        })(jQuery);
    </script>
@endpush