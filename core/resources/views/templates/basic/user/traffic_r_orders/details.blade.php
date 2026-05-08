@extends($activeTemplate . 'layouts.master')
@section('content')
    @if ($order->status == 0)
        <div class="alert alert-danger alert-dismissible fade show d-flex" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>@lang('ERROR: ') {{ $order->error }} </strong></span>
        </div>
    @endif
    <div class="dashboard-table__header d-flex justify-content-end pt-0 px-0">
        <div class="row gy-4">
            @if ($order->status == 6)
                <div class="dashboard-table__btn">
                    <a href="javascript:void(0)" class="btn btn-outline--base btn--sm text--small playBtn"
                        title="Resume Campaign" data-original-title="@lang('Resume Campaign')" data-toggle="tooltip"
                        data-url="{{ route('user.realistic.resume', $order->id) }}">
                        <i class="fa-solid fa-play"></i> @lang(' Resume Campaign')
                    </a>
                </div>
            @elseif($order->status == 1)
                <div class="dashboard-table__btn">
                    <a href="javascript:void(0)" class="btn btn-outline--base btn--sm text--small pauseBtn"
                        title="Pause Campaign" data-original-title="@lang('Pause Campaign')" data-toggle="tooltip"
                        data-url="{{ route('user.realistic.pause', $order->id) }}">
                        <i class="fa-solid fa-pause"></i> @lang(' Pause Campaign')
                    </a>
                </div>
            @elseif($order->status == 5 || $order->status == 2)
                <div class="dashboard-table__btn">
                    <a href="javascript:void(0)" class="btn btn-outline--base btn--sm text--small renewBtn"
                        title="Pause Campaign" data-original-title="@lang('Renew Campaign')" data-toggle="tooltip"
                        data-url="{{ route('user.realistic.renew', $order->id) }}">
                        <i class="fa-solid fa-refresh"></i> @lang(' Renew Campaign')
                    </a>
                </div>
            @endif
        </div>
    </div>
    <div class="card custom--card">
        <div class="card-header">
            <div class="row mb-none-30">
                <!-- First Section -->
                <div class="form-group col-md-4">
                    <div class="dashboard-w1 bg--primary b-radius--10 box-shadow">
                        <div class="icon">
                            <i class="fa-solid fa-qrcode"></i>
                        </div>
                        <div class="details">
                            <div class="desciption">
                                <span class="text--medium">@lang('Campaign') <strong>(ID:
                                        {{ $order->id }})</strong></span>
                            </div>
                            <div class="numbers">
                                @if ($order->traffic_plan == 111)
                                    <span class="amount">@lang('Nano / 2000 Visitors')</span>
                                @elseif($order->traffic_plan == 112)
                                    <span class="amount">@lang('Mini / 20,000 Visitors')</span>
                                @elseif($order->traffic_plan == 113)
                                    <span class="amount">@lang('Small / 100,000 Visitors')</span>
                                @elseif($order->traffic_plan == 114)
                                    <span class="amount">@lang('Medium / 200,000 Visitors')</span>
                                @elseif($order->traffic_plan == 115)
                                    <span class="amount">@lang('Large / 333,333 Visitors')</span>
                                @elseif($order->traffic_plan == 116)
                                    <span class="amount">@lang('Ultimate / 666,666 Visitors')</span>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Second Section -->
                <div class="form-group col-md-4 text-center">
                    <div class="dashboard-w1 bg--teal b-radius--10 box-shadow ">
                        <div class="icon">
                            <i class="fa-solid fa-poo-storm"></i>
                        </div>
                        <div class="details">
                            <div class="desciption">
                                <span class="text--medium">@lang('Status')</span>
                            </div>
                            <div class="numbers">
                                @if ($order->status == 1)
                                    <span class="amount">
                                        @lang('ACTIVE')
                                    </span>
                                @elseif($order->status == 0)
                                    <span class="amount">
                                        @lang('ERROR')
                                    </span>
                                @elseif($order->status == 2)
                                    <span class="amount">
                                        @lang('COMPLETED')
                                    </span>
                                @elseif($order->status == 3)
                                    <span class="amount">
                                        @lang('DENIED')
                                    </span>
                                @elseif($order->status == 4)
                                    <span class="amount">
                                        @lang('CANCELLED')
                                    </span>
                                @elseif($order->status == 5)
                                    <span class="amount">
                                        @lang('EXPIRED')
                                    </span>
                                @elseif($order->status == 6)
                                    <span class="amount">
                                        @lang('PAUSED')
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Third Section -->
                <div class="form-group col-md-4">
                    <div class="dashboard-w1 bg--red b-radius--10 box-shadow ">
                        <div class="icon">
                            <i class="fa-solid fa-calendar-xmark"></i>
                        </div>
                        <div class="details">
                            <div class="desciption">
                                <span class="text--medium">@lang('Expires on')</span>
                            </div>
                            <div class="numbers">
                                <span class="amount">{{ showMonthTime($order->traffic_exp) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row gy-4 mb-none-30">
                <div class="col-xl-8 mb-30">
                    <div class="card">
                        <div class="card-body">
                            <div id="loader" class="loader"></div>
                            <div id="chartContainer" style="height: 400px;"></div>
                            <p class="text--small" id="totalVisits" style="font-weight: bold;"></p>
                        </div>
                    </div>
                </div>
                <br><br>
                <div class="col-xl-4 mb-30">
                    <div class="card">
                        <div class="card-body">
                            <div id="loader2" class="loader"></div>
                            <div id="realtime" style="height: 400px;"></div>
                            <p class="text--small" id="realtimetotal" style="font-weight: bold;"></p>
                        </div>
                    </div>
                </div>
            </div>
            <hr style="height:2px;border-width:0;color:gray;background-color:gray">
            <form class="dashboard-form" role="form" method="POST"
                action="{{ route('user.realistic.update', $order->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form--label" style="font-size: 14px;"><strong>@lang('Campaign Name')</strong></label>
                        <span data-bs-toggle="tooltip" title="@lang('Keep it Short and Catchy')" class="vat-fee-info"><i
                                class="las la-info-circle"></i> </span>
                        <input type="text" name="title" class="form--control form--control-lg"
                            value="{{ $order->name }}" placeholder="Keep it Short and Catchy">
                    </div>
                    <div class="form-group col-md-8">
                        <div class="form-group">
                            <label class="form--label font-weight-bold" for="url"
                                style="font-size: 14px;"><strong>@lang('Your URL')</strong></label>
                            <span data-bs-toggle="tooltip" title="@lang('We start sending visits with this URL.')" class="vat-fee-info"><i
                                    class="las la-info-circle"></i> </span>
                            <br><small>@lang('We start with this URL')</small>
                            @if ($order->traffic_plan == 111 && !empty($order->link) && $order->status != 0)
                                <input type="text" name="link" value="{{ urldecode($order->link) }}"
                                    class="form--control form--control-lg" readonly>
                            @else
                                <input type="text" name="link" value="{{ urldecode($order->link) }}"
                                    class="form--control form--control-lg"
                                    placeholder="URL of your website, eg: https://website.com/ or https://www.website.com/">
                            @endif
                        </div>

                    </div>

                    <div class="form-group col-md-8">
                        <label class="form--label" style="font-size: 14px;"><strong>@lang('Daily Limit: ')</strong></label>
                        <!-- Range input field -->
                        @php
                            $min = 1;
                            $max = 666666;
                            $def = 1;

                            if ($order->traffic_plan == 111) {
                                $min = $max = 2000;
                            } elseif ($order->traffic_plan == 112) {
                                $max = 20000;
                                $def = 667;
                            } elseif ($order->traffic_plan == 113) {
                                $max = 100000;
                                $def = 3334;
                            } elseif ($order->traffic_plan == 114) {
                                $max = 200000;
                                $def = 6667;
                            } elseif ($order->traffic_plan == 115) {
                                $max = 333333;
                                $def = 11111;
                            } elseif ($order->traffic_plan == 116) {
                                $max = 666666;
                                $def = 22222;
                            }
                        @endphp
                        <!-- Number input field (editable) -->
                        @if ($order->traffic_plan == 111)
                            <input type="number" id="bouncerate_input"
                                class="form--control form--control-lg font-weight-bold text--success"
                                style="font-size: 14px; width: 100px; display: inline-block;" value="{{ $order->speed }}"
                                min="{{ $min }}" max="{{ $max }}" disabled="">
                            <small for="floatingSelect" class="text "
                                style="font-size: 14px;">@lang('Visits')</small>
                            <span data-bs-toggle="tooltip" title="@lang('For Nano, all the visits are delivered within 24 ~ 48 hours')" class="vat-fee-info"><i
                                    class="las la-info-circle"></i> </span>
                        @else
                            <input type="number" id="bouncerate_input"
                                class="form--control form--control-lg font-weight-bold text--success"
                                style="font-size: 14px; width: 100px; display: inline-block;" value="{{ $order->speed }}"
                                min="{{ $min }}" max="{{ $max }}">
                            <small for="floatingSelect" class="text "
                                style="font-size: 14px;">@lang('Visits per Day')</small>
                            <span data-bs-toggle="tooltip"
                                title="@lang('Visits we send every day. We recommend') {{ $def }} @lang('visits per day. Increasing traffic speed may decrease your time per visit to 30sec ~ 1min.')" class="vat-fee-info"><i
                                    class="las la-info-circle"></i> </span>
                        @endif


                        <br>
                        <small for="floatingSelect" class="text--small font-weight-bold text--danger">
                            *By increasing the daily limit, the campaign will finish earlier. 
                        </small>
                        <div style="display: flex; align-items: center; gap: 10px;">

                            <input type="range" class="form-range flex-grow-1" id="bouncerate_range"
                                name="bouncerate_range" min="{{ $min }}" max="{{ $max }}"
                                step="1" value="{{ $order->speed }}" {{ $min == $max ? 'readonly' : '' }}>

                        </div>
                        <small for="floatingSelect" class="text--small font-weight-bold text--danger">
                            We recommend {{ $def }} visits per day. Increasing traffic speed may decrease your time per visit to 30sec ~ 1min.
                        </small>


                        @if ($order->traffic_plan == 111)
                            <label for="floatingSelect" style="font-size: 14px;">
                                Max. <span id="dailyhits">@lang('2000')</span> @lang('visits for')
                                <b>@lang('Nano')</b>
                            </label>
                        @endif
                        <div style="margin-top:10px;">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                @if ($order->traffic_plan == 111)
                                    <input type="checkbox" class="custom-control-input" id="randomize_time"
                                        name="randomize_time" disabled {{ $order->random_time_page ? 'checked' : '' }}>
                                @else
                                    <input type="checkbox" class="custom-control-input" id="randomize_time"
                                        name="randomize_time" {{ $order->random_time_page ? 'checked' : '' }}>
                                @endif
                                <label class="form--label font-weight-bold" style="font-size: 14px;"
                                    for="randomize_time">@lang('Randomize the time on page.')</label>
                                <span data-bs-toggle="tooltip" title="@lang('Each visitor will stay on the page for a random amount of time. However, on average, the overall traffic will match the selected Time per Visit setting.')" class="vat-fee-info"><i
                                        class="las la-info-circle"></i> </span>
                            </div>
                        </div>
                    </div>
                    <hr style="height:2px;border-width:0;color:gray;background-color:gray">
                    <div class="row gy-4">
                        <div class="form-group col-md-4">
                            <label class="form--label"
                                style="font-size: 14px;"><strong>@lang('Time per Visit')</strong></label>
                            <span data-bs-toggle="tooltip" title="@lang('Online duration per visit. Engagement and Session time may vary')" class="vat-fee-info"><i
                                    class="las la-info-circle"></i> </span>
                            <select class="form--control form--control-lg" id="timeonpage" name="timeonpage"
                                value="">
                                @if ($order->traffic_plan == 111)
                                    <option name="timeonpage" value="1" {{ $order->tp == 1 ? 'selected' : null }}>30
                                        seconds</option>
                                    <option name="timeonpage" value="2" {{ $order->tp == 2 ? 'selected' : null }}
                                        disabled="">1 minute</option>
                                    <option name="timeonpage" value="3" {{ $order->tp == 3 ? 'selected' : null }}
                                        disabled="">2 minutes</option>
                                    <option name="timeonpage" value="4" {{ $order->tp == 4 ? 'selected' : null }}
                                        disabled="">3 minutes</option>
                                    <option name="timeonpage" value="5" {{ $order->tp == 5 ? 'selected' : null }}
                                        disabled="">4 minutes</option>
                                    <option name="timeonpage" value="6" {{ $order->tp == 6 ? 'selected' : null }}
                                        disabled="">5 minutes</option>
                                @else
                                    <option name="timeonpage" value="1" {{ $order->tp == 1 ? 'selected' : null }}>30
                                        seconds</option>
                                    <option name="timeonpage" value="2" {{ $order->tp == 2 ? 'selected' : null }}>1
                                        minute</option>
                                    <option name="timeonpage" value="3" {{ $order->tp == 3 ? 'selected' : null }}>2
                                        minutes</option>
                                    <option name="timeonpage" value="4" {{ $order->tp == 4 ? 'selected' : null }}>3
                                        minutes</option>
                                    <option name="timeonpage" value="5" {{ $order->tp == 5 ? 'selected' : null }}>4
                                        minutes</option>
                                    <option name="timeonpage" value="6" {{ $order->tp == 6 ? 'selected' : null }}>5
                                        minutes</option>
                                @endif
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label font-weight-bold"
                                for="behaviour"><strong>@lang('Devices')</strong></label>
                            <span data-bs-toggle="tooltip" title="@lang('Online duration per visit. Engagement and Session time may vary')" class="vat-fee-info"><i
                                    class="las la-info-circle"></i> </span><br>
                            <select class="form--control form--control-lg" id="behaviour" name="behaviour">
                                <option value="Mixed" {{ old('behaviour', $order->td) == 'Mixed' ? 'selected' : '' }}>
                                    @lang('Mixed (Upto 30% Mobile)')</option>
                                <option value="Desktop" {{ old('behaviour', $order->td) == 'Desktop' ? 'selected' : '' }}>
                                    @lang('Desktop Only')</option>
                                <option value="Mobile" {{ old('behaviour', $order->td) == 'Mobile' ? 'selected' : '' }}>
                                    @lang('Mobile Only')</option>
                                <option value="Random" {{ old('behaviour', $order->td) == 'Random' ? 'selected' : '' }}>
                                    @lang('Random')</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label font-weight-bold" for="countries"
                                style="font-size: 14px;"><strong>@lang('Countries Geo-Targeting')</strong></label>
                            <span data-bs-toggle="tooltip" title="@lang('Worldwide: Traffic comes from random countries. Geo Target: Choose specific countries to receive traffic from.')" class="vat-fee-info"><i
                                    class="las la-info-circle"></i> </span>
                            @if ($order->traffic_plan == 111)
                                <select name="country" id="country" class="form--control form--control-lg" disabled>
                                    <option value="Worldwide" {{ !$order->country ? 'selected' : '' }}>@lang('Worldwide')
                                    </option>
                                </select>
                            @else
                                <select name="country" id="country" class="form--control form--control-lg"
                                    value="{{ $order->country }}">
                                    @include('partials.traffic')
                                </select>
                            @endif
                            <label class="form-label font-weight-bold justify-content-end" for="behaviour"></label>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label font-weight-bold"
                            for="click_type"><strong>@lang('Click Type')</strong></label>
                        <span data-bs-toggle="tooltip" title="@lang('Select the click behaviour. Internal: Clicks random internal links available on the page, giving up to 3 page views per visit. External: Clicks random external links available on the page, generating 2 clicks, with 1 page view per click (totaling 2 external page views).')" class="vat-fee-info"><i
                                class="las la-info-circle"></i> </span><br>
                        <select class="form--control form--control-lg" id="click_type" name="click_type">
                            @if ($order->traffic_plan == 111)
                                <option value="i"
                                    {{ old('click_type', $order->click_type) == 'i' ? 'selected' : '' }}>@lang('Internal')
                                </option>
                                <option value="e"
                                    {{ old('click_type', $order->click_type) == 'e' ? 'selected' : '' }} disabled="">
                                    @lang('External')</option>
                            @else
                                <option value="i"
                                    {{ old('click_type', $order->click_type) == 'i' ? 'selected' : '' }}>@lang('Internal')
                                </option>
                                <option value="e"
                                    {{ old('click_type', $order->click_type) == 'e' ? 'selected' : '' }}>@lang('External')
                                </option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group col-md-8">
                        <label class="form--label" style="font-size: 14px;">@lang('HTTP Accept-Language')</label>
                        <span data-bs-toggle="tooltip" title="@lang('For example. en-US, Comma Separated or One per line')" class="vat-fee-info"><i
                                class="las la-info-circle"></i> </span>
                        <textarea type="text" name="lang" class="form--control form--control-lg" rows="3"
                            placeholder="For example. en-US, Comma Separated or One per line">{{ str_replace(',', "\n", $order->lang) }}</textarea>
                        <small>@lang('Get the complete list of Language codes ')<a href="https://www.w3.org/International/ms-lang.html"
                                target="_blank">@lang(' here.')</a></small>

                    </div>
                    <div class="form-group col-md-3">
                        <label for="traffictype" class="form--label" style="font-size: 14px;">@lang('Traffic Type')</label>
                        <span data-bs-toggle="tooltip" title="@lang('Choose Traffic sources like Organic, Social or Referral. If you want to mix different kinds of traffic in the same campaign, use Referral and manually add the URLs from where the traffic should come. Example:https://www.google.com/search?q=sparkcliks, https://www.facebook.com/sparkcliks, https://www.instagram.com/sparkcliks')" class="vat-fee-info"><i
                                class="las la-info-circle"></i> </span>
                        <select class="form--control form--control-lg" id="traffictype" name="traffictype"
                            data-minimum-results-for-search="-1">
                            @if ($order->traffic_plan == 111)
                                <option value="1" {{ old('traffictype', $order->tt) == 1 ? 'selected' : '' }}>
                                    @lang('Direct')</option>
                                <option value="2" {{ old('traffictype', $order->tt) == 2 ? 'selected' : '' }}
                                    disabled="">@lang('Organic')</option>
                                <option value="3" {{ old('traffictype', $order->tt) == 3 ? 'selected' : '' }}
                                    disabled="">@lang('Social')</option>
                                <option value="4" {{ old('traffictype', $order->tt) == 4 ? 'selected' : '' }}
                                    disabled="">@lang('Referral')</option>
                            @else
                                <option value="1" {{ old('traffictype', $order->tt) == 1 ? 'selected' : '' }}>
                                    @lang('Direct')</option>
                                <option value="2" {{ old('traffictype', $order->tt) == 2 ? 'selected' : '' }}>
                                    @lang('Organic')</option>
                                <option value="3" {{ old('traffictype', $order->tt) == 3 ? 'selected' : '' }}>
                                    @lang('Social')</option>
                                <option value="4" {{ old('traffictype', $order->tt) == 4 ? 'selected' : '' }}>
                                    @lang('Referral')</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group col-md-8 d-none" id="keyword">
                        <label class="form--label" style="font-size: 14px;">@lang('Keywords')</label>
                        <span data-bs-toggle="tooltip" title="@lang('Keywords to show in your Google Analytics, Comma Separated or One per line')" class="vat-fee-info"><i
                                class="las la-info-circle"></i> </span>
                        <textarea name="keyword" class="form-control form--control" rows="5"
                            placeholder="Keywords to show in your Google Analytics, Comma Separated or One per line">{{ str_replace(',', "\n", $order->keyword) }}</textarea>
                    </div>
                    <div class="form-group col-md-8 d-none" id="social">
                        <label class="form--label" style="font-size: 14px;">@lang('Social')</label>
                        <span data-bs-toggle="tooltip" title="@lang('Full links to your social media/profile/networks pages, for example, https://www.facebook.com/sparkcliks. Comma Separated or One per line')" class="vat-fee-info"><i
                                class="las la-info-circle"></i> </span>
                        <textarea name="social" class="form-control form--control" rows="5"
                            placeholder="Full links to your social media/profile/networks pages, for example, https://www.facebook.com/sparkcliks. Comma Separated or One per line">{{ str_replace(',', "\n", $order->social) }}</textarea>
                    </div>
                    <div class="form-group col-md-8 d-none" id="referrer">
                        <label class="form--label" style="font-size: 14px;">@lang('Referral')</label>
                        <span data-bs-toggle="tooltip" title="@lang('List of URLs from where you want the traffic to come from, For example, http://www.myblog.com/. Comma Separated or One per line')" class="vat-fee-info"><i
                                class="las la-info-circle"></i> </span>
                        <textarea name="referrer" class="form-control form--control" rows="5"
                            placeholder="List of URLs from where you want the traffic to come from, For example, http://www.myblog.com/. Comma Separated or One per line">{{ str_replace(',', "\n", $order->ref) }}</textarea>
                    </div>
                    @if ($order->status == 0 || $order->status == 1 || $order->status == 6)
                        <hr style="height:2px;border-width:0;color:gray;background-color:gray">
                        <div class="form-group col-md-12">
                            <button type="submit" class="btn btn--base btn--lg w-100"
                                id="from-prevent-multiple-submits">@lang('Save')</button>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Pause MODAL --}}
    <div class="dashboard-modal modal" id="pauseModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel" style="font-size: 14px;">@lang('Pause Campaign')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="" id="from-prevent-multiple-submits">
                    @csrf
                    <input type="hidden" name="delete_id" id="delete_id" class="delete_id" value="0">
                    <div class="modal-body">
                        <p class="text-muted" style="font-size: 18px;"><strong>@lang('Would you like to pause this campaign?')<strong></p>
                        <p class="text-muted" style="font-size: 16px;"><strong>@lang('*Traffic will be stopped in 5 ~ 10 minutes.')</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--sm btn--dark"
                            data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--sm btn--primary"
                            id="btn-save">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Resume MODAL --}}
    <div class="dashboard-modal modal" id="playModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel" style="font-size: 14px;">@lang('Resume Campaign')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="" id="from-prevent-multiple-submits">
                    @csrf
                    <input type="hidden" name="delete_id" id="delete_id" class="delete_id" value="0">
                    <div class="modal-body">
                        <p class="text-muted" style="font-size: 14px;">@lang('Would you like to resume this campaign?')</p>
                        <p class="text-muted" style="font-size: 14px;">@lang('Note: Traffic will be start in 5 ~ 30 minutes.')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--base" id="btn-save">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Renew MODAL --}}
    <div class="dashboard-modal modal" id="renewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel" style="font-size: 14px;">@lang('Renew Campaign')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="" id="from-prevent-multiple-submits">
                    @csrf
                    <input type="hidden" name="delete_id" id="delete_id" class="delete_id" value="0">
                    <div class="modal-body">
                        <p class="text-muted" style="font-size: 14px;">@lang('Would you like to renew this campaign?')</p>
                        <p class="text-muted" style="font-size: 14px;">@lang('Note: Ensure sufficient credits in your account.')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--base" id="btn-save">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            // const range = document.getElementById('bouncerate_range');;
            // //const desktop = document.getElementById('desktop');
            // range.addEventListener('change', (e) => {
            //     const brValue = e.target.value;
            //     bouncerate.textContent = brValue;
            //     //const desktopValue = 100 - Number(mobileValue);
            //     //modal.find('input[name=bouncerate_range]').val(brValue);
            //     //desktop.textContent = desktopValue;
            // });

            const range = document.getElementById('bouncerate_range');
            const input = document.getElementById('bouncerate_input');

            // When range slider changes, update number input
            range.addEventListener('input', () => {
                input.value = range.value;
            });

            // Optional: when number input changes, update range slider
            input.addEventListener('input', () => {
                if (parseInt(input.value) >= parseInt(range.min) && parseInt(input.value) <= parseInt(range
                        .max)) {
                    range.value = input.value;
                }
            });

            $('.statusBtn').on('click', function() {
                var modal = $('#statusModal');
                var url = $(this).data('url');

                modal.find('form').attr('action', url);
                modal.modal('show');
            });
            $('.pauseBtn').on('click', function() {
                var modal = $('#pauseModal');
                var url = $(this).data('url');

                modal.find('form').attr('action', url);
                modal.modal('show');
            });
            $('.playBtn').on('click', function() {
                var modal = $('#playModal');
                var url = $(this).data('url');

                modal.find('form').attr('action', url);
                modal.modal('show');
            });
            $('.renewBtn').on('click', function() {
                var modal = $('#renewModal');
                var url = $(this).data('url');

                modal.find('form').attr('action', url);
                modal.modal('show');
            });
            $('#from-prevent-multiple-submits').on('submit', function() {
                $("#btn-save", this)
                    .html("Please wait...")
                    .attr('disabled', 'disabled');
                return true;
            })
        })(jQuery);
    </script>
    <script>
        (function($) {
            "use strict";

            $('#traffictype').on("change", function() {
                var traffictype = $(this).val();
                if (traffictype == 2) {
                    $("#keyword").removeClass('d-none');
                    $("#social").addClass('d-none');
                    $("#referrer").addClass('d-none');
                } else if (traffictype == 3) {
                    $("#social").removeClass('d-none');
                    $("#keyword").addClass('d-none');
                    $("#referrer").addClass('d-none');
                } else if (traffictype == 4) {
                    $("#social").addClass('d-none');
                    $("#keyword").addClass('d-none');
                    $("#referrer").removeClass('d-none');
                } else {
                    $("#social").addClass('d-none');
                    $("#keyword").addClass('d-none');
                    $("#referrer").addClass('d-none');
                }
            }).change();

        })(jQuery);
    </script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script>
        document.getElementById('loader').style.display = 'block';
        // Fetch data from the endpoint
        fetch("{{ route('user.realistic_campaign.chart', $order->id) }}")
            .then(response => response.json())
            .then(data => {
                // Configure and render the chart
                const maxDataValue = Math.max(...data.visit);
                const yAxisMax = Math.max(maxDataValue, 10);
                const tickInterval = Math.ceil(yAxisMax / 5);
                const minPadding = (yAxisMax > 10) ? 0 : 1 - (yAxisMax / 10);
                Highcharts.chart('chartContainer', {
                    chart: {
                        type: 'spline'
                    },
                    title: {
                        text: null
                    },
                    xAxis: {
                        categories: data.dates.map(date => formatDate(date)), // Format dates
                        labels: {
                            rotation: -45, // Rotate labels by 45 degrees for better readability
                            style: {
                                fontSize: '12px', // Adjust font size
                            }
                        },
                        tickInterval: 2 // Display labels for every other day
                    },
                    yAxis: {
                        title: {
                            text: null
                        }
                    },
                    series: [{
                        name: 'Visits',
                        data: data.visit,
                        color: '#41C1BA',
                        marker: {
                            enabled: false // Disable data point markers
                        }
                    }],
                    credits: {
                        text: 'Last 30 Days',
                        href: '',
                        position: {
                            align: 'right',
                            x: -10,
                            verticalAlign: 'bottom',
                            y: -10
                        },
                        style: {
                            color: '#365B6D',
                            fontSize: '12px',
                            fontWeight: 'normal'
                        }
                    }
                });

                // Calculate total visits
                var totalVisits = data.visit.reduce((a, b) => a + b, 0);

                // Display total visits
                document.getElementById('totalVisits').textContent = 'TOTAL VISITS DELIVERED: ' + totalVisits;
                // Hide loader after data is fetched
                document.getElementById('loader').style.display = 'none';
            });

        // Function to format dates as "01 Jan"
        function formatDate(dateString) {
            var date = new Date(dateString);
            return date.getDate() + ' ' + monthNames[date.getMonth()];
        }

        // Array of month names
        var monthNames = [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
        ];
    </script>
    <script>
        // Function to fetch data from Laravel function and update chart
        function updateChart() {
            $.ajax({
                url: "{{ route('user.realistic_campaign.realtime', $order->id) }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    // Calculate total visits in the last 30 minutes
                    var totalVisits = response.visit.reduce((total, visit) => total + visit, 0);

                    var chartData = {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: '<p><span class="dot"></span></p>'
                        },
                        xAxis: {
                            categories: response.timestamps,
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: null
                            }
                        },
                        credits: {
                            text: '',
                            href: '',
                            position: {
                                align: 'right',
                                x: -10,
                                verticalAlign: 'bottom',
                                y: -10
                            },
                            style: {
                                color: '#666666',
                                fontSize: '12px',
                                fontWeight: 'normal'
                            }
                        },
                        series: [{
                            name: 'Users',
                            data: response.visit,
                            color: '#41C1BA' // Specify the color for the bars here
                        }]
                    };

                    // Update or create the chart
                    if (window.chart) {
                        window.chart.update(chartData);
                    } else {
                        document.getElementById('realtime').style.display = 'block'; // Show the chart container
                        window.chart = Highcharts.chart('realtime', chartData, function() {
                            // Callback function to hide the loaders after the chart is rendered
                            document.getElementById('loader2').style.display = 'none';
                        });
                    }
                    // Update the total visits container
                    $('#realtimetotal').html('<span class="dot"></span>&nbsp; USERS IN LAST 30 MINUTES: ' +
                        totalVisits);
                }
            });
        }

        // Call updateChart function initially and then every 10 seconds
        document.addEventListener('DOMContentLoaded', function() {
            // Show loader
            document.getElementById('loader2').style.display = 'block';
            updateChart();
            // Call updateChart every 10 seconds
            setInterval(updateChart, 10000);
        });
    </script>
