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
                                <th scope="col">@lang('Name')</th>
                                <th scope="col">@lang('Country')</th>                                
                                <th scope="col">@lang('Visitors Sent')</th>
                                <th scope="col">@lang('Status')</th>
                                <th scope="col">@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $item)
                            @if ($item->service_id == 90)
                            <tr>
                                <td data-label="@lang('ID')">{{ $item->id }}</td>
                                <td data-label="@lang('Name')">{{ $item->name }}</td>
                                <td data-label="@lang('Country')">{{ $item->country }}</td>
                                <td data-label="@lang('Visitors Sent')">{{ $item->start_counter }}</td>
                                <td data-label="@lang('Status')">
                                @if($item->status == 0)
                                    <span class="text--small badge font-weight-normal badge--warning">@lang('Error')</span>
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
                                <td data-label="@lang('Actions')">
                                    <a href="{{ route('user.premium.details', $item->id) }}" class="icon-btn btn--primary ml-1" title="Edit">
                                        <i class="la la-edit"></i>
                                    </a>&nbsp;
                                    <a href="{{ route('user.premium.reports', $item->id) }}" class="icon-btn btn--primary ml-1" title="Reports">
                                        <i class="las la-chart-bar"></i>
                                    </a>&nbsp;
                                    @if($item->status == 2 || $item->status == 5)
                                    <a href="javascript:void(0)" class="icon-btn btn--success ml-1 statusBtn" title="Renew" data-original-title="@lang('Renew')" data-toggle="tooltip" data-url="{{ route('user.premium.renew', $item->id) }}">
                                        <i class="las la-sync"></i>
                                    </a>
                                    @else
                                    <button class="icon-btn btn--danger ml-1 statusBtn" disabled="">
                                        <i class="las la-sync" title="You can renew the campaign once its completed."></i>
                                    </button>
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

@endsection

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
        $('#from-prevent-multiple-submits').on('submit', function() {
            $("#btn-save", this)
                .html("Renewing...")
                .attr('disabled', 'disabled');
            return true;
        })
    })(jQuery);
</script>
@endpush