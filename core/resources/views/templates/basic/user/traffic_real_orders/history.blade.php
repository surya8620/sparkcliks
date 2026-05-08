@extends($activeTemplate . 'layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">

        <div class="card b-radius--10 mb-4">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light tabstyle--two custom-data-table">
                        <thead>
                            <tr>
                                <th scope="col">@lang('ID')</th>
                                <th scope="col">@lang('Pack')</th>
                                <th scope="col">@lang('Name')</th>
                                <th scope="col">@lang('Traffic Source')</th>
                                <th scope="col" title="Visitors Sent">@lang('Visitors Sent')</th>
                                <th scope="col">@lang('Status')</th>
                                <th scope="col">@lang('Analytics Tag')</th>
                                <th scope="col">@lang('Expires On')</th>
                                <th scope="col">@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $item)
                            @if ($item->service_id == 88)
                            <tr>
                                <td data-label="@lang('ID')">{{ $item->id }}</td>
                                <td data-label="@lang('pack')">
                                    @if($item->traffic_plan == 1)
                                    <span class="text-center">@lang('Nano')</span>
                                    @elseif($item->traffic_plan == 2)
                                    <span class="text-center">@lang('Mini')</span>
                                    @elseif($item->traffic_plan == 3)
                                    <span class="text-center">@lang('Small')</span>
                                    @elseif($item->traffic_plan == 4)
                                    <span class="text-center">@lang('Medium')</span>
                                    @elseif($item->traffic_plan == 5)
                                    <span class="text-center">@lang('Large')</span>
                                    @elseif($item->traffic_plan == 6)
                                    <span class="text-center">@lang('Ultimate')</span>
                                    @endif
                                </td>
                                <td data-label="@lang('Name')">{{ $item->name }}</td>
                                <td data-label="@lang('Traffic Source')">
                                    @if($item->tt == 1)
                                    <span class="text-center">@lang('Direct')</span>
                                    @elseif($item->tt == 2)
                                    <span class="text-center">@lang('Organic')</span>
                                    @elseif($item->tt == 3)
                                    <span class="text-center">@lang('Social')</span>
                                    @elseif($item->tt == 4)
                                    <span class="text-center">@lang('Referral')</span>
                                    @endif
                                </td>
                                <td data-label="@lang('Visitors Sent')">{{ $item->start_counter }}</td>
                                <td data-label="@lang('Status')">
                                @if($item->status == 0)
                                    <span class="text--small badge font-weight-normal badge--warning" title="{{ $item->error }}">{{ $item->error }}</span>
                                    @elseif($item->status == 1)
                                    <span class="text--small badge font-weight-normal badge--primary" title="Campaign is active and running">@lang('Active')</span>
                                    @elseif($item->status == 2)
                                    <span class="text--small badge font-weight-normal badge--success">@lang('Completed')</span>
                                    @elseif($item->status == 3)
                                    <span class="text--small badge font-weight-normal badge--danger">@lang('Denied')</span>
                                    @elseif($item->status == 4)
                                    <span class="text--small badge font-weight-normal badge--dark">@lang('Cancelled')</span>
                                    @elseif($item->status == 5)
                                    <span class="text--small badge font-weight-normal badge--danger">@lang('Expired')</span>
                                    @else
                                    <span class="text--small badge font-weight-normal badge--primary" title="Grace Period">@lang('Grace')</span>
                                    @endif
                                </td>
                                <td data-label="@lang('Analytics Tag')">
                                    @if($item->ga_tag !== null || $item->histats !== null)
				    <span class="text--small badge font-weight-normal badge--success">@lang('FOUND')</span>
                                    @else
                                    <span class="text--small badge font-weight-normal badge--danger">@lang('NOT FOUND')</span>
                                    @endif
                                </td>

                                <td data-label="@lang('Expires On')">{{ showDateTime($item->traffic_exp) }}</td>
                                <td data-label="@lang('Actions')">
                                    <a href="{{ route('user.web.details', $item->id) }}" class="icon-btn btn--primary ml-1" title="Edit">
                                        <i class="la la-edit"></i>
                                    </a>&nbsp;
                                    <a href="{{ route('user.web.reports', $item->id) }}" class="icon-btn btn--primary ml-1" title="Reports">
                                        <i class="las la-chart-bar"></i>
                                    </a>&nbsp;
                                    @if($item->status == 2 || $item->status == 5)
                                    <a href="javascript:void(0)" class="icon-btn btn--success ml-1 statusBtn" title="Renew" data-original-title="@lang('Renew')" data-toggle="tooltip" data-url="{{ route('user.web.renew', $item->id) }}">
                                        <i class="las la-sync"></i>
                                    </a>
                                    @elseif($item->status == 0)
                                    <a href="javascript:void(0)" class="icon-btn btn--danger ml-1 playBtn" title="Resume Campaign" data-original-title="@lang('Resume Campaign')" data-toggle="tooltip" data-url="{{ route('user.web.resume', $item->id) }}">
                                        <i class="las la-play"></i>
                                    </a>
                                    @elseif($item->status == 1)
                                    <a href="javascript:void(0)" class="icon-btn btn--success ml-1 pauseBtn" title="Pause Campaign" data-original-title="@lang('Pause Campaign')" data-toggle="tooltip" data-url="{{ route('user.web.pause', $item->id) }}">
                                        <i class="las la-pause"></i>
                                    </a>
				    @else
                                    @endif
                                </td>
                            </tr>
                            @else
                            @endif
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
                {{ $orders->links('admin.partials.paginate') }}
            </div>
        </div><!-- card end -->

    </div>
