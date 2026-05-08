@extends($activeTemplate . 'layouts.app')
@section('panel')
    <div class="row gy-4">
        @forelse($categories as $category)
            <div class="col-lg-12">
                <div class="d-flex flex-wrap justify-content-between mb-3 gap-2">
                    <h3>{{ __($category->name) }}</h3>
                </div>
                <div class="card b-radius--10">
                    <div class="card-body p-0">
                        <div class="table-responsive--lg table-responsive">
                            <table class="table table--light tabstyle--two">
                                <thead>
                                    <tr>
                                        <th>@lang('Campaign')</th>

                                        <th>@lang('Make Order')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        
                                        $services = $category
                                            ->services()
                                            ->limit(10)
                                            ->get();
                                    @endphp
                                    @foreach ($services as $service)
                                        <tr>
                                            <td class="break_line">{{ __($service->name) }}
                                            </td>
                                            <td>
                                                <button type="button" class=" btn btn-sm btn-outline--primary orderBtn"
                                                    data-url="{{ route('user.seo.create', $service->id) }}"
                                                    data-api_provider_id="{{ $service->api_provider_id }}"
                                                    data-price_per_k="{{ getAmount($service->price_per_k) }}"
                                                    data-min="{{ $service->min }}" data-max="{{ $service->max }}">
                                                    <i class="las la-cart-plus"></i> @lang('Order')
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table><!-- table end -->
                        </div>
                    </div>
                </div><!-- card end -->
            </div>
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

    {{-- Details MODAL --}}
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"> @lang('Details')</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="details">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark btn-sm" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Order MODAL --}}
    <div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Place a new order')</h4>
                    <button type="button" class="close" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="post" class="resetForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="api_provider_id">
                        <div class="form-group">
                            <label>@lang('Link')</label>
                            <input type="url" class="form-control" name="link" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Quantity')</label>
                            <input type="number" class="form-control" name="quantity" required>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-text">@lang('Min')</div>
                                    <input type="number" name="min" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-text">@lang('Max')</div>
                                    <input type="number" name="max" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="input-group">
                                <div class="input-group-text">@lang('Price')</div>
                                <input type="text" class="form-control total_price " name="price" readonly>
                                <div class="input-group-text">@lang('Per 1K')</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </div>
                </form>
            </div>
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
                $('.resetForm').trigger('reset');
                var url = $(this).data('url');
                var pricePerK = parseFloat($(this).data('price_per_k'));
                var min = $(this).data('min');
                var max = $(this).data('max');
                let apiProviderId = $(this).data('api_provider_id');
                //Calculate total price
                $(document).on("keyup", "#quantity", function() {
                    var quantity = parseInt($('#quantity').val());
                    var totalPrice = parseFloat((pricePerK / 1000) * quantity);
                    modal.find('input[name=price]').val("{{ $general->cur_sym }}" + totalPrice
                        .toFixed(2));
                });

                modal.find('form').attr('action', url);
                modal.find('input[name=quantity]').attr('min', min).attr('max', max);
                modal.find('input[name=min]').val(min);
                modal.find('input[name=max]').val(max);
                modal.find('input[name=api_provider_id]').val(apiProviderId)
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
        })(jQuery);
    </script>
@endpush
