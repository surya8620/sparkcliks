@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="show-filter mb-3 text-end">
        <button type="button" class="btn btn--base showFilterBtn btn--sm"><i class="las la-filter"></i>
            @lang('Filter')</button>
    </div>
    <div class="card responsive-filter-card mb-4 custom--card">
        <div class="card-body">
            <form>
                <div class="d-flex flex-wrap gap-4">
                    <div class="flex-grow-1">
                        <label class="form--label form-label">@lang('Transaction Number')</label>
                        <input type="text" name="search" value="{{ request()->search }}"
                            class="form-control form--control">
                    </div>
                    <div class="flex-grow-1 align-self-end">
                        <button class="btn btn--base w-100"><i class="fa fa-search"></i> @lang('Search')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card custom--card mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table--responsive--lg table">
                    <thead>
                        <tr>
                            <th>@lang('Trx')</th>
                            <th>@lang('Credits')</th>
                            <th>@lang('Details')</th>
                            <th>@lang('Transacted On')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trx)
                            <tr>
                                <td>
                                    <strong>{{ $trx->trx }}</strong>
                                </td>

                                <td class="budget">
                                    <span
                                        class="fw-bold @if ($trx->trx_type == '+') text-success @else text-danger @endif">
                                        {{ $trx->trx_type }} {{ $trx->credits }}
                                    </span>
                                </td>

                                <td
                                    style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ __($trx->details) }}
                                </td>

                                <td>
                                    {{ showDateTime($trx->created_at) }}<br>{{ diffForHumans($trx->created_at) }}
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
    </div>
    {{ paginateLinks($transactions) }}
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
@endpush
