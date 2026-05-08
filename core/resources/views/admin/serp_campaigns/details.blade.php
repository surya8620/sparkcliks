@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 mb-4">
                <div class="card--body">
                    <form action="{{ route('admin.serp.update', $order->id) }}" method="post">
                        @csrf
                        <div class="card-body p-0">
                            <div class="table-responsive--sm table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th>@lang('ID')</th>
                                            <td class="text-start">{{ $order->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('User')</th>
                                            <td class="text-start">
                                                <a href="{{ route('admin.users.detail', $order->user_id) }}">
                                                    {{ $order->user->username }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>@lang('Service')</th>
                                            <td class="text-start">
                                                {{ __($order->service->name) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>@lang('Link')</th>
                                            <td class="text-start">
                                                <a href="{{ empty(parse_url($order->link, PHP_URL_SCHEME)) ? 'https://' : null }}{{ $order->link }}"
                                                    target="_blank">{{ $order->link }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>@lang('Link 2')</th>
                                            <td class="text-start">
                                                <a href="{{ empty(parse_url($order->link2, PHP_URL_SCHEME)) ? 'https://' : null }}{{ $order->link2 }}"
                                                    target="_blank">{{ $order->link2 }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>@lang('Keywords')</th>
                                            <td class="text-start"><textarea type="text" class="form--control" readonly>{{ $order->keyword }}</textarea></td>
                                        </tr>
                                        <tr>
                                            <th>@lang('Clicks/day')</th>
                                            <td class="text-start">{{ $order->clicks }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('Qualitly')</th>
                                            <td class="text-start">{{ $order->quality }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('Quantity')</th>
                                            <td class="text-start">{{ $order->quantity }}</td>
                                        </tr>
                                        <tr>
                                            <th>
                                                @lang('Start Counter')
                                            </th>
                                            <td class="text-start">
                                                @if ($order->status == Status::ORDER_PENDING || $order->status == Status::ORDER_PROCESSING)
                                                    <input class="form-control" name="start_count" type="text"
                                                        value="{{ $order->start_counter }}"
                                                        required>
                                                @else
                                                    {{ $order->start_counter }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>@lang('Remains')</th>
                                            <td class="text-start">{{ $order->remain }}</td>
                                        </tr>
                                        <tr>
                                            <th>
                                                @lang('Attempts')
                                            </th>
                                            <td class="text-start">
                                                @if ($order->status == Status::ORDER_PENDING || $order->status == Status::ORDER_PROCESSING)
                                                    <input class="form-control" name="attempt" type="text"
                                                        value="{{ $order->attempt }}"
                                                        required>
                                                @else
                                                    {{ $order->attempt }}
                                                @endif
                                            </td>
                                        </tr>
                                      <tr>
                                            <th>@lang('Error')</th>
                                            <td data-label="@lang('Error')" class="text-start">
                                                @if($order->status == 0 || $order->status == 1 || $order->status == 6)
                                                <input type="text" name="error" value="{{ $order->error }}" class="form-control">
                                                @else
                                                {{ $order->error }}
                                                @endif
                                            </td>
                                        <tr>
                                        <tr>
                                            <th>@lang('Status')</th>
                                            <td class="text-start">
                                                @if ($order->status == Status::ORDER_PENDING || $order->status == Status::ORDER_PROCESSING)
                                                    <div class="form-group">
                                                        <select class="form-control select2"
                                                            data-minimum-results-for-search="-1" name="status" required>
                                                            <option>@lang('Select Status')</option>
                                                            <option value="0" @selected($order->status == Status::ORDER_PENDING)>
                                                                @lang('Pending')</option>
                                                            <option value="1" @selected($order->status == Status::ORDER_PROCESSING)>
                                                                @lang('Processing')</option>
                                                            <option value="2" @selected($order->status == Status::ORDER_COMPLETED)>
                                                                @lang('Completed')</option>
                                                            <option value="3" @selected($order->status == Status::ORDER_DENIED)>
                                                                @lang('Denied')</option>
                                                            <option value="4" @selected($order->status == Status::ORDER_CANCELLED)>
                                                                @lang('Cancelled')</option>
                                                            <option value="5" @selected($order->status == Status::ORDER_EXPIRED)>
                                                                @lang('Expired')</option>   
                                                        </select>
                                                    </div>
                                                @elseif($order->status == Status::ORDER_COMPLETED)
                                                    <span class="badge  badge--success">@lang('Completed')</span>
                                                @elseif($order->status == Status::ORDER_CANCELLED)
                                                    <span class="badge  badge--danger">@lang('Cancelled')</span>
                                                @elseif($order->status == Status::ORDER_DENIED)
                                                    <span class="badge  badge--danger">@lang('Denied')</span>  
                                                @else
                                                    <span class="badge  badge--dark">@lang('Refunded')</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if ($order->status == Status::ORDER_PENDING || $order->status == Status::ORDER_PROCESSING || $order->status == Status::ORDER_PAUSED)
                            <div class="card-footer">
                                <button class="btn btn--primary w-100 h-45 " type="submit">@lang('Submit')</button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.serp.index') }}" />
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';

            $('.select2').select2();
        })(jQuery)
    </script>
@endpush
