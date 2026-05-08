@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            @if ($user->seocredit > 0 || $user->mem_credit > 0)
                @forelse($categories as $category)
                    @if ($category->id < 16)
                        @continue(count($category->services) < 1)

                        <div class="card b-radius--10 mb-4">
                            <div class="card-header">
                                <h3>@lang($category->name)</h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table--responsive--lg table-bordered table-striped table">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="text-left">@lang('Campaign')</th>
                                            <th scope="col" class="text-end">@lang('New Campaign')</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @php
                                            $services = $category
                                                ->services()
                                                ->active()
                                                ->latest('id')
                                                ->paginate(getPaginate(10), ['*'], slug($category->name));
                                        @endphp

                                        @forelse ($services as $item)
                                            <tr>
                                                <td class="text-left" data-label="@lang('Campaign')" class="break_line">
                                                    {{ __($item->name) }}</td>

                                                <td data-label="@lang('New Campaign')">

                                                    <a href="javascript:void(0)"
                                                        class="btn  btn--base custom-success deposi orderBtn"
                                                        data-original-title="@lang('New Campaign')" data-toggle="tooltip"
                                                        data-url="{{ route('user.seo.create', $item->id) }}"
                                                        data-api_provider_id="{{ $item->api_provider_id }}"
                                                        data-price_per_k="{{ getAmount($item->price_per_k) }}"
                                                        data-min="{{ $item->min }}" data-max="{{ $item->max }}">
                                                        Create
                                                    </a>

                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="text-muted text-center" colspan="100%">
                                                    {{ __($empty_message) }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table><!-- table end -->
                            </div>
                        </div><!-- card end -->
                    @else
                    @endif
                @empty
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4>
                                    {{ __($emptyMessage) }}
                                </h4>
                            </div>
                        </div>
                    </div>
                @endforelse

        </div>
    </div>

    {{-- Order MODAL --}}
    <div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('New Campaign')</h4>
                    <button type="button" class="close" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="post" class="resetForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-row form-group">
                            <label for="link" class="font-weight-bold">@lang('Campaign Name')</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control has-error bold" id="title" name="title"
                                    placeholder="Name of your campaign" required>
                            </div>
                        </div>

                        <div class="form-row form-group">
                            <label for="link" class="font-weight-bold">@lang('Website URL')</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control has-error bold" id="link" name="link"
                                    placeholder="URL of your website, eg: https://example.com" required>
                            </div>
                        </div>
                        <div class="form-row form-group">
                            <label for="link2" class="font-weight-bold">@lang('Website URL #2')</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control has-error bold" id="link2" name="link2"
                                    placeholder="Second URL of your website, eg: https://example.com/page-2" required>
                            </div>
                        </div>

                        <div class="form-row form-group">
                            <label for="keyword" class="font-weight-bold">@lang('Keyword Phrase')<br>
                                <small>@lang('Clickers search for your keywords in the Search Engine. Separate multiple keywords by') <code>,</code>(@lang('comma'))</small></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control has-error bold" id="keyword" name="keyword"
                                    placeholder="Keyword must be ranked in top 100 on the keyword" required>
                            </div><label for="floatingSelect" class="text-danger">In Google Search Console,
                                you can see which keywords your domain is referenced
                                on Google for a given location and It must be ranked
                                in top 100 on the keyword. </label>
                        </div>

                        <div class="form-row form-group">
                            <label for="country" class="font-weight-bold">@lang('Select Country')</label>
                            <select name="country" id="country" class="form-control" value="Worldwide">
                                @include('partials.country_seo')
                            </select>
                            <label class="font-weight-bold"><span class="text-danger">@lang('Geo Target = 2 x Clicker IP Quality')</span>
                        </div>

                        <div class="form-row form-group">
                            <label for="quality" class="font-weight-bold">@lang('Select Clicker IP Quality')</label>
                            <select name="quality" id="quality" class="form-control" value="Mixed">
                                @include('partials.quality')
                            </select>
                            <label class="font-weight-bold"><span class="text-danger">@lang('Mixed IP Quality - 1 x Credit / High IP Quality - 1.2 x Credit')</span><br>
                                <label class="font-weight-bold"><span class="text-danger">@lang('Mixed = Low/Medium/High IP Quality Clickers')</span>
                        </div>
                        <div class="form-row form-group">
                            <label for="quantity" class="font-weight-bold">@lang('Quantity - Total Clicks')</label>
                            <div class="col-sm-12">
                                <input type="number" class="form-control has-error bold" id="quantity" name="quantity"
                                    placeholder="Total no of clicks or visits to your website" required>
                            </div>
                        </div>

                        <div class="form-row form-group">
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-text">@lang('Total Credits')</div>
                                    <input type="text" class="form-control total_price text--success" name="price"
                                        value="0.00" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-row form-group">
                            <label for="clicks" class="font-weight-bold">@lang('Number of Clicks per day')</label>
                            <div class="col-sm-12">
                                <input type="number" class="form-control has-error bold" id="clicks" name="clicks"
                                    placeholder="No of clicks or visits to your website per day" required>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--primary"
                            data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--base font-weight-bold" id="btn-save"
                            value="add">@lang('Create Campaign')</button>
                    </div>
                </form>
            </div>
        @else
            <div class="col-lg-12 col-md-3 mb-4">
                <div class="card card-deposit text-center">
                    <div class="card-body card-body-deposit">
                        {{-- <img class="card-img-top" src="#" alt="Card image cap"> --}}
                        <h5 class="card-title font-weight-bold">You're Out of Credits!</h5>
                        <p class="card-text">Please purchase credits to create a new campaign.</p>
                    </div>
                    <div class="card-footer">
                        <div class="d-grid gap-3 col-6 mx-auto">
                            <a href="{{ route('user.seo.buy') }}"
                                class="btn  btn--primary btn-block custom-success deposi orderBtn font-weight-bold"
                                data-original-title="@lang('Buy Credits')">
                                Buy Credits
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.detailsBtn').on('click', function() {
                var modal = $('#detailsModal');
                var details = $(this).data('details');

                modal.find('#details').html(details);
                modal.modal('show');
            });

            $('.orderBtn').on('click', function() {
                var modal = $('#orderModal');
                var url = $(this).data('url');
                var title = $(this).data('title ');
                var link = $(this).data('link');
                var link2 = $(this).data('link2');
                var keyword = $(this).data('keyword');
                var clicks = $(this).data('clicks');
                var country = $(this).data('country');
                var quality = $(this).data('quality');
                var price_per_k = $(this).data('price_per_k');
                var min = $(this).data('min');
                var max = $(this).data('max');

                //Calculate total price
                $(document).on("change", "#quantity", function() {
                    //    var country =  modal.find('input[name=country]').val(country);
                    var country = $('#country').val()
                    var quality = $('#quality').val();
                    var quantity = $('#quantity').val()

                    if (quality == 'Mixed' && country == 'Worldwide') {
                        var total_price = (price_per_k) * quantity;
                    } else if (quality == 'High' && country == 'Worldwide') {
                        var total_price = ((price_per_k) * quantity * 1.2);
                    } else if (quality == 'Mixed' && country !== 'Worldwide') {
                        var total_price = ((price_per_k) * quantity * 2);
                    } else if (quality == 'High' && country !== 'Worldwide') {
                        var total_price = ((price_per_k) * quantity * 2.4);
                    }
                    modal.find('input[name=price]').val(total_price.toFixed(2));
                    modal.find('input[name=clicks]').attr('min', 1).attr('max', quantity);
                });

                $('select').on("change", function() {
                    //    var country =  modal.find('input[name=country]').val(country);
                    var country = $('#country').val();
                    var quality = $('#quality').val();
                    var quantity = $('#quantity').val() || ''

                    if (quantity && quality == 'Mixed' && country == 'Worldwide') {
                        var total_price = (price_per_k) * quantity;
                    } else if (quantity && quality == 'High' && country == 'Worldwide') {
                        var total_price = ((price_per_k) * quantity * 1.2);
                    } else if (quantity && quality == 'Mixed' && country !== 'Worldwide') {
                        var total_price = ((price_per_k) * quantity * 2);
                    } else if (quantity && quality == 'High' && country !== 'Worldwide') {
                        var total_price = ((price_per_k) * quantity * 2.4);
                    }

                    modal.find('input[name=price]').val(total_price.toFixed(2));
                    modal.find('input[name=clicks]').attr('min', 1).attr('max', quantity);
                });

                modal.find('form').attr('action', url);
                modal.find('input[name=link]').val(link);
                modal.find('input[name=link2]').val(link2);
                modal.find('input[name=title]').val(title);
                modal.find('input[name=keyword]').val(keyword);
                modal.find('input[name=country]').val(country);
                modal.find('input[name=quality]').val(quality);
                modal.find('input[name=quantity]').attr('min', min).attr('max', max);
                modal.find('input[name=min]').val(min);
                modal.find('input[name=max]').val(max);
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
            $('#from-prevent-multiple-submits').on('submit', function() {
                $("#btn-save", this)
                    .html("Please wait...")
                    .attr('disabled', 'disabled');
                return true;
            })

        })(jQuery);
    </script>
@endpush
