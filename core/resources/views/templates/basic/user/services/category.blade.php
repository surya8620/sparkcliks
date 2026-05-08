@extends($activeTemplate . 'layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light tabstyle--two">
                            <thead>
                                <tr>
                                    <th>@lang('Service ID')</th>
                                    <th>@lang('Service')</th>
                                    <th>@lang('Price Per 1k')</th>
                                    <th>@lang('Min')</th>
                                    <th>@lang('Max')</th>
                                    <th>@lang('Make Order')</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse ($services as $service)
                                    <tr>
                                        <td>{{ $service->id }}</td>
                                        <td class="break_line">{{ __($service->name) }}
                                        </td>
                                        <td>
                                            {{ $general->cur_sym . showAmount($service->price_per_k) }}
                                        </td>
                                        <td>{{ @$service->min }}</td>
                                        <td>{{ @$service->max }}</td>

                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--info detailsBtn"
                                                data-original-title="@lang('Details')" data-toggle="tooltip"
                                                data-details="{{ $service->details }}" @disabled(!$service->details)> <i
                                                    class="la la-desktop"></i> @lang('Details')
                                            </button>

                                            <button type="button" class=" btn btn-sm btn-outline--primary orderBtn"
                                                data-original-title="@lang('Edit')" data-toggle="tooltip"
                                                data-url="{{ route('user.order.create', [$service->category_id, $service->id]) }}"
                                                data-price_per_k="{{ getAmount($service->price_per_k) }}"
                                                data-min="{{ $service->min }}" data-max="{{ $service->max }}">
                                                <i class="las la-cart-plus"></i> @lang('Order')
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($services->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($services) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>

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
                <form method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Link')</label>
                            <input type="text" class="form-control" name="link" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Quantity')</label>
                            <input type="number" class="form-control" name="quantity" required>

                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <div class="input-group">

                                    <div class="input-group-text">@lang('Min')</div>
                                    <input type="text" name="min" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-text">@lang('Max')</div>
                                    <input type="text" name="max" class="form-control" readonly>
                                </div>

                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="input-group">
                                <div class="input-group-text">@lang('Price')</div>
                                <input type="text" class="form-control total_price text--success" name="price"
                                    readonly>
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
@push('breadcrumb-plugins')
    <x-back route="{{ route('user.services') }}" />
@endpush

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

            $('.detailsBtn').on('click', function() {
                var modal = $('#detailsModal');
                var details = $(this).data('details');

                modal.find('#details').html(details);
                modal.modal('show');
            });

            $('.orderBtn').on('click', function() {
                var modal = $('#orderModal');
                var url = $(this).data('url');
                var price_per_k = $(this).data('price_per_k');
                var min = $(this).data('min');
                var max = $(this).data('max');

                //Calculate total price
                $(document).on("keyup", "#quantity", function() {
                    var quantity = $('#quantity').val()
                    var total_price = (price_per_k / 1000) * quantity;
                    modal.find('input[name=price]').val("{{ $general->cur_sym }}" + total_price
                        .toFixed(2));
                });

                modal.find('form').attr('action', url);
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
        })(jQuery);
    </script>
@endpush
