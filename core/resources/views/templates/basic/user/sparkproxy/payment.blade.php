@extends($activeTemplate . 'layouts.app')

@push('style')
<style>
    *, *::before, *::after { box-sizing: border-box; }
    body { background: #f6f9fc; margin: 0; }

    .sp-checkout {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* ── Header ── */
    .sp-checkout__header {
        background: #fff;
        border-bottom: 1px solid #e6ebf1;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 100;
    }
    .sp-logo {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }
    .sp-logo__icon {
        width: 34px; height: 34px;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 17px;
        flex-shrink: 0;
    }
    .sp-logo__text { font-size: 16px; font-weight: 700; color: #1a1f36; }
    .sp-logo__sub  { font-size: 11px; color: #8898aa; display: block; line-height: 1; margin-top: 1px; }
    .sp-secure-badge { font-size: 12px; color: #8898aa; display: flex; align-items: center; gap: 4px; white-space: nowrap; }
    .sp-header-cancel {
        font-size: 13px; color: #8898aa; text-decoration: none;
        display: flex; align-items: center; gap: 4px;
        transition: color .15s; white-space: nowrap;
    }
    .sp-header-cancel:hover { color: #e53e3e; }

    /* ── Body ── */
    .sp-checkout__body {
        flex: 1;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 32px 16px 48px;
        gap: 0;
    }

    /* ── Panels ── */
    .sp-summary, .sp-payment {
        background: #fff;
        border: 1px solid #e6ebf1;
        padding: 28px 24px;
    }
    .sp-summary {
        width: 360px;
        min-width: 0;
        border-radius: 12px 0 0 12px;
        border-right: none;
    }
    .sp-payment {
        width: 420px;
        min-width: 0;
        border-radius: 0 12px 12px 0;
    }

    /* ── Summary content ── */
    .sp-summary__to {
        font-size: 11px; color: #8898aa; text-transform: uppercase;
        letter-spacing: .6px; margin-bottom: 3px;
    }
    .sp-summary__merchant {
        font-size: 18px; font-weight: 700; color: #1a1f36; margin-bottom: 20px;
    }
    .sp-summary__amount {
        font-size: 38px; font-weight: 800; color: #1a1f36; line-height: 1;
        word-break: break-all;
    }
    .sp-summary__currency {
        font-size: 16px; font-weight: 600; color: #8898aa; margin-left: 4px;
    }
    .sp-summary__plan {
        margin-top: 8px; font-size: 14px; color: #525f7f;
    }
    .sp-summary__divider {
        border: none; border-top: 1px solid #e6ebf1; margin: 20px 0;
    }
    .sp-summary__line {
        display: flex; justify-content: space-between; align-items: baseline;
        font-size: 13px; color: #525f7f; margin-bottom: 8px; gap: 8px;
    }
    .sp-summary__line--total {
        font-size: 15px; font-weight: 700; color: #1a1f36; margin-top: 4px;
    }
    .sp-summary__back {
        margin-top: 28px; display: inline-flex; align-items: center;
        gap: 6px; font-size: 13px; color: #8898aa;
        text-decoration: none; transition: color .15s;
        touch-action: manipulation;
    }
    .sp-summary__back:hover { color: #4f46e5; }

    /* ── Payment panel ── */
    .sp-payment__label {
        font-size: 11px; text-transform: uppercase; letter-spacing: .7px;
        color: #8898aa; font-weight: 600; margin-bottom: 10px;
    }

    .sp-gateway-list {
        display: flex; flex-direction: column; gap: 8px; margin-bottom: 18px;
    }
    .sp-gateway-item {
        display: flex; align-items: center; justify-content: space-between;
        border: 1.5px solid #e6ebf1; border-radius: 8px;
        padding: 10px 14px; cursor: pointer;
        transition: border-color .15s, background .15s;
        -webkit-tap-highlight-color: transparent;
        touch-action: manipulation;
        min-height: 48px; /* comfortable touch target */
    }
    .sp-gateway-item:hover  { border-color: #4f46e5; background: #f8f7ff; }
    .sp-gateway-item.selected { border-color: #4f46e5; background: #f8f7ff; }
    .sp-gateway-item__info { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .sp-gateway-item__img  { width: 40px; height: 26px; object-fit: contain; flex-shrink: 0; }
    .sp-gateway-item__name { font-size: 14px; color: #1a1f36; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .sp-gateway-item__radio { accent-color: #4f46e5; width: 18px; height: 18px; flex-shrink: 0; cursor: pointer; }

    .sp-more-btn {
        font-size: 13px; color: #4f46e5; background: none; border: none;
        padding: 6px 0; cursor: pointer; display: flex; align-items: center; gap: 4px;
        touch-action: manipulation;
    }

    .sp-pay-btn {
        width: 100%; padding: 15px;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: #fff; border: none; border-radius: 8px;
        font-size: 16px; font-weight: 700; cursor: pointer;
        transition: opacity .15s; margin-top: 4px;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
        min-height: 52px; /* thumb-friendly */
    }
    .sp-pay-btn:disabled { opacity: .5; cursor: not-allowed; }
    .sp-pay-btn:not(:disabled):active { opacity: .85; }
    .sp-pay-btn:not(:disabled):hover  { opacity: .9; }

    .sp-trust {
        text-align: center; margin-top: 14px;
        font-size: 12px; color: #8898aa;
    }

    .sp-footer {
        text-align: center; font-size: 12px; color: #8898aa;
        padding: 16px 16px 28px;
    }
    .sp-footer a { color: #8898aa; text-decoration: underline; }

    /* ─────────────────────────────────────────────
       MOBILE: stack panels vertically, full width
    ───────────────────────────────────────────── */
    @media (max-width: 820px) {
        .sp-checkout__body {
            flex-direction: column;
            align-items: stretch;
            padding: 0 0 48px;
            gap: 0;
        }
        .sp-summary {
            width: 100%;
            border-radius: 0;
            border-right: 1px solid #e6ebf1;
            border-bottom: none;
            padding: 20px 16px;
        }
        .sp-payment {
            width: 100%;
            border-radius: 0;
            padding: 20px 16px;
        }
        .sp-summary__amount { font-size: 30px; }
        .sp-summary__back   { margin-top: 20px; }
    }

    /* very small screens */
    @media (max-width: 380px) {
        .sp-checkout__header { padding: 10px 12px; }
        .sp-logo__text       { font-size: 14px; }
        .sp-logo__sub        { display: none; }
        .sp-secure-text      { display: none; }
        .sp-summary__amount  { font-size: 26px; }
        .sp-pay-btn          { font-size: 15px; }
    }
</style>
@endpush

@section('panel')
<div class="sp-checkout">

    {{-- Header --}}
    <div class="sp-checkout__header">
        <a href="{{ env('SPARKPROXY_URL', '#') }}" target="_blank" class="sp-logo">
            <div class="sp-logo__icon"><i class="las la-bolt"></i></div>
            <div>
                <span class="sp-logo__text">SparkProxy</span>
                <span class="sp-logo__sub">Secure Checkout</span>
            </div>
        </a>
        <div style="display:flex;align-items:center;gap:16px;">
            <a href="{{ $spPayment['return_url'] }}" class="sp-header-cancel">
                <i class="las la-times"></i> <span class="sp-header-cancel__text">Cancel</span>
            </a>
            <span class="sp-secure-badge">
                <i class="las la-lock"></i> <span class="sp-secure-text">SSL Secured</span>
            </span>
        </div>
    </div>

    {{-- Body --}}
    <div class="sp-checkout__body">

        {{-- Left: Order Summary --}}
        <div class="sp-summary">
            <div class="sp-summary__to">Pay to</div>
            <div class="sp-summary__merchant">SparkProxy</div>

            <div class="sp-summary__amount">
                {{ gs('cur_sym') }}<span id="sp-display-amount">{{ number_format($spPayment['amount'], 2) }}</span><span class="sp-summary__currency" id="sp-display-currency">{{ $spPayment['currency'] }}</span>
            </div>
            <div class="sp-summary__plan">{{ $spPayment['plan_name'] }}</div>

            <hr class="sp-summary__divider">

            {{-- Fee breakdown --}}
            <div id="sp-breakdown" style="display:none">
                <div class="sp-summary__line">
                    <span>Plan price</span>
                    <span>{{ gs('cur_sym') }}{{ number_format($spPayment['amount'], 2) }}</span>
                </div>
                <div class="sp-summary__line" id="sp-charge-row" style="display:none">
                    <span>Processing fee</span>
                    <span>{{ gs('cur_sym') }}<span id="sp-charge">0.00</span></span>
                </div>
                <div class="sp-summary__line" id="sp-vat-row" style="display:none">
                    <span>VAT</span>
                    <span>{{ gs('cur_sym') }}<span id="sp-vat">0.00</span></span>
                </div>
                <div class="sp-summary__line sp-summary__line--total">
                    <span>Total due</span>
                    <span><span id="sp-total-currency">{{ gs('cur_text') }}</span> <span id="sp-total">{{ number_format($spPayment['amount'], 2) }}</span></span>
                </div>
                <div id="sp-conversion-row" style="display:none; margin-top:6px;">
                    <small style="color:#8898aa">≈ <span id="sp-converted"></span> <span id="sp-converted-currency"></span> after conversion</small>
                </div>
            </div>

            <a href="{{ $spPayment['return_url'] }}" class="sp-summary__back">
                <i class="las la-arrow-left"></i> Cancel &amp; return to SparkProxy
            </a>
        </div>

        {{-- Right: Payment --}}
        <div class="sp-payment">
            <div class="sp-payment__label">Choose payment method</div>

            <form action="{{ route('user.sparkproxy.pay.insert') }}" method="post" id="sp-form">
                @csrf
                <input type="hidden" name="currency" id="sp_currency">

                <div class="sp-gateway-list" id="sp-gateway-list">
                    @foreach ($gatewayCurrency as $item)
                    <label class="sp-gateway-item @if($loop->index >= 5) sp-hidden @endif @if($loop->first) selected @endif"
                           for="spgw_{{ $item->method_code }}_{{ titleToKey($item->currency) }}">
                        <div class="sp-gateway-item__info">
                            <img class="sp-gateway-item__img"
                                 src="{{ getImage(getFilePath('gateway') . '/' . $item->method->image) }}"
                                 alt="{{ $item->name }}">
                            <span class="sp-gateway-item__name">{{ __($item->name) }}</span>
                        </div>
                        <input class="sp-gateway-item__radio"
                               type="radio" name="gateway"
                               id="spgw_{{ $item->method_code }}_{{ titleToKey($item->currency) }}"
                               value="{{ $item->method_code }}"
                               data-gateway='@json($item)'
                               @if($loop->first) checked @endif>
                    </label>
                    @endforeach

                    @if ($gatewayCurrency->count() > 5)
                    <button type="button" class="sp-more-btn" id="sp-show-more">
                        <i class="las la-chevron-down"></i> Show {{ $gatewayCurrency->count() - 5 }} more options
                    </button>
                    @endif
                </div>

                <button type="submit" class="sp-pay-btn" id="sp-pay-btn" disabled>
                    <i class="las la-lock"></i>
                    <span id="sp-btn-text">Calculating...</span>
                </button>
            </form>

            <div style="text-align:center; margin-top:16px;">
                <small style="color:#8898aa; font-size:12px;">
                    <i class="las la-shield-alt"></i>
                    Your payment is encrypted and secured by SparkCliks
                </small>
            </div>
        </div>

    </div>

    <div class="sp-footer">
        Powered by <strong>SparkCliks</strong> &bull;
        <a href="{{ route('home') }}" target="_blank">Terms</a> &bull;
        <a href="{{ route('home') }}" target="_blank">Privacy</a>
    </div>

</div>
@endsection

@push('script')
<style>.sp-hidden { display: none !important; }</style>
<script>
"use strict";
(function ($) {
    var baseAmount   = {{ (float) $spPayment['amount'] }};
    var siteCurrency = "{{ gs('cur_text') }}";
    var curSym       = "{{ gs('cur_sym') }}";

    function calculate() {
        var radio   = $('input[name=gateway]:checked');
        if (!radio.length) return;
        var gateway = radio.data('gateway');
        if (!gateway) return;

        var charge      = parseFloat(gateway.fixed_charge) + (baseAmount * parseFloat(gateway.percent_charge) / 100);
        var afterCharge = baseAmount + charge;
        var vat         = afterCharge * parseFloat(gateway.vat_charge || 0) / 100;
        var total       = afterCharge + vat;
        var rate        = parseFloat(gateway.rate) || 1;
        var converted   = total * rate;
        var isCrypto    = gateway.method && gateway.method.crypto == 1;
        var isForeign   = gateway.currency !== siteCurrency && !isCrypto;

        // Summary breakdown
        $('#sp-breakdown').show();
        if (charge > 0) {
            $('#sp-charge').text(charge.toFixed(2));
            $('#sp-charge-row').show();
        } else {
            $('#sp-charge-row').hide();
        }
        if (vat > 0) {
            $('#sp-vat').text(vat.toFixed(2));
            $('#sp-vat-row').show();
        } else {
            $('#sp-vat-row').hide();
        }

        var displayTotal, displayCurrency;
        if (isCrypto || isForeign) {
            displayTotal    = converted.toFixed(isCrypto ? 8 : 2);
            displayCurrency = gateway.currency;
            $('#sp-conversion-row').hide();
        } else {
            displayTotal    = total.toFixed(2);
            displayCurrency = siteCurrency;
            if (rate !== 1) {
                $('#sp-converted').text(converted.toFixed(2));
                $('#sp-converted-currency').text(gateway.currency);
                $('#sp-conversion-row').show();
            } else {
                $('#sp-conversion-row').hide();
            }
        }

        $('#sp-total').text(displayTotal);
        $('#sp-total-currency').text(displayCurrency);
        $('#sp-display-amount').text(displayTotal);
        $('#sp-display-currency').text(' ' + displayCurrency);

        // Min/max guard
        var payable = parseFloat(isCrypto || isForeign ? converted : total);
        if (payable < parseFloat(gateway.min_amount) || payable > parseFloat(gateway.max_amount)) {
            $('#sp-pay-btn').prop('disabled', true);
            $('#sp-btn-text').text('Amount out of gateway range');
            return;
        }

        var btnLabel = isCrypto || isForeign
            ? 'Pay ' + converted.toFixed(isCrypto ? 8 : 2) + ' ' + gateway.currency
            : 'Pay ' + curSym + total.toFixed(2) + ' ' + siteCurrency;

        $('#sp_currency').val(gateway.currency);
        $('#sp-btn-text').text(btnLabel);
        $('#sp-pay-btn').prop('disabled', false);
    }

    // Highlight selected gateway label
    $(document).on('change', 'input[name=gateway]', function () {
        $('.sp-gateway-item').removeClass('selected');
        $(this).closest('.sp-gateway-item').addClass('selected');
        calculate();
    });

    // Show more gateways
    $('#sp-show-more').on('click', function () {
        $('.sp-hidden').removeClass('sp-hidden');
        $(this).remove();
    });

    // Disable double submit
    $('#sp-form').on('submit', function () {
        $('#sp-pay-btn').prop('disabled', true).html('<i class="las la-spinner la-spin"></i> Processing...');
    });

    calculate();
}(jQuery));
</script>
@endpush
