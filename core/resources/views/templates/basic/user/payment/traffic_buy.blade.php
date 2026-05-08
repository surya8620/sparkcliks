@extends($activeTemplate . 'layouts.master')
@section('content')
    @php
        session()->forget('coupon');
    @endphp

    @if ($widget['nano'] == 0)
        <div class="dashboard-table__header d-flex justify-content-end pt-0 px-0">
            <div class="dashboard-table__btn">
                <input type="button" id="trial" value="Get Free Nano Credit/Free Trial" class="btn btn-sm btn--base"
                    data-bs-toggle="modal">
            </div>
        </div>
    @else
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-6 mb-4">
            <form action="{{ route('user.billing.insert') }}" method="post" class="deposit-form">
                @csrf
                <h4 class="form-label font-weight-bold text-center"><strong>@lang('Choose Your Pack')</strong></h4>
                <input type="hidden" name="final_amount" id="final_amount">
                <input type="hidden" name="price" id="price">
                <input type="hidden" name="amount" id="amount">
                <input type="hidden" name="processing_fee" id="processing_fee">
                <input type="hidden" name="vat_fee" id="vat_fee">
                <input type="hidden" name="discount_amount" id="discount_amount">
                <input type="hidden" name="discount_percentage" id="discount_percentage">
                <input type="hidden" name="coupon" id="coupon">
                <input type="hidden" name="conversion" id="conversion">
                <input type="hidden" name="currency">
                <input type="hidden" id="category_id" value="{{ $plans->first()->category_id ?? '' }}">
                <div class="payment-system-list p-3">
                    <div class="input-group mb-3">
                        <span class="input-group">
                            <select name="plans" id="plans" required="" class="form-control" placeholder="Select"
                                onchange="updatePackDetails(this.value)">
                                @foreach ($plan as $category)
                                    <option data-title="{{ __($category->name) }}"
                                        data-price="{{ $category->original_price }}" value="{{ $category->value }}"
                                        @selected($loop->first)>{{ __($category->name) }}</option>
                                @endforeach
                            </select>
                        </span>
                    </div>
                    <div id="pack-details" class="mt-4"></div>
					<hr>
					<div class="col-12">
						<div class="payment-system-list rounded shadow-sm p-4">
							<h4 class="form-label font-weight-bold text-center"><strong>@lang('Looking for Real User Engagement?')</strong></h4>
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
					<hr>
                    <div class="payment-card-title rounded shadow-sm p-2">
                        <img src="{{ asset('assets/images/WT2026.png') }}" class="img-fluid w-50" alt="Coupon">
                    </div>
					<hr>
                    <div class="deposit-info">
                        <div class="deposit-info__title">
                            <p class="text mb-0">@lang('Price')</p>
                        </div>
                        <div class="deposit-info__input">
                            <p class="text"><span class="amount">@lang('0.00')</span>
                                {{ __(gs('cur_text')) }}
                            </p>
                        </div>
                    </div>
                    <div class="deposit-info mt-4">
                        <div class="deposit-info__title">
                            <p class="text has-icon">@lang('Discount')
                                <span data-bs-toggle="tooltip" title="@lang('This is the discount applied based on quantity')" class="discount-info"><i
                                        class="las la-info-circle"></i> </span>
                            </p>
                        </div>
                        <div class="deposit-info__input">
                            <p class="text">
                                <span class="discount-amount">0.00</span> {{ __(gs('cur_text')) }}
                                <span class="discount-percentage">0</span>
                            </p>
                        </div>
                    </div>
                    <hr>
                    <div class="deposit-info">
                        <div class="deposit-info__title">
                            <p class="text has-icon">@lang('Processing Fee')
                                <span data-bs-toggle="tooltip" title="@lang('Processing charge for payment gateways')" class="proccessing-fee-info"><i
                                        class="las la-info-circle"></i> </span>
                            </p>
                        </div>
                        <div class="deposit-info__input">
                            <p class="text"><span class="processing-fee">@lang('0.00')</span>
                                {{ __(gs('cur_text')) }}
                            </p>
                        </div>
                    </div>
                    <div class="deposit-info">
                        <div class="deposit-info__title">
                            <p class="text has-icon">@lang('GST')
                                <span data-bs-toggle="tooltip" title="@lang('Goods and Service Tax - India: 18%, Other Countries: 0% ')" class="vat-fee-info"><i
                                        class="las la-info-circle"></i> </span>
                            </p>
                        </div>
                        <div class="deposit-info__input">
                            <p class="text"><span class="vat-fee">@lang('0.00')</span>
                                {{ __(gs('cur_text')) }}
                            </p>
                        </div>
                    </div>
                    <div class="deposit-info mt-4">
                        <div class="deposit-info__title">
                            <p class="text mb-0">@lang('Quantity')</p>
                        </div>
                        <div class="deposit-info__input">
                            <div class="input-group" style="width: auto;">
                                <button type="button" class="btn btn--base w-10" id="decrease-quantity">-</button>
                                <input type="number" class="form-control text-center" id="quantity" name="quantity"
                                    value="1" min="1" readonly>
                                <button type="button" class="btn btn--base w-10" id="increase-quantity">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="deposit-info total-amount pt-3">
                        <div class="deposit-info__title">
                            <p class="text">@lang('Total')</p>
                        </div>
                        <div class="deposit-info__input">
                            <p class="text"><span class="final-amount">@lang('0.00')</span>
                                {{ __(gs('cur_text')) }}
                            </p>
                        </div>
                    </div>

                    <div class="deposit-info gateway-conversion d-none total-amount pt-2">
                        <div class="deposit-info__title">
                            <p class="text">@lang('Conversion')
                            </p>
                        </div>
                        <div class="deposit-info__input">
                            <p class="text"></p>
                        </div>
                    </div>
                    <div class="deposit-info conversion-currency d-none total-amount pt-2">
                        <div class="deposit-info__title">
                            <p class="text">
                                @lang('Final')
                            </p>
                        </div>
                        <div class="deposit-info__input">
                            <p class="text">
                                <span class="in-currency"></span> <span class="gateway-currency"></span>
                            </p>
                        </div>
                    </div>
                    <!-- <div class="d-none crypto-message mb-3">
                            @lang('Conversion with') <span class="gateway-currency"></span> @lang('and final value will Show on next step')
                        </div> -->
                    <div class="info-text pt-3">
                        <p class="text">@lang('')</p>
                    </div>
                    <div class="info-text pt-3">
                        <label for="coupon_code">@lang('Coupon Code')</label>
                        <div id="coupon_response" class="alert" style="display: none;"></div>
                    </div>

                    <div class="deposit-info">
                        <div class="deposit-info__title">
                            <input type="text" id="coupon_code" name="coupon_code"
                                class="form-control form--control w-100" placeholder="@lang('Enter coupon code')"
                                value="{{ session('coupon.code') ?? '' }}">
                        </div>
                        <div class="deposit-info__input">
                            <div class="table-responsive">
                                <button type="button" class="btn btn--sm btn-outline--base" id="apply_coupon"
                                    style="{{ session('coupon') ? 'display: none;' : '' }}">@lang('Apply Coupon')</button>
                                <button type="button" class="btn btn-outline--base btn--sm" id="remove_coupon"
                                    style="{{ session('coupon') ? '' : 'display: none;' }}">@lang('Remove Coupon')</button>
                            </div>
                        </div>
                        <!-- <button type="button" class="btn btn-outline--base btn--sm" id="remove_coupon" style="{{ session('coupon') ? '' : 'display: none;' }}">@lang('Remove Coupon')</button> -->
                    </div>
                </div>
                <hr>
                <div class="payment-system-list is-scrollable gateway-option-list">
                    @foreach ($gatewayCurrency as $data)
                        <label for="{{ titleToKey($data->name) }}"
                            class="payment-item @if ($loop->index > 4) d-none @endif gateway-option">
                            <div class="payment-item__info">
                                <span class="payment-item__check"></span>
                                <img class="payment-item__thumb-img"
                                    src="{{ getImage(getFilePath('gateway') . '/' . $data->image) }}"
                                    alt="@lang('payment-thumb')">
                            </div>
                            <div class="payment-item__thumb">
                                <img class="payment-item__thumb-img"
                                    src="{{ getImage(getFilePath('gateway') . '/' . $data->method->image) }}"
                                    alt="@lang('payment-thumb')">
                            </div>
                            <input class="payment-item__radio gateway-input" id="{{ titleToKey($data->name) }}" hidden
                                data-gateway='@json($data)' type="radio" name="gateway"
                                value="{{ $data->method_code }}" @checked(old('gateway', $loop->first) == $data->method_code)
                                data-min-amount="{{ showAmount($data->min_amount) }}"
                                data-max-amount="{{ showAmount($data->max_amount) }}">
                        </label>
                    @endforeach
                    @if ($gatewayCurrency->count() > 4)
                        <button type="button" class="payment-item__btn more-gateway-option">
                            <p class="payment-item__btn-text">@lang('Show All Payment Options')</p>
                            <span class="payment-item__btn__icon"><i class="fas fa-chevron-down"></i></i></span>
                        </button>
                    @endif
                    <br>
                    <div class="payment-system-list">
                        <div style="display: flex; align-items: flex-start; gap: 10px; margin-left: 20px;">
                            <input type="checkbox" id="agreeCheck" name="agree" value="1" required
                                style="margin-top: 4px;">
                            <label for="agreeCheck" style="margin: 0;">
                                <strong>
                                    @lang('I have read and agree to comply with the')
                                    <a href="https://www.sparkcliks.com/refund-policy/" class="text--base "
                                        style="font-size: 14px;" target="_blank">@lang(' Refund Policy')</a>,
                                    <a href="https://www.sparkcliks.com/disclaimer/" class="text--base "
                                        style="font-size: 14px;" target="_blank">@lang(' Disclaimer')</a>@lang(', and')
                                    <a href="https://www.sparkcliks.com/terms-of-use/" class="text--base "
                                        style="font-size: 14px;" target="_blank">@lang(' Terms of Use ')</a>
                                </strong>
                            </label>
                        </div>
                    </div>
                    <br>
                    <button type="submit" class="btn btn--base w-50 d-block mx-auto mb-4" disabled>
                        @lang('Buy')
                    </button>
                </div>
                <hr>
                <h5 class="payment-card-title font-weight-bold ">@lang('MONEY BACK GUARANTEE')</h5>
                <div class="payment-system-list rounded shadow-sm p-4">
                    <div class="info-text pt-3">
                        <ul class="list-unstyled">
                            <li class="mb-2">✔️ @lang('Refund requests must be made within 72 hours of the purchase.')</li>
                            <li class="mb-2">✔️ @lang('Refunds are applicable exclusively to new subscribers on first credit only.')</li>
                            <li class="mb-2">✔️ @lang('This is a non-recurring monthly subscription. You will not be charged automatically.')</li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>

    </div>
    <hr style="height:2px;border-width:0;color:gray;background-color:gray"><br>
    <div class="modal fade" id="trialModal" tabindex="-1" role="dialog" aria-labelledby="trialModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="trialModalLabel"><strong>@lang('Get Free Nano Credit')</strong></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('user.web.nano') }}" method="post" class="resetForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group text-center">
                            <input type="hidden" name="nano" class="edit-amount" value="1">
                            <div class="col-lg-12 col-md-3 mb-4">
                                <div class="card card-deposit text-center">
                                    <div class="card-body card-body-deposit">
                                        <div class="text-bold h5 text--primary">NANO</div>
                                        <div class="card-title font-weight-bold text--primary"><small>upto <strong>
                                                    6,000</strong> page views</small></div>
                                        @if ($widget['nano_exp'] > $widget['time'])
                                            <strong class="text--small font-weight-normal text--danger">
                                                Next Credit will be available after {{ showDateTime($widget['nano_exp']) }}
                                            </strong>
                                        @endif
                                    </div>
                                    <div class="card-body text--primary">
                                        <div class="row">
                                            <div class="col-8 text-start">Unique Visitors</div>
                                            <div class="col-4 text-center"><strong>2,000</strong></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-8 text-start">Maximum URLs</div>
                                            <div class="col-4 text-center"><strong>3</strong></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-8 text-start">Up to pages per visit</div>
                                            <div class="col-4 text-center"><strong>3</strong></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-8 text-start">Countries Geo Targeting</div>
                                            <div class="col-4 text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                    <path
                                                        d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"
                                                        style="fill: red;" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-8 text-start">Google Analytics 4 Engagement</div>
                                            <div class="col-4 text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                    <path
                                                        d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"
                                                        style="fill: red;" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-8 text-start">Google Analytics 4 Natural Events</div>
                                            <div class="col-4 text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                    <path
                                                        d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"
                                                        style="fill: red;" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-8 text-start">Automatic Website Crawler</div>
                                            <div class="col-4 text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                    <path
                                                        d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"
                                                        style="fill: red;" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-8 text-start">Content Engagement</div>
                                            <div class="col-4 text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                    <path
                                                        d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"
                                                        style="fill: red;" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-8 text-start">Random Engagement Time</div>
                                            <div class="col-4 text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                    <path
                                                        d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"
                                                        style="fill: red;" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-8 text-start">Random Session Time</div>
                                            <div class="col-4 text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                    <path
                                                        d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"
                                                        style="fill: red;" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-8 text-start">Random Scroll & Clicks</div>
                                            <div class="col-4 text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                    <path
                                                        d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"
                                                        style="fill: red;" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-8 text-start">Referral, Organic, Social Traffic Types</div>
                                            <div class="col-4 text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                    <path
                                                        d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"
                                                        style="fill: red;" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-8 text-start">Shorteners bit.ly and cutt.ly</div>
                                            <div class="col-4 text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                    <path
                                                        d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"
                                                        style="fill: red;" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-8 text-start">Random Time on Page</div>
                                            <div class="col-4 text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                    <path
                                                        d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"
                                                        style="fill: red;" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-8 text-start">Up to 30 seconds on every visit</div>
                                            <div class="col-4 text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="25"
                                                    fill="currentColor" class="bi bi-check color--primary"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <small class="card-title font-weight-bold">*Nano Credit can be redeemed once every
                                30days.</small>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        @if ($widget['nano_exp'] < $widget['time'])
                            <button type="submit" class="btn btn--base btn-sm">@lang('Get Now')</button>
                        @else
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        "use strict";

        // Show Trial modal
        $('#trial').on('click', function() {
            $('#trialModal').modal('show');
        });

        // Handle deposit button click (event delegation)
        $(document).on('click', '.deposit', function() {
            var id = $(this).data('id');
            var nano = $(this).data('nano'); // Fix the space issue
            //console.log("ID:", id, "Nano:", nano);
        });
    </script>
    <script>
        "use strict";
        (function($) {
            var gateway, minAmount, maxAmount;
            let couponApplied = !!sessionStorage.getItem('coupon'); // Check if coupon exists in session storage

            var firstOption = $('#plans option:first'); // Get the first option of plans
            var amount = parseFloat(firstOption.data('price')) || 0; // Default price from the first plan
            var quantity = parseInt($('#quantity').val()) || 1; // Default quantity value

            updatePriceDisplay(amount, quantity); // Initial price update

            // Plan change event
            $('#plans').on('change', function() {
                amount = parseFloat($(this).find(':selected').data('price')) || 0;
                updatePriceDisplay(amount, quantity);
                calculation(); // Recalculate values
            });

            // Quantity increase event
            $('#increase-quantity').on('click', function() {
                quantity++;
                $('#quantity').val(quantity);
                updatePriceDisplay(amount, quantity);
                calculation();
            });

            // Quantity decrease event
            $('#decrease-quantity').on('click', function() {
                if (quantity > 1) {
                    quantity--;
                    $('#quantity').val(quantity);
                    updatePriceDisplay(amount, quantity);
                    calculation();
                }
            });

            // Update total price in UI
            function updatePriceDisplay(price, qty) {
                let totalAmount = price * qty;
                $('.deposit-info .amount').text(totalAmount.toFixed(2));
                $("input[name='price']").val(totalAmount.toFixed(2));
            }

            // Payment method change event
            $('.gateway-input').on('change', function() {
                gatewayChange();
            });

            function gatewayChange() {
                let gatewayElement = $('.gateway-input:checked');
                gateway = gatewayElement.data('gateway');
                minAmount = gatewayElement.data('min-amount');
                maxAmount = gatewayElement.data('max-amount');
                calculation(); // Trigger calculation on gateway change
            }

            function calculation() {
                if (!gateway) return; // Stop if no gateway is selected

                let totalAmount = amount * quantity; // Base price * quantity
                let quantityDiscountPercentage = 0;
                let quantityDiscountAmount = 0;

                // Apply default quantity-based discount
                if (quantity >= 12) {
                    quantityDiscountPercentage = 20; // 20% discount for 12 or more
                } else if (quantity >= 9) {
                    quantityDiscountPercentage = 15; // 15% discount for 9 or more
                } else if (quantity >= 6) {
                    quantityDiscountPercentage = 10; // 10% discount for 6 or more
                } else if (quantity >= 3) {
                    quantityDiscountPercentage = 5; // 5% discount for 3 or more
                }

                quantityDiscountAmount = (totalAmount * quantityDiscountPercentage) / 100;
                totalAmount -= quantityDiscountAmount;

                // Update UI with quantity discount details
                $(".quantity-discount").text('-' + quantityDiscountAmount.toFixed(2));
                $(".quantity-discount-percentage").text(' (' + quantityDiscountPercentage + '%)').css('color', 'blue');

                let couponDiscountAmount = 0;
                let discountedAmount = totalAmount;
                let couponCode = "";

                // Apply coupon discount if available
                if (couponApplied) {
                    let couponData = sessionStorage.getItem('coupon');
                    if (couponData) {
                        let coupon = JSON.parse(couponData);

                        // Ensure the discount is applied after quantity discount, not before
                        couponDiscountAmount = ((amount * quantity) * coupon.discount) / 100;

                        // Prevent negative values
                        couponDiscountAmount = Math.min(couponDiscountAmount, discountedAmount);

                        discountedAmount -= couponDiscountAmount;
                        // Set coupon code in hidden field
                        $("#coupon_code_hidden").val(couponCode);
                    }
                }
                $(".discounted-price").text(discountedAmount.toFixed(2));

                // Gateway charges
                let percentCharge = parseFloat(discountedAmount / 100 * gateway.percent_charge);
                let fixedCharge = parseFloat(gateway.fixed_charge);

                let totalCharge = percentCharge + fixedCharge;
                let finalAmountBeforeVAT = discountedAmount + totalCharge;

                // Now calculate VAT on the final amount (after processing fees)
                let vatCharge = parseFloat(finalAmountBeforeVAT / 100 * gateway.vat_charge);

                // Final payable amount after VAT
                let finalAmount = finalAmountBeforeVAT + vatCharge;
                // ✅ Convert `finalAmount` based on Gateway Conversion Rate
                let convertedAmount = finalAmount * gateway.rate;

                $(".final-amount").text(finalAmount.toFixed(2));
                $(".processing-fee").text(totalCharge.toFixed(2));
                $(".vat-fee").text(vatCharge.toFixed(2));
                $("input[name=currency]").val(gateway.currency);
                $(".gateway-currency").text(gateway.currency);

                if (finalAmount < Number(gateway.min_amount) || finalAmount > Number(gateway.max_amount)) {
                    $(".deposit-form button[type=submit]").attr('disabled', true);
                } else {
                    $(".deposit-form button[type=submit]").removeAttr('disabled');
                }

                if (gateway.currency != "{{ gs('cur_text') }}" && gateway.method.crypto != 1) {
                    $('.deposit-form').addClass('adjust-height')

                    $(".gateway-conversion, .conversion-currency").removeClass('d-none');
                    $(".gateway-conversion").find('.deposit-info__input .text').html(
                        `1 {{ __(gs('cur_text')) }} = <span class="rate">${parseFloat(gateway.rate).toFixed(2)}</span>  <span class="method_currency">${gateway.currency}</span>`
                    );
                    $('.in-currency').text(parseFloat(finalAmount * gateway.rate).toFixed(gateway.method.crypto == 1 ?
                        8 : 2))
                } else {
                    $(".gateway-conversion, .conversion-currency").addClass('d-none');
                    $('.deposit-form').removeClass('adjust-height')
                }

                if (gateway.method.crypto == 1) {
                    $('.crypto-message').removeClass('d-none');
                } else {
                    $('.crypto-message').addClass('d-none');
                }

                // Update coupon discount display
                if (couponApplied) {
                    let couponPercentage = JSON.parse(sessionStorage.getItem('coupon')).discount;
                    let totalDiscountPercentage = quantityDiscountPercentage + couponPercentage;
                    let totalDiscountAmount = quantityDiscountAmount + couponDiscountAmount;

                    $(".discount-amount").text('-' + totalDiscountAmount.toFixed(2));
                    $(".discount-percentage").text(' (' + totalDiscountPercentage + '%)').css('color', 'green');
                } else {
                    // ❌ Don't reset everything to zero, just remove coupon discount!
                    $(".discount-amount").text('-' + quantityDiscountAmount.toFixed(2)); // ✅ Keep quantity discount
                    $(".discount-percentage").text(' (' + quantityDiscountPercentage + '%)').css('color',
                        'green'); // ✅ Show quantity discount %
                }
                // Update Hidden Fields for Form Submission
                $("#amount").val(totalAmount.toFixed(2));
                $("#final_amount").val(convertedAmount.toFixed(2));
                $("#processing_fee").val(totalCharge.toFixed(2));
                $("#conversion").val(gateway.rate);
                $("#vat_fee").val(vatCharge.toFixed(2));
                $("#discount_amount").val((quantityDiscountAmount + couponDiscountAmount).toFixed(2));
                $("#discount_percentage").val(quantityDiscountPercentage + (couponApplied ? JSON.parse(sessionStorage
                    .getItem('coupon')).discount : 0));

                // ✅ Call updatePackDetails with the calculated `finalAmount`
                let selectedPackIndex = document.getElementById("plans").value;
                updatePackDetails(selectedPackIndex, finalAmount, quantity);

            }


            function applyCoupon() {
                let couponCode = $('#coupon_code').val();
                let totalAmount = amount * quantity;
                let categoryId = $('#category_id').val(); // Get category_id

                if (couponCode === '') {
                    $('#coupon_response').html('<div class="alert alert-danger">Please enter a coupon code.</div>')
                        .show();
                    return;
                }

                $.ajax({
                    url: "{{ route('user.billing.apply.coupon') }}",
                    type: "POST",
                    data: {
                        coupon_code: couponCode,
                        price: totalAmount,
                        category_id: categoryId, // Pass category_id
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            let couponData = {
                                code: response.coupon_code,
                                discount: response.discount_percentage,
                                discount_amount: response.discount_amount
                            };

                            sessionStorage.setItem('coupon', JSON.stringify(couponData));
                            couponApplied = true;

                            // Set the hidden input field value
                            $('#coupon').val(couponCode);

                            calculation();
                            // $(".discount-amount").text('-' + response.discount_amount.toFixed(2));
                            // $(".discount-percentage").text(' (' + response.discount_percentage + '%)').css('color', 'green');

                            $('#coupon_response').html('<div class="alert alert-success">' + response
                                .message + '</div>').show();
                            $('#apply_coupon').hide();
                            $('#remove_coupon').show();
                            $('#coupon_code').prop('disabled', true).css('background-color', '#e9ecef');
                        } else {
                            $('#coupon_response').html('<div class="alert alert-danger">' + response
                                .message + '</div>').show();
                        }
                    },
                    error: function() {
                        $('#coupon_response').html(
                            '<div class="alert alert-danger">Error applying coupon. Please try again.</div>'
                        ).show();
                    }
                });
            }

            function removeCoupon() {
                $.ajax({
                    url: "{{ route('user.billing.remove.coupon') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            sessionStorage.removeItem('coupon');
                            couponApplied = false;

                            calculation();
                            // $(".discount-amount").text('0.00');
                            // $(".discount-percentage").text('');

                            $('#coupon_response').html('<div class="alert alert-success">' + response
                                .message + '</div>').show();
                            $('#apply_coupon').show();
                            $('#remove_coupon').hide();
                            $('#coupon_code').prop('disabled', false).css('background-color', '');
                        }
                    },
                    error: function() {
                        $('#coupon_response').html(
                            '<div class="alert alert-danger">Error removing coupon. Please try again.</div>'
                        ).show();
                    }
                });
            }

            // Bind button actions
            $('#apply_coupon').on('click', function() {
                applyCoupon();
            });

            $('#remove_coupon').on('click', function() {
                removeCoupon();
            });


            $(document).ready(function() {
                $('.gateway-input:checked').change(); // Trigger change event for gateway

                // Get coupon from Laravel session (backend)
                let backendCouponCode = "{{ session('coupon.code') ?? '' }}".trim();

                if (backendCouponCode !== '') {
                    $('#coupon_code').val(backendCouponCode); // Set input field
                    $('#coupon_code').prop('disabled', true).css('background-color',
                        '#e9ecef'); // Disable input
                    $('#apply_coupon').hide();
                    $('#remove_coupon').show();
                    couponApplied = true;
                } else {
                    // Fallback: Get coupon from sessionStorage (frontend)
                    let couponData = sessionStorage.getItem('coupon');
                    if (couponData) {
                        let coupon = JSON.parse(couponData);
                        $('#coupon_code').val(coupon.code);
                        couponApplied = true;
                    }
                }

                calculation(); // Run calculations on page load
            });
            $(document).ready(function() {


                sessionStorage.removeItem('coupon');
                couponApplied = false;

                calculation();
            });

        })(jQuery);
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            handlePlanChange(); // Initialize with default values
        });

        function handlePlanChange() {
            let selectElement = document.getElementById("plans");
            let packIndex = selectElement.value;
            let finalAmountInput = document.getElementById("final-amount").value;

            let finalAmount = parseFloat(finalAmountInput);

            if (isNaN(finalAmount) || finalAmount <= 0) {
                //console.error("Invalid final amount:", finalAmount);
                document.getElementById("pack-details").innerHTML = "<p class='text-danger'>Enter a valid final amount</p>";
                return;
            }

            updatePackDetails(packIndex, finalAmount);
        }

        function updatePackDetails(packIndex, finalAmount, quantity) {
            let packs = {
                15: {
                    name: "MINI",
                    visitors: "20,000",
                    daily: "500~800",
                    views: 60000
                },
                16: {
                    name: "SMALL",
                    visitors: "100,000",
                    daily: "2.5K~4K",
                    views: 300000
                },
                17: {
                    name: "MEDIUM",
                    visitors: "200,000",
                    daily: "5K~7.5K",
                    views: 600000
                },
                18: {
                    name: "LARGE",
                    visitors: "333,333",
                    daily: "10K~12K",
                    views: 1000000
                },
                19: {
                    name: "ULTIMATE",
                    visitors: "666,666",
                    daily: "20K~24K",
                    views: 2000000
                },
            };

            let pack = packs[packIndex];
            if (!pack) {
                //console.error("Invalid pack index:", packIndex);
                return;
            }

            // ✅ Multiply pack views with quantity
            let totalViews = pack.views * quantity;

            let pricePer1000Hits = (finalAmount / totalViews) * 1000;

            let detailsHtml = `
        <div class="col-lg-12 col-md-12 mb-4">
            <div class="card card-deposit text-center">
                <div class="card-body card-body-deposit">
                    <div class="text-bold h5 text--primary">${pack.name}
                    </div>
                    <div class="card-title font-weight-bold text--primary">
                        <small>upto <strong>${pack.views.toLocaleString()}</strong> page views</small>
                    </div>
                    <small class="font-weight-bold text--primary">
                        price per 1000 hits <strong>$${pricePer1000Hits.toFixed(3)}</strong>
                    </small>
                </div>
                <div class="card-body text--primary">
                                <hr>
                                <div class="row">
                                    <div class="col-8 text-start">Unique Visitors</div>
                                    <div class="col-4 text-center"><strong>${pack.visitors}</strong></div>
                                </div>
                                <div class="row">
                                    <div class="col-8 text-start">Daily Average Users</div>
                                    <div class="col-4 text-center"><strong>${pack.daily}</strong></div>
                                </div>
                                <div class="row">
                                    <div class="col-8 text-start">Maximum URLs</div>
                                    <div class="col-4 text-center"><strong>3</strong></div>
                                </div>
                                <div class="row">
                                    <div class="col-8 text-start">Up to pages per visit</div>
                                    <div class="col-4 text-center"><strong>3</strong></div>
                                </div>
                                <div class="row">
                                    <div class="col-8 text-start">Countries Geo Targeting</div>
                                    <div class="col-4 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="25" fill="currentColor" class="bi bi-check color--primary" viewBox="0 0 16 16">
                                            <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-8 text-start">Google Analytics 4 Engagement</div>
                                    <div class="col-4 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" style="fill: red;"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-8 text-start">Google Analytics 4 Natural Events</div>
                                    <div class="col-4 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" style="fill: red;"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-8 text-start">Automatic Website Crawler</div>
                                    <div class="col-4 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" style="fill: red;"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-8 text-start">Content Engagement</div>
                                    <div class="col-4 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" style="fill: red;"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-8 text-start">Random Engagement Time</div>
                                    <div class="col-4 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" style="fill: red;"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-8 text-start">Random Session Time</div>
                                    <div class="col-4 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" style="fill: red;"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-8 text-start">Random Scroll & Clicks</div>
                                    <div class="col-4 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" style="fill: red;"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-8 text-start">Referral, Organic, Social Traffic Types</div>
                                    <div class="col-4 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="25" fill="currentColor" class="bi bi-check color--primary" viewBox="0 0 16 16">
                                            <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-8 text-start">Shorteners bit.ly and cutt.ly</div>
                                    <div class="col-4 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="25" fill="currentColor" class="bi bi-check color--primary" viewBox="0 0 16 16">
                                            <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-8 text-start">Random Time on Page</div>
                                    <div class="col-4 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="25" fill="currentColor" class="bi bi-check color--primary" viewBox="0 0 16 16">
                                            <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-8 text-start">Up to 5 minutes on every visit</div>
                                    <div class="col-4 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="25" fill="currentColor" class="bi bi-check color--primary" viewBox="0 0 16 16">
                                            <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-8 text-start">Campaign Validity</div>
                                    <div class="col-4 text-center"><strong>upto 30 days</strong></div>
                                </div>
                            </div>
            </div>
        </div>
    `;

            document.getElementById("pack-details").innerHTML = detailsHtml;
        }
    </script>
@endpush
