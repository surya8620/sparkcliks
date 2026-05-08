@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="show-filter mb-3 text-end">
                <button type="button" class="btn btn-outline--primary showFilterBtn btn-sm"><i class="las la-filter"></i>
                    @lang('Filter')</button>
            </div>
            <div class="card responsive-filter-card mb-4">
                <div class="card-body">
                    <form>
                        <div class="d-flex flex-wrap gap-4">
                            <div class="flex-grow-1">
                                <label>@lang('Search')</label>
                                <input type="search" name="search" value="{{ request()->search }}" class="form-control" placeholder="@Lang('Search by name')">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Category')</label>
                                <select name="category_id" class="form-control select2">
                                    <option value="">@lang('All')</option>
                                    @foreach ($categories ?? [] as $category)
                                        <option value="{{ $category->id }}" @selected(request()->category_id == $category->id)>
                                            {{ __($category->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Api Provider')</label>
                                <select class="form-control select2" name="api_provider_id">
                                    <option value="">@lang('All')</option>
                                    @foreach ($apiLists ?? [] as $provider)
                                        <option value="{{ $provider->id }}" @selected(request()->api_provider_id == $provider->id)>
                                            {{ __($provider->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Dripfeed')</label>
                                <select class="form-control select2" data-minimum-results-for-search="-1" name="dripfeed">
                                    <option value="" @selected(request()->dripfeed === null)>@lang('All')</option>
                                    <option value="1" @selected(request()->dripfeed == 1)>@lang('Yes')</option>
                                    <option value="0" @selected(request()->dripfeed === '0')>@lang('No')</option>
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Date')</label>
                                <input name="date" type="search"
                                    class="datepicker-here form-control bg--white pe-2 date-range"
                                    placeholder="@lang('Start Date - End Date')" autocomplete="off" value="{{ request()->date }}">
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i>
                                    @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--lg table-responsive">
                        <table class="table table--light tabstyle--two">
                            <thead>
                                <tr>
                                    <th>
                                        <label class="m-0 selectAll">
                                            <i class="th-check-all fa fa-stop"></i>
                                        </label>
                                    </th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Price Per 1k')</th>
                                    <th>@lang('Min / Max')</th>
                                    <th>@lang('API Service ID')</th>
                                    <th>@lang('Dripfeed')</th>
                                    <th>@lang('Refill')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($services as $service)
                                    <tr>
                                        <td>
                                            <input class="childCheckBox" name="checkbox_id" data-id="{{ $service->id }}"
                                                type="checkbox">
                                        </td>
                                        <td class="break_line">
                                            {{ __(@$service->name) }}
                                            @if (@$service->provider->short_name)
                                                <span class="badge badge--primary">{{ __(@$service->provider->short_name) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="break_line">{{ __(@$service->category->name) }}</td>
                                        <td>
                                            <strong>{{ showAmount(@$service->price_per_k) }}</strong>
                                            <br>
                                            @if ($service->provider)
                                                {{ showAmount(@$service->original_price) }} (@lang('Provider'))
                                            @else
                                                @lang('N/A')
                                            @endif
                                        </td>
                                        <td>{{ @$service->min }} / {{ @$service->max }}</td>
                                        <td>
                                            @if ($service->api_service_id)
                                                {{ @$service->api_service_id }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if (@$service->dripfeed == Status::YES)
                                                <span class="badge badge--success"> @lang('Yes')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('No')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (@$service->refill == Status::YES)
                                                <span class="badge badge--success"> @lang('Yes')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('No')</span>
                                            @endif
                                        </td>
                                        <td> @php echo $service->statusBadge; @endphp </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline--info" id="bulkAction"
                                                data-bs-toggle="dropdown">
                                                <i class="las la-ellipsis-v"></i>
                                                @lang('Action')
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.service.edit', $service->id) }}">
                                                    <i class="la la-pen"></i> @lang('Edit')
                                                </a>
                                                @if ($service->status == Status::DISABLE)
                                                    <button class="dropdown-item confirmationBtn"
                                                        data-action="{{ route('admin.service.status', $service->id) }}"
                                                        data-question="@lang('Are you sure to enable this service?')">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="dropdown-item confirmationBtn"
                                                        data-action="{{ route('admin.service.status', $service->id) }}"
                                                        data-question="@lang('Are you sure to disable this service?')">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif
                                            </div>
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
                @if ($services->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($services) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="bulkModal" class="modal fade">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Confirmation Alert!')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.service.bulk.action') }}" method="POST">
                    @csrf
                    <input type="hidden" name="ids">
                    <input type="hidden" name="type">
                    <div class="modal-body">
                        <p class="question"></p>
                        <div class="form-group d-none price mt-2">
                            <label>@lang('Increase Price')</label>
                            <div class="input-group mb-3">
                                <input type="number" name="price_increase" step="any" class="form-control">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark btn-sm"
                            data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn-primary btn-sm">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Import New Services')</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="la la-times" aria-hidden="true"></i>
                    </button>
                </div>
                <form method="post" action="{{ route('admin.service.import') }}" id="importForm"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="alert alert-warning p-3" role="alert">
                                <p>
                                    @lang('The file you wish to upload has to be formatted as we provided template files.Any changes to these files will be considered as an invalid file format. Download links are provided below.')
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="fw-bold required" for="file">@lang('Select File')</label>
                            <input type="file" class="form-control" name="file" accept=".txt,.csv,.xlsx" id="file">
                            <div class="mt-1">
                                <small class="d-block">
                                    @lang('Supported files:')
                                    <b class="fw-bold">@lang('csv, excel')</b>
                                </small>
                                <small>
                                    @lang('Download all of the template files from here')
                                    <a href="{{ asset('/assets/admin/file_template/sample.csv') }}" title=""
                                        class="text--primary" download="" data-bs-original-title="Download csv file"
                                        target="_blank">
                                        <b>@lang('csv'),</b>
                                    </a>
                                    <a href="{{ asset('/assets/admin/file_template/sample.xlsx') }}" title=""
                                        class="text--primary" download="" data-bs-original-title="Download excel file"
                                        target="_blank">
                                        <b>@lang('excel')</b>
                                    </a>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="Submit" class="btn btn--primary w-100 h-45">@lang('Upload')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a class="btn btn-outline--primary btn-sm" href="{{ route('admin.service.add') }}">
        <i class="las la-plus"></i>@lang('Add New')
    </a>
    <button type="button" class="btn btn-outline--info btn-sm importBtn">
        <i class="las la-cloud-upload-alt"></i> @lang('Import')
    </button>
    <button class="btn btn-sm btn-outline--info" id="actionButton" data-bs-toggle="dropdown">
        <i class="las la-ellipsis-v"></i>
        @lang('API Services')
    </button>
    <div class="dropdown-menu p-0">
        @foreach ($apiLists as $apiList)
            <a class="dropdown-item" href="{{ route('admin.service.api', $apiList->id) }}">
                <i class="las la-cloud-download-alt"></i>
                {{ __($apiList->name) }}
            </a>
        @endforeach
    </div>
    <button class="btn btn-sm btn-outline--warning" id="bulkAction" data-bs-toggle="dropdown">
        <i class="las la-ellipsis-v"></i>
        @lang('Bulk Action')
    </button>
    <div class="dropdown-menu p-0">
        <button class="dropdown-item bulkBtn" data-type="enable" data-question="@lang('Are you sure to enable all the selected services?')">
            <i class="lar la-check-square"></i>
            @lang('Enable')
        </button>
        <button class="dropdown-item bulkBtn" data-type="disable" data-question="@lang('Are you sure to disable all the selected services?')">
            <i class="lar la-times-circle"></i>
            @lang('Disable')
        </button>
        <button class="dropdown-item bulkBtn" data-type="price" data-question="@lang('Are you sure to increase price of all the selected services according to below percentage?')">
            <i class="las la-dollar-sign"></i>
            @lang('Price Update')
        </button>
    </div>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"

            $('.importBtn').on('click', function() {
                var modal = $('#importModal');
                $('#importModal').modal('show');
            });

            let ids = [];
            let checkBox = [];
            $('.selectAll').on('click', function() {
                var isChecked = $(this).find('.th-check-all').hasClass('fa-stop');
                $('.childCheckBox').prop('checked', isChecked);
                $(this).find('.th-check-all').toggleClass('fa-stop fa-check');
            });

            let bulkModal = $('#bulkModal');

            $('.bulkBtn').on('click', function() {
                checkBox = $('input:checkbox[name=checkbox_id]:checked');
                let question = $(this).data('question');
                if (checkBoxData()) {
                    bulkModal.find('[name=ids]').val(ids);
                    bulkModal.find('[name=type]').val($(this).data('type'));
                } else {
                    ids = [];
                    notify('error', `@lang('Before submitting select minimum one item.')`);
                    return false;
                }
                if ($(this).data('type') == 'price') {
                    $('.price').removeClass('d-none');
                    bulkModal.find('.question').html(`<div class="card bl--5 border--primary mb-3 modal--card">
                        <div class="card-body">
                            <p class="text--primary">
                                ${question} You have selected a total of ${checkBox.length} services for the price update.
                            </p>
                        </div>
                    </div>`);
                } else {
                    $('.price').addClass('d-none');
                    bulkModal.find('.question').text(question);
                }
                bulkModal.modal('show');
            });

            $('.childCheckBox').on('click', function() {
                if (!$('.selectAll').find('.th-check-all').hasClass('fa-stop')) {
                    $('.selectAll').find('.th-check-all').toggleClass('fa-stop fa-check');
                }
            });

            function checkBoxData() {
                checkBox = $('input:checkbox[name=checkbox_id]:checked');
                if (checkBox.length) {
                    checkBox.each(function() {
                        ids.push($(this).data('id'));
                    })
                    return true;
                } else {
                    return false;
                }
            }

            const datePicker = $('.date-range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                },
                showDropdowns: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
                        .endOf('month')
                    ],
                    'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                },
                maxDate: moment()
            });
            const changeDatePickerText = (event, startDate, endDate) => {
                $(event.target).val(startDate.format('MMMM DD, YYYY') + ' - ' + endDate.format('MMMM DD, YYYY'));
            }

            $('.date-range').on('apply.daterangepicker', (event, picker) => changeDatePickerText(event, picker
                .startDate, picker.endDate));

            if ($('.date-range').val()) {
                let dateRange = $('.date-range').val().split(' - ');
                $('.date-range').data('daterangepicker').setStartDate(new Date(dateRange[0]));
                $('.date-range').data('daterangepicker').setEndDate(new Date(dateRange[1]));
            }
        })(jQuery)
    </script>
@endpush

@push('style')
    <style>
        .modal--card {
            background: #4634ff14 !important;
        }
    </style>
@endpush
