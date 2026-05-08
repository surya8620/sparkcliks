@extends($activeTemplate . 'layouts.app')
@section('panel')
<div class="row">
    <div class="col">
	@if($user->traffic_nano > 0 || $user->traffic_mini > 0 || $user->traffic_small > 0 || $user->traffic_medium > 0 || $user->traffic_large > 0 || $user->traffic_ultimate > 0 )
        @forelse($categories as $category)
        @if($category->id == 17)
        @continue(count($category->services) < 1) @php $services=$category->services()->active()->latest('id')->paginate(getPaginate(10), ['*'], slug($category->name))
            @endphp
            @forelse ($services as $item)
            <div class="col-lg-12 col-md-3 mb-4">
                <div class="card card-deposit text-center">
                    <div class="card-body card-body-deposit">
                        {{--<img class="card-img-top" src="#" alt="Card image cap">--}}
                        <h5 class="card-title font-weight-bold">{{__($item->name)}}</h5>
			<small class="font-weight-bold text--primary">One Credit allows you to run 1 campaign for 30 days.
                    </div>
                    <div class="card-header"><small class="font-weight-bold text-danger">IMPORTANT: For Nano pack, we don't allow 3rd level domains, adult site, shorteners, adfly or any similar services for free traffic campaigns,
                            neither can you use the domains that are already in our system, use only paid campaigns in this case.<br> Google Analytics takes up to 24 hours to process the data. Hence it is essential to check after 24 hours from the time of completion(an email is sent).</small>
                    </div>
                    <div class="card-footer">
                        <div class="d-grid gap-3 col-6 mx-auto">
                            <a href="javascript:void(0)" class="btn  btn--primary btn-block custom-success deposi orderBtn font-weight-bold" data-original-title="@lang('New Campaign')" data-toggle="tooltip" data-url="{{ route('user.web.create', $item->id)}}" data-price_per_k="{{ getAmount($item->price_per_k) }}" data-min="{{ $item->min }}" data-max="{{ $item->max }}">
                                New Campaign
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            @endforelse
            @else
            @endif
            @empty
            @endforelse
    </div>
	@else
            <div class="col-lg-12 col-md-3 mb-4">
                <div class="card card-deposit text-center">
                    <div class="card-body card-body-deposit">
                        {{--<img class="card-img-top" src="#" alt="Card image cap">--}}
                        <h5 class="card-title font-weight-bold">You're Out of Credits!</h5>
                        <p class="card-text">Please purchase credits to create a new campaign.</p>
                    </div>
                    <div class="card-footer">
                        <div class="d-grid gap-3 col-6 mx-auto">
                            <a href="/web-traffic/buy" class="btn  btn--primary btn-block custom-success deposi orderBtn font-weight-bold" data-original-title="@lang('Buy Credits')">
                                Buy Credits
                            </a>
                        </div>
                    </div>
                </div>
            </div>
	@endif

</div>
</div>

{{-- Order MODAL --}}
<div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel" class="form-group text-center">@lang('New Campaign')</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" id="from-prevent-multiple-submits">
                @csrf
                <div class="modal-body">
                    <div class="form-row form-group">
                        <label for="plan" class="font-weight-bold" placeholder="">@lang('Campaign - Package') </label>
                        <div class="col-sm-12">
                            <select name="plan" id="plan" class="form-control" value="0">
                                <option value="0">Choose a pack</option>
                                @if($user->traffic_nano > 0)
                                <option value="1" class="font-weight-bold">Nano (Available Credits - {{__($user->traffic_nano)}})</option>
                                @else
                                <option disabled>Nano (Available Credits - {{__($user->traffic_nano)}})</option>
                                @endif
                                @if($user->traffic_mini > 0)
                                <option value="2" class="font-weight-bold">Mini (Available Credits - {{__($user->traffic_mini)}})</option>
                                @else
                                <option disabled>Mini (Available Credits - {{__($user->traffic_mini)}})</option>
                                @endif
                                @if($user->traffic_small > 0)
                                <option value="3" class="font-weight-bold">Small (Available Credits - {{__($user->traffic_small)}})</option>
                                @else
                                <option disabled>Small (Available Credits - {{__($user->traffic_small)}})</option>
                                @endif
                                @if($user->traffic_medium > 0)
                                <option value="4" class="font-weight-bold">Medium (Available Credits - {{__($user->traffic_medium)}})</option>
                                @else
                                <option disabled>Medium (Available Credits - {{__($user->traffic_medium)}})</option>
                                @endif
                                @if($user->traffic_large > 0)
                                <option value="5" class="font-weight-bold">Large (Available Credits - {{__($user->traffic_large)}})</option>
                                @else
                                <option disabled>Large (Available Credits - {{__($user->traffic_large)}})</option>
                                @endif
                                @if($user->traffic_ultimate > 0)
                                <option value="6" class="font-weight-bold">Ultimate (Available Credits - {{__($user->traffic_ultimate)}})</option>
                                @else
                                <option disabled>Ultimate (Available Credits - {{__($user->traffic_ultimate)}})</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="form-row form-group">
                        <label for="link" class="font-weight-bold">@lang('Campaign Name')</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control has-error bold" id="title" name="title" placeholder="Name of the Campaign" required>
                        </div>
                    </div>
                    <div class="form-row form-group">
                        <label for="link" class="font-weight-bold">@lang('Website URL')</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control has-error bold" id="link" name="link" placeholder="URL of your website, eg: https://example.com" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">@lang('You will have more settings after creating the campaign')</label><br>
                        <label class="font-weight-bold">@lang('The traffic will be delivered as per the Delivery Policy.')</label>
                    </div>
                        <div class="form-group">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="agree" value="1" required>
                                <span class="custom-control-label text-uppercase"><strong>I have read and agree to the <a href="https://www.sparkcliks.com/delivery-policy/">Delivery Policy</a></strong></span>
                            </label>
                        </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--primary font-weight-bold" id="btn-save" value="add">@lang('Create Campaign')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('style')
<style>
    .break_line {
        white-space: initial !important;
    }
</style>
@endpush

@push('script')
<script>
    (function($) {
        "use strict";
        $('.orderBtn').on('click', function() {
            var modal = $('#orderModal');
            var plan = $(this).data('plan');
            var url = $(this).data('url');
            var title = $(this).data('title');

            modal.find('form').attr('action', url);
            modal.find('input[name=plan]').val(plan);
            modal.find('input[name=title]').val(title);
            modal.modal('show');

        });

        //Scroll to paginate position
        var pathName = document.location.pathname;
        window.onbeforeunload = function() {
            var scrollPosition = $(document).scrollTop();
            sessionStorage.setItem("scrollPosition_" + pathName, scrollPosition.toString());
        }
        if (sessionStorage["scrollPosition_" + pathName]) {
            $(document).scrollTop(sessionStorage.getItem("scrollPosition_" + pathName));
        }
    $('#from-prevent-multiple-submits').on('submit', function(){
    $("#btn-save", this)
      .html("Please wait...")
      .attr('disabled', 'disabled');
    return true;    })

    })(jQuery);
</script>
@endpush