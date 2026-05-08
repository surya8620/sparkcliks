@php
    $user = auth()->user();

    //RT
    $creditColumnsRealistic = [
        112 => 'traffic_r_mini',
        113 => 'traffic_r_small',
        114 => 'traffic_r_medium',
        115 => 'traffic_r_large',
        116 => 'traffic_r_ultimate',
    ];

    $realisticServices = realisticTrafficServices();
    $minRealisticServiceId = $realisticServices->min('id');
    // Calculate the total available credits
    $totalAvailableCreditsRealistic = 0;
    foreach ($realisticServices as $realisticService) {
        $columnNameRealistic = $creditColumnsRealistic[$realisticService->id] ?? null;
        $availableCreditsRealistic = $columnNameRealistic ? $user->$columnNameRealistic : 0;
        $totalAvailableCreditsRealistic += $availableCreditsRealistic;
    }

@endphp
@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6 mb-4">
            @if($totalAvailableCreditsRealistic > 0)
                    <form method="post" id="from-prevent-multiple-submits" action="{{ route('user.realistic.create', $realisticService->id) }}">
                        @csrf
                        <div class="modal-body">
                            <div class="form-row form-group">
                                <label for="plan" class="font-weight-bold"><strong>@lang('Campaign - Package')</strong></label>
                                <div class="col-sm-12">
                                    <select name="plan" id="plan" class="form--control form--control-lg">
                                        @foreach ($realisticServices as $realisticService)
                                                                @php
                                                                    $columnNameRealistic = $creditColumnsRealistic[$realisticService->id] ?? null;
                                                                    $availableCreditsRealistic = $columnNameRealistic ? $user->$columnNameRealistic : 0;
                                                                @endphp

                                                                @if($availableCreditsRealistic > 0)
                                                                    <option value="{{ $realisticService->id }}" class="font-weight-bold"
                                                                        data-title="{{ __($realisticService->name) }}"
                                                                        @selected($realisticService->id == $minRealisticServiceId)>
                                                                        {{ __($realisticService->name) }} (Available Credits - {{ $availableCreditsRealistic }})
                                                                    </option>
                                                                @else
                                                                    <option value="{{ $realisticService->id }}" disabled
                                                                        data-title="{{ __($realisticService->name) }}">
                                                                        {{ __($realisticService->name) }} (Available Credits - {{ $availableCreditsRealistic }})
                                                                    </option>
                                                                @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-row form-group">
                                <label for="title" class="font-weight-bold"><strong>@lang('Campaign Name')</strong></label>
                                <div class="col-sm-12">
                                    <input type="text" class="form--control form--control-lg bold" id="title" name="title"
                                        placeholder="Name of the Campaign" required>
                                </div>
                            </div>

                            <div class="form-row form-group">
                                <label for="link" class="font-weight-bold"><strong>@lang('Website URL')</strong></label>
                                <div class="col-sm-12">
                                    <input type="text" class="form--control form--control-lg bold" id="link" name="link"
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
                                <a href="{{ route('user.realistic.buy') }}"
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

    {{-- Order MODAL --}}
    <div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel" class="form-group text-center">@lang('New Campaign')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="from-prevent-multiple-submits">
                    @csrf
                    <div class="modal-body">
                        <div class="form-row form-group">
                            <label for="plan" class="font-weight-bold" placeholder="">@lang('Campaign - Package') </label>
                            <div class="col-sm-12">
                                <select name="plan" id="plan" class="form-control" value="0">
                                    <option value="0">Choose a pack</option>
                                    @if($user->traffic_r_mini > 0)
                                        <option value="2" class="font-weight-bold">Mini (Available Credits -
                                            {{__($user->traffic_r_mini)}})</option>
                                    @else
                                        <option disabled>Mini (Available Credits - {{__($user->traffic_r_mini)}})</option>
                                    @endif
                                    @if($user->traffic_r_small > 0)
                                        <option value="3" class="font-weight-bold">Small (Available Credits -
                                            {{__($user->traffic_r_small)}})</option>
                                    @else
                                        <option disabled>Small (Available Credits - {{__($user->traffic_r_small)}})</option>
                                    @endif
                                    @if($user->traffic_r_medium > 0)
                                        <option value="4" class="font-weight-bold">Medium (Available Credits -
                                            {{__($user->traffic_r_medium)}})</option>
                                    @else
                                        <option disabled>Medium (Available Credits - {{__($user->traffic_r_medium)}})</option>
                                    @endif
                                    @if($user->traffic_r_large > 0)
                                        <option value="5" class="font-weight-bold">Large (Available Credits -
                                            {{__($user->traffic_r_large)}})</option>
                                    @else
                                        <option disabled>Large (Available Credits - {{__($user->traffic_r_large)}})</option>
                                    @endif
                                    @if($user->traffic_r_ultimate > 0)
                                        <option value="6" class="font-weight-bold">Ultimate (Available Credits -
                                            {{__($user->traffic_r_ultimate)}})</option>
                                    @else
                                        <option disabled>Ultimate (Available Credits - {{__($user->traffic_r_ultimate)}})
                                        </option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="form-row form-group">
                            <label for="link" class="font-weight-bold">@lang('Campaign Name')</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control has-error bold" id="title" name="title"
                                    placeholder="Name of the Campaign" required>
                            </div>
                        </div>
                        <div class="form-row form-group">
                            <label for="link" class="font-weight-bold">@lang('Website URL')</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control has-error bold" id="link" name="link"
                                    placeholder="URL of your website, eg: https://example.com" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label
                                class="font-weight-bold">@lang('You will have more settings after creating the campaign')</label><br>
                            <label
                                class="font-weight-bold">@lang('The traffic will be delivered as per the Delivery Policy.')</label>
                        </div>
                        <div class="form-group">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="agree" value="1" required>
                                <span class="custom-control-label text-uppercase"><strong>I have read and agree to the <a
                                            href="https://www.sparkcliks.com/delivery-policy/">Delivery
                                            Policy</a></strong></span>
                            </label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn--primary" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--base font-weight-bold" id="btn-save"
                            value="add">@lang('Create Campaign')</button>
                    </div>
                </form>
            </div>
        </div>
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