@php
    $lastCron = Carbon\Carbon::parse($general->last_report_update)->diffInSeconds();
@endphp

<div class="dashboard-table__header d-flex justify-content-end">
    <small class="text--info">
        @lang('Last Updated')
        <strong>{{ diffForHumans($general->last_report_update) }}</strong>
    </small>
</div>
<table class="table--responsive--lg table-bordered table-striped table">
    <thead>
        <tr>
            <th scope="col" class="text-center" style="font-size: 14px;">@lang('Campaign ID')</th>
            <th scope="col" class="text-center" style="font-size: 14px;">@lang('Pack')</th>
            <th scope="col" class="text-center" style="font-size: 14px;">@lang('Name')</th>
            <th scope="col" class="text-center" style="font-size: 14px;">@lang('Traffic Source')</th>
            <th scope="col" class="text-center" style="font-size: 14px;" title="Visitors Sent">@lang('Visitors Sent')</th>
            <th scope="col" class="text-center" style="font-size: 14px;">@lang('Status')</th>
            <th scope="col" class="text-center" style="font-size: 14px;">@lang('Expires On')</th>
            <th scope="col" class="text-center" style="font-size: 14px;">@lang('Actions')</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($orders as $item)
            <tr>
                <td style="font-size: 14px;">{{ $item->id }}</td>
                <td class="break_line">
                    <span class="text-center" style="font-size: 14px;">{{ $item->service->name }}</span>
                </td>
                <td class="break_line" style="font-size: 14px;">
                    {{ $item->name }}
                </td>
                <td class="break_line" style="font-size: 14px;">
                    @if ($item->tt == 1)
                        <span class="text-center">@lang('Direct')</span>
                    @elseif($item->tt == 2)
                        <span class="text-center">@lang('Organic')</span>
                    @elseif($item->tt == 3)
                        <span class="text-center">@lang('Social')</span>
                    @elseif($item->tt == 4)
                        <span class="text-center">@lang('Referral')</span>
                    @endif
                </td>
                <td style="font-size: 14px;">{{ $item->start_counter }}</td>
                <td style="font-size: 14px;">
                    @if ($item->status == 0)
                        <span class="text--small badge font-weight-normal badge--warning"
                            title="{{ $item->error }}">@lang('Error')</span>
                    @elseif($item->status == 1)
                        <span class="text--small badge font-weight-normal badge--primary"
                            title="Campaign is active and running">@lang('Active')</span>
                    @elseif($item->status == 2)
                        <span class="text--small badge font-weight-normal badge--success"
                            title="Completed">@lang('Completed')</span>
                    @elseif($item->status == 3)
                        <span class="text--small badge font-weight-normal badge--danger"
                            title="Denied">@lang('Denied')</span>
                    @elseif($item->status == 4)
                        <span class="text--small badge font-weight-normal badge--dark"
                            title="Cancelled">@lang('Cancelled')</span>
                    @elseif($item->status == 5)
                        <span class="text--small badge font-weight-normal badge--danger"
                            title="Campaign has been Expired">@lang('Expired')</span>
                    @elseif($item->status == 6)
                        <span class="text--small badge fw-normal badge--warning"
                            title="Campaign has been Paused">@lang('Paused')</span>
                    @else
                        <span class="text--small badge font-weight-normal badge--primary"
                            title="Grace Period - Campaign is active and running">@lang('Grace')</span>
                    @endif
                </td>
                <td style="font-size: 14px;">{{ showDateTime($item->traffic_exp) }}</td>
                <td>

                    @if ($item->status == 2 || $item->status == 5)
                        <a href="javascript:void(0)" class="action-btn edit-btn ml-1 statusBtn" title="Renew"
                            data-original-title="@lang('Renew')" data-toggle="tooltip"
                            data-url="{{ route('user.web.renew', $item->id) }}">
                            <i class="fa-solid fa-arrows-rotate text--green" style="font-size: 18px;"></i>
                        </a>
                    @elseif($item->status == 6)
                        <a href="javascript:void(0)" class="action-btn edit-btn ml-1 playBtn" title="Resume Campaign"
                            data-original-title="@lang('Resume Campaign')" data-toggle="tooltip"
                            data-url="{{ route('user.web.resume', $item->id) }}">
                            <i class="fa-solid fa-play text--red" style="font-size: 18px;"></i>
                        </a>
                    @elseif($item->status == 1)
                        <a href="javascript:void(0)" class="action-btn edit-btn ml-1 pauseBtn" title="Pause Campaign"
                            data-original-title="@lang('Pause Campaign')" data-toggle="tooltip"
                            data-url="{{ route('user.web.pause', $item->id) }}">
                            <i class="fa-solid fa-pause text--green" style="font-size: 18px;"></i>
                        </a>
                    @endif
                    <a href="{{ route('user.web.details', $item->id) }}" class="action-btn edit-btn ml-1"
                        title="Edit">
                        <i class="fa-solid fa-pen-to-square text--primary" style="font-size: 18px;"></i>
                    </a>&nbsp;

                </td>
            </tr>
        @empty
            <tr>
                <td class="text-muted text-center" colspan="100%">{{ __($empty_message) }}</td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- ==================== Dashboard Modal Start ==================== -->
{{-- Renew MODAL --}}
<div class="dashboard-modal modal" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">@lang('Renew Order')</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="" id="renew-form">
                @csrf
                <input type="hidden" name="delete_id" id="delete_id" class="delete_id" value="0">
                <div class="modal-body">
                    <p class="text-muted">@lang('Would you like to renew this campaign?')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary" id="btn-renew">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Pause MODAL --}}
