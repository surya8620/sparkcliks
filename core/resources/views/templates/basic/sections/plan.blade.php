@php
    $planContent = getContent('plan.content', true);
    $plans = App\Models\Plan::where('status', 1)->get();
    $classes = ['text--base', 'text--primary', 'text--base-three', 'text--base-two', 'text--dark', 'text--success'];

    $gatewayCurrency = App\Models\GatewayCurrency::whereHas('method', function ($gate) {
        $gate->where('status', 1);
    })
        ->with('method')
        ->orderby('name')
        ->get();
    $index = 0;
@endphp

<div class="price-plan pt-85 pb-170">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="section-heading style-center text-center">
                    <span class="section-heading__subtitle"> {{ __(@$planContent->data_values->section_title) }} </span>
                    <h2 class="section-heading__title" s-break="4" s-color="bg--base-two text-white">
                        {{ __(@$planContent->data_values->heading) }}</h2>
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
                                <a class="btn btn--base w-100" href="{{ route('user.buyPlan', @$plan->id) }}"
                                    data-plan="{{ $plan }}">@lang('Subscribe Now')</a>
                            @endif
                        </div>
                        <div class="price-item__footer">
                            <ul class="price-item__list">
                                <li><i class="far fa-check-circle {{ $class }}"></i> <span>@lang('Daily Limit') :
                                        {{ $plan->user_limit }} @lang('PTC')</span></li>
                                <li><i class="far fa-check-circle {{ $class }}"></i> <span> @lang('Referral Bonus') :
                                        @lang('Upto') {{ $plan->ref_level }} @lang('Level') </span></li>
                                <li><i class="far fa-check-circle {{ $class }}"></i> <span>@lang('Plan Price') :
                                        {{ showAmount($plan->price) }} </span></li>
                                <li><i class="far fa-check-circle {{ $class }}"></i> <span>@lang('Validity') :
                                        {{ $plan->validity }} @lang('Days') </span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
