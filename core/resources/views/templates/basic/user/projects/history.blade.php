@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="card custom--card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table--responsive--lg table">
                    <thead>
                        <tr>
                            <th class="text-center">@lang('ID')</th>
                            <th class="text-center">@lang('Name')</th>
                            <th class="text-center">@lang('Active Users')</th>
                            <th class="text-center">@lang('Status')</th>
                            <th class="text-center">@lang('Date Created')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse($campaign as $item)
                            <tr>
                                <td class=" text-end text-md-center">
                                    <div>
                                        <span class="fw-bold text--base"> {{ @$item->id }}</span>
                                    </div>
                                </td>
                                <td class=" text-end text-md-center">
                                    <div>
                                        <span class="fw-bold text--base"> {{ @$item->name }}</span>
                                    </div>
                                </td>
                                <td class=" text-end text-md-center">
                                    <div>
                                        <span class="fw-bold text--base"> {{ @$item->active_users }}</span>
                                    </div>
                                </td>
                                <td class=" text-end text-md-center">
                                    <div>
                                        @if($item->status == 'initializing')
                                        <span class="text--small badge font-weight-normal badge--warning" title="Campaign is active">@lang('Initializing')</span>
                                        @elseif($item->status == 'pending')
                                        <span class="text--small badge font-weight-normal badge--success" title="Campaign is active">@lang('Active')</span>
                                        @elseif($item->status == 'processing')
                                        <span class="text--small badge font-weight-normal badge--success" title="Campaign is active">@lang('Active')</span>
                                        @elseif($item->status == 'completed')
                                        <span class="text--small badge font-weight-normal badge--dark" title="Campaign is completed">@lang('Completed')</span>
                                        @elseif($item->status == 'expired')
                                        <span class="text--small badge font-weight-normal badge--danger" title="Campaign is inactive">@lang('Inactive')</span>
                                        @elseif($item->status == 'paused')
                                        <span class="text--small badge font-weight-normal badge--warning" title="Campaign is paused">@lang('Paused')</span>
                                        @else
                                        <span class="text--small badge font-weight-normal badge--primary" title="Grace Period">@lang('Grace')</span>
                                        @endif
                                    </div>
                                </td>

                                <td class=" text-end text-md-center text--base">
                                    <div>
                                        {{ showDateTime($item->created_at) }} <br> {{ diffForHumans($item->created_at) }}
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-outline--base btn--sm detailBtn"
                                        data-user_data="{{ json_encode($item->withdraw_information) }}"
                                        @if ($item->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $item->admin_feedback }}" @endif>
                                        <i class="la la-desktop"></i>
                                    </button>
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
                    <ul class="list-group userData list-group-flush pt-0"></ul>
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
                var userData = $(this).data('user_data');
                var html = ``;
                userData.forEach(element => {
                    if (element.type != 'file') {
                        html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${element.name}</span>
                            <span">${element.value}</span>
                        </li>`;
                    }
                });
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
