@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="dashboard-table__header d-flex justify-content-end pt-0 px-0">
    <div class="dashboard-table__btn">
        <form class="dashboard-form">
            <div class="input-group">
                <input class="form--control form-control" name="search" type="text" value="{{ request()->search }}" placeholder="@lang('Search by transactions')">
                <button class="input-group-text bg--base text-white">
                    <i class="las la-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>
<div class="card custom--card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table--responsive--lg table">
                <thead>
                    <tr>
                        <th>@lang('Transaction')</th>
                        <th class="text-center">@lang('Date')</th>                        
                        <th class="text-center">@lang('Amount')</th>
                        <th class="text-center">@lang('Status')</th>
                        <th class="text-center">@lang('Invoices')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deposits as $deposit)
                    <tr>
                        <td>
                            <div>
                                <span class="fw-bold"> <span class="text-primary">{{ __($deposit->plan_name) }}</span> </span>
                                <br>
                                <small> {{ $deposit->trx }} </small>
                            </div>
                        </td>

                        <td class="text-end text-md-center">
                            <div>
                                <span class="d-block">{{ showDateTime($deposit->created_at) }} </span>
                                <span>{{ diffForHumans($deposit->created_at) }}</span>

                            </div>
                        </td>
                        <td class="text-end text-md-center">
                            <div>
                                <strong title="@lang('Amount')">
                                   <span class="d-block">{{ showAmount($deposit->final_amo, currencyFormat: false) }}
                                    {{ __($deposit->method_currency) }}</span>
                                    <span>via {{ __($deposit->gateway?->name) }}</span>
                                </strong>
                            </div>
                        </td>
                        <td class="text-end text-md-center">
                            <span class="d-block">@php echo $deposit->statusBadge @endphp</span>
                           
                        </td>
                        <td>
                            @if($deposit->status == 1 && !is_null($deposit->inv_num))
                            <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
                                <a class="btn btn-outline--base btn--sm"
                                    href="{{ route('user.billing.invoice', $deposit->trx) }}">
                                    <i class="fa fa-file-invoice"></i>
                                </a>
                            </div>
                            @elseif($deposit->status == 1 && is_null($deposit->inv_num))
                            <strong class="d-flex align-items-center justify-content-center text--base" title="@lang('Invoice will be available soon')">@lang('Processing')</strong>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@if ($deposits->hasPages())
{{ $deposits->links() }}
@endif
{{-- APPROVE MODAL --}}
<div class="modal fade" id="detailModal" role="dialog" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Details')</h5>
                <span class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </span>
            </div>
            <div class="modal-body">
                <ul class="list-group userData mb-2 list-group-flush">
                </ul>
                <div class="feedback"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    (function($) {
        "use strict";
        $('.detailBtn').on('click', function() {
            var modal = $('#detailModal');

            var userData = $(this).data('info');
            var html = '';
            if (userData) {
                userData.forEach(element => {
                    if (element.type != 'file') {
                        html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${element.name}</span>
                                <span">${element.value}</span>
                            </li>`;
                    }
                });
            }

            modal.find('.userData').html(html);

            if ($(this).data('admin_feedback') != undefined) {
                var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
            } else {
                var adminFeedback = '';
            }

            modal.find('.feedback').html(adminFeedback);


            modal.modal('show');
        });
    })(jQuery);
</script>
@endpush