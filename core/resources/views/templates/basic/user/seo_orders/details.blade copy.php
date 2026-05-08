@extends($activeTemplate . 'layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 mb-4">

            <form action="{{ route('user.seo.update', $order->id) }}" method="post">
                @csrf
			@if ($order->service->id != 88)
                <div class="card-body p-0">
                    <div class="container">

                        <div class="col-lg-12">
                            <div id="panel-3" class="panel">
                                <div class="panel-hdr" style="min-height: 4rem;"><br>
                                    <ul class="nav nav-pills" role="tablist" style="padding: 5px">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="pills-details-tab"
                                                    data-bs-toggle="pill" data-bs-target="#pills-details" type="button"
                                                    role="tab" aria-controls="pills-details"
                                                    aria-selected="true">Details</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="pills-targeting-settings-tab" data-bs-toggle="pill"
                                                    data-bs-target="#pills-targeting-settings" type="button" role="tab"
                                                    aria-controls="pills-targeting-settings" aria-selected="false">Edit&nbsp;Campaign</button>
                                            </li>
                                    </ul>
                                </div>
                                <div class="panel-container show">
                                    <div class="panel-content poisition-relative">
                                        <div class="tab-content py-3">
                                            <div class="tab-pane fade active show" id="pills-details" role="tabpanel">
                                                <div class="container">
                                                    <h4 class="font-weight-bold text-center">Campaign Details</h4><br>
                                                        @if( $order->status == 0 )
                                                        <h6 class="font-weight-bold text-center text--warning" for="url"><i class="las la-exclamation-triangle"></i> Error : {{ $order->error }}</h6>
                                                        @else
                                                        @endif<br>
                                                    <table class="table table-bordered table-lg">
                                                        <tbody>
                                                            <tr>
                                                                <td scope="row" class="font-weight-bold" style="text-align: center;"> Campaign Name </td>
                                                                <td class="font-weight-bold" style="text-align: center; vertical-align: middle;">{{ $order->name }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td scope="row" class="font-weight-bold" style="text-align: center;"> Campaign Type </td>
                                                                <td class="font-weight-bold" style="text-align: center; vertical-align: middle;">{{ $order->service->name}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td scope="row" class="font-weight-bold" style="text-align: center; vertical-align: middle;"> Website </td>
                                                                <td class="font-weight-bold" style="text-align: center; vertical-align: middle;">{{ $order->link }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td scope="row" class="font-weight-bold" style="text-align: center; vertical-align: middle;"> Country </td>
                                                                <td class="font-weight-bold" style="text-align: center; vertical-align: middle;">{{ $order->country }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td scope="row" class="font-weight-bold" style="text-align: center; vertical-align: middle;"> Clicker IP Quality </td>
                                                                <td class="font-weight-bold" style="text-align: center; vertical-align: middle;">{{ $order->quality }}</td>
                                                            </tr>

                                                            <tr>
                                                                <td scope="row" class="font-weight-bold" style="text-align: center; vertical-align: middle;"> Quantity</td>
                                                                <td class="font-weight-bold" style="text-align: center; vertical-align: middle;">{{ $order->quantity}}</td>
                                                            </tr>

                                                            <tr>
                                                                <td scope="row" class="font-weight-bold" style="text-align: center; vertical-align: middle;"> Clicks per day </td>
                                                                <td class="font-weight-bold" style="text-align: center; vertical-align: middle;">{{ $order->clicks }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td scope="row" class="font-weight-bold" style="text-align: center; vertical-align: middle;"> Completed Clicks </td>
                                                                <td class="font-weight-bold" style="text-align: center; vertical-align: middle;">{{ $order->start_counter}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td scope="row" class="font-weight-bold" style="text-align: center; vertical-align: middle;"> Remaining  Clicks </td>
                                                                <td class="font-weight-bold" style="text-align: center; vertical-align: middle;">{{ $order->remain }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td scope="row" class="font-weight-bold" style="text-align: center; vertical-align: middle;"> Attempts </td>
                                                                <td class="font-weight-bold" style="text-align: center; vertical-align: middle;">{{ $order->attempt }}</td>
                                                            </tr>


                                                        </tbody>
                                                    </table><br>
                                                    <div class="container">
                                                        <div class="form-group">
                                                            <h4 class="font-weight-bold text-center">Keywords</h4>
                                                            <select name="keyword[]" class="form-control select2-auto-tokenize" multiple="multiple" disabled>
                                                            @if (@$order->keyword)
                                                            @foreach (explode(',', $order->keyword) as $option)
                                                            <option value="{{ $option }}" selected>{{ __($option) }}</option>
                                                            @endforeach
                                                            @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-targeting-settings" role="tabpanel">
                                                <div class="form-group">
                                                    <label class="font-weight-bold" for="title">Campaign Name</label>
                                                    <input type="text" id="title" class="form-control" value="{{ $order->name }}" name="title">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label font-weight-bold" for="url">Website URL<br><small>Clickers look for your website available on Search Engine Result Page.</small></label>
                                                    <input type="text" name="link" value="{{ $order->link }}" class="form-control">
                                                    <label for="floatingSelect">URL of your website, eg: https://www.website.com</label>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label font-weight-bold" for="url">Second URL<br><small>Clickers navigate to this page after visiting the first page.</small></label>
                                                    <input type="text" name="link2" value="{{ $order->link2 }}" class="form-control">
                                                    <label for="floatingSelect">Second URL of your website, eg: https://www.website.com/contact</label>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label font-weight-bold" for="url">Keywords<br><small>Clickers search for your keywords in the Search Engine. Add keywords with (,) comma separated.</small></label>
                                                    <select name="keyword[]" class="form-control select2-auto-tokenize" multiple="multiple" required>
                                                    @if (@$order->keyword)
                                                    @foreach (explode(',', $order->keyword) as $option)
                                                    <option value="{{ $option }}" selected>{{ __($option) }}</option>
                                                    @endforeach
                                                    @endif
                                                    </select>
                                                    <label for="floatingSelect">In Google Search Console, you can see which keywords your domain is referenced on Google for a given location and It must be ranked in top 100 on the keyword. </label>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label font-weight-bold" for="url">No of Clicks per day.</label>
                                                    <input type="number" class="form-control has-error bold" id="clicks" name="clicks" value="{{ $order->clicks }}" max="{{ $order->quantity }}">
                                                    <label for="floatingSelect">No of Clickers who visit your website daily.</label>
                                                </div>
                                                <div class="form-group geo_type_countries">
                                                    <label class="font-weight-bold" for="countries">Countries Geo-Targeting</label>
						    @if ( $order->country == 'Worldwide')
                                                    <input type="text" value="{{ $order->country }}" class="form-control" disabled>
                                                    <label for="floatingSelect">Option is avaiable only for Targetted Countries.</label>
						    @else						
                                                    <select name="country" id="country" class="form-control" value="{{ $order->country }}">
                                                        <option name="country" value="{{ $order->country }}">Choose a Geo Target...</option>
                                                        @include('partials.country_seo')
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
                        <button type="submit" class="btn btn--primary font-weight-bold" id="btn-save" value="add">@lang('Save Changes')</button>
                    </div>
                    @endif
@else
<?php 
     header('Location: /search-console/campaign/history');
     exit();
?>
@endif

            </form>
        </div><!-- card end -->
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a class="btn btn-sm btn--primary box--shadow1 text-white text--small" href="{{ url()->previous() }}"><i
            class="fa fa-fw fa-backward"></i>@lang('Go Back')</a>
@endpush
