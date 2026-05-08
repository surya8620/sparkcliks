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
            <th scope="col" class="text-center" style="font-size: 14px;">@lang('Name')</th>
            <th scope="col" class="text-center" style="font-size: 14px;">@lang('Type')</th>
            <th scope="col" class="text-center" style="font-size: 14px;">@lang('Clicks')</th>
            <th scope="col" class="text-center" style="font-size: 14px;">@lang('Quantity')</th>
            <th scope="col" class="text-center" style="font-size: 14px;">@lang('Status')</th>
            <th scope="col" class="text-center" style="font-size: 14px;">@lang('Created On')</th>
            <th scope="col" class="text-center" style="font-size: 14px;">@lang('Actions')</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($orders as $item)
            <tr>
                <td data-label="@lang('Order ID')" class="text-center" style="font-size: 14px;">{{ $item->id }}</td>
                <td data-label="@lang('Name')" class="text-center" style="font-size: 14px;">{{ __($item->name) }}
                </td>
                <td data-label="@lang('Type')" class="text-center" style="font-size: 14px;">
                    {{ __($item->service->name) }}</td>
                <td data-label="@lang('Quantity')" class="text-center" style="font-size: 14px;">
                    {{ $item->start_counter }}
                </td>
                <td data-label="@lang('Quantity')" class="text-center" style="font-size: 14px;">{{ $item->quantity }}
                </td>
                <td data-label="@lang('Status')" class="text-center" style="font-size: 14px;">
                    @if ($item->status == 0)
                        <span class="text--small badge font-weight-normal badge--warning">@lang('Error')</span>
                    @elseif($item->status == 1)
                        <span class="text--small badge font-weight-normal badge--primary">@lang('Active')</span>
                    @elseif($item->status == 2)
                        <span class="text--small badge font-weight-normal badge--success">@lang('Completed')</span>
                    @elseif($item->status == 3)
                        <span class="text--small badge font-weight-normal badge--danger">@lang('Denied')</span>
                    @elseif($item->status == 4)
                        <span class="text--small badge font-weight-normal badge--dark">@lang('Cancelled')</span>
                    @elseif($item->status == 5)
                        <span class="text--small badge font-weight-normal badge--danger">@lang('Expired')</span>
                    @else
                        <span class="text--small badge font-weight-normal badge--primary">@lang('Grace')</span>
                    @endif
                </td>
                <td data-label="@lang('Created On')" class="text-center" style="font-size: 14px;">
                    {{ showDateTime($item->created_at) }}</td>
                <td data-label="@lang('Actions')" class="text-center">
                    <a href="{{ route('user.seo.details', $item->id) }}" class="action-btn edit-btn ml-1"
                        title="Edit">
                        <i class="fa-solid fa-pen-to-square text--primary" style="font-size: 18px;"></i>
                    </a>&nbsp;
                    @if ($item->status == 0 || $item->status == 1)
                        <a href="javascript:void(0)" class="action-btn edit-btn ml-1 statusBtn" title="Cancel"
                            data-original-title="@lang('Cancel')" data-toggle="tooltip"
                            data-url="{{ route('user.seo.cancel', $item->id) }}">
                            <i class="fa-solid fa-close text--primary" style="font-size: 18px;"></i>
                        </a>
                    @else
                    @endif
                </td>

            </tr>

        @empty
            <tr>
                <td class="text-muted text-center" colspan="100%">{{ __($empty_message) }}</td>
            </tr>
        @endforelse
    </tbody>
</table><!-- table end -->

{{-- Cancel MODAL --}}
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">@lang('Cancel Campaign')</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form method="post" action="" id="cancel-form">
                @csrf
                <input type="hidden" name="delete_id" id="delete_id" class="delete_id" value="0">
                <div class="modal-body">
                    <p class="text-muted">@lang('Are you sure you want to cancel this campaign?')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--base" data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary" id="btn-cancel">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('style')
    <style>
        .break_line {
            white-space: initial !important;
        }
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
@endpush
@push('script')
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
            
            // Prevent double-clicking on edit buttons (direct navigation)
            $('a[href*="details"]').on('click', function(e) {
                if (!preventDoubleClick(this, 1500)) {
                    e.preventDefault();
                    return false;
                }
            });
            
            // Handle cancel form submission
            $('#cancel-form').on('submit', function() {
                const submitBtn = $("#btn-cancel", this);
                
                // Immediately disable the submit button and show loading state
                submitBtn.prop('disabled', true)
                    .addClass('btn-submitting')
                    .html('<i class="fa fa-spinner fa-spin"></i> Cancelling...');
                
                return true;
            })
        })(jQuery);
    </script>
@endpush
