@extends($activeTemplate . 'layouts.master')
@section('content')
    @php
        session()->forget('coupon');
    @endphp
    @if ($widget['mem_type'] == 0)
        <div class="dashboard-table__header d-flex justify-content-end pt-0 px-0">
            <div class="dashboard-table__btn">
                <button class="btn btn-sm btn--base" onclick="window.location.href='{{ route('user.seo.trial') }}'">
                    Activate Trial
                </button>
            </div>
        </div>
    @endif
    <div class="row mb-none-30">
        <div class="col-xl-12 col-sm-6 mb-30">
            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                <i class="fab la-servicestack overlay-icon text--warning"></i>
                <div class="widget-two__icon b-radius--5 bg--green">
                    <i class="fab la-servicestack text-white"></i>
                </div>
                <div class="widget-two__content">
                    <h3 class="">
                        @if ($widget['mem_type'] == 2)
                            <span class="amount">@lang('STARTER')</span>
                        @elseif($widget['mem_type'] == 3)
                            <span class="amount">@lang('LITE')</span>
                        @elseif($widget['mem_type'] == 4)
                            <span class="amount">@lang('BASIC')</span>
                        @elseif($widget['mem_type'] == 5)
                            <span class="amount">@lang('BRONZE')</span>
                        @elseif($widget['mem_type'] == 6)
                            <span class="amount">@lang('SILVER')</span>
                        @elseif($widget['mem_type'] == 7)
                            <span class="amount">@lang('GOLD')</span>
                        @elseif($widget['mem_type'] == 8)
                            <span class="amount">@lang('PLATINUM')</span>
                        @elseif($widget['mem_type'] == 9)
                            <span class="amount">@lang('DIAMOND')</span>
                        @elseif($widget['mem_type'] == 10)
                            <span class="amount">@lang('ONE TIME - $150')</span>
                        @elseif($widget['mem_type'] == 11)
                            <span class="amount">@lang('ONE TIME - $250')</span>
                        @elseif($widget['mem_type'] == 12)
                            <span class="amount">@lang('ONE TIME - $500')</span>
                        @elseif($widget['mem_type'] == 13)
                            <span class="amount">@lang('ONE TIME - $1000')</span>
                        @elseif($widget['mem_type'] == 14)
                            <span class="amount">@lang('ONE TIME - $2500')</span>
                        @elseif($widget['mem_type'] == 15)
                            <span class="amount">@lang('ONE TIME - $5000')</span>
                        @elseif($widget['mem_type'] == 16)
                            <span class="amount">@lang('ONE TIME PURCHASE')</span>
                        @elseif($widget['mem_type'] == 1)
                            <span class="amount">@lang('TRIAL')</span>
                        @else
                            <span class="amount">@lang('INACTIVE')</span>
                        @endif
                    </h3>
                    <p>@lang('Membership Pack')</p>
                </div>
            </div><!-- widget-two end -->
        </div>
    </div><br>
    <div class="row mb-none-30">
        <div class="col-xl-4 col-lg-4 col-sm-6 mb-4">
            <div class="dashboard-w1 bg--primary b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="la la-coins"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        @if ($widget['mem_type'] <= 8)
                            <span class="amount">{{ getamount($widget['mem_credit']) }}</span>
                        @else
                            <span class="amount">{{ getamount($widget['seocredit']) }}</span>
                        @endif
                    </div>
                    <div class="desciption">
                        <span class="text--medium">@lang('Credits')</span>
                    </div>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xl-4 col-lg-4 col-sm-6 mb-4">
            <div class="dashboard-w1 bg--teal b-radius--10 box-shadow">
                <div class="icon">
                    <i class="la la-hourglass-half"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        @if ($widget['cur_time'] > $widget['mem_exp'])
                            <span class="amount">@lang('Expired')</span>
                        @else
                            <span class="amount">@lang('Active')</span>
                        @endif
                    </div>
                    <div class="desciption">
                        <span class="text--medium">@lang('Membership Status')</span>
                    </div>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xl-4 col-lg-4 col-sm-6 mb-4">
            <div class="dashboard-w1 bg--red b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="la la-clock-o"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        @if ($widget['mem_type'] <= 8)
                            <span class="amount">{{ showMonthTime($widget['mem_exp']) }}
                            </span>
                        @else
                            <span class="amount">@lang('Unlimited')</span>
                        @endif
                    </div>
                    <div class="desciption">
                        <span class="text--medium">@lang('Membership Validity')</span>
                    </div>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
    </div><!-- row end-->
    <br>
    <div class="row mb-none-30">
        <div class="col-xl-4 col-sm-6 mb-4">
            <a href="{{ route('user.seo.history') }}" class="d-block text-decoration-none" style="color: inherit;">
                <div class="widget-two box--shadow2 b-radius--5 bg--white">
                    <i class="las la-shopping-cart overlay-icon text--primary"></i>
                    <div class="widget-two__icon b-radius--5 bg--primary">
                        <i class="las la-shopping-cart text-white"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="">{{ $widget['total_order'] }}</h3>
                        <p>@lang('Total')</p>
                    </div>
                </div><!-- widget-two end -->
            </a>
        </div>

        <div class="col-xl-4 col-sm-6 mb-4">
            <a href="{{ route('user.seo.pending') }}" class="d-block text-decoration-none" style="color: inherit;">
                <div class="widget-two box--shadow2 b-radius--5 bg--white">
                    <i class="las la-exclamation-triangle overlay-icon text--warning"></i>
                    <div class="widget-two__icon b-radius--5 bg--warning">
                        <i class="las la-exclamation-triangle text-white"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="">{{ $widget['pending_order'] }}</h3>
                        <p>@lang('Needs Action')</p>
                    </div>
                </div><!-- widget-two end -->
            </a>
        </div>

        <div class="col-xl-4 col-sm-6 mb-4">
            <a href="{{ route('user.seo.processing') }}" class="d-block text-decoration-none" style="color: inherit;">
                <div class="widget-two box--shadow2 b-radius--5 bg--white">
                    <i class="la la-refresh overlay-icon text--teal"></i>
                    <div class="widget-two__icon b-radius--5 bg--teal">
                        <i class="la la-refresh text-white"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="">{{ $widget['processing_order'] }}</h3>
                        <p>@lang('Active')</p>
                    </div>
                </div><!-- widget-two end -->
            </a>
        </div>
    </div>
    <br>
    <div class="row mt-50 mb-none-30">

        <div class="col-xl-4 col-sm-6 mb-4">
            <a href="{{ route('user.seo.completed') }}" class="d-block text-decoration-none" style="color: inherit;">
                <div class="widget-two box--shadow2 b-radius--5 bg--white">
                    <i class="las la-check-circle overlay-icon text--teal"></i>
                    <div class="widget-two__icon b-radius--5 bg--teal">
                        <i class="las la-check-circle text-white"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="">{{ $widget['completed_order'] }}</h3>
                        <p>@lang('Completed')</p>
                    </div>
                </div><!-- widget-two end -->
            </a>
        </div>

        <div class="col-xl-4 col-sm-6 mb-4">
            <a href="{{ route('user.seo.denied') }}" class="d-block text-decoration-none" style="color: inherit;">
                <div class="widget-two box--shadow2 b-radius--5 bg--white">
                    <i class="la la-times-circle overlay-icon text--pink"></i>
                    <div class="widget-two__icon b-radius--5 bg--pink">
                        <i class="la la-times-circle text-white"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="">{{ $widget['denied_order'] }}</h3>
                        <p>@lang('Denied')</p>
                    </div>
                </div><!-- widget-two end -->
            </a>
        </div>

        <div class="col-xl-4 col-sm-6 mb-4">
            <a href="{{ route('user.seo.cancelled') }}" class="d-block text-decoration-none" style="color: inherit;">
                <div class="widget-two box--shadow2 b-radius--5 bg--white">
                    <i class="la la-fast-backward overlay-icon text--secondary"></i>
                    <div class="widget-two__icon b-radius--5 bg--secondary">
                        <i class="la la-fast-backward text-white"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="">{{ $widget['cancelled_order'] }}</h3>
                        <p>@lang('Cancelled')</p>
                    </div>
                </div><!-- widget-two end -->
            </a>
        </div>
    </div>
@endsection
