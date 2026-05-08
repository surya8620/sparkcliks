@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row">
        <div class="col-lg-12">

            <div class="card b-radius--10 mb-4">
                    <div class="card-body p-0">
                        <div class="table-responsive--sm table-responsive">
                            <table class="table table--light tabstyle--two custom-data-table">
                                <thead>
                                <tr>
                                    <th scope="col">@lang('Order ID')</th>
                                    <th scope="col">@lang('URL')</th>
                                    <th scope="col">@lang('Keyword')</th>
                                    <th scope="col">@lang('Clicker Region')</th>
                                    <th scope="col">@lang('Clicker Country')</th>
                                    <th scope="col">@lang('Clicker IP')</th>
                                    <th scope="col">@lang('Timestamp(UTC)')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($clicks as $item)
                                    <tr>
                                        <td data-label="@lang('Order ID')">{{ $item->order_id }}</td>
                                        <td data-label="@lang('URL')"><a href="{{ empty(parse_url($item->link, PHP_URL_SCHEME)) ? 'https://' : null }}{{ $item->link }}" target="_blank">{{ $item->link }}</a></td>
                                        <td data-label="@lang('Keyword')">{{ $item->keyword }}</td>
                                        <td data-label="@lang('Clicker Region')">{{ $item->clicker_region }}</td>
                                        <td data-label="@lang('Clicker Country')">{{ $item->clicker_country }}</td>
                                        <td data-label="@lang('Clicker IP')">{{ $item->clicker_ip }}</td>
                                        <td data-label="@lang('Timestamp(UTC)')">{{ showDateTime($item->created_at) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($empty_message) }}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table><!-- table end -->
                        </div>
                    </div>

                    <div class="card-footer">
                        {{ $clicks->links('admin.partials.paginate') }}
                    </div>
                </div><!-- card end -->

        </div>
    </div>
@endsection

@push('style')
    <style>
        .break_line{
            white-space: initial !important;
        }
    </style>
@endpush
