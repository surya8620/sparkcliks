@extends($activeTemplate . 'layouts.master')
@section('content')

@php
$planId = null;
if ($plan) {
    $planId = $plan->id;
    $amount =  getAmount(@$plan->price);
}else{
    $amount = old('amount');
}
$appliedCoupon = session('coupon');
$discount = $appliedCoupon ? $appliedCoupon['discount'] : 0;
$amount = $appliedCoupon ? $amount - ($amount * $discount / 100) : $amount;
$planContent = getContent('plan.content', true);
        $classes     = ['text--base', 'text--primary', 'text--base-three', 'text--base-two', 'text--dark', 'text--success'];
        $index       = 0;
@endphp

<div class="cmn-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <form action="{{ route('user.billing.insert') }}" method="post" class="deposit-form">
                    @csrf
                    <input type="hidden" name="currency">
                    <input type="hidden" name="plan_id" value="{{ @$plan->id }}">
                    <div class="gateway-card">
                        <div class="row justify-content-center gy-sm-4 gy-3">
                            <div class="col-12">
                                <h5 class="payment-card-title">@lang('SUBSCRIBE')</h5>
                            </div>
                            <div class="col-lg-6">
                            <div class="col-12">
                                <h5 class="payment-card-title">@lang('CHOOSE YOUR PLAN')</h5>
                            </div>
                                <div class="payment-system-list is-scrollable gateway-option-list">
                                    @foreach ($plans as $data)
                                        @php
                                            $class = @$classes[$index];
                                            $index >= 5 ? ($index = 0) : $index++;
                                        @endphp
                                        <label for="{{ titleToKey($data->name) }}"
                                            class="payment-item @if ($loop->index > 4) d-none @endif gateway-option">
                                            <div class="payment-item__info">
                                                <span class="payment-item__check"></span>
                                                
                                                <span class="payment-system-list">{{ __(gs('cur_sym')) }} {{ getAmount($data->price / $data->user_limit) }}@lang('/user')</span>
                                            </div>
                                            <div class="payment-item__thumb">
                                            <span class="payment-item__name">{{ __(gs('cur_sym')) }} {{ getAmount($data->price) }}</span>
                                            </div>
                                            <input class="payment-item__radio plan-input" id="{{ titleToKey($data->name) }}" hidden
                                                data-gateway='@json($data)' type="radio" name="plan"
                                                value="{{ $data->id }}" data-price="{{ getAmount($data->price) }}"
                                                data-original-price="{{ getAmount($data->price) }}"  {{-- Store original price --}}
                                                @if ($loop->first) checked @endif>
                                        </label>
                                    @endforeach
                                    @if ($plans->count() > 4)
                                        <button type="button" class="payment-item__btn more-gateway-option">
                                            <p class="payment-item__btn-text">@lang('Show more plans')</p>
                                            <span class="payment-item__btn__icon"><i class="fas fa-chevron-down"></i></i></span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="col-12">
                                    <h5 class="payment-card-title">@lang('MONEY BACK GUARANTEE')</h5>
                                </div>
                                <div class="payment-system-list p-3">
                                    <div class="info-text pt-3">
                                        <h6 class="text">@lang('No risk sign-up with our generous refund policy')</h6>
                                    </div>
                                    <hr>
                                    <div class="info-text pt-3">
                                        <h6 class="text">@lang('Refund requests must be made within 72 hours of the purchase.')</h6>
                                    </div>
                                    <hr>
                                    <div class="info-text pt-3">
                                        <h6 class="text">@lang('Refunds are applicable exclusively to new subscribers on first subscription only.')</h6>
                                    </div>
                                    <hr>
                                    <div class="info-text pt-3">
                                        <h6 class="text">@lang('This is a non-recurring monthly subscription. You will not be charged automatically.')</h6>
                                    </div>
                                    <hr>
                                </div>
                            </div>
                            <div class="col-lg-6">
                            <div class="col-12">
                                <h5 class="payment-card-title">@lang('PAYMENT METHOD')</h5>
                            </div>
                                <div class="payment-system-list is-scrollable gateway-option-list">

                                    @foreach ($gatewayCurrency as $data)
                                        <label for="{{ titleToKey($data->name) }}"
                                            class="payment-item @if ($loop->index > 4) d-none @endif gateway-option">
                                            <div class="payment-item__info">
                                                <span class="payment-item__check"></span>
                                                <span class="payment-item__name">{{ __($data->name) }}</span>
                                            </div>
                                            <div class="payment-item__thumb">
                                                <img class="payment-item__thumb-img"
                                                    src="{{ getImage(getFilePath('gateway') . '/' . $data->method->image) }}"
                                                    alt="@lang('payment-thumb')">
                                            </div>
                                            <input class="payment-item__radio gateway-input" id="{{ titleToKey($data->name) }}" hidden
                                                data-gateway='@json($data)' type="radio" name="gateway" value="{{ $data->method_code }}"
                                                @if (old('gateway')) @checked(old('gateway') == $data->method_code) @else @checked($loop->first) @endif
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
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="col-12">
                                    <h5 class="payment-card-title">@lang('Confirm Payment')</h5>
                                </div>
                                <div class="payment-system-list p-3">
                                    <div class="deposit-info">
                                        <div class="deposit-info__title">
                                            <p class="text mb-0">@lang('Amount')</p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <div class="deposit-info__input-group input-group">
                                                <span class="deposit-info__input-group-text">{{ gs('cur_sym') }}</span>
                                                <input type="text" class="form-control form--control amount" name="amount"
                                                    placeholder="@lang('00.00')" value="{{ $amount }}" @if(@$plan) readonly @endif autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="deposit-info hideInfo">
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
                                    <div class="deposit-info total-amount pt-3">
                                        <div class="deposit-info__title">
                                            <p class="text">@lang('Discount ')<span class="discount-percentage" style="color: green;"></span></p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <p class="text"><span class="discount-amount">@lang('0.00')</span>
                                                {{ __(gs('cur_text')) }}</p>
                                        </div>
                                    </div>
                                    <div class="deposit-info total-amount pt-3">
                                        <div class="deposit-info__title">
                                            <p class="text">@lang('Total')</p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <p class="text"><span class="final-amount">@lang('0.00')</span>
                                                {{ __(gs('cur_text')) }}</p>
                                        </div>
                                    </div>
                                    <div class="info-text pt-3">
                                        <label for="coupon_code">@lang('Coupon Code')</label>
                                        <div id="coupon_response" class="alert" style="display: none;"></div>
                                    </div>
                                    
                                    <div class="deposit-info">
                                        <div class="deposit-info__title">
                                            <input type="text" id="coupon_code" name="coupon_code" class="form-control form--control w-80" placeholder="@lang('Enter coupon code')">
                                        </div>
                                        <div class="deposit-info__input">
                                            <div class="table-responsive">
                                                <button type="button" class="btn btn--sm btn-outline--base" id="apply_coupon">@lang('Apply Coupon')</button>
                                                <button type="button" class="btn btn-outline--secondary btn--sm" id="remove_coupon" style="display: none;">@lang('Remove Coupon')</button>
                                            </div>
                                        </div>
                                    </div><hr>

                                    <button type="submit" class="btn btn--base w-100" @if(!@$plan) disabled @endif>
                                        @if(@$plan) @lang('BUY NOW') @else  @lang('BUY NOW') @endif
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('script')
<script>
    "use strict";
        (function($) {
            var amount = parseFloat($('.amount').val() || 0);
            var plan = "{{@$planId}}"
            var gateway, minAmount, maxAmount;

            $('.amount').on('input', function(e) {
                amount = parseFloat($(this).val());

                if (!amount) {
                   amount = 0;
                }
                calculation();
            });

            $('.gateway-input').on('change', function(e) {
                gatewayChange();
            });

            $('.plan-input').on('change', function(e) {
                planChange();
            });


            function gatewayChange() {

                let gatewayElement = $('.gateway-input:checked');
                let methodCode = gatewayElement.val();

                let gatewayValue = $('.gateway-input:checked').val();

                if (gatewayValue == 'wallet') {
                    @if (auth()->user()->balance < @$plan->price)
                        $(".deposit-form button[type=submit]").attr('disabled', true);
                    @else
                        $(".deposit-form button[type=submit]").removeAttr('disabled');
                        @endif
                        var totalAmount = parseFloat('{{ @$plan->price }}');
                        $('.hideInfo').addClass('d-none')
                        $(".final-amount").text(totalAmount.toFixed(2));
                    } else {
                        
                    $(".deposit-form button[type=submit]").removeAttr('disabled');
                    $('.hideInfo').removeClass('d-none')
                    gateway = gatewayElement.data('gateway');

                    if(!plan){
                        minAmount = gatewayElement.data('min-amount');
                        maxAmount = gatewayElement.data('max-amount');
                    }

                    let processingFeeInfo =
                        `${parseFloat(gateway.percent_charge).toFixed(2)}% with ${parseFloat(gateway.fixed_charge).toFixed(2)} {{ __(gs('cur_text')) }} charge for payment gateway processing fees`
                    $(".proccessing-fee-info").attr("data-bs-original-title", processingFeeInfo);
                    calculation();
                }

            }
            gatewayChange();

            function planChange() {
                let planElement = $('.plan-input:checked');
                let price = planElement.data('price'); // Get the price from data attribute

                $('.amount').val(price).trigger('input'); // Set the amount input field to the selected plan's price and trigger input event
                resetCouponFields();
                gatewayChange(); // Recalculate charges based on the new amount
            }

            $(".more-gateway-option").on("click", function(e) {
                let paymentList = $(".gateway-option-list");
                paymentList.find(".gateway-option").removeClass("d-none");
                $(this).addClass('d-none');
                paymentList.animate({
                    scrollTop: (paymentList.height() - 60)
                }, 'slow');
            });

            $('#apply_coupon').click(function() {
                var couponCode = $('#coupon_code').val();
                var planPrice = parseFloat($('.amount').val()) || 0;
                if (couponCode === '') {
                    $('#coupon_response').html('<div class="alert alert-danger">please enter a coupon code.</div>').show();
                    return;  // Prevent empty submissions
                }
                $.ajax({
                    url: "{{ route('user.billing.apply.coupon') }}", // Ensure this matches your actual route
                    type: "POST",
                    data: {
                        coupon_code: couponCode,
                        price: planPrice,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if(response.success) {
                            $('.amount').val(response.new_price);
                            $('.discount-amount').text('-' + response.discount_amount);
                            calculation(); // Recalculate other amounts based on new price
                            $('.discount-percentage').text(' (' + response.discount_percentage + '%)').css('color', 'green');
                            $('.final-amount').text(response.total_price);
                            $('#coupon_response').html('<div class="alert alert-success">' + response.message + '</div>').show();
                            $('#remove_coupon').show();
                            $('#apply_coupon').hide(); // Show the remove coupon button when a coupon is successfully applied
                            $('#coupon_code').prop('disabled', true);  // Disable the input field
                            $('#coupon_code').css('background-color', '#e9ecef');  // Change background color to grey to indicate it's disabled
                        } else {
                            $('#coupon_response').html('<div class="alert alert-danger">' + response.message + '</div>').show();
                            $('#remove_coupon').hide(); // Hide the remove coupon button if coupon application fails
                            $('#apply_coupon').show();
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#coupon_response').html('<div class="alert alert-danger">Error applying coupon. Please try again.</div>').show();
                    }
                });
            });

            $('#remove_coupon').click(function() {
                var planElement = $('.plan-input:checked'); // Get the checked plan input
                var originalPrice = planElement.data('original-price'); // Get the original price from the data attribute

                $.ajax({
                    url: "{{ route('user.billing.remove.coupon') }}", // Ensure this matches your actual route
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if(response.success) {
                            $('.amount').val(originalPrice); // Reset the amount to the original price of the selected plan
                            $('.discount-amount').text('0.00');
                            calculation(); // Recalculate other amounts based on new price
                            $('.discount-percentage').text('').css('color', '');
                            $('#coupon_response').html('<div class="alert alert-success">' + response.message + '</div>').show();
                            $('#remove_coupon').hide(); // Hide the remove coupon button after removing the coupon
                            $('#apply_coupon').show();
                            $('#coupon_code').prop('disabled', false); // Enable the coupon code input field
                            $('#coupon_code').css('background-color', ''); // Reset background color to default
                        } else {
                            $('#coupon_response').html('<div class="alert alert-danger">' + response.message + '</div>').show();
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#coupon_response').html('<div class="alert alert-danger">Error applying coupon. Please try again.</div>').show();
                    }
                });
            });
            function resetCouponFields() {
                $('.discount-amount').text('0.00');
                $('.discount-percentage').text('').css('color', '');
                $('.final-amount').text($('.amount').val()); // Reset the final amount to the original price
                $('#coupon_response').empty().hide(); // Clear any messages related to the coupon
                $('#coupon_code').val('').prop('disabled', false).css('background-color', ''); // Enable and clear the coupon code input
                $('#remove_coupon').hide();
                $('#apply_coupon').show();
            }

            function calculation() {
                if (!gateway) return;

                
                if(!plan){
                $(".gateway-limit").text(minAmount + " - " + maxAmount);
                }

    
                let percentCharge =0;
                let fixedCharge =0;
                let totalPercentCharge = 0;

                if (amount) {
                percentCharge = parseFloat(gateway.percent_charge);

                fixedCharge = parseFloat(gateway.fixed_charge);
                    totalPercentCharge = parseFloat(amount / 100 * percentCharge);
                }

                let totalCharge = parseFloat(totalPercentCharge + fixedCharge);
                let totalAmount = parseFloat((amount || 0) + totalPercentCharge + fixedCharge);

                $(".final-amount").text(totalAmount.toFixed(2));
                $(".processing-fee").text(totalCharge.toFixed(2));
                $("input[name=currency]").val(gateway.currency);
                $(".gateway-currency").text(gateway.currency);

             
                @if (!$planId) 
                    if (amount < Number(gateway.min_amount) || amount > Number(gateway.max_amount)) {
                        $(".deposit-form button[type=submit]").attr('disabled', true);
                    } else {
                        $(".deposit-form button[type=submit]").removeAttr('disabled');
                    }
                @endif

                if (gateway.currency != "{{ gs('cur_text') }}" && gateway.method.crypto != 1) {
                    $('.deposit-form').addClass('adjust-height')

                    $(".gateway-conversion, .conversion-currency").removeClass('d-none');
                    $(".gateway-conversion").find('.deposit-info__input .text').html(
                        `1 {{ __(gs('cur_text')) }} = <span class="rate">${parseFloat(gateway.rate).toFixed(2)}</span>  <span class="method_currency">${gateway.currency}</span>`
                    );
                    $('.in-currency').text(parseFloat(totalAmount * gateway.rate).toFixed(gateway.method.crypto == 1 ?
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
            }

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })


            $('.gateway-input').change();
            $(document).ready(function() {
                // Trigger changes manually on page load to initialize all fields
                $('.plan-input:checked').change();  // This will call planChange if you have bound it to the change event
                $('.gateway-input:checked').change();  // Ensure the gateway is also set if there's a default one

                // Other initialization if necessary
            });

        })(jQuery);
</script>
@endpush