<div class="dashboard-modal modal" id="pauseModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">@lang('Pause Campaign')</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="" id="pause-form">
                @csrf
                <input type="hidden" name="delete_id" id="delete_id" class="delete_id" value="0">
                <div class="modal-body">
                    <p class="text-muted">@lang('Would you like to pause this campaign?')</p>
                    <p class="text-muted">@lang('Note: Traffic will be stopped in a while.')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary" id="btn-pause">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Resume MODAL --}}
<div class="dashboard-modal modal" id="playModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">@lang('Resume Campaign')</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="" id="resume-form">
                @csrf
                <input type="hidden" name="delete_id" id="delete_id" class="delete_id" value="0">
                <div class="modal-body">
                    <p class="text-muted">@lang('Would you like to resume this campaign?')</p>
                    <p class="text-muted">@lang('Note: Traffic will be start in a while.')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary" id="btn-resume">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="dashboard-modal modal" id="orderDetailModal" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="d-flex align-items-center">
                                <div class="order_icon me-2">
                                    <i class="las la-calendar fs-25"></i>
                                </div>
                                <div class="order_details">
                                    <div class="fw-bold">@lang('Order Date')</div>
                                    <span class="date-detail"></span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="d-flex align-items-center">
                                <div class="order_icon me-2">
                                    <i class="lab la-sith fs-25"></i>
                                </div>
                                <div class="order_details">
                                    <div class="fw-bold">@lang('Category')</div>
                                    <span class="category-detail"></span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="d-flex align-items-center">
                                <div class="order_icon me-2">
                                    <i class="las la-sort-numeric-up fs-25"></i>
                                </div>
                                <div class="order_details">
                                    <div class="fw-bold">@lang('Start Counter')</div>
                                    <span class="start-counter-detail"></span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="d-flex align-items-center">
                                <div class="order_icon me-2">
                                    <i class="las la-spinner fs-25"></i>
                                </div>
                                <div class="order_details">
                                    <div class="fw-bold">@lang('Remains')</div>
                                    <span class="remains-detail"></span>
                                </div>
                            </div>
                        </div>
                    </li>

                    <div class="dripfeed d-none">
                        <span class="my-2">@lang('Dripfeed:')</span>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="d-flex align-items-center">
                                    <div class="order_icon me-2">
                                        <i class="las la-running fs-25"></i>
                                    </div>
                                    <div class="order_details">
                                        <div class="fw-bold">@lang('Runs')</div>
                                        <span class="runs-detail"></span>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="d-flex align-items-center">
                                    <div class="order_icon me-2">
                                        <i class="las la-history fs-25"></i>
                                    </div>
                                    <div class="order_details">
                                        <div class="fw-bold">@lang('Intervals')</div>
                                        <span class="intervals-detail"></span>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </div>

                </ul>
            </div>
        </div>
    </div>
</div>

<x-confirmation-modal />
<!-- ==================== Dashboard Modal End ==================== -->

