@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row">
        <div class="col-lg-12">

            <div class="card b-radius--10 mb-4">
                <div class="table-responsive mb-4">
                    <table class="table--responsive--lg table-bordered table-striped table">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center" style="font-size: 14px;">@lang('Campaign ID')</th>
                                <th scope="col" class="text-center" style="font-size: 14px;">@lang('Clicker IP')</th>
                                <th scope="col" class="text-center" style="font-size: 14px;">@lang('Clicker Region')</th>
                                <th scope="col" class="text-center" style="font-size: 14px;">@lang('Clicker Country')</th>
                                <th scope="col" class="text-center" style="font-size: 14px;">@lang('Timestamp(UTC)')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($clicks as $item)
                                <tr>
                                    <td data-label="@lang('ID')" class="text-center" style="font-size: 14px;">
                                        {{ $item->order_id }}</td>
                                    <td data-label="@lang('Clicker IP')" class="text-center" style="font-size: 14px;">
                                        {{ $item->clicker_ip }}</td>
                                    <td data-label="@lang('Clicker Region')" class="text-center" style="font-size: 14px;">
                                        {{ $item->clicker_region }}</td>
                                    <td data-label="@lang('Clicker Country')" class="text-center" style="font-size: 14px;">
                                        {{ $item->clicker_country }}</td>
                                    <td data-label="@lang('Timestamp(UTC)')" class="text-center" style="font-size: 14px;">
                                        {{ showMonthTime($item->updated_at) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($empty_message) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table><!-- table end -->
                </div>

                @if ($clicks->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($clicks) }}
                    </div>
                @endif
            </div><!-- card end -->

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
@push('breadcrumb-plugins')
    <a class="btn btn-sm btn--primary box--shadow1 text-white text--small" href="{{ url()->previous() }}"><i
            class="fa fa-fw fa-backward"></i>@lang('Go Back')</a>
@endpush
