@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $planContent = getContent('plan.content', true);
        $classes     = ['text--base', 'text--primary', 'text--base-three', 'text--base-two', 'text--dark', 'text--success'];
        $index       = 0;
    @endphp
    <div class="price-plan pt-85 pb-170">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="section-heading style-center text-center">
                        <span class="section-heading__subtitle"> {{ __($planContent->data_values->section_title) }} </span>
                        <h2 class="section-heading__title" s-break="4" s-color="bg--base-two text-white">
                            {{ __($planContent->data_values->heading) }}</h2>
                    </div>
                </div>
            </div>
            <div class="row gy-4">
                @foreach ($plans as $plan)
                    @php
                        $class = @$classes[$index];
                        $index >= 5 ? ($index = 0) : $index++;
                    @endphp
                    <div class="col-lg-4 col-md-6">
                        <div class="price-item section-bg">
                            @if ($plan->highlight == 1)
                                <span class="price-item__badge">@lang('Most Popular')</span>
                            @endif
                            <div class="price-item__header">
                                <h5 class="price-item__title {{ $class }}">
                                    {{ __($plan->name) }}
                                </h5>
                                <p class="price-item__desc">
                                    {{ __($plan->tagline) }}
                                </p>
                            </div>
                            <div class="price-item__body">
                                <h2 class="price-item__price {{ $class }} mb-0">
                                    {{ getAmount($plan->price) }}<span>/{{ __(gs('cur_text')) }}</span>
                                </h2>
                                @if (@auth()->user()->runningPlan && @auth()->user()->plan_id == $plan->id)
                                    <button class="btn btn--base w-100 " disabled>@lang('Current Package')</button>
                                @else
                                    <a class=" btn btn--base w-100 " href="{{ route('user.buyPlan', @$plan->id) }}">@lang('Get Started')</a>
                                @endif
                            </div>
                            <div class="price-item__footer">
                                <ul class="price-item__list">
                                    <li><i class="far fa-check-circle {{ $class }}"></i> <span>@lang('Daily Limit') :
                                            {{ $plan->user_limit }} @lang('PTC')</span></li>
                                    <li><i class="far fa-check-circle {{ $class }}"></i> <span> @lang('Referral Bonus') :
                                            @lang('Upto') {{ $plan->ref_level }} @lang('Level') </span></li>
                                    <li><i class="far fa-check-circle {{ $class }}"></i> <span>@lang('Plan Price') :
                                            {{ showAmount($plan->price) }}</span></li>
                                    <li><i class="far fa-check-circle {{ $class }}"></i> <span> @lang('Validity') :
                                            {{ $plan->validity }} @lang('Days') </span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>


    <div class="modal custom--modal fade" id="BuyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <form method="post" action="{{ route('user.buyPlan') }}">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-header">
                        <strong class="modal-title"> @lang('Confirmation to purchase ')<span class="planName"></span>
                            @lang('Plan')</strong>

                        <button type="button" class="close btn btn--sm btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">

                        @auth
                            <div class="form-group">
                                @if (auth()->user()->runningPlan)
                                    <code class="d-block">@lang('If you subscribe to this one. Your old limitation will reset according to this package.')</code>
                                @endif

                                <h6 class="text-center dailyLimit"></h6>
                                <p class="text-center refLevel"></p>
                                <p class="text-center mt-1 validity"></p>

                                <label>@lang('Select Wallet')</label>
                                <select class="form--control" name="wallet_type" required>
                                    <option value="">@lang('Select One')</option>
                                    @if (auth()->user()->balance > 0)
                                        <option value="deposit_wallet">@lang('Deposit Wallet - ' . showAmount(auth()->user()->balance))</option>
                                    @endif
                                    @foreach ($gatewayCurrency as $data)
                                        <option value="{{ $data->id }}" @selected(old('wallet_type') == $data->method_code) data-gateway="{{ $data }}">
                                            {{ $data->name }}</option>
                                    @endforeach
                                </select>
                                <code class="gateway-info rate-info d-none">@lang('Rate'): 1 {{ __(gs('cur_text')) }}
                                    = <span class="rate"></span> <span class="method_currency"></span></code>
                            </div>
                            <div class="form-group">
                                <label>@lang('Invest Amount')</label>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control form--control" name="amount" required>
                                    <span class="input-group-text text-white bg--base">{{ __(gs('cur_text')) }}</span>
                                </div>
                                <code class="gateway-info d-none">@lang('Charge'): <span class="charge"></span>
                                    {{ __(gs('cur_text')) }}. @lang('Total amount'): <span class="total"></span>
                                    {{ __(gs('cur_text')) }}</code>
                            </div>
                        @else
                            <p>@lang('Please login to subscribe plan')</p>
                        @endauth
                    </div>
                    <div class="modal-footer">
                        @auth
                            <button type="button" class="btn btn-dark" data-bs-dismiss="modal">@lang('No')</button>
                            <button type="submit" class="btn btn--base">@lang('Yes')</button>
                        @else
                            <a href="{{ route('user.login') }}" class="btn btn--base w-100">@lang('Login')</a>
                        @endauth
                    </div>

                </form>

            </div>
        </div>
    </div>

    @if (@$sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif

@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.buyBtn').on("click", function() {
                let symbol = '{{ gs('cur_sym') }}';
                let currency = '{{ __(gs('cur_text')) }}';
                $('.gateway-info').addClass('d-none');
                let modal = $('#BuyModal');
                let plan = $(this).data('plan')
                modal.find('.planName').text(plan.name)
                modal.find('[name=id]').val(plan.id)
                let planPrice = parseFloat(plan.price).toFixed(2);
                modal.find('[name=amount]').val(planPrice);
                modal.find('[name=amount]').attr('readonly', true);

                modal.find('.dailyLimit').html(
                    `Daily Ads Limit:<span class="text--danger"> ${plan.user_limit}</span>`)
                modal.find('.refLevel').html(
                    `Referral Level: <span class="text--danger">${plan.ref_level} </span>`)
                modal.find('.validity').html(
                    `Plan Validity:  <span class="text--danger"> ${plan.validity} Days </span>`)

                $('[name=amount]').on('input', function() {
                    $('[name=wallet_type]').trigger('change');
                })

                $('[name=wallet_type]').on("change", function() {
                    var amount = $('[name=amount]').val();
                    if ($(this).val() != 'deposit_wallet' && $(this).val() != 'interest_wallet' &&
                        amount) {
                        var resource = $('select[name=wallet_type] option:selected').data('gateway');
                        var fixed_charge = parseFloat(resource.fixed_charge);
                        var percent_charge = parseFloat(resource.percent_charge);
                        var charge = parseFloat(fixed_charge + (amount * percent_charge / 100)).toFixed(
                            2);
                        $('.charge').text(charge);
                        $('.rate').text(parseFloat(resource.rate));
                        $('.gateway-info').removeClass('d-none');
                        if (resource.currency == '{{ __(gs('cur_text')) }}') {
                            $('.rate-info').addClass('d-none');
                        } else {
                            $('.rate-info').removeClass('d-none');
                        }
                        $('.method_currency').text(resource.currency);
                        $('.total').text(parseFloat(charge) + parseFloat(amount));
                    } else {
                        $('.gateway-info').addClass('d-none');
                    }
                });
                modal.find('input[name=id]').val(plan.id);
                modal.modal('show');
            });




        })(jQuery);
    </script>
@endpush
