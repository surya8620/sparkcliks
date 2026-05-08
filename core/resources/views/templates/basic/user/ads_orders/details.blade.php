@extends($activeTemplate . 'layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 mb-4">
            <form action="{{ route('user.ad.update', $order->id) }}" method="post" id="from-prevent-multiple-submits">
                @csrf
		@if ($order->service->id == 91)
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
                                        </ul>
                                </div>
                                <div class="panel-container show">
                                    <div class="panel-content poisition-relative">
                                        <div class="tab-content py-3">
                                            <div class="tab-pane fade active show" id="pills-details" role="tabpanel">
                                                <div class="container">
                                                    <h4 class="font-weight-bold text-center">Campaign Details</h4><br>
						    @if( $order->status == 0 || $order->status == 7 || $order->status == 8 || $order->status == 9 )
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
                                                                <td style="text-align: center; vertical-align: middle;">{{ $order->link }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td scope="row" style="text-align: center; vertical-align: middle;"> Country </td>
                                                                <td style="text-align: center; vertical-align: middle;">{{ $order->country }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td scope="row" style="text-align: center; vertical-align: middle;"> Bounce Rate </td>
                                                                <td style="text-align: center; vertical-align: middle;">{{ $order->br }}%</td>
                                                            </tr>
                                                            <tr>
                                                                <td scope="row" style="text-align: center; vertical-align: middle;"> Devices </td>
                                                                <td style="text-align: center; vertical-align: middle;">{{ $order->td }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td scope="row" style="text-align: center; vertical-align: middle;"> Session Time </td>
                                                                                                    @if($order->tp == 0)
                                    <td style="text-align: center; vertical-align: middle;">30 Seconds</td>
				    @elseif($order->tp == 1)
                                    <td style="text-align: center; vertical-align: middle;">45 Seconds</td>
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
                                                    <input type="checkbox" class="custom-control-input" id="autorenew" disabled="">
                                                    <label class="custom-control-label font-weight-bold" for="autorenew">Automatically Renew The Campaign - Coming Soon!</label>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label font-weight-bold" for="bouncerate_range">Bounce Rate</label>
                                                    <span class="slider-info font-weight-bold text--success" id="bouncerate">{{ $order->br }}</span>%
                                                    <input type="range" class="form-range" id="bouncerate_range" name="bouncerate_range" min="0" max="100" value="{{ $order->br }}">
                                                    <br><label for="floatingSelect">When a visitor opens a single page on your site and then exits without triggering any other requests</label>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label font-weight-bold" for="timeonpage">Session Time</label>
                                                    <select class="form-control" id="timeonpage" name="timeonpage" value="">
                                                        <option name="timeonpage" value="0" {{ $order->tp == 0 ? 'selected' : null }}>30 seconds</option>
                                                        <option name="timeonpage" value="1" {{ $order->tp == 1 ? 'selected' : null }}>45 seconds</option>
                                                        <option name="timeonpage" value="2" {{ $order->tp == 2 ? 'selected' : null }}>1 minute</option>
                                                        <option name="timeonpage" value="3" {{ $order->tp == 3 ? 'selected' : null }}>2 minutes</option>
                                                        <option name="timeonpage" value="4" {{ $order->tp == 4 ? 'selected' : null }}>3 minutes</option>
                                                        <option name="timeonpage" value="5" {{ $order->tp == 5 ? 'selected' : null }}>4 minutes</option>
                                                    </select>
                                                    <label for="floatingSelect">Increasing this parameter may decrease your page views. We recommend 30sec or 1min</label>
                                                    <div style="margin-top:10px;">
                                                        <div class="custom-control custom-checkbox custom-control-inline">
                                                            <input type="checkbox" class="custom-control-input" id="randomize_time" name="randomize_time" disabled="">
                                                            <label class="custom-control-label font-weight-bold" for="randomize_time">Randomize the time on page. - Coming Soon!</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-urls" role="tabpanel">
                                                <div class="form-group">
                                                    <label class="form-label font-weight-bold" for="url">Your URLs<br><small>We start with these urls</small></label>
                                                    <input type="text" name="link" value="{{ $order->link }}" class="form-control">
                                                    <label for="floatingSelect">URL of your website, eg: https://www.website.com</label>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label font-weight-bold" for="url">1st Click URL<br><small>We simulate clicks on these URLs later</small></label>
                                                    <input type="text" name="link2" value="{{ $order->link2 }}" class="form-control">
                                                    <label for="floatingSelect">URL of your website, eg: https://www.website.com/page1</label>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-targeting-settings" role="tabpanel">
                                                <div class="form-group">
                                                    <label class="form-label font-weight-bold" for="behaviour">Traffic Type</label><br>
                                                    <div id="traffictype" class="form-row form-group text-center">
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
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div id="dynmSelect"></div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label font-weight-bold" for="behaviour">Devices</label><br>
                                                    <select class="form-control m-bot15" id="behaviour" name="behaviour" value="{{ $order->td }}">
                                                        <option name="behaviour" value="{{ $order->td }}">Choose Devices...</option>
                                                        <option name="behaviour" value="Realistic Behaviour(30% Mobile)">Realistic Behaviour (30% Mobile)</option>
                                                        <option name="behaviour" value="Desktop Only">Desktop Only</option>
                                                        <option name="behaviour" value="Mobile Only">Mobile Only</option>
                                                        <option name="behaviour" value="Completely Random">Completely Random</option>
                                                    </select>
                                                </div>
                                                <div class="form-group geo_type_countries">
                                                    <label class="font-weight-bold" for="countries">Countries Geo-Targeting</label>
						    @if($order->country == 'Worldwide')
                                                    <select name="country" id="country" class="form-control" value="{{ $order->country }}" disabled="">
                                                        <option name="country" value="{{ $order->country }}">{{ $order->country }}</option>
                                                    </select>
						    @else
                                                    <select name="country" id="country" class="form-control" value="{{ $order->country }}">
                                                        <option name="country" value="{{ $order->country }}">{{ $order->country }}</option>
                                                        @include('partials.country_ads')
                                                    </select>
						    @endif
                                                    <label class="form-label font-weight-bold justify-content-end" for="behaviour"></label>
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
     header('Location: /ad-traffic/campaign/history');
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