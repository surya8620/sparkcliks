@extends($activeTemplate . 'layouts.master')
@section('content')

    <div class="dashboard-table__header d-flex justify-content-end pt-0 px-0">
        <div class="dashboard-table__btn">
            <a class="btn btn--sm btn-outline--base" href="{{ route('ticket.open') }}"> <i class="las la-plus"></i> @lang('New Ticket')</a>
        </div>
    </div>
    <div class="table-responsive mb-3">
        <table class="table--responsive--lg table">
            <thead>
                <tr>
                    <th>@lang('Subject')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Priority')</th>
                    <th>@lang('Last Reply')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($supports as $support)
                    <tr>
                        <td style="max-width: 300px;">
                            <a class="fw-bold d-inline-block text-truncate w-100"
                            href="{{ route('ticket.view', $support->ticket) }}"
                            title="[@lang('Ticket')#{{ $support->ticket }}] {{ __($support->subject) }}">
                                [@lang('Ticket')#{{ $support->ticket }}] {{ __($support->subject) }}
                            </a>
                        </td>
                        <td>
                            @php echo $support->statusBadge; @endphp
                        </td>
                        <td>
                            @if ($support->priority == Status::PRIORITY_LOW)
                                <span class="badge badge--dark">@lang('Low')</span>
                            @elseif($support->priority == Status::PRIORITY_MEDIUM)
                                <span class="badge badge--warning">@lang('Medium')</span>
                            @elseif($support->priority == Status::PRIORITY_HIGH)
                                <span class="badge badge--danger">@lang('High')</span>
                            @endif
                        </td>
                        <td>{{ diffForHumans($support->last_reply) }} </td>

                        <td>
                            <a class="btn btn-outline--base btn--sm" href="{{ route('ticket.view', $support->ticket) }}">
                                <i class="las la-desktop"></i>
                            </a>
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

    {{ paginateLinks($supports) }}
@endsection
