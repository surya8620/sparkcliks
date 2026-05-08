@extends($activeTemplate . 'layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">

            <div class="card b-radius--10 mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light ">
                            <thead>
                                <tr>
                                    <th>@lang('Order ID')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Service')</th>
                                    <th>@lang('Link')</th>
                                    <th>@lang('Quantity')</th>
                                    <th>@lang('Start Counter')</th>
                                    <th>@lang('Remains')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td class="break_line">{{ __($item->category->name) }}</td>
                                        <td class="break_line">
                                            {{ __($item->service->name) }}</td>
                                        <td class="break_line"><a
                                                href="{{ empty(parse_url($item->link, PHP_URL_SCHEME)) ? 'https://' : null }}{{ $item->link }}"
                                                target="_blank">{{ $item->link }}</a></td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ $item->start_counter }}</td>
                                        <td>{{ $item->remain }}</td>
                                        <td>{{ showDateTime($item->created_at) }}</td>
                                        <td>
                                            @if ($item->status == 0)
                                                <span
                                                    class="text--small badge fw-normal badge--warning">@lang('Pending')</span>
                                            @elseif($item->status == 1)
                                                <span
                                                    class="text--small badge fw-normal badge--primary">@lang('Processing')</span>
                                            @elseif($item->status == 2)
                                                <span
                                                    class="text--small badge fw-normal badge--success">@lang('Completed')</span>
                                            @elseif($item->status == 3)
                                                <span
                                                    class="text--small badge fw-normal badge--danger">@lang('Cancelled')</span>
                                            @else
                                                <span
                                                    class="text--small badge fw-normal badge--dark">@lang('Refunded')</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>

                @if ($orders->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($orders) }}
                    </div>
                @endif
            </div><!-- card end -->

        </div>
    </div>
@endsection

@push('breadcrumb-plugins')

        <x-search-form placeholder="Search here ..." />

@endpush


@push('style')
    <style>
        .break_line {
            white-space: initial !important;
        }
    </style>
@endpush
