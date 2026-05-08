@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 mb-4">
                <div class="card--body">
                    <form action="{{ route('admin.wt.update', $order->id) }}" method="post">
                        @csrf
                        <div class="card-body p-0">
                            <div class="table-responsive--sm table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th>@lang('Order ID')</th>
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
                                            <th>@lang('Category')</th>
                                            <td class="text-start">
                                                {{ __($order->category->name) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>@lang('Service')</th>
                                            <td class="text-start">
                                                {{ __($order->service->name) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>@lang('Campaign Name')</th>
                                            <td class="text-start">
                                                {{ __($order->name) }}
                                            </td>
                                        </tr>
                                        <tr>
                                    		<th data-label="@lang('Source')">@lang('Source')</th>
                                    		@if($order->tt == 1)
                                    		<td class="text-start" data-label="@lang('Direct')">Direct</td>
                                    		@elseif($order->tt == 2)
                                    		<td class="text-start" data-label="@lang('Organic')">Organic</td>
                                    		@elseif($order->tt == 3)
                                    		<td class="text-start" data-label="@lang('Social')">Social</td>
                                    		@else
                                    		<td class="text-start" data-label="@lang('Refferal')">Refferal</td>
                                    		@endif
                                	    </tr>
                                        <tr>
                                            <th>@lang('Link')</th>
                                            <td class="text-start">
                                                <a href="{{ empty(parse_url(urldecode($order->link), PHP_URL_SCHEME)) ? 'https://' : null }}{{ urldecode($order->link) }}"
                                                    target="_blank">{{ urldecode($order->link) }}
                                                </a>
                                            </td>
                                        </tr>
                                        @if($order->tt == 2)
                                        <tr>
                                            <th>@lang('Keywords')</th>
                                            <td class="text-start"><textarea type="text" class="form--control" readonly>{{ $order->keyword }}</textarea></td>
                                        </tr>
                                        @elseif($order->tt == 3)
                                        <tr>
                                            <th>@lang('Social')</th>
                                            <td class="text-start"><textarea type="text" class="form--control" readonly>{{ $order->social }}</textarea></td>
                                        </tr>
                                        @elseif($order->tt == 4)
                                        <tr>
                                            <th>@lang('Refferal')</th>
                                            <td class="text-start"><textarea type="text" class="form--control" readonly>{{ $order->ref }}</textarea></td>
                                        </tr>
                                        @else
                                        @endif
                                        <tr>
                                            <th>@lang('Total')</th>
                                            <td class="text-start">{{ $order->quantity }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('Daily Speed')</th>
                                            <td class="text-start">
                                                @if ($order->status == Status::ORDER_PENDING || $order->status == Status::ORDER_PROCESSING || $order->status == Status::ORDER_PAUSED || $order->status == Status::ORDER_EXPIRED)
                                                    <input class="form-control" name="speed" type="text"
                                                        value="{{ $order->speed }}" max="{{ $order->quantity }}"
                                                        required>
                                                @else
                                                    {{ $order->speed }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>@lang('Country')</th>
                                            <td class="text-start">
                                                @if ($order->status == Status::ORDER_PENDING || $order->status == Status::ORDER_PROCESSING || $order->status == Status::ORDER_PAUSED || $order->status == Status::ORDER_EXPIRED)
                                                    <select name="country" id="country" class="form--control form--control-lg"
                                                        value="{{ $order->country }}" readonly>
                                                        @include('partials.traffic')
                                                    </select>
                                                @else
                                                    {{ $order->country }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>@lang('Devices')</th>
                                            <td class="text-start">
                                                @if ($order->status == Status::ORDER_PENDING || $order->status == Status::ORDER_PROCESSING || $order->status == Status::ORDER_PAUSED || $order->status == Status::ORDER_EXPIRED)
                                                    <select class="form--control form--control-lg" id="behaviour" name="behaviour">
                                                        <option value="Mixed" {{ old('behaviour', $order->td) == 'Mixed' ? 'selected' : '' }}>
                                                            @lang('Mixed ')</option>
                                                        <option value="Desktop" {{ old('behaviour', $order->td) == 'Desktop' ? 'selected' : '' }}>
                                                            @lang('Desktop ')</option>
                                                        <option value="Mobile" {{ old('behaviour', $order->td) == 'Mobile' ? 'selected' : '' }}>
                                                            @lang('Mobile ')</option>
                                                        <option value="Random" {{ old('behaviour', $order->td) == 'Random' ? 'selected' : '' }}>
                                                            @lang('Random')</option>
                                                    </select>
                                                @else
                                                    {{ $order->td }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th data-label="@lang('Time on Page')">@lang('Session Time')</th>
                                            <td class="text-start">
                                                @if ($order->status == Status::ORDER_PENDING || $order->status == Status::ORDER_PROCESSING || $order->status == Status::ORDER_PAUSED || $order->status == Status::ORDER_EXPIRED)
                                                    <select class="form--control form--control-lg" id="timeonpage" name="timeonpage"
                                                    value="">
                                                        <option name="timeonpage" value="1" {{ $order->tp == 1 ? 'selected' : null }}>30
                                                            seconds</option>
                                                        <option name="timeonpage" value="2" {{ $order->tp == 2 ? 'selected' : null }}>1
                                                            minute</option>
                                                        <option name="timeonpage" value="3" {{ $order->tp == 3 ? 'selected' : null }}>2
                                                            minutes</option>
                                                        <option name="timeonpage" value="4" {{ $order->tp == 4 ? 'selected' : null }}>3
                                                            minutes</option>
                                                        <option name="timeonpage" value="5" {{ $order->tp == 5 ? 'selected' : null }}>4
                                                            minutes</option>
                                                        <option name="timeonpage" value="6" {{ $order->tp == 6 ? 'selected' : null }}>5
                                                            minutes</option>

                                                    </select>
                                                @else
                                                    @if($order->tp == 0)
                                                    <td class="text-start" data-label="@lang('Time on Page')">5 Seconds</td>
                                                    @elseif($order->tp == 1)
                                                    <td class="text-start" data-label="@lang('Time on Page')">30 Seconds</td>
                                                    @elseif($order->tp == 2)
                                                    <td class="text-start" data-label="@lang('Time on Page')">1 Minute</td>
                                                    @elseif($order->tp == 3)
                                                    <td class="text-start" data-label="@lang('Time on Page')">2 Minutes</td>
                                                    @elseif($order->tp == 4)
                                                    <td class="text-start" data-label="@lang('Time on Page')">3 Minutes</td>
                                                    @elseif($order->tp == 5)
                                                    <td class="text-start" data-label="@lang('Time on Page')">4 Minutes</td>
                                                    @elseif($order->tp == 6)
                                                    <td class="text-start" data-label="@lang('Time on Page')">5 Minutes</td>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>     
                                        @if($order->random_time_page == 1)
                                        <tr>
                                            <th>@lang('Random Time On Page')</th>
                                            <td class="text-start">Enabled</td>
                                        </tr>
                                        @else
                                        <tr>
                                            <th>@lang('Random Time On Page')</th>
                                            <td class="text-start">Disabled</td>
                                        </tr>
                                        @endif 
                                        <tr>
                                            <th>
                                                @lang('Start Counter')
                                            </th>
                                            <td class="text-start">
                                                @if ($order->status == Status::ORDER_PENDING || $order->status == Status::ORDER_PROCESSING || $order->status == Status::ORDER_PAUSED || $order->status == Status::ORDER_EXPIRED)
                                                    <input class="form-control" name="start_count" type="text"
                                                        value="{{ $order->start_counter }}" max="{{ $order->quantity }}"
                                                        required>
                                                @else
                                                    {{ $order->start_counter }}
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
                                            <th>@lang('Remains')</th>
                                            <td class="text-start">{{ $order->remain }}</td>
                                        </tr>
                                        @if ($order->api_order)
                                            <tr>
                                                <th>@lang('Order Placed To API')</th>
                                                <td class="text-start">
                                                    @if ($order->order_placed_to_api)
                                                        <span class="badge  badge--primary">{{ @$order->api_order_id }}</span>
                                                    @else
                                                        <span class="badge  badge--danger">@lang('No')</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>@lang('Status')</th>
                                            <td class="text-start">
                                                @if ($order->status == Status::ORDER_PENDING || $order->status == Status::ORDER_PROCESSING || $order->status == Status::ORDER_PAUSED)
                                                    <div class="form-group">
                                                        <select class="form-control select2"
                                                            data-minimum-results-for-search="-1" name="status" required>
                                                            <option value="0" @selected($order->status == Status::ORDER_PENDING)>
                                                                @lang('Pending')</option>
                                                            <option value="1" @selected($order->status == Status::ORDER_PROCESSING)>
                                                                @lang('Processing')</option>
                                                            <option value="2" @selected($order->status == Status::ORDER_COMPLETED)>
                                                                @lang('Completed')</option>
                                                            <option value="4" @selected($order->status == Status::ORDER_CANCELLED)>
                                                                @lang('Cancelled')</option>
                                                            <option value="5" @selected($order->status == Status::ORDER_EXPIRED)>
                                                                @lang('Expired')</option>
                                                            <option value="6" @selected($order->status == Status::ORDER_PAUSED)>
                                                                @lang('Paused')</option>
                                                        </select>
                                                    </div>
                                                @elseif($order->status == Status::ORDER_COMPLETED)
                                                    <span class="badge  badge--success">@lang('Completed')</span>
                                                @elseif($order->status == Status::ORDER_CANCELLED)
                                                    <span class="badge  badge--danger">@lang('Cancelled')</span>
                                                @else
                                                    <span class="badge  badge--dark">@lang('Expired')</span>
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
    <a href="{{ route('admin.orders.block', $order->id) }}" class="btn btn--primary">Block Domain</a>
    <x-back route="{{ url()->previous() }}" />
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';

            $('.select2').select2();
        })(jQuery)
    </script>
@endpush
