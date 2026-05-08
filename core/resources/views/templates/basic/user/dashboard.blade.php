@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row gy-4 justify-content-center">
        <div class="col-12">
            <!-- Dashboard Card Start -->
            <div class="row gy-4 dashboard-widget-wrapper">
                <div class="col-xxl-3 col-sm-6">
                    <div class="dashboard-widget d-flex justify-content-between flex-wrap gap-3">
                        <div class="dashboard-widget__left flex-between">
                            <div class="widget-two__icon b-radius--5 bg--green">
                                <i class="fab la-servicestack"></i>
                            </div>
                        </div>
                        <div class="dashboard-widget__content">
                            <div
                                class="dashboard-widget__number d-flex align-items-center justify-content-between flex-wrap">
                                <span class="dashboard-widget__number-amount">
                                    <h4>
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
                                    </h4>
                                </span>
                            </div>
                            <span class="dashboard-widget__text"> @lang('Membership Pack') </span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <div class="dashboard-widget d-flex justify-content-between flex-wrap gap-3">
                        <div class="dashboard-widget__left flex-between">
                            <div class="dashboard-widget__left-thumb">
                                <div class="widget-two__icon b-radius--5 bg--green">
                                    <i class="la la-coins"></i>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-widget__content">
                            <div
                                class="dashboard-widget__number d-flex align-items-center justify-content-between flex-wrap">
                                <span class="dashboard-widget__number-amount">
                                    <h4>
                                        @if ($widget['mem_type'] <= 8)
                                            <span class="amount">{{ getamount($widget['mem_credit']) }}</span>
                                        @else
                                            <span class="amount">{{ getamount($widget['seocredit']) }}</span>
                                        @endif
                                    </h4>
                                </span>
                            </div>
                            <span class="dashboard-widget__text"> @lang('Total Credits') </span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <div class="dashboard-widget d-flex justify-content-between flex-wrap gap-3 bg--red">
                        <div class="dashboard-widget__left flex-between">
                            <div class="dashboard-widget__left-thumb">
                                <div class="widget-two__icon b-radius--5 text-white">
                                    <i class="la la-hourglass-half"></i>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-widget__content">
                            <div
                                class="dashboard-widget__number d-flex align-items-center justify-content-between flex-wrap">
                                <span class="dashboard-widget__number-amount fs-6 text-white">
                                    <h4>
                                        @if ($widget['cur_time'] > $widget['mem_exp'])
                                            <span class="amount text-white">@lang('Expired')</span>
                                        @else
                                            <span class="amount text-white">@lang('Active')</span>
                                        @endif
                                    </h4>
                                </span>
                            </div>
                            <span class="dashboard-widget__text text-white"> @lang('Membership Status') </span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <div class="dashboard-widget d-flex justify-content-between flex-wrap gap-3">
                        <div class="dashboard-widget__left flex-between">
                            <div class="dashboard-widget__left-thumb">
                                <div class="widget-two__icon b-radius--5 bg--green">
                                    <i class="la la-clock-o"></i>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-widget__content">
                            <div
                                class="dashboard-widget__number d-flex align-items-center justify-content-between flex-wrap">
                                <span class="dashboard-widget__number-amount " style="font-size: 14px;">
                                    <h4>
                                        @if ($widget['mem_type'] <= 8)
                                            <small class="amount">{{ showMonthTime($widget['mem_exp']) }}</small>
                                        @else
                                            <span class="amount">@lang('Unlimited')</span>
                                        @endif
                                    </h4>
                                </span>
                            </div>
                            <span class="dashboard-widget__text"> @lang('Membership Validity') </span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4 col-sm-6">
                    <div class="dashboard-widget d-flex justify-content-between flex-wrap gap-3">
                        <div class="dashboard-widget__left flex-between">
                            <div class="dashboard-widget__left-thumb">
                                <img src="{{ asset($activeTemplateTrue . 'images/thumbs/today-click.png') }}">
                            </div>
                        </div>
                        <div class="dashboard-widget__content">
                            <a href="#" class="dashboard-widget__text"> @lang("Today's View") </a>
                            <div
                                class="dashboard-widget__number d-flex align-items-center justify-content-between flex-wrap">
                                <span class="dashboard-widget__number-amount">

                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6 col-sm-6">
                    <div class="dashboard-widget d-flex justify-content-between flex-wrap gap-3">
                        <div class="dashboard-widget__left flex-between">
                            <div class="dashboard-widget__left-thumb"><img
                                    src="{{ asset($activeTemplateTrue . 'images/thumbs/referral-commission.png') }}">
                            </div>
                        </div>
                        <div class="dashboard-widget__content">
                            <a href="#" class="dashboard-widget__text"> @lang('Referral Credits') </a>
                            <div
                                class="dashboard-widget__number d-flex align-items-center justify-content-between flex-wrap">
                                <span class="dashboard-widget__number-amount">

                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6 col-sm-6">
                    <div class="dashboard-widget d-flex justify-content-between flex-wrap gap-3">
                        <div class="dashboard-widget__left flex-between">
                            <div class="dashboard-widget__left-thumb"><img
                                    src="{{ asset($activeTemplateTrue . 'images/thumbs/reminder-click.png') }}">
                            </div>
                        </div>
                        <div class="dashboard-widget__content">
                            <span class="dashboard-widget__text"> @lang('Next Reminder') </span>
                            <div
                                class="dashboard-widget__number d-flex align-items-center justify-content-between flex-wrap">
                                <span class="dashboard-widget__number-amount">
                                    <div class="dashboard-widget__content">
                                        <h4 class="dashboard-widget__amount timer" id="counter"></h4>
                                    </div>

                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Dashboard Card End -->
        </div>
    </div>


    <div class="mt-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">@lang('Credit Report')</h5>
                <div id="apex-bar-chart"></div>
            </div>
        </div>
    </div>
@endsection
