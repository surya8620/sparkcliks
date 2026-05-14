@extends($activeTemplate . 'layouts.master')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7 col-md-9">

        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex align-items-center gap-2 py-3">
                <i class="las la-bolt text-warning fs-4"></i>
                <h5 class="mb-0 fw-bold">@lang('SparkProxy Plan Payment')</h5>
            </div>

            <div class="card-body">

                {{-- Plan summary --}}
                <div class="alert alert-primary d-flex justify-content-between align-items-center py-2 mb-3">
                    <div>
                        <span class="fw-bold">{{ $spPayment['plan_name'] }}</span>
                        <small class="text-muted ms-2">@lang('via SparkProxy')</small>
                    </div>
                    <span class="fw-bold fs-5">
                        {{ gs('cur_sym') }}{{ number_format($spPayment['amount'], 2) }}
                        <small class="text-muted">{{ $spPayment['currency'] }}</small>
                    </span>
                </div>

                {{-- Fee breakdown (populated by JS once a gateway is chosen) --}}
                <div class="card bg-light border-0 mb-3 fee-breakdown d-none">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>@lang('Plan price')</span>
                            <span>{{ gs('cur_sym') }}<span class="sp-base-amount">{{ number_format($spPayment['amount'], 2) }}</span></span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>@lang('Processing fee')</span>
                            <span>{{ gs('cur_sym') }}<span class="sp-charge">0.00</span></span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1 sp-vat-row d-none">
                            <span>@lang('VAT')</span>
                            <span>{{ gs('cur_sym') }}<span class="sp-vat">0.00</span></span>
                        </div>
                        <hr class="my-1">
                        <div class="d-flex justify-content-between fw-bold">
                            <span>@lang('Total')</span>
                            <span><span class="sp-currency">{{ gs('cur_text') }}</span> <span class="sp-total">{{ number_format($spPayment['amount'], 2) }}</span></span>
                        </div>
                        <div class="sp-conversion-row d-none">
                            <small class="text-muted">
                                @lang('≈') <span class="sp-converted"></span> <span class="sp-gateway-currency"></span>
                                @lang('after conversion')
                            </small>
                        </div>
                    </div>
                </div>

                <form action="{{ route('user.sparkproxy.pay.insert') }}" method="post" id="sparkproxy-pay-form" class="deposit-form">
                    @csrf
                    <input type="hidden" name="currency" id="sp_currency">

                    <div class="payment-system-list is-scrollable gateway-option-list mb-3">
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
                                         alt="">
                                </div>
                                <input class="payment-item__radio gateway-input"
                                       id="{{ titleToKey($data->name) }}" hidden
                                       data-gateway='@php echo json_encode($data) @endphp'
                                       type="radio" name="gateway" value="{{ $data->method_code }}"
                                       @if (old('gateway')) @checked(old('gateway') == $data->method_code)
                                       @else @checked($loop->first) @endif>
                            </label>
                        @endforeach

                        @if ($gatewayCurrency->count() > 4)
                            <button type="button" class="payment-item__btn more-gateway-option">
                                <p class="payment-item__btn-text">@lang('Show All Payment Options')</p>
                                <span class="payment-item__btn__icon"><i class="fas fa-chevron-down"></i></span>
                            </button>
                        @endif
                    </div>

                    <button type="submit" class="btn btn--base w-100 py-3 fw-bold" id="sp-pay-btn" disabled>
                        <i class="las la-lock me-2"></i>
                        @lang('Pay') <span class="sp-btn-amount">...</span> @lang('Securely')
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="{{ $spPayment['return_url'] }}" class="text-muted small">
                        <i class="las la-arrow-left"></i> @lang('Cancel and return to SparkProxy')
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection

@push('script')
<script>
"use strict";
(function ($) {
    var baseAmount = {{ (float) $spPayment['amount'] }};
    var siteCurrency = "{{ gs('cur_text') }}";
    var curSym = "{{ gs('cur_sym') }}";

    function calculate() {
        var checked = $('.gateway-input:checked');
        if (!checked.length) return;

        var gateway = checked.data('gateway');
        if (!gateway) return;

        var charge    = parseFloat(gateway.fixed_charge) + (baseAmount * parseFloat(gateway.percent_charge) / 100);
        var afterCharge = baseAmount + charge;
        var vat       = afterCharge * parseFloat(gateway.vat_charge || 0) / 100;
        var total     = afterCharge + vat;           // in site currency
        var converted = total * parseFloat(gateway.rate); // in gateway currency

        // Update breakdown
        $('.sp-charge').text(charge.toFixed(2));
        $('.sp-vat').text(vat.toFixed(2));
        if (vat > 0) {
            $('.sp-vat-row').removeClass('d-none');
        } else {
            $('.sp-vat-row').addClass('d-none');
        }

        var isForeign = gateway.currency !== siteCurrency && gateway.method.crypto != 1;
        if (isForeign || gateway.method.crypto == 1) {
            $('.sp-currency').text(gateway.currency);
            $('.sp-total').text(converted.toFixed(gateway.method.crypto == 1 ? 8 : 2));
            $('.sp-converted').text('');
            $('.sp-conversion-row').addClass('d-none');
        } else {
            $('.sp-currency').text(siteCurrency);
            $('.sp-total').text(total.toFixed(2));
            if (parseFloat(gateway.rate) !== 1) {
                $('.sp-converted').text(converted.toFixed(2));
                $('.sp-gateway-currency').text(gateway.currency);
                $('.sp-conversion-row').removeClass('d-none');
            } else {
                $('.sp-conversion-row').addClass('d-none');
            }
        }

        // Min/max guard
        var payable = isForeign || gateway.method.crypto == 1 ? converted : total;
        if (payable < parseFloat(gateway.min_amount) || payable > parseFloat(gateway.max_amount)) {
            $('#sp-pay-btn').attr('disabled', true);
            $('.fee-breakdown').removeClass('d-none');
            return;
        }

        // Button label
        var displayAmt = (isForeign || gateway.method.crypto == 1)
            ? converted.toFixed(gateway.method.crypto == 1 ? 8 : 2) + ' ' + gateway.currency
            : curSym + total.toFixed(2);
        $('.sp-btn-amount').text(displayAmt);

        // Populate hidden currency input
        $('#sp_currency').val(gateway.currency);

        $('.fee-breakdown').removeClass('d-none');
        $('#sp-pay-btn').removeAttr('disabled');
    }

    // Run on page load with first gateway selected
    calculate();

    // Re-run on gateway change
    $(document).on('change', '.gateway-input', function () {
        calculate();
    });

    // Show more gateways
    $(document).on('click', '.more-gateway-option', function () {
        $('.gateway-option.d-none').removeClass('d-none');
        $(this).remove();
    });
}(jQuery));
</script>
@endpush