</div>

{{-- Renew MODAL --}}
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">@lang('Renew Order')</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="" id="from-prevent-multiple-submits">
                @csrf
                <input type="hidden" name="delete_id" id="delete_id" class="delete_id" value="0">
                <div class="modal-body">
                    <p class="text-muted">@lang('Would you like to renew this campaign?')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary" id="btn-save">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Pause MODAL --}}
<div class="modal fade" id="pauseModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">@lang('Pause Campaign')</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="" id="from-prevent-multiple-submits">
                @csrf
                <input type="hidden" name="delete_id" id="delete_id" class="delete_id" value="0">
                <div class="modal-body">
                    <p class="text-muted">@lang('Would you like to pause this campaign?')</p>
                    <p class="text-muted">@lang('Note: Traffic will be stopped in a while.')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary" id="btn-save">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Resume MODAL --}}
<div class="modal fade" id="playModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">@lang('Pause Campaign')</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="" id="from-prevent-multiple-submits">
                @csrf
                <input type="hidden" name="delete_id" id="delete_id" class="delete_id" value="0">
                <div class="modal-body">
                    <p class="text-muted">@lang('Would you like to resume this campaign?')</p>
                    <p class="text-muted">@lang('Note: Traffic will be start in a while.')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary" id="btn-save">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@push('breadcrumb-plugins')
    @php
        $lastCron = Carbon\Carbon::parse($general->last_report_update)->diffInSeconds();
    @endphp
    <span
        class="text--info">
        @lang('Last Report Updated')
        <strong>{{ diffForHumans($general->last_report_update) }}</strong>
    </span>
@endpush


@push('style')
<style>
    .break_line {
        white-space: initial !important;
    }
</style>
@endpush
@push('breadcrumb')
<a class="btn btn-sm btn--primary box--shadow1 text-white text--small" href="{{ url()->previous() }}"><i class="fa fa-fw fa-backward"></i>@lang('Go Back')</a>
@endpush

@push('script')
<script>
    (function($) {
        "use strict";
        $('.statusBtn').on('click', function() {
            var modal = $('#statusModal');
            var url = $(this).data('url');

            modal.find('form').attr('action', url);
            modal.modal('show');
        });
        $('.pauseBtn').on('click', function() {
            var modal = $('#pauseModal');
            var url = $(this).data('url');

            modal.find('form').attr('action', url);
            modal.modal('show');
        });
        $('.playBtn').on('click', function() {
            var modal = $('#playModal');
            var url = $(this).data('url');

            modal.find('form').attr('action', url);
            modal.modal('show');
        });
        $('#from-prevent-multiple-submits').on('submit', function() {
            $("#btn-save", this)
                .html("Please wait...")
                .attr('disabled', 'disabled');
            return true;
        })
    })(jQuery);
</script>
@endpush