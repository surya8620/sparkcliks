@extends($activeTemplate . 'layouts.master')
@section('content')
    @php
        session()->forget('coupon');
        // Redirect if user hasn't acknowledged bot guidelines
        if (auth()->user()->bot_ack == 0) {
            header('Location: ' . route('user.bot.home'));
            exit();
        }
        
        // Check if user has an active subscription
        $user = auth()->user();
        $hasActiveSubscription = $user->bot_status == 1 && $user->bot_exp && \Carbon\Carbon::parse($user->bot_exp)->isFuture();
    @endphp
    
    @if($hasActiveSubscription)
    <div class="row justify-content-center mb-4">
        <div class="col-lg-6">
            <div class="alert alert-warning" style="border-left: 5px solid #ffc107; background-color: #fff3cd; border-radius: 8px;">
                <div class="d-flex align-items-start">
                    <i class="las la-exclamation-triangle" style="font-size: 32px; color: #856404; margin-right: 15px; margin-top: 5px;"></i>
                    <div style="flex: 1;">
                        <h5 class="mb-2" style="color: #856404; font-weight: 700;">
                             @lang('Active Subscription Detected')
                        </h5>
                        <p class="mb-2" style="color: #856404; font-size: 14px; line-height: 1.6;">
                            @lang('You currently have an active Traffic Bot subscription that expires on') 
                            <strong>{{ showDateTime($user->bot_exp, 'd M Y') }}</strong>.
                        </p>
                        <p class="mb-2" style="color: #856404; font-size: 14px; line-height: 1.6;">
                            <strong>@lang('Important:')</strong> @lang('Purchasing a new plan will replace and expire your current active subscription immediately.')
                        </p>
                        <p class="mb-0" style="color: #856404; font-size: 14px; line-height: 1.6;">
                            <i class="las la-headset"></i> @lang('To upgrade your existing plan, please') 
                            <a href="{{ route('ticket.open') }}" class="text-decoration-underline" style="color: #856404; font-weight: 600;">@lang('contact support')</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                    <div class="mt-4 mb-3">
                        <div class="col-12">
                            <div class="payment-card-title rounded shadow-sm p-2">
                                <img src="{{ asset('assets/images/newbot.png') }}" class="img-fluid w-50" alt="Coupon">
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 mb-3">
                        <input type="hidden" id="quantity" name="quantity" value="1">
                        <div class="btn-group d-flex period-buttons-container" role="group">
                            <button type="button" class="btn btn--base  flex-fill period-btn active" data-quantity="1" data-period="Monthly">
                                <span>@lang('Monthly')</span>
                            </button>
                            <button type="button" class="btn btn--base  flex-fill period-btn" data-quantity="3" data-period="Quarterly">
                                <span>@lang('Quarterly')</span><small class="text--success">@lang('Save 5%')</small>
                            </button>
                            <button type="button" class="btn btn--base  flex-fill period-btn" data-quantity="6" data-period="Half Yearly">
                                <span>@lang('Half Yearly')</span><small class="text--success">@lang('Save 10%')</small>
                            </button>
                            <button type="button" class="btn btn--base  flex-fill period-btn" data-quantity="12" data-period="Annually">
                                <span>@lang('Annually')</span><small class="text--success">@lang('Save 20%')</small>
                            </button>
                        </div>
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
                    <div class="col-lg-10">
                        <div class="rounded shadow-sm p-3 mb-3" style="background-color: #f8d7da; border: 1px solid #f5c6cb;">
                            <div class="d-flex align-items-start">
                                <i class="las la-exclamation-triangle text-danger" style="font-size: 24px; margin-right: 12px; margin-top: 2px;"></i>
                                <div style="flex: 1;">
                                    <p class="mb-0" style="color: #721c24; font-size: 13px; line-height: 1.6;">
                                        @lang('Please Note: Traffic Bot service is non-refundable. Once you have purchased, refunds will not be provided under any circumstances.')
                                    </p>
                                </div>
                            </div>
                    </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- Right Section (Money-Back Guarantee) -->

    </div>
@endsection

