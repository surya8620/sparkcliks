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
                <div class="alert alert-primary d-flex justify-content-between align-items-center py-2 mb-4">
                    <div>
                        <span class="fw-bold">{{ $spPayment['plan_name'] }}</span>
                        <small class="text-muted ms-2">@lang('via SparkProxy')</small>
                    </div>
                    <span class="fw-bold fs-5">
                        {{ gs('cur_sym') }}{{ number_format($spPayment['amount'], 2) }}
                        <small class="text-muted">{{ $spPayment['currency'] }}</small>
                    </span>
                </div>

                <div class="alert alert-info py-2 mb-4">
                    <small>
                        <i class="las la-info-circle"></i>
                        @lang('Choose any payment method below. Once your payment is confirmed, your SparkProxy plan will be activated automatically within seconds.')
                    </small>
                </div>

                {{-- Payment form — posts to the existing billing insert route --}}
                <form action="{{ route('user.billing.insert') }}" method="post" id="sparkproxy-pay-form">
                    @csrf
                    <input type="hidden" name="currency">
                    {{-- Hidden field carrying the SparkProxy ref; gets written to deposits.sparkproxy_ref --}}
                    <input type="hidden" name="sparkproxy_ref" value="{{ $spPayment['ref'] }}">
                    {{-- Override amount from the signed token (server validated) --}}
                    <input type="hidden" name="sparkproxy_amount" value="{{ $spPayment['amount'] }}">
                    <input type="hidden" name="sparkproxy_return_url" value="{{ $spPayment['return_url'] }}">

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
                                       @else @checked($loop->first) @endif
                                       data-min-amount="{{ showAmount($data->min_amount) }}"
                                       data-max-amount="{{ showAmount($data->max_amount) }}">
                            </label>
                        @endforeach

                        @if ($gatewayCurrency->count() > 4)
                            <button type="button" class="payment-item__btn more-gateway-option">
                                <p class="payment-item__btn-text">@lang('Show All Payment Options')</p>
                                <span class="payment-item__btn__icon"><i class="fas fa-chevron-down"></i></span>
                            </button>
                        @endif
                    </div>

                    <button type="submit" class="btn btn--base w-100 py-3 fw-bold">
                        <i class="las la-lock me-2"></i>
                        @lang('Pay') {{ gs('cur_sym') }}{{ number_format($spPayment['amount'], 2) }} @lang('Securely')
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
