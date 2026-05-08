@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            @if (request()->routeIs('admin.serp.index'))
                <div class="show-filter mb-3 text-end">
                    <button type="button" class="btn btn-outline--primary showFilterBtn btn-sm"><i class="las la-filter"></i>
                        @lang('Filter')</button>
                </div>
                <div class="card responsive-filter-card mb-4">
                    <div class="card-body">
                        <form>
                            <div class="d-flex flex-wrap gap-4">
                                <div class="flex-grow-1">
                                    <label>@lang('Search')</label>
                                    <input type="search" name="search" value="{{ request()->search }}"
                                        class="form-control" placeholder="@Lang('Username / Campaign Id')">
                                </div>
                                <div class="flex-grow-1">
                                    <label>@lang('Date')</label>
                                    <input name="date" type="search"
                                        class="datepicker-here form-control bg--white pe-2 date-range"
                                        placeholder="@lang('Start Date - End Date')" autocomplete="off" value="{{ request()->date }}">
                                </div>
                                <div class="flex-grow-1 align-self-end">
                                    <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i>
                                        @lang('Filter')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
            <div class="card b-radius--10 mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive--lg table-responsive">
                        <table class="table table--light tabstyle--two">
                            <thead>
                                <tr>
                                    <th>@lang('ID')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Service')</th>
                                    <th>@lang('Country')</th>
                                    <th>@lang('Quantity')</th>
                                    <th>@lang('Completed')</th>
                                    <th>@lang('Remains')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>
                                            <span class="d-block">{{ __(@$order->user->fullname) }}</span>
                                            <a href="{{ route('admin.users.detail', $order->user_id) }}">
                                                <span>@</span>{{ __(@$order->user->username) }}
                                            </a>
                                        </td>
                                        <td class="break_line">
                                            {{ __(@$order->service->name) }}
                                        </td>
                                        <td>{{ $order->country }}</td>
                                        <td>{{ $order->quantity }}</td>
                                        <td>{{ $order->start_counter }}</td>
                                        <td>{{ $order->remain }}</td>
                                        <td>
                                            @if ($order->status == Status::ORDER_PENDING)
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @elseif($order->status == Status::ORDER_PROCESSING)
                                                <span class="badge badge--primary">@lang('Processing')</span>
                                            @elseif($order->status == Status::ORDER_COMPLETED)
                                                <span class="badge badge--success">@lang('Completed')</span>
                                            @elseif($order->status == Status::ORDER_CANCELLED)
                                                <span class="badge badge--danger">@lang('Cancelled')</span>
                                            @else
                                                <span class="badge badge--dark">@lang('Refunded')</span>
                                            @endif
                                            <br>
                                            <span>{{ showDateTime($order->created_at) }}</span>
                                        </td>
                                        <td>
                                            <a class="btn btn-sm btn-outline--primary"
                                                href="{{ route('admin.serp.details', $order->id) }}">
                                                <i class="la la-desktop"></i>
                                            </a>
                                                <a href="{{ route('admin.orders.reports', $order->id) }}" class="btn btn-sm btn--primary ml-1">
                                                <i class="las la-chart-bar"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($orders->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($orders) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if (request()->routeIs('admin.orders.processing'))
        <x-confirmation-modal />
    @endif
@endsection

@push('breadcrumb-plugins')
    @if (!request()->routeIs('admin.serp.index'))
        <x-search-form placeholder="Search here..." />
    @endif
    @if (request()->routeIs('admin.serp.processing'))
        <button class="btn btn-outline--primary confirmationBtn" data-question="@lang('Are you sure to update orders information from Provider?')"
            data-action="{{ route('admin.orders.provider.information.update') }}">
            <i class="far fa-edit"></i> @lang('Update Provider Information')
        </button>
    @endif
@endpush

@if (request()->routeIs('admin.serp.index'))
    @push('script-lib')
        <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
        <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
    @endpush

    @push('style-lib')
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
    @endpush

    @push('script')
        <script>
            (function($) {
                "use strict"

                const datePicker = $('.date-range').daterangepicker({
                    autoUpdateInput: false,
                    locale: {
                        cancelLabel: 'Clear'
                    },
                    showDropdowns: true,
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
                            .endOf('month')
                        ],
                        'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                        'This Year': [moment().startOf('year'), moment().endOf('year')],
                    },
                    maxDate: moment()
                });
                const changeDatePickerText = (event, startDate, endDate) => {
                    $(event.target).val(startDate.format('MMMM DD, YYYY') + ' - ' + endDate.format('MMMM DD, YYYY'));
                }

                $('.date-range').on('apply.daterangepicker', (event, picker) => changeDatePickerText(event, picker
                    .startDate, picker.endDate));

                if ($('.date-range').val()) {
                    let dateRange = $('.date-range').val().split(' - ');
                    $('.date-range').data('daterangepicker').setStartDate(new Date(dateRange[0]));
                    $('.date-range').data('daterangepicker').setEndDate(new Date(dateRange[1]));
                }
            })(jQuery)
        </script>
    @endpush
@endif