@push('style')
    <style>
        /* Custom Select Dropdown Styling */
        #plans {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 12px;
            padding-right: 36px !important;
            cursor: pointer;
        }

        #plans:hover {
            border-color: hsl(var(--base));
        }

        #plans:focus {
            outline: none;
            border-color: hsl(var(--base));
            box-shadow: 0 0 0 0.2rem hsla(var(--base), 0.25);
        }

        .period-buttons-container {
            gap: 8px;
        }

        .period-btn {
            padding: 12px 8px;
            font-size: 13px;
            font-weight: 600;
            border: 2px solid hsl(var(--base));
            background-color: hsl(var(--base));
            color: white;
            transition: all 0.3s ease;
            white-space: normal;
            line-height: 1.3;
            min-height: 60px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .period-btn span {
            display: block;
            margin-bottom: 2px;
        }
        
        .period-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .period-btn.active {
            background-color: #c51e39 !important;
            color: white !important;
            border-color: #c51e39 !important;
        }
        
        .period-btn small {
            display: block;
            font-size: 10px;
            margin-top: 2px;
            font-weight: 500;
        }
        
        .period-btn.active small {
            color: white !important;
        }

        /* Mobile Optimization */
        @media (max-width: 768px) {
            .period-buttons-container {
                gap: 6px;
            }

            .period-btn {
                padding: 10px 5px;
                font-size: 11.5px;
                min-height: 65px;
                border-width: 1.5px;
            }

            .period-btn span {
                font-size: 11px;
            }
            
            .period-btn small {
                font-size: 9px;
                margin-top: 3px;
            }
        }

        @media (max-width: 576px) {
            .period-buttons-container {
                gap: 5px;
            }

            .period-btn {
                padding: 8px 3px;
                font-size: 10.5px;
                min-height: 70px;
                border-width: 1.5px;
            }

            .period-btn span {
                font-size: 10px;
                line-height: 1.2;
            }
            
            .period-btn small {
                font-size: 8.5px;
                margin-top: 3px;
            }
        }

        @media (max-width: 400px) {
            .period-buttons-container {
                gap: 4px;
            }

            .period-btn {
                padding: 7px 2px;
                font-size: 9.5px;
                min-height: 72px;
                border-width: 1px;
            }

            .period-btn span {
                font-size: 9.5px;
                line-height: 1.1;
            }
            
            .period-btn small {
                font-size: 8px;
                margin-top: 2px;
            }
        }
    </style>
@endpush

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
            $('.period-btn').on('click', function() {
                // Remove active class from all buttons and reset "Saved" to "Save"
                $('.period-btn').removeClass('active').each(function() {
                    let smallText = $(this).find('small');
                    if (smallText.length > 0) {
                        let text = smallText.text();
                        smallText.text(text.replace('Saved', 'Save'));
                    }
                });
                
                // Add active class to clicked button
                $(this).addClass('active');
                
                // Change "Save" to "Saved" for the active button
                let activeSmallText = $(this).find('small');
                if (activeSmallText.length > 0) {
                    let text = activeSmallText.text();
                    activeSmallText.text(text.replace('Save', 'Saved'));
                }
                
                // Update quantity value
                quantity = parseInt($(this).data('quantity'));
                $('#quantity').val(quantity);
                
                updatePriceDisplay(amount, quantity);
                calculation();
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
                41: {
                    name: "LITE",
                    views: 10
                },
                42: {
                    name: "BASIC",
                    views: 25
                },
                43: {
                    name: "BRONZE",
                    views: 50
                },
                44: {
                    name: "SILVER",
                    views: 100
                },
                45: {
                    name: "GOLD",
                    views: 250
                },
                46: {
                    name: "PLATINUM",
                    views: 500
                },
                47: {
                    name: "DIAMOND",
                    views: 1000
                },
            };

            let pack = packs[packIndex];
            if (!pack) {
                //console.error("Invalid pack index:", packIndex);
                return;
            }

            // ✅ Multiply pack views with quantity
            let totalViews = pack.views * quantity;

            let pricePer1000Hits = (finalAmount / totalViews);

            let detailsHtml = `
        <div class="col-lg-12 col-md-12 mb-4">
            <div class="card card-deposit text-center">
                <div class="card-body card-body-deposit">
                    <div class="text-bold h5 text--primary">${pack.name}
                    </div>
                    <div class="card-title font-weight-bold text--primary">
                        <small>upto <strong>${pack.views.toLocaleString()}</strong> Active Browsers</small>
                    </div>
                    <small class="font-weight-bold text--primary">
                        price per Browser <strong>$${pricePer1000Hits.toFixed(3)}</strong>
                    </small>
                </div>
            </div>
        </div>
    `;

            document.getElementById("pack-details").innerHTML = detailsHtml;
        }
    </script>
@endpush
