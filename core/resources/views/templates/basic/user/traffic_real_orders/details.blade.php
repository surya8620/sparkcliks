@extends($activeTemplate . 'layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 mb-4">
            <form action="{{ route('user.web.update', $order->id) }}" method="post" id="from-prevent-multiple-submits">
                @csrf
		@if ($order->service->id == 88)
                <div class="card-body p-0">
                    <div class="container">

                        <div class="col-lg-12">
                            <div id="panel-3" class="panel">
                                <div class="panel-hdr" style="min-height: 4rem;"><br>
                                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="pills-details-tab"
                                                    data-bs-toggle="pill" data-bs-target="#pills-details" type="button"
                                                    role="tab" aria-controls="pills-details"
                                                    aria-selected="true">Details</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="pills-urbasic-settingss-tab" data-bs-toggle="pill"
                                                    data-bs-target="#pills-basic-settings" type="button" role="tab"
                                                    aria-controls="pills-basic-settings" aria-selected="false">Basic&nbsp;Settings</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="pills-urls-tab" data-bs-toggle="pill"
                                                    data-bs-target="#pills-urls" type="button" role="tab"
                                                    aria-controls="pills-urls" aria-selected="false">URLs</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="pills-targeting-settings-tab" data-bs-toggle="pill"
                                                    data-bs-target="#pills-targeting-settings" type="button" role="tab"
                                                    aria-controls="pills-targeting-settings" aria-selected="false">Targeting&nbsp;Settings</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="pills-tags-tab" data-bs-toggle="pill"
                                                    data-bs-target="#pills-tags" type="button" role="tab"
                                                    aria-controls="pills-tags" aria-selected="false">Analytics&nbsp;Tags</button>
                                            </li>
                                        </ul>
                                </div>
                                <div class="panel-container show">
                                    <div class="panel-content poisition-relative">
                                        <div class="tab-content py-3">
                                            <div class="tab-pane fade active show" id="pills-details" role="tabpanel">
                                                <div class="container">
                                                    <h4 class="font-weight-bold text-center">Campaign Details</h4><br>
						    @if( $order->status == 0 || $order->status == 5 )
						    <h6 class="font-weight-bold text-center text--warning" for="url"><i class="las la-exclamation-triangle"></i> Error : {{ $order->error }}</h6>
						    @else
						    @endif<br>

                                                    <table class="table table-bordered table-lg">
                                                        <tbody>
                                                            <tr>
                                                                <td scope="row" style="text-align: center;"> Campaign Name </td>
                                                                <td style="text-align: center; vertical-align: middle;">{{ $order->name }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td scope="row" style="text-align: center; vertical-align: middle;">Auto Renew</td>
                                                                @if($order->auto_renew == 0)
                                    <td style="text-align: center; vertical-align: middle;">Off</td>
				    @elseif($order->auto_renew == 1)
                                    <td style="text-align: center; vertical-align: middle;">Renews on {{ showMonth($order->traffic_exp) }}</td>
				    @endif
                                                            </tr>
                                                            <tr>
                                                                <td scope="row" style="text-align: center; vertical-align: middle;"> Traffic Type </td>
                                                                @if($order->tt == 1)
                                                                <td style="text-align: center; vertical-align: middle;">Direct</td>
                                                                @elseif($order->tt == 2)
                                                                <td style="text-align: center; vertical-align: middle;">Organic</td>
                                                                @elseif($order->tt == 3)
                                                                <td style="text-align: center; vertical-align: middle;">Social</td>
                                                                @elseif($order->tt == 4)
                                                                <td style="text-align: center; vertical-align: middle;">Referral</td>
                                                                @endif
                                                            </tr>
                                                            <tr>
                                                                <td scope="row" style="text-align: center; vertical-align: middle;"> Website </td>
                                                                <td style="text-align: center; vertical-align: middle;">{{ urldecode($order->link) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td scope="row" style="text-align: center; vertical-align: middle;"> Country </td>
                                                                <td style="text-align: center; vertical-align: middle;">{{ $order->country }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td scope="row" style="text-align: center; vertical-align: middle;">Realistic User Engagement</td>
                                                                @if($order->engagement == 0)
                                                                <td style="text-align: center; vertical-align: middle;">Disabled</td>
                                                                @else
                                                                <td style="text-align: center; vertical-align: middle;">Enabled</td>
                                                                @endif
                                                            </tr>
                                                            <tr>
                                                                <td scope="row" style="text-align: center; vertical-align: middle;"> Devices </td>
                                                                <td style="text-align: center; vertical-align: middle;">{{ $order->td }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td scope="row" style="text-align: center; vertical-align: middle;">Traffic Speed</td>
                                                                <td style="text-align: center; vertical-align: middle;">{{ $order->speed }} Visitors/day</td>
                                                            </tr>
                                                            <tr>
                                                                <td scope="row" style="text-align: center; vertical-align: middle;"> Visit Time </td>
                                                                                                    @if($order->tp == 0)
                                    <td style="text-align: center; vertical-align: middle;">5 Seconds</td>
				    @elseif($order->tp == 1)
                                    <td style="text-align: center; vertical-align: middle;">30 Seconds</td>
				    @elseif($order->tp == 2)
                                    <td style="text-align: center; vertical-align: middle;">1 Minutes</td>
				    @elseif($order->tp == 3)
                                    <td style="text-align: center; vertical-align: middle;">2 Minutes</td>
				    @elseif($order->tp == 4)
                                    <td style="text-align: center; vertical-align: middle;">3 Minutes</td>
				    @elseif($order->tp == 5)
                                    <td style="text-align: center; vertical-align: middle;">4 Minutes</td>
				    @else
                                    <td style="text-align: center; vertical-align: middle;">5 Minutes</td>
				    @endif
                                                            </tr>
                                                            <tr>
                                                                <td scope="row" style="text-align: center; vertical-align: middle;">Random Time on Page</td>
                                                                @if($order->random_time_page == 0)
                                                                <td style="text-align: center; vertical-align: middle;">Disabled</td>
                                                                @else
                                                                <td style="text-align: center; vertical-align: middle;">Enabled</td>
                                                                @endif
                                                            </tr>
                                                        </tbody>
                                                    </table><br>
                                                    <div class="container">
                                                        @if($order->tt == 2)
                                                        <div class="form-group">
                                                            <h4 class="font-weight-bold text-center">Organic Keywords</h4>
                                                            <textarea type="text" class="form-control" readonly>{{ $order->keyword }}</textarea>
                                                        </div>
                                                        @elseif($order->tt == 3)
                                                        <div class=" form-group">
                                                            <h4 class="font-weight-bold text-center">Social Referrers</h4>
                                                            <textarea type="text" class="form-control" readonly>{{ $order->social }}</textarea>
                                                        </div>
                                                        @elseif($order->tt == 4)
                                                        <div class="form-group">
                                                            <h4 class="font-weight-bold text-center">Referrerss</h4>
                                                            <textarea type="text" class="form-control" readonly>{{ $order->ref }}</textarea>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-basic-settings" role="tabpanel">
                                                <div class="form-group">
                                                    <label class="font-weight-bold" for="title">Campaign Name</label>
                                                    <input type="text" id="title" class="form-control" value="{{ $order->name }}" name="title">
                                                </div>
                                                <div class="custom-control custom-switch" style="margin-bottom: 20px;margin-top: 5px;">
						    @if($order->traffic_plan == 1)
                                                    <input type="checkbox" class="custom-control-input" id="autorenew" name="autorenew" disabled="" {{ $order->auto_renew ? 'checked' : '' }}>
                                                    <label class="custom-control-label font-weight-bold" for="autorenew">Automatically Renew - Unavailable in Nano</label>
						    @else
                                                    <input type="checkbox" class="custom-control-input" id="autorenew" name="autorenew" disabled="" {{ $order->auto_renew ? 'checked' : '' }}>
                                                    <label class="custom-control-label font-weight-bold" for="autorenew">Automatically Renew - Coming Soon!</label>
						    @endif
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label font-weight-bold" for="bouncerate_range">Traffic Speed:</label>
                                                    <span class="slider-info font-weight-bold text--success" id="bouncerate">{{ $order->speed }}</span> Visitors/day
                                                    <br><label for="floatingSelect" class="text--small badge font-weight-normal badge--danger">*Increasing traffic speed, the campaign will end early.</label>
						    @if($order->traffic_plan == 1)
                                                    <input type="range" class="form-range" id="bouncerate_range" name="bouncerate_range" min="2000" max="2000" value="{{ $order->speed }}" readonly>
                                                    <label for="floatingSelect">Max. <span id="dailyhits">2000</span> users/day for
                                                        <b>Nano</b></label>
						    @elseif($order->traffic_plan == 2)
                                                    <input type="range" class="form-range" id="bouncerate_range" name="bouncerate_range" min="650" max="20000" step="50" value="{{ $order->speed }}">
                            @elseif($order->traffic_plan == 3)
                                                    <input type="range" class="form-range" id="bouncerate_range" name="bouncerate_range" min="3300" max="100000" step="100" value="{{ $order->speed }}">
                                                    @elseif($order->traffic_plan == 4)
                                                    <input type="range" class="form-range" id="bouncerate_range" name="bouncerate_range" min="6650" max="200000" step="100" value="{{ $order->speed }}">
                                                    @elseif($order->traffic_plan == 5)
                                                    <input type="range" class="form-range" id="bouncerate_range" name="bouncerate_range" min="11100" max="333333" step="100" value="{{ $order->speed }}">
                                                    @elseif($order->traffic_plan == 6)
                                                    <input type="range" class="form-range" id="bouncerate_range" name="bouncerate_range" min="22200" max="666666" step="100" value="{{ $order->speed }}">
                            @endif
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label font-weight-bold" for="timeonpage">Visit Time</label>
                                                    <select class="form-control" id="timeonpage" name="timeonpage" value="">
						    @if($order->traffic_plan == 1)
                                                        <option name="timeonpage" value="0" {{ $order->tp == 0 ? 'selected' : null }}>5 seconds</option>
                                                        <option name="timeonpage" value="1" {{ $order->tp == 1 ? 'selected' : null }}>30 seconds</option>
                                                        <option name="timeonpage" value="2" {{ $order->tp == 2 ? 'selected' : null }} disabled="">1 minute</option>
                                                        <option name="timeonpage" value="3" {{ $order->tp == 3 ? 'selected' : null }} disabled="">2 minutes</option>
                                                        <option name="timeonpage" value="4" {{ $order->tp == 4 ? 'selected' : null }} disabled="">3 minutes</option>
                                                        <option name="timeonpage" value="5" {{ $order->tp == 5 ? 'selected' : null }} disabled="">4 minutes</option>
                                                        <option name="timeonpage" value="6" {{ $order->tp == 6 ? 'selected' : null }} disabled="">5 minutes</option>
						    @else
                                                        <option name="timeonpage" value="0" {{ $order->tp == 0 ? 'selected' : null }}>5 seconds</option>
                                                        <option name="timeonpage" value="1" {{ $order->tp == 1 ? 'selected' : null }}>30 seconds</option>
                                                        <option name="timeonpage" value="2" {{ $order->tp == 2 ? 'selected' : null }}>1 minute</option>
                                                        <option name="timeonpage" value="3" {{ $order->tp == 3 ? 'selected' : null }}>2 minutes</option>
                                                        <option name="timeonpage" value="4" {{ $order->tp == 4 ? 'selected' : null }}>3 minutes</option>
                                                        <option name="timeonpage" value="5" {{ $order->tp == 5 ? 'selected' : null }}>4 minutes</option>
                                                        <option name="timeonpage" value="6" {{ $order->tp == 6 ? 'selected' : null }}>5 minutes</option>
						    @endif
                                                    </select>
                                                    <label for="floatingSelect">Total duration of each visit.</label>
                                                    <div style="margin-top:10px;">
                                                        <div class="custom-control custom-checkbox custom-control-inline">
                                                        @if($order->traffic_plan == 1)
                                                        <input type="checkbox" class="custom-control-input" id="randomize_time" name="randomize_time" disabled {{ $order->random_time_page ? 'checked' : '' }}>
                                                        <label class="custom-control-label font-weight-bold" for="randomize_time">Randomize the time on page.</label>
                                                        @else
                                                            <input type="checkbox" class="custom-control-input" id="randomize_time" name="randomize_time" {{ $order->random_time_page ? 'checked' : '' }}>
                                                            <label class="custom-control-label font-weight-bold" for="randomize_time">Randomize the time on page.</label>
                                                        @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-urls" role="tabpanel">
                                                <div class="form-group">
                                                    <label class="form-label font-weight-bold" for="url">Your URL<br><small>We start with these urls</small></label>
                                                    @if($order->traffic_plan == 1 && !empty($order->link)  && $order->status != 0)
                                                    <input type="text" name="link" value="{{ urldecode($order->link) }}" class="form-control" readonly>
                                                    @else
                                                    <input type="text" name="link" value="{{ urldecode($order->link) }}" class="form-control">
                                                    @endif
                                                    <label for="floatingSelect">URL of your website, eg: https://website.com or https://www.website.com</label><br>
                                                        <div class="custom-control custom-checkbox custom-control-inline">
                                                        @if($order->traffic_plan == 1 || $order->traffic_plan == 2)
                                                        <input type="checkbox" class="custom-control-input" id="engagement" name="engagement" disabled {{ $order->engagement ? 'checked' : '' }}>
                                                        <label class="custom-control-label font-weight-bold" for="engagement">Realistic User Engagement - Automatically Visits Internal Pages(Unavailable in Nano/Mini).</label><br>
                                                        @else
                                                        <input type="checkbox" class="custom-control-input" id="engagement" name="engagement" {{ $order->engagement ? 'checked' : '' }}>
                                                        <label class="custom-control-label font-weight-bold" for="engagement">Realistic User Engagement(Beta Testing) - Automatically Visits Internal Pages.</label><br>
                                                        <label for="floatingSelect" class="text--small badge font-weight-normal badge--danger">*Enabling Realistic User Engagement, 1st Click URL & 2nd Click URL will be disabled</label>
                                                        @endif
                                                        </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label font-weight-bold" for="url">1st Click URL<br><small>We simulate clicks on these URLs later</small></label>
                                                    @if($order->traffic_plan == 1 && !empty($order->link2) && $order->status != 0)
                                                    <input type="text" name="link2" value="{{ urldecode($order->link2) }}" class="form-control" readonly>
                                                    @else
                                                    <input type="text" name="link2" value="{{ urldecode($order->link2) }}" class="form-control">
                                                    @endif
                                                    <label for="floatingSelect">URL of your website, eg: https://website.com/page1 or https://www.website.com/page1</label>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label font-weight-bold" for="url">2nd Click URL<br><small>We simulate clicks on these URLs later</small></label>
                                                    @if($order->traffic_plan == 1 && !empty($order->link3)  && $order->status != 0)
                                                    <input type="text" name="link3" value="{{ urldecode($order->link3) }}" class="form-control" readonly>
                                                    @else
                                                    <input type="text" name="link3" value="{{ urldecode($order->link3) }}" class="form-control">
                                                    @endif
                                                    <label for="floatingSelect">URL of your website, eg: https://website.com/page2 or https://www.website.com/page2</label>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-targeting-settings" role="tabpanel">
                                                <div class="form-group">
                                                    <label class="form-label font-weight-bold" for="behaviour">Traffic Type</label><br>
                                                    <div id="traffictype" class="form-row form-group text-center">
							@if($order->traffic_plan == 1)
                                                        <div class="form-check form-check-inline">
                                                            <label><input type="radio" name="traffictype" value="1" id="direct" {{ $order->tt == 1 ? 'selected' : null }} />
                                                                <img src="{{ getImage(getFilePath('payments') . '/1.png') }}" alt="Direct"></label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <label><input type="radio" name="traffictype" value="2" disabled="" id="organic" {{ $order->tt == 2 ? 'selected' : null }} />
                                                                <img src="{{ getImage(getFilePath('payments') . '/2.png') }}" alt="Organic" width="120"></label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <label><input type="radio" name="traffictype" value="3" disabled="" id="social" {{ $order->tt == 3 ? 'selected' : null }} />
                                                                <img src="{{ getImage(getFilePath('payments') . '/4.png') }}" alt="Social" width="120"></label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <label><input type="radio" name="traffictype" value="4" disabled="" id="referral" {{ $order->tt == 4 ? 'selected' : null }} />
                                                                <img src="{{ getImage(getFilePath('payments') . '/3.png') }}" alt="Referral" width="120"></label>
                                                        </div>
							@else
                                                        <div class="form-check form-check-inline">
                                                            <label><input type="radio" name="traffictype" value="1" id="direct" {{ $order->tt == 1 ? 'selected' : null }} />
                                                                <img src="{{ getImage(getFilePath('payments') . '/1.png') }}" alt="Direct"></label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <label><input type="radio" name="traffictype" value="2" id="organic" {{ $order->tt == 2 ? 'selected' : null }} />
                                                                <img src="{{ getImage(getFilePath('payments') . '/2.png') }}" alt="Organic" width="120"></label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <label><input type="radio" name="traffictype" value="3" id="social" {{ $order->tt == 3 ? 'selected' : null }} />
                                                                <img src="{{ getImage(getFilePath('payments') . '/4.png') }}" alt="Social" width="120"></label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <label><input type="radio" name="traffictype" value="4" id="referral" {{ $order->tt == 4 ? 'selected' : null }} />
                                                                <img src="{{ getImage(getFilePath('payments') . '/3.png') }}" alt="Referral" width="120"></label>
                                                        </div>
							@endif
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div id="dynmSelect"></div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label font-weight-bold" for="behaviour">Devices</label><br>
                                                    <select class="form-control m-bot15" id="behaviour" name="behaviour" value="{{ $order->td }}">
                                                        <option name="behaviour" value="{{ $order->td }}">Choose Devices...</option>
                                                        <option name="behaviour" value="Mixed">Mixed (Upto 30% Mobile)</option>
                                                        <option name="behaviour" value="Desktop">Desktop Only</option>
                                                        <option name="behaviour" value="Mobile">Mobile Only</option>
                                                        <option name="behaviour" value="Random">Completely Random</option>
                                                    </select>
                                                </div>
                                                <div class="form-group geo_type_countries">
                                                    <label class="font-weight-bold" for="countries">Countries Geo-Targeting</label>
						    @if($order->traffic_plan == 1)
                                                    <select name="country" id="country" class="form-control" value="{{ $order->country }}" disabled="">
                                                        <option name="country" value="{{ $order->country }}">Choose a Geo Target...</option>
                                                        @include('partials.traffic')
                                                    </select>
						    @else
                                                    <select name="country" id="country" class="form-control" value="{{ $order->country }}">
                                                        <option name="country" value="{{ $order->country }}">Choose a Geo Target...</option>
                                                        @include('partials.traffic')
                                                    </select>
						    @endif
                                                    <label class="form-label font-weight-bold justify-content-end" for="behaviour"></label>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-tags" role="tabpanel">
                                                <div class="form-group font-weight-bold text-center">
                                                    <h5>Analytics Tags that are installed on your website ({{ $order->link }})</h5>
                                                </div>
                                                <div class="form-group">
                                                <table class="table table-bordered table-lg">
                                                        <tbody>
                                                            <tr>
                                                                <td scope="row" style="text-align: center; font-weight:bold">Google&nbsp;Analytics</td>
                                                                @if ($order->ga_tag !== null)
                                                                <td style="text-align: center; vertical-align: middle; font-weight:bold;color:green">{{ $order->ga_tag }}</td>
                                                                @else
                                                                <td style="text-align: center; vertical-align: middle; font-weight:bold;color:red">NOT FOUND</td>
                                                                @endif
                                                            </tr>
                                                            <tr>
                                                                <td scope="row" style="text-align: center; font-weight:bold">HiStats</td>
                                                                @if ($order->histats !== null)
                                                                <td style="text-align: center; vertical-align: middle; font-weight:bold;color:green">{{ $order->histats }}</td>
                                                                @else
                                                                <td style="text-align: center; vertical-align: middle; font-weight:bold;color:red">NOT FOUND</td>
                                                                @endif
                                                            </tr>
                                                            <tr>
                                                                <td scope="row" style="text-align: center; font-weight:bold">Yandex</td>
                                                                @if ($order->histats !== null)
                                                                <td style="text-align: center; vertical-align: middle; font-weight:bold;color:green">{{ $order->jetpack }}</td>
                                                                @else
                                                                <td style="text-align: center; vertical-align: middle; font-weight:bold;color:red">Coming Soon!</td>
                                                                @endif
                                                            </tr> 
                                                            <tr>
                                                                <td scope="row" style="text-align: center; font-weight:bold">Wordpress&nbsp;Jetpack</td>
                                                                @if ($order->jetpack !== null)
                                                                <td style="text-align: center; vertical-align: middle; font-weight:bold;color:green">{{ $order->jetpack }}</td>
                                                                @else
                                                                <td style="text-align: center; vertical-align: middle; font-weight:bold;color:red">Coming Soon!</td>
                                                                @endif
                                                            </tr> 
                                                            <tr>
                                                                <td scope="row" style="text-align: center; font-weight:bold">ComScore</td>
                                                                @if ($order->comscore !== null)
                                                                <td style="text-align: center; vertical-align: middle; font-weight:bold;color:green">{{ $order->comscore }}</td>
                                                                @else
                                                                <td style="text-align: center; vertical-align: middle; font-weight:bold;color:red">Coming Soon!</td>
                                                                @endif
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="form-group font-weight-bold text-center">
                                                    <p>
                                                        Please contact us at
                                                        <a href="mailto:support@sparkcliks.com"><b>support@sparkcliks.com</b></a>
                                                        if you see anything wrong in here
                                                    </p>
                                                </div>

                                            </div>
                                            <div class="tab-pane fade" id="tab-advanced-settings" role="tabpanel">
                                                <div class="form-group">
                                                    <label class="form-label" for="respect_time_day">Change the traffic volume depending on the current Time of Day and Day of Week</label>
                                                    <select class="form-control" id="respect_time_day" disabled="">
                                                        <option name="respect_time_day" value="no" selected="">No</option>
                                                        <option value="12">Yes,&nbsp;Timezone: GMT-12</option>
                                                        <option value="-11">Yes,&nbsp;Timezone: GMT-11</option>
                                                        <option value="-10">Yes,&nbsp;Timezone: GMT-10</option>
                                                        <option value="-9">Yes,&nbsp;Timezone: GMT-9</option>
                                                        <option value="-8">Yes,&nbsp;Timezone: GMT-8</option>
                                                        <option value="-7">Yes,&nbsp;Timezone: GMT-7</option>
                                                        <option value="-6">Yes,&nbsp;Timezone: GMT-6</option>
                                                        <option value="-5">Yes,&nbsp;Timezone: GMT-5</option>
                                                        <option value="-4">Yes,&nbsp;Timezone: GMT-4</option>
                                                        <option value="-3">Yes,&nbsp;Timezone: GMT-3</option>
                                                        <option value="-2">Yes,&nbsp;Timezone: GMT-2</option>
                                                        <option value="-1">Yes,&nbsp;Timezone: GMT-1</option>
                                                        <option value="0">Yes,&nbsp;Timezone: GMT</option>
                                                        <option value="1">Yes,&nbsp;Timezone: GMT+1</option>
                                                        <option value="2">Yes,&nbsp;Timezone: GMT+2</option>
                                                        <option value="3">Yes,&nbsp;Timezone: GMT+3</option>
                                                        <option value="4">Yes,&nbsp;Timezone: GMT+4</option>
                                                        <option value="5">Yes,&nbsp;Timezone: GMT+5</option>
                                                        <option value="6">Yes,&nbsp;Timezone: GMT+6</option>
                                                        <option value="7">Yes,&nbsp;Timezone: GMT+7</option>
                                                        <option value="8">Yes,&nbsp;Timezone: GMT+8</option>
                                                        <option value="9">Yes,&nbsp;Timezone: GMT+9</option>
                                                        <option value="10">Yes,&nbsp;Timezone: GMT+10</option>
                                                        <option value="11">Yes,&nbsp;Timezone: GMT+11</option>
                                                    </select>
                                                    <span class="help-block">Make less traffic at night time, more at day time. More on Monday till Friday, less on Saturday and Sunday <br>Your project should be at least Medium, Large, or Ultimate for this feature to be active</span>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="shortener">Shortener</label>
                                                    <input type="text" class="form-control" name="shortener" id="shortener" value="" disabled="" placeholder="Upgrade to a Professional to unlock the field">
                                                    <span class="help-block">We will first visit the shortener. It consumes 1 page view per visit<br>Only for <b>Professional Projects</b>. Ex.: https://bit.ly/ or https://cutt.ly/</span>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="utm_source">utm_source</label>
                                                    <label class="col-xs-3 col-lg-3 control-label"><br><small></small></label>
                                                    <input type="text" class="form-control" name="utm_source" id="utm_source" value="" disabled="" placeholder="Upgrade to a Professional to unlock the field">
                                                    <span class="help-block">We will add it to your URLs<br>Only for <b>Professional projects</b>, Ex.: sparkcliks_source, Optional</span>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="utm_medium">utm_medium</label>
                                                    <input type="text" class="form-control" name="utm_medium" id="utm_medium" value="" disabled="" placeholder="Upgrade to a Professional to unlock the field">
                                                    <span class="help-block">We will add it to your URLs<br>Only for <b>Professional projects</b>, Ex.: sparkcliks_medium, Optional</span>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="utm_campaign">utm_campaign</label>
                                                    <input type="text" class="form-control" name="utm_campaign" id="utm_campaign" value="" disabled="" placeholder="Upgrade to a Professional to unlock the field">
                                                    <span class="help-block">We will add it to your URLs<br>Only for <b>Professional projects</b>, Ex.: sparkcliks_campaign, Optional</span>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="language">HTTP Accept-Language</label>
                                                    <textarea class="form-control" id="language" rows="3" placeholder="" data-gramm="false" wt-ignore-input="true"></textarea>
                                                    <span class="help-block">For example. en-US. One per line. read more <a href="#" style="text-decoration: underline;" target="_blank">here</a></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($order->status == 0 || $order->status == 1)
                    <div class="modal-footer">
			<a class="btn btn--secondary box--shadow1 text-white font-weight-bold" href="{{ url()->previous() }}">
			<i class="fa fa-fw fa-backward"></i>@lang('Go Back')</a>
                        <button type="submit" class="btn btn--primary button-prevent-multiple-submits font-weight-bold" id="btn-save" value="add">@lang('Save Changes')</button>
                    </div>
                    @endif
@else
<?php 
     header('Location: /web-traffic/campaign/history');
     exit();
?>

@endif
            </form>
        </div><!-- card end -->
    </div>
</div>
@endsection

@push('script')
<script>
    (function($) {
        "use strict";
        const range = document.getElementById('bouncerate_range');;
        //const desktop = document.getElementById('desktop');
        range.addEventListener('change', (e) => {
            const brValue = e.target.value;
            bouncerate.textContent = brValue;
            //const desktopValue = 100 - Number(mobileValue);
            //modal.find('input[name=bouncerate_range]').val(brValue);
            //desktop.textContent = desktopValue;
        });
        $(document).on("click", "#traffictype [id]", function() {
            var $this = $(this),
                thisSelectedValue = $this.val();
            if (thisSelectedValue == 2) {
                $("#dynmSelect").empty().append("<label for='keyword' class='font-weight-bold'>@lang('Keywords')</label><input type='text' class='form-control has-error bold' id='keyword' name='keyword' placeholder='keyword phrases, eg: mywebsite, website name'><label for='floatingSelect'>@lang('The keywords from Google.com to show in your Google Analytics, Comma Separated')</label>");
            } else if (thisSelectedValue == 3) {
                $("#dynmSelect").empty().append("<label for='social' class='font-weight-bold'>@lang('Social Profiles')</label><textarea class='form-control' id='social' name='social' rows='2' value='{ !!html_entity_decode($order->social)!! }'></textarea><label for='floatingSelect'>@lang('Full links to your social media/profile/networks pages, for example, https://www.facebook.com/sparkcliks/, One per line')</label>")
            } else if (thisSelectedValue == 4) {
                $("#dynmSelect").empty().append("<label for='referrer' class='font-weight-bold'>@lang('Referrers')</label><textarea class='form-control' id='referrer' name='referrer' rows='3' value='{{ $order->ref }}'></textarea><label for='floatingSelect'>@lang('List of URLs from where you want the traffic to come, For example, http://www.myblog.com/, One per line')</label>")
            } else if (thisSelectedValue == 1) {
                $("#dynmSelect").empty().append("<label for='floatingSelect' class='font-weight-bold'>@lang('Visits will be sent Directly to your Website')</label>")
            }
        });
    $('#from-prevent-multiple-submits').on('submit', function(){
    $("#btn-save", this)
      .html("Saving...")
      .attr('disabled', 'disabled');
    return true;    })
    })(jQuery);
</script>
@endpush