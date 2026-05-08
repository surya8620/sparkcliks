@extends($activeTemplate.'layouts.master')
@section('content')
<div class="row">
    <div class="col">
        @forelse($categories as $category)
        @if($category->id == 17)
        @continue(count($category->services) < 1) @php $services=$category->services()->active()->latest('id')->paginate(getPaginate(10), ['*'], slug($category->name))
            @endphp
            @forelse ($services as $item)
            <div class="col-lg-12 col-md-3 mb-4">
                <div class="card card-deposit text-center">
                    <div class="card-body card-body-deposit">
                        {{--<img class="card-img-top" src="#" alt="Card image cap">--}}
                        <h5 class="card-title">{{__($item->name)}}</h5>
                    </div>
                    <label for="floatingSelect">The traffic will start coming within 20 minutes to 6 hours.</label>
                    <div class="card-footer">
                        <div class="d-grid gap-3 col-6 mx-auto">
                            <a href="javascript:void(0)" class="btn  btn--primary btn-block custom-success deposi orderBtn font-weight-bold" data-original-title="@lang('New Order')" data-toggle="tooltip" data-url="{{ route('user.traffic', [$category->id, $item->id])}}" data-price_per_k="{{ getAmount($item->price_per_k) }}" data-min="{{ $item->min }}" data-max="{{ $item->max }}">
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

</div>
</div>

{{-- Order MODAL --}}
<div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel" class="form-group text-center">@lang('New Campaign')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
            </div>
            <form method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-row form-group">
                        <label for="traffictype" class="font-weight-bold">@lang('Traffic Source')</label>
                    </div>
                    <div class="form-row form-group align-center">
                        <div id="traffictype" class="form-row form-group text-center">
                            <div class="radio">
                                <label><input type="radio" name="traffictype" value="1" id="direct" checked />
                                    <img src="https://www.sparkcliks.com/wp-content/uploads/2022/09/1.png" alt="Direct"></label>
                            </div>
                            <div class="radio">
                                <label><input type="radio" name="traffictype" value="2" id="organic" />
                                    <img src="https://www.sparkcliks.com/wp-content/uploads/2022/09/2.png" alt="Organic" width="120"></label>
                            </div>
                            <div class="radio">
                                <label><input type="radio" name="traffictype" value="3" id="social" />
                                    <img src="https://www.sparkcliks.com/wp-content/uploads/2022/09/4.png" alt="Social" width="120"></label>
                            </div>
                            <div class="radio">
                                <label><input type="radio" name="traffictype" value="4" id="referral " />
                                    <img src="https://www.sparkcliks.com/wp-content/uploads/2022/09/3.png" alt="Referral" width="100%"></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row form-group">
                        <div class="col-sm-12">
                            <div id="dynmSelect"></div>
                        </div>
                    </div>

                    <div class="form-row form-group">
                        <label for="link" class="font-weight-bold">@lang('Website URL')</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control has-error bold" id="link" name="link" placeholder="URL of your website, eg: https://example.com" required>
                        </div>
                    </div>

                    <div class="form-row form-group">
                        <label for="country" class="font-weight-bold">@lang('Targetting Country')</label>
                        <select name="country" id="country" class="form-control" value="Worldwide">
                            @include('partials.country')
                        </select>
                        <label for="floatingSelect">Works with selects</label>
                    </div>

                    <div class="form-row form-group">
                        <label for="quantity" class="font-weight-bold">@lang('Quantity - Total Visits') </label>
                        <div class="col-sm-12">
                            <input type="number" class="form-control has-error bold" id="quantity" name="quantity" placeholder="Total no of visits to your website" required>
                        </div>
                    </div>

                    <div class="form-row form-group">
                        <div class="form-group col-md-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">@lang('Total Credits')</div>
                                </div>
                                <input type="text" class="form-control total_price text--success" name="price" value="0" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-row form-group">
                        <button class="form-control font-weight-bold" type="button" data-toggle="collapse" data-target="#collapseAdvance" aria-expanded="false" aria-controls="collapseAdvance">
                            Advance Options <i class="fa-solid fa fa-sliders"></i>
                        </button>
                    </div>
                    <div class="collapse" id="collapseAdvance">
                        <div class="card card-body">
                            <div class="form-row form-group">
                                <label for="devices" class="font-weight-bold">@lang('Targetting Devices')</label>
                                <select name="devices" id="devices" class="form-control" value="Random">
                                    <option value="Random">Completely Random</option>
                                    <option value="Realistic Behaviour(30% Mobile)">Realistic Behaviour(30% Mobile)</option>
                                    <option value="Desktop">Desktop</option>
                                    <option value="Tablet">Tablet</option>
                                    <option value="Mobile">Mobile</option>
                                </select>
                            </div>

                            <div class="form-row form-group">
                                <div class="col-6">
                                    <label for="br" class="font-weight-bold">@lang('Bounce Rate - ')<span id="br"></span>%
                                        <!-- Default value -->
                                    </label>
                                </div>
                                <input type="range" class="form-control-range" id="bounce" name="bounce" value="50" min="0" max="100" step="10" onInput="$('#bounce').html($(this).val())">
                                <label for="floatingSelect">Default Bounce Rate - 50%</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
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
            var url = $(this).data('url');
            var keyword = $(this).data('keyword');
            var referrer = $(this).data('referrer');
            var country = $(this).data('country');
            var social1 = $(this).data('social1');
            var social2 = $(this).data('social2');
            var social3 = $(this).data('social3');
            var devices = $(this).data('devices');
            var traffictype = $(this).data('traffictype');
            var price_per_k = $(this).data('price_per_k');
            var min = $(this).data('min');
            var max = $(this).data('max');

            //Calculate total price
            $(document).on("keyup", "#quantity", function() {
                var quantity = $('#quantity').val()
                var total_price = (price_per_k) * quantity;
                modal.find('input[name=price]').val(total_price);
            });

            const range = document.getElementById('bounce');
            const br = document.getElementById('br');
            //const desktop = document.getElementById('desktop');
            range.addEventListener('change', (e) => {
                const brValue = e.target.value;
                br.textContent = brValue;
                //const desktopValue = 100 - Number(mobileValue);
                modal.find('input[name=bounce]').val(brValue);
                //desktop.textContent = desktopValue;
            });

            $(document).on("click", "#traffictype [id]", function() {
                var $this = $(this),
                    thisSelectedValue = $this.val();
                if (thisSelectedValue == 2) {
                    $("#dynmSelect").empty().append("<label for='keyword' class='font-weight-bold'>@lang('Keywords')</label><input type='text' class='form-control has-error bold' id='keyword' name='keyword' placeholder='keyword phrases, eg: mywebsite, website name'><label for='floatingSelect'>@lang('The keywords from Google.com to show in your Google Analytics')</label>");
                } else if (thisSelectedValue == 3) {
                    $("#dynmSelect").empty().append("<label for='social' class='font-weight-bold'>@lang('Select Social Media Referrers,')</label><br><div class='form-check form-check-inline'><input class='form-check-input' type='checkbox' id='inlineCheckbox1' name='social1' value='facebook' /><label class='form-check-label font-weight-bold' for='inlineCheckbox1'>Facebook</label></div><div class='form-check form-check-inline'><input class='form-check-input' type='checkbox' id='inlineCheckbox2' name='social2' value='instagram' /><label class='form-check-label font-weight-bold' for='inlineCheckbox2'>Instagram</label></div><div class='form-check form-check-inline'><input class='form-check-input' type='checkbox' id='inlineCheckbox3' name='social3' value='youtube'/><label class='form-check-label font-weight-bold' for='inlineCheckbox3'>Youtube</label></div><br><label for='floatingSelect'>@lang('Visits will be sent from the selected options.')</label>");
                } else if (thisSelectedValue == 4) {
                    $("#dynmSelect").empty().append("<label for='referrer' class='font-weight-bold'>@lang('Referrers')</label><textarea class='form-control' id='referrer' name='referrer' rows='2' value=''></textarea><label for='floatingSelect'>@lang('List of URLs from where you want the traffic to come, For example, http://www.myblog.com/')</label>")
                } else if (thisSelectedValue == 1) {
                    $("#dynmSelect").empty().append("<label for='floatingSelect' class='font-weight-bold'>@lang('Visits will be sent Directly to your Website')</label>")
                }
            });
            modal.find('form').attr('action', url);
            modal.find('input[name=keyword]').val(keyword);
            modal.find('input[name=social1]').val(social1);
            modal.find('input[name=social2]').val(social2);
            modal.find('input[name=social3]').val(social3);
            modal.find('textarea[name=referrer]').val(referrer);
            modal.find('input[name=country]').val(country);
            modal.find('input[name=bounce]').val(bounce);
            modal.find('input[name=quantity]').attr('min', min).attr('max', max);
            modal.find('input[name=min]').val(min);
            modal.find('input[name=max]').val(max);
            modal.modal('show');
            $(document).on("click", "#submitSelect", function(e) {
                e.preventDefault();
                var $this = $("#dynmSelect select").val();
                if ($this) {
                    document.location.href = $this;
                } else {
                    $("#dynmSelect").text("You must select the type of traffic source");
                }
            });
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
    })(jQuery);
</script>
@endpush