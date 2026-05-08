@extends($activeTemplate . 'layouts.master')
@section('content')
    @php
        session()->forget('coupon');
    @endphp
    @if ($widget['trial'] == 0)
        <div class="row justify-content-center">
            <div class="col-lg-6 mb-4">
                <form action="{{ route('user.billing.insert') }}" method="post" class="deposit-form">
                    @csrf
                    <h4 class="form-label font-weight-bold text-center"><strong>@lang('Activate Trial')</strong></h4>
                    <input type="hidden" name="final_amount" id="final_amount" value="1">
                    <input type="hidden" name="price" id="price" value="1">
                    <input type="hidden" name="plans" id="plans" value="0">
                    <input type="hidden" name="amount" id="amount">
                    <input type="hidden" name="processing_fee" id="processing_fee">
                    <input type="hidden" name="vat_fee" id="vat_fee">
                    <input type="hidden" name="discount_amount" id="discount_amount">
                    <input type="hidden" name="discount_percentage" id="discount_percentage">
                    <input type="hidden" name="coupon" id="coupon">
                    <input type="hidden" name="conversion" id="conversion">
                    <input type="hidden" name="quantity" id="quantity" value="1">
                    <input type="hidden" name="currency">
                    <input type="hidden" id="category_id" value="21">
                    <div class="payment-system-list p-3">
                        <div id="pack-details" class="mt-4"></div>
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
                                            style="font-size: 14px;"
                                            target="_blank">@lang(' Disclaimer')</a>@lang(', and')
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
                </form>
            </div>
            <!-- Right Section (Money-Back Guarantee) -->
            <div class="col-lg-4 mb-4">
                <div class="col-12">
                    <h5 class="payment-card-title font-weight-bold ">@lang('Search Console Click Traffic')</h5>
                    <div class="payment-system-list rounded shadow-sm p-4">
                        <p class="font-weight-bold text-center"><strong>@lang('How it works?')</strong>
                        </p>
                        <hr>
                        <div class="payment-system-list rounded shadow-sm p-4" style="text-align: center;">
                            <img src="{{ asset('assets/images/gs.webp') }}" class="img-fluid" style="width: 40%;"
                                alt="Google Search"><br>
                            <small class="mb-0">@lang('Someone will search for your keyword in Google Search, find your website and clicks on it')</small>
                        </div><br>
                        <i class="fas fa-arrow-down text-center"
                            style="font-size: 24px; display: block; margin: auto;"></i>
                        <div class="payment-system-list rounded shadow-sm p-4" style="text-align: center;">
                            <img src="{{ asset('assets/images/click.svg') }}" class="img-fluid w-40"
                                alt="Google Search">
                            <br>
                            <small class="mb-0">@lang('A visitor opens your website, navigates and scrolls to the end of the page.')</small>
                        </div><br>
                        <i class="fas fa-arrow-down text-center"
                            style="font-size: 24px; display: block; margin: auto;"></i>
                        <div class="payment-system-list rounded shadow-sm p-4" style="text-align: center;">
                            <img src="{{ asset('assets/images/ga.webp') }}" class="img-fluid" style="width: 40%;"
                                alt="Google Analytics">
                            <br>
                            <small class="mb-0">@lang('The visitor is tracked in real-time reports within Google Analytics.')</small>
                        </div><br>
                        <i class="fas fa-arrow-down text-center"
                            style="font-size: 24px; display: block; margin: auto;"></i>
                        <div class="payment-system-list rounded shadow-sm p-4" style="text-align: center;">
                            <img src="{{ asset('assets/images/gsc.svg') }}" class="img-fluid" style="width: 30%;"
                                alt="Google Analytics">
                            <br><br>
                            <small class="mb-0">@lang('The visitor\'s data appears in Google Search Console within 24 to 72 hours.')</small>
                        </div>
                        <hr>
                        <small class="mb-0">@lang('Note: Your website needs to be added to Google Search Console and keywords needs to be ranked within the top 100.')</small>
                    </div>
                </div>

            </div>
        </div>
    @endif
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            var gateway, minAmount, maxAmount;
            let couponApplied = !!sessionStorage.getItem('coupon'); // Check if coupon exists in session storage

            var firstOption = $('#plans option:first'); // Get the first option of plans
            var amount = parseInt($('#price').val()) || 1; // Default price from the first plan
            var quantity = parseInt($('#quantity').val()) || 1; // Default quantity value

            updatePriceDisplay(amount, quantity); // Initial price update

            // Plan change event
            $('#plans').on('change', function() {
                amount = parseFloat($(this).find(':selected').data('price')) || 0;
                updatePriceDisplay(amount, quantity);
                calculation(); // Recalculate values
            });

            // Update total price in UI
            function updatePriceDisplay(price, qty) {
                let totalAmount = price * 1;
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
            let finalAmountInput = document.getElementById("price").value;

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
                0: {
                    name: "TRIAL",
                    credits: "50",
                    v: "7 days",
                    e: "50",
                    ww: "50",
                    geo: "25"
                }
            };

            let pack = packs[packIndex];
            if (!pack) {
                //console.error("Invalid pack index:", packIndex);
                return;
            }

            // ✅ Multiply pack views with quantity
            let totalViews = pack.credits;

            let pricePer1000Hits = (finalAmount / totalViews);

            let detailsHtml = `
        <div class="col-lg-12 col-md-3 mb-4">
            <div class="card card-deposit text-center">
                <div class="card-body card-body-deposit">
                    <div class="text-bold h5 text--primary">${pack.name}
                    </div>
                    <div class="card-title font-weight-bold text--primary">
                        <small>Get <strong>${pack.credits.toLocaleString()}</strong> Credits</small>
                    </div>
                    <small class="font-weight-bold text--primary">
                        price per click <strong>$${pricePer1000Hits.toFixed(3)}</strong>
                    </small>
                </div>
                <div class="card-body text--primary">
                                <hr>
                                <div class="row">
                                    <div class="col-8 text-start">@lang('Credits')</div>
                                    <div class="col-4 text-center"><strong>@lang('${pack.credits}')</strong></div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-7 text-start">@lang('Worldwide Targeting')</div>
                                    <div class="col-5 text-center"><strong>@lang('Up to ${pack.ww} Clicks')</strong></div>
                                </div>
                                <div class="row">
                                    <div class="col-12 text-center">@lang('Or')</div>
                                </div>                                
                                <div class="row">
                                    <div class="col-7 text-start">@lang('Country Targeting')</div>
                                    <div class="col-5 text-center"><strong>@lang('Up to ${pack.geo} Clicks')</strong></div>
                                </div>                                                               
                                <hr>                               
                                <div class="row">
                                    <div class="col-8 text-start">@lang('Campaigns')</div>
                                    <div class="col-4 text-center"><strong>@lang('Unlimited')</strong></div>
                                </div>
                                <div class="row">
                                    <div class="col-8 text-start">@lang('Keywords')</div>
                                    <div class="col-4 text-center"><strong>@lang('Unlimited')</strong></div>
                                </div> 
                                <div class="row">
                                    <div class="col-8 text-start">@lang('Page Views')</div>
                                    <div class="col-4 text-center"><strong>@lang('Up to 2 views')</strong></div>
                                </div>                                
                                <div class="row">
                                    <div class="col-8 text-start">@lang('Time of site')</div>
                                    <div class="col-4 text-center"><strong>@lang('Up to 2 minutes')</strong></div>
                                </div>
                               <div class="row">
                                    <div class="col-8 text-start">@lang('Geo-Targeting')</div>
                                    <div class="col-4 text-center"><strong>@lang('Country Level')</strong></div>
                                </div>   
                                <div class="row">
                                    <div class="col-8 text-start">@lang('Valid Up To')</div>
                                    <div class="col-4 text-center"><strong>@lang(' ${pack.v} ')</strong></div>
                                </div>                                                                                                                              
                            </div>
            </div>
        </div>
    `;

            document.getElementById("pack-details").innerHTML = detailsHtml;
        }
    </script>
@endpush