<script>
(function($) {
    "use strict";
    const input = document.getElementById('bouncerate_input');
    const range = document.getElementById('bouncerate_range');
    const timeSelect = document.getElementById('timeonpage');
    const def = {{ $def }};
    const max = {{ $max }};
    const unlockMsgId = "unlock-time-msg";

    function showUnlockMsg(show, msgText) {
        let msg = document.getElementById(unlockMsgId);
        if (!msg) {
            msg = document.createElement('div');
            msg.id = unlockMsgId;
            msg.className = "text--small font-weight-bold text--danger";
            msg.style.marginTop = "5px";
            timeSelect.parentNode.appendChild(msg);
        }
        msg.innerText = msgText;
        msg.style.display = show ? "block" : "none";
    }

    function updateTimeOptions() {
        const speed = parseInt(input.value);
        const percent = Math.round((speed / max) * 100);

        // Enable all options first
        Array.from(timeSelect.options).forEach(opt => opt.disabled = false);

        let msg = "";

        if (speed === max || percent >= 60) {
            // Only 30sec enabled
            Array.from(timeSelect.options).forEach(opt => opt.value != "1" ? opt.disabled = true : opt.disabled = false);
            timeSelect.value = "1";
            msg = "Please decrease the daily limit to unlock more options.";
        } else if (percent >= 30) {
            // 30sec, 1min enabled
            Array.from(timeSelect.options).forEach(opt => (opt.value != "1" && opt.value != "2") ? opt.disabled = true : opt.disabled = false);
            if (timeSelect.value != "1" && timeSelect.value != "2") timeSelect.value = "1";
            msg = "Please decrease the daily limit to unlock more options.";
        } else if (percent >= 20) {
            // 30sec, 1min, 2min enabled
            Array.from(timeSelect.options).forEach(opt => (["1","2","3"].includes(opt.value)) ? opt.disabled = false : opt.disabled = true);
            if (!["1","2","3"].includes(timeSelect.value)) timeSelect.value = "1";
            msg = "Please decrease the daily limit to unlock more options.";
        } else if (percent >= 10) {
            // 30sec, 1min, 2min, 3min, 4min enabled
            Array.from(timeSelect.options).forEach(opt => (["1","2","3","4","5"].includes(opt.value)) ? opt.disabled = false : opt.disabled = true);
            if (!["1","2","3","4","5"].includes(timeSelect.value)) timeSelect.value = "1";
            msg = "Please decrease the daily limit to unlock more options.";
        } else {
            // All options enabled
            Array.from(timeSelect.options).forEach(opt => opt.disabled = false);
            msg = "";
        }

        showUnlockMsg(msg !== "", msg);
    }

    input.addEventListener('input', updateTimeOptions);
    range.addEventListener('input', updateTimeOptions);

    // Run on page load
    updateTimeOptions();
})(jQuery);
</script>
<script>
(function($) {
    "use strict";
    const clickType = document.getElementById('click_type');
    const msgId = "click-type-msg";

    function updateClickTypeMsg() {
        let msg = document.getElementById(msgId);
        if (!msg) {
            msg = document.createElement('div');
            msg.id = msgId;
            msg.className = "text--small font-weight-bold text--info";
            msg.style.marginTop = "5px";
            clickType.parentNode.appendChild(msg);
        }
        if (clickType.value === "i") {
            msg.innerText = "Internal: Automatically clicks random internal pages and gives you up to 3 page views per session.";
        } else if (clickType.value === "e") {
            msg.innerText = "External: Gives one page view and clicks on 2 external links, totaling 3 page views per session.";
        } else {
            msg.innerText = "";
        }
    }

    clickType.addEventListener('change', updateClickTypeMsg);

    // Run on page load
    updateClickTypeMsg();
})(jQuery);
</script>
@endpush