@push('script')
    <style>
        .btn-loading {
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
        }
        
        .action-btn.btn-loading {
            opacity: 0.5;
            transform: scale(0.95);
            transition: all 0.2s ease;
        }
        
        .action-btn.btn-loading i {
            animation: pulse 1s infinite;
        }
        
        .btn-submitting {
            opacity: 0.7;
            cursor: not-allowed;
            pointer-events: none;
            position: relative;
        }
        
        .btn-submitting i.fa-spin {
            margin-right: 5px;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }
        
        /* Center align all table content */
        .table td, .table th {
            text-align: center !important;
            vertical-align: middle !important;
        }
        
        .table .break_line {
            text-align: center !important;
        }
    </style>
    <script>
        (function($) {
            "use strict";
            
            // Prevent double-clicking on action buttons
            let buttonClickTimeout = {};
            
            function preventDoubleClick(button, delay = 1000) {
                const buttonId = $(button).attr('data-url') || $(button).attr('href');
                
                if (buttonClickTimeout[buttonId]) {
                    return false; // Prevent action if recently clicked
                }
                
                // Disable the button temporarily
                $(button).prop('disabled', true).addClass('btn-loading');
                
                // Set timeout to prevent rapid clicks
                buttonClickTimeout[buttonId] = true;
                setTimeout(() => {
                    delete buttonClickTimeout[buttonId];
                    $(button).prop('disabled', false).removeClass('btn-loading');
                }, delay);
                
                return true;
            }
            
            $('.statusBtn').on('click', function(e) {
                if (!preventDoubleClick(this, 2000)) {
                    e.preventDefault();
                    return false;
                }
                
                var modal = $('#statusModal');
                var url = $(this).data('url');

                modal.find('form').attr('action', url);
                modal.modal('show');
            });
            
            $('.pauseBtn').on('click', function(e) {
                if (!preventDoubleClick(this, 2000)) {
                    e.preventDefault();
                    return false;
                }
                
                var modal = $('#pauseModal');
                var url = $(this).data('url');

                modal.find('form').attr('action', url);
                modal.modal('show');
            });
            
            $('.playBtn').on('click', function(e) {
                if (!preventDoubleClick(this, 2000)) {
                    e.preventDefault();
                    return false;
                }
                
                var modal = $('#playModal');
                var url = $(this).data('url');

                modal.find('form').attr('action', url);
                modal.modal('show');
            });
            
            // Prevent double-clicking on edit buttons (direct navigation)
            $('a[href*="details"]').on('click', function(e) {
                if (!preventDoubleClick(this, 1500)) {
                    e.preventDefault();
                    return false;
                }
            });
            
            // Handle form submissions for all modals
            $('#renew-form').on('submit', function() {
                const submitBtn = $("#btn-renew", this);
                submitBtn.prop('disabled', true)
                    .addClass('btn-submitting')
                    .html('<i class="fa fa-spinner fa-spin"></i> Processing...');
                return true;
            });
            
            $('#pause-form').on('submit', function() {
                const submitBtn = $("#btn-pause", this);
                submitBtn.prop('disabled', true)
                    .addClass('btn-submitting')
                    .html('<i class="fa fa-spinner fa-spin"></i> Pausing...');
                return true;
            });
            
            $('#resume-form').on('submit', function() {
                const submitBtn = $("#btn-resume", this);
                submitBtn.prop('disabled', true)
                    .addClass('btn-submitting')
                    .html('<i class="fa fa-spinner fa-spin"></i> Resuming...');
                return true;
            });
        })(jQuery);
    </script>
    <script>
        'use strict';
        $(document).ready(function() {
            $(".orderDetailBtn").on("click", function() {
                var modal = $("#orderDetailModal");
                let data = $(this).data('resource')
                modal.find('.category-detail').text(data.category.name);
                modal.find('.start-counter-detail').text(data.start_counter);
                modal.find('.remains-detail').text(data.remain);
                modal.find('.date-detail').text($(this).data('order-date'));
                modal.find('.modal-title').text(data.service.name);
                if (data.runs && data.interval) {
                    $(".dripfeed").removeClass('d-none');
                    modal.find('.runs-detail').text(data.runs + ' ' + "@lang('Times')");
                    modal.find('.intervals-detail').text(data.interval + ' ' + "@lang('Minutes')");
                } else {
                    $(".dripfeed").addClass('d-none')
                }
                modal.modal('show');

            });
        });

        function redirectToUrl(button) {
            const url = button.getAttribute('data-action');
            if (url) {
                window.location.href = url;
            }
        }
    </script>
@endpush
