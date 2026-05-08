@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-xl-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                        <h5 class="card-title">@lang('User Statistics')</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary active" data-user-chart-type="line" title="Line Chart">
                                    <i class="las la-chart-line"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-user-chart-type="bar" title="Bar Chart">
                                    <i class="las la-chart-bar"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-user-chart-type="pie" title="Pie Chart">
                                    <i class="las la-chart-pie"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-user-chart-type="table" title="Table View">
                                    <i class="las la-table"></i>
                                </button>
                            </div>
                            <div id="userDatePicker" class="border p-1 cursor-pointer rounded">
                                <i class="la la-calendar"></i>&nbsp;
                                <span></span> <i class="la la-caret-down"></i>
                            </div>
                        </div>
                    </div>
                    <div id="usersChartArea"></div>

                    <!-- Percentage Widgets (Shown with Charts) -->
                    <div id="percentageWidgets" class="row mt-4">
                        <div class="col-xl-4 col-lg-4 col-sm-6 mb-30">
                            <div class="card border--success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-2">@lang('Active Users')</h6>
                                            <h3 class="mb-0" id="widgetActiveCount">0</h3>
                                        </div>
                                        <div>
                                            <div class="widget-icon bg--success">
                                                <i class="las la-user-check"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-muted">@lang('Percentage')</small>
                                            <strong class="text-success" id="widgetActivePercent">0%</strong>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-success" role="progressbar" id="widgetActiveProgress" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-4 col-sm-6 mb-30">
                            <div class="card border--danger">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-2">@lang('Banned Users')</h6>
                                            <h3 class="mb-0" id="widgetBannedCount">0</h3>
                                        </div>
                                        <div>
                                            <div class="widget-icon bg--danger">
                                                <i class="las la-user-slash"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-muted">@lang('Percentage')</small>
                                            <strong class="text-danger" id="widgetBannedPercent">0%</strong>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-danger" role="progressbar" id="widgetBannedProgress" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-4 col-sm-6 mb-30">
                            <div class="card border--warning">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-2">@lang('Unverified Users')</h6>
                                            <h3 class="mb-0" id="widgetUnverifiedCount">0</h3>
                                        </div>
                                        <div>
                                            <div class="widget-icon bg--warning">
                                                <i class="las la-user-clock"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-muted">@lang('Percentage')</small>
                                            <strong class="text-warning" id="widgetUnverifiedPercent">0%</strong>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-warning" role="progressbar" id="widgetUnverifiedProgress" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table View for User Statistics -->
                    <div id="usersTableView" style="display: none;">
                        <!-- Pagination Info & Controls (Top) -->
                        <div id="tablePaginationTop" class="d-flex justify-content-between align-items-center mb-3" style="display: none !important;">
                            <div>
                                <span class="text-muted">Showing <span id="showingFrom">1</span> to <span id="showingTo">10</span> of <span id="totalRows">0</span> entries</span>
                            </div>
                            <div>
                                <select id="rowsPerPage" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                                    <option value="10">10 rows</option>
                                    <option value="25">25 rows</option>
                                    <option value="50">50 rows</option>
                                    <option value="100">100 rows</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive" id="tableContainer">
                            <table class="table table-striped table-bordered">
                                <thead class="table-light sticky-header">
                                    <tr>
                                        <th class="text-center">@lang('Date')</th>
                                        <th class="text-center">@lang('Active Users')</th>
                                        <th class="text-center">@lang('Banned Users')</th>
                                        <th class="text-center">@lang('Unverified Users')</th>
                                        <th class="text-center">@lang('Total')</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody">
                                    <!-- Will be populated via AJAX -->
                                </tbody>
                                <tfoot class="table-light">
                                    <tr class="fw-bold">
                                        <td class="text-center">@lang('Grand Total')</td>
                                        <td class="text-center">
                                            <span id="totalActive">0</span>
                                            <br>
                                            <small class="text-success" id="activePercentage">(0%)</small>
                                        </td>
                                        <td class="text-center">
                                            <span id="totalBanned">0</span>
                                            <br>
                                            <small class="text-danger" id="bannedPercentage">(0%)</small>
                                        </td>
                                        <td class="text-center">
                                            <span id="totalUnverified">0</span>
                                            <br>
                                            <small class="text-warning" id="unverifiedPercentage">(0%)</small>
                                        </td>
                                        <td class="text-center" id="grandTotal">0</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Pagination Controls (Bottom) -->
                        <div id="tablePaginationBottom" class="d-flex justify-content-center mt-3" style="display: none !important;">
                            <nav>
                                <ul class="pagination pagination-sm" id="paginationControls">
                                    <!-- Will be generated dynamically -->
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Users by Country Section --}}
        <div class="col-xl-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">@lang('Users by Country')</h5>
                        <div class="d-flex gap-2 flex-wrap align-items-center">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary active" data-country-chart-type="line" title="Line Chart">
                                    <i class="las la-chart-line"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-country-chart-type="bar" title="Bar Chart">
                                    <i class="las la-chart-bar"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-country-chart-type="pie" title="Percentage Area Chart">
                                    <i class="las la-chart-area"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-country-chart-type="table" title="Table View">
                                    <i class="las la-table"></i>
                                </button>
                            </div>
                            <div style="position: relative;">
                                <select id="countryLimitFilter" class="form-select form-select-sm" style="width: auto; min-width: 200px;">
                                    <option value="5">Top 5 Countries</option>
                                    <option value="10" selected>Top 10 Countries</option>
                                    <option value="20">Top 20 Countries</option>
                                    <option value="0">All Countries</option>
                                    <option value="custom">Select Specific Countries</option>
                                </select>
                            </div>
                            <div id="countryDatePicker" class="border p-1 cursor-pointer rounded">
                                <i class="la la-calendar"></i>&nbsp;
                                <span></span> <i class="la la-caret-down"></i>
                            </div>
                        </div>
                    </div>

                    <div id="countryChartArea"></div>

                    <!-- Table View for Country Statistics -->
                    <div id="countryTableView" style="display: none;">
                        <!-- Pagination Info & Controls (Top) -->
                        <div id="countryTablePaginationTop" class="d-flex justify-content-between align-items-center mb-3" style="display: none !important;">
                            <div>
                                <span class="text-muted">Showing <span id="countryShowingFrom">1</span> to <span id="countryShowingTo">10</span> of <span id="countryTotalRows">0</span> entries</span>
                            </div>
                            <div>
                                <select id="countryRowsPerPage" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                                    <option value="10">10 rows</option>
                                    <option value="25">25 rows</option>
                                    <option value="50">50 rows</option>
                                    <option value="100">100 rows</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive" id="countryTableContainer">
                            <table class="table table-striped table-bordered">
                                <thead class="table-light sticky-header">
                                    <tr>
                                        <th class="text-center">@lang('Date')</th>
                                        <!-- Country columns will be dynamically generated -->
                                    </tr>
                                </thead>
                                <tbody id="countryTableBody">
                                    <!-- Will be populated via AJAX -->
                                </tbody>
                                <tfoot class="table-light">
                                    <tr class="fw-bold">
                                        <td class="text-center">@lang('Total')</td>
                                        <!-- Country totals will be dynamically generated -->
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Pagination Controls (Bottom) -->
                        <div id="countryTablePaginationBottom" class="d-flex justify-content-center mt-3" style="display: none !important;">
                            <nav>
                                <ul class="pagination pagination-sm" id="countryPaginationControls">
                                    <!-- Will be generated dynamically -->
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <h5 class="mb-4">@lang('Banned User Accounts List')</h5>
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--lg table-responsive">
                        <table class="table table--light tabstyle--two">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Mobile')</th>
                                    <th>@lang('Country')</th>
                                    <th>@lang('Joined At')</th>
                                    <th>@lang('Banned At')</th>
                                    <th>@lang('Ban Reason')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bannedUsers as $user)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $user->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a href="{{ route('admin.users.detail', $user->id) }}"><span>@</span>{{ $user->username }}</a>
                                            </span>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->mobileNumber }}</td>
                                        <td>
                                            {{ ($user->country) ?? 'N/A' }}
                                        </td>
                                        <td>
                                            {{ showDateTime($user->created_at) }}
                                            <br>
                                            {{ diffForHumans($user->created_at) }}
                                        </td>
                                        <td>
                                            {{ showDateTime($user->updated_at) }}
                                            <br>
                                            {{ diffForHumans($user->updated_at) }}
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--primary viewReasonBtn"
                                                data-reason="{{ $user->ban_reason }}">
                                                <i class="las la-eye"></i> @lang('View')
                                            </button>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.users.detail', $user->id) }}" 
                                                class="btn btn-sm btn-outline--primary">
                                                <i class="las la-user"></i> @lang('Details')
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">@lang('No banned users found')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($bannedUsers->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($bannedUsers) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Ban Reason Modal --}}
    <div class="modal fade" id="reasonModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Ban Reason')</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="reason-text"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="close btn btn--dark w-100 h-45" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> @lang('Close')
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Country Selector Modal --}}
    <div class="modal fade" id="countrySelectorModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="las la-globe"></i> @lang('Select Countries')
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <i class="las la-info-circle"></i> 
                        <small>Hold <kbd>Ctrl</kbd> (or <kbd>Cmd</kbd> on Mac) to select multiple countries</small>
                    </div>
                    <select id="specificCountries" class="form-select" multiple size="12">
                        <option disabled>Loading countries...</option>
                    </select>
                    <div class="mt-2">
                        <small id="countrySelectionCount" class="text-primary fw-bold"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="las la-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="clearCountryFilter">
                        <i class="las la-redo"></i> Clear Selection
                    </button>
                    <button type="button" class="btn btn-primary" id="applyCountryFilter">
                        <i class="las la-check"></i> Apply Selection
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('style')
<style>
    /* Chart type toggle buttons */
    .btn-group button[data-user-chart-type] {
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .btn-group button[data-user-chart-type].active {
        background-color: #7367f0;
        color: #fff;
        border-color: #7367f0;
    }
    
    .btn-group button[data-user-chart-type]:hover {
        background-color: #5e50ee;
        color: #fff;
        border-color: #5e50ee;
    }
    
    .btn-group button[data-user-chart-type] i {
        font-size: 1.1rem;
    }

    /* Table styling */
    #usersTableView {
        transition: all 0.3s ease;
    }

    /* Sticky table header for scrollable tables */
    #tableContainer {
        max-height: 600px;
        overflow-y: auto;
        position: relative;
    }

    #tableContainer.scrollable {
        max-height: 500px;
    }

    .sticky-header th {
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        z-index: 10;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
    }

    /* Pagination styles */
    .pagination-sm .page-link {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        min-width: 32px;
        text-align: center;
    }

    .pagination {
        display: flex;
        flex-wrap: nowrap;
        justify-content: center;
        align-items: center;
        margin: 0;
    }

    .pagination .page-item {
        margin: 0 2px;
    }

    .pagination .page-item.active .page-link {
        background-color: #7367f0;
        border-color: #7367f0;
    }

    .pagination .page-link {
        color: #7367f0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pagination .page-link:hover {
        background-color: #f0f0f0;
        color: #5e50ee;
    }

    .pagination .page-link i {
        font-size: 1rem;
        line-height: 1;
    }

    #tablePaginationBottom {
        display: flex !important;
        justify-content: center;
        align-items: center;
    }

    #tablePaginationBottom nav {
        margin: 0 auto;
    }

    /* Widget Cards */
    .widget-icon {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: #fff;
    }

    .border--success {
        border-left: 4px solid #28a745 !important;
    }

    .border--danger {
        border-left: 4px solid #dc3545 !important;
    }

    .border--warning {
        border-left: 4px solid #ffc107 !important;
    }

    .bg--success {
        background-color: #28a745;
    }

    .bg--danger {
        background-color: #dc3545;
    }

    .bg--warning {
        background-color: #ffc107;
    }

    .progress {
        background-color: #e9ecef;
        border-radius: 4px;
    }

    /* Country Multi-Select Modal Styling */
    #specificCountries {
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 8px;
        width: 100%;
        height: 300px;
        overflow-y: auto;
    }

    #specificCountries option {
        padding: 10px 12px;
        cursor: pointer;
        border-radius: 4px;
        margin-bottom: 3px;
        transition: all 0.2s ease;
    }

    #specificCountries option:hover {
        background-color: #f0f0f0;
    }

    #specificCountries option:checked {
        background: linear-gradient(135deg, #7367f0 0%, #9e95f5 100%);
        color: white;
        font-weight: 500;
    }

    #countrySelectionCount {
        font-weight: 600;
        display: block;
    }

    /* Chart Areas - Ensure proper dimensions */
    #countryChartArea,
    #usersChartArea {
        min-height: 450px;
        width: 100%;
        margin-top: 20px;
    }

    /* Sticky column for country table (first column) */
    .sticky-column {
        position: sticky;
        left: 0;
        z-index: 5;
        background-color: #f8f9fa !important;
    }

    /* Make country table scrollable horizontally */
    #countryTableContainer {
        overflow-x: auto;
    }

    #countryTableContainer thead th.sticky-column,
    #countryTableContainer tfoot td.sticky-column {
        z-index: 15;
    }
</style>
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';

            // Date range picker configuration
            const start = moment().subtract(6, 'days');
            const end = moment();

            const dateRangeOptions = {
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                    'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
                    'All Time': [moment('2020-01-01'), moment()]
                },
                maxDate: moment()
            };

            const changeDatePickerText = (element, startDate, endDate) => {
                $(element).html(startDate.format('MMMM D, YYYY') + ' - ' + endDate.format('MMMM D, YYYY'));
            };

            // Store chart data globally
            let currentChartData = {
                categories: [],
                active: [],
                banned: [],
                unverified: []
            };

            let currentChartType = 'line'; // Default chart type
            let userChart = null;

            // Initialize User Statistics Chart
            function initializeChart(type = 'line') {
                if (userChart) {
                    userChart.destroy();
                }

                if (type === 'line' || type === 'bar') {
                    let chartConfig = {
                        chart: { 
                            type: type === 'line' ? 'spline' : 'column',
                            height: 450
                        },
                        title: { text: null },
                        xAxis: { 
                            categories: currentChartData.categories,
                            crosshair: true
                        },
                        yAxis: { 
                            title: { text: 'Users' },
                            min: 0
                        },
                        tooltip: {
                            shared: true,
                            valueSuffix: ' users'
                        },
                        plotOptions: {},
                        series: [
                            { name: 'Active', data: currentChartData.active, color: '#28a745' },
                            { name: 'Banned', data: currentChartData.banned, color: '#dc3545' },
                            { name: 'Unverified', data: currentChartData.unverified, color: '#ffc107' }
                        ],
                        credits: { enabled: false },
                        exporting: {
                            enabled: true,
                            buttons: {
                                contextButton: {
                                    menuItems: ['downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadSVG']
                                }
                            }
                        }
                    };

                    if (type === 'line') {
                        chartConfig.plotOptions.spline = {
                            marker: { enabled: false }
                        };
                    } else {
                        chartConfig.plotOptions.column = {
                            pointPadding: 0.2,
                            borderWidth: 0,
                            borderRadius: 5
                        };
                    }

                    userChart = Highcharts.chart('usersChartArea', chartConfig);
                } else if (type === 'pie') {
                    // Calculate totals for pie chart
                    let activeTotal = currentChartData.active.reduce((a, b) => a + b, 0);
                    let bannedTotal = currentChartData.banned.reduce((a, b) => a + b, 0);
                    let unverifiedTotal = currentChartData.unverified.reduce((a, b) => a + b, 0);

                    userChart = Highcharts.chart('usersChartArea', {
                        chart: { 
                            type: 'pie',
                            height: 450
                        },
                        title: { text: null },
                        tooltip: {
                            pointFormat: '<b>{point.y} users</b><br/>({point.percentage:.1f}%)'
                        },
                        plotOptions: {
                            pie: {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                dataLabels: {
                                    enabled: true,
                                    format: '<b>{point.name}</b>: {point.percentage:.1f}%'
                                },
                                showInLegend: true
                            }
                        },
                        series: [{
                            name: 'Users',
                            colorByPoint: true,
                            data: [
                                { name: 'Active', y: activeTotal, color: '#28a745' },
                                { name: 'Banned', y: bannedTotal, color: '#dc3545' },
                                { name: 'Unverified', y: unverifiedTotal, color: '#ffc107' }
                            ]
                        }],
                        credits: { enabled: false },
                        exporting: {
                            enabled: true,
                            buttons: {
                                contextButton: {
                                    menuItems: ['downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadSVG']
                                }
                            }
                        }
                    });
                }
            }

            // Table pagination variables
            let currentPage = 1;
            let rowsPerPage = 10;
            let allTableData = [];

            // Populate table view with pagination
            function populateTable() {
                let totalActive = 0;
                let totalBanned = 0;
                let totalUnverified = 0;

                // Build complete dataset
                allTableData = [];
                currentChartData.categories.forEach((date, index) => {
                    let active = currentChartData.active[index];
                    let banned = currentChartData.banned[index];
                    let unverified = currentChartData.unverified[index];
                    let total = active + banned + unverified;

                    totalActive += active;
                    totalBanned += banned;
                    totalUnverified += unverified;

                    allTableData.push({
                        date: date,
                        active: active,
                        banned: banned,
                        unverified: unverified,
                        total: total
                    });
                });

                // Calculate grand total and percentages
                let grandTotal = totalActive + totalBanned + totalUnverified;
                let activePercent = grandTotal > 0 ? ((totalActive / grandTotal) * 100).toFixed(1) : 0;
                let bannedPercent = grandTotal > 0 ? ((totalBanned / grandTotal) * 100).toFixed(1) : 0;
                let unverifiedPercent = grandTotal > 0 ? ((totalUnverified / grandTotal) * 100).toFixed(1) : 0;

                // Update grand totals with percentages
                $('#totalActive').text(totalActive);
                $('#totalBanned').text(totalBanned);
                $('#totalUnverified').text(totalUnverified);
                $('#grandTotal').text(grandTotal);
                
                $('#activePercentage').text('(' + activePercent + '%)');
                $('#bannedPercentage').text('(' + bannedPercent + '%)');
                $('#unverifiedPercentage').text('(' + unverifiedPercent + '%)');

                // Determine if pagination is needed
                let totalRows = allTableData.length;
                
                if (totalRows <= 10) {
                    // No pagination needed - show all data
                    $('#tablePaginationTop').hide();
                    $('#tablePaginationBottom').hide();
                    $('#tableContainer').removeClass('scrollable');
                    renderTableRows(allTableData);
                } else {
                    // Show pagination
                    $('#tablePaginationTop').show();
                    $('#tablePaginationBottom').show();
                    
                    // Add scrollable class for large datasets
                    if (totalRows > 50) {
                        $('#tableContainer').addClass('scrollable');
                    } else {
                        $('#tableContainer').removeClass('scrollable');
                    }
                    
                    currentPage = 1;
                    renderPaginatedTable();
                }
            }

            // Render table rows (all or subset)
            function renderTableRows(data) {
                let tableHtml = '';
                data.forEach(row => {
                    // Calculate percentages for this row
                    let activePercent = row.total > 0 ? ((row.active / row.total) * 100).toFixed(1) : 0;
                    let bannedPercent = row.total > 0 ? ((row.banned / row.total) * 100).toFixed(1) : 0;
                    let unverifiedPercent = row.total > 0 ? ((row.unverified / row.total) * 100).toFixed(1) : 0;

                    tableHtml += `
                        <tr>
                            <td class="text-center">${row.date}</td>
                            <td class="text-center">
                                ${row.active}
                                <br>
                                <small class="text-success">(${activePercent}%)</small>
                            </td>
                            <td class="text-center">
                                ${row.banned}
                                <br>
                                <small class="text-danger">(${bannedPercent}%)</small>
                            </td>
                            <td class="text-center">
                                ${row.unverified}
                                <br>
                                <small class="text-warning">(${unverifiedPercent}%)</small>
                            </td>
                            <td class="text-center">${row.total}</td>
                        </tr>
                    `;
                });
                $('#usersTableBody').html(tableHtml);
            }

            // Render paginated table
            function renderPaginatedTable() {
                let totalRows = allTableData.length;
                let totalPages = Math.ceil(totalRows / rowsPerPage);
                let startIndex = (currentPage - 1) * rowsPerPage;
                let endIndex = Math.min(startIndex + rowsPerPage, totalRows);
                
                // Get current page data
                let pageData = allTableData.slice(startIndex, endIndex);
                
                // Render rows
                renderTableRows(pageData);
                
                // Update pagination info
                $('#showingFrom').text(startIndex + 1);
                $('#showingTo').text(endIndex);
                $('#totalRows').text(totalRows);
                
                // Generate pagination controls
                generatePaginationControls(totalPages);
            }

            // Generate pagination controls
            function generatePaginationControls(totalPages) {
                let paginationHtml = '';
                
                // Previous button with icon
                paginationHtml += `
                    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${currentPage - 1}" tabindex="-1" aria-label="Previous">
                            <i class="las la-angle-left"></i>
                        </a>
                    </li>
                `;
                
                // Page numbers (show max 5 pages at a time)
                let startPage = Math.max(1, currentPage - 2);
                let endPage = Math.min(totalPages, currentPage + 2);
                
                if (startPage > 1) {
                    paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
                    if (startPage > 2) {
                        paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    }
                }
                
                for (let i = startPage; i <= endPage; i++) {
                    paginationHtml += `
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>
                    `;
                }
                
                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) {
                        paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    }
                    paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
                }
                
                // Next button with icon
                paginationHtml += `
                    <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Next">
                            <i class="las la-angle-right"></i>
                        </a>
                    </li>
                `;
                
                $('#paginationControls').html(paginationHtml);
            }

            // Pagination click handler
            $(document).on('click', '#paginationControls .page-link', function(e) {
                e.preventDefault();
                let page = parseInt($(this).data('page'));
                if (page && page !== currentPage) {
                    currentPage = page;
                    renderPaginatedTable();
                    // Scroll to top of table
                    $('#usersTableView').get(0).scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });

            // Rows per page change handler
            $('#rowsPerPage').on('change', function() {
                rowsPerPage = parseInt($(this).val());
                currentPage = 1;
                renderPaginatedTable();
            });

            const usersChart = (startDate, endDate) => {
                const data = {
                    start_date: startDate.format('YYYY-MM-DD'),
                    end_date: endDate.format('YYYY-MM-DD')
                };
                const url = @json(route('admin.order.report.report.statistics'));

                $.get(url, data, function(response, status) {
                    if (status === 'success') {
                        // Store data globally
                        currentChartData.categories = response.created_on;
                        currentChartData.active = response.data[0].data;
                        currentChartData.banned = response.data[1].data;
                        currentChartData.unverified = response.data[2].data;

                        // Update percentage widgets
                        updatePercentageWidgets();

                        // Render based on current chart type
                        if (currentChartType === 'table') {
                            populateTable();
                        } else {
                            initializeChart(currentChartType);
                        }
                    }
                });
            };

            // Update percentage widgets
            function updatePercentageWidgets() {
                // Calculate totals
                let totalActive = currentChartData.active.reduce((a, b) => a + b, 0);
                let totalBanned = currentChartData.banned.reduce((a, b) => a + b, 0);
                let totalUnverified = currentChartData.unverified.reduce((a, b) => a + b, 0);
                let grandTotal = totalActive + totalBanned + totalUnverified;

                // Calculate percentages
                let activePercent = grandTotal > 0 ? ((totalActive / grandTotal) * 100).toFixed(1) : 0;
                let bannedPercent = grandTotal > 0 ? ((totalBanned / grandTotal) * 100).toFixed(1) : 0;
                let unverifiedPercent = grandTotal > 0 ? ((totalUnverified / grandTotal) * 100).toFixed(1) : 0;

                // Update Active widget
                $('#widgetActiveCount').text(totalActive.toLocaleString());
                $('#widgetActivePercent').text(activePercent + '%');
                $('#widgetActiveProgress').css('width', activePercent + '%').attr('aria-valuenow', activePercent);

                // Update Banned widget
                $('#widgetBannedCount').text(totalBanned.toLocaleString());
                $('#widgetBannedPercent').text(bannedPercent + '%');
                $('#widgetBannedProgress').css('width', bannedPercent + '%').attr('aria-valuenow', bannedPercent);

                // Update Unverified widget
                $('#widgetUnverifiedCount').text(totalUnverified.toLocaleString());
                $('#widgetUnverifiedPercent').text(unverifiedPercent + '%');
                $('#widgetUnverifiedProgress').css('width', unverifiedPercent + '%').attr('aria-valuenow', unverifiedPercent);
            }

            // Chart type toggle buttons handler
            $('button[data-user-chart-type]').on('click', function() {
                let chartType = $(this).data('user-chart-type');
                currentChartType = chartType;

                // Update active button
                $('button[data-user-chart-type]').removeClass('active');
                $(this).addClass('active');

                // Toggle visibility
                if (chartType === 'table') {
                    $('#usersChartArea').hide();
                    $('#percentageWidgets').hide();
                    $('#usersTableView').show();
                    populateTable();
                } else {
                    $('#usersChartArea').show();
                    $('#percentageWidgets').show();
                    $('#usersTableView').hide();
                    initializeChart(chartType);
                }
            });

            // Initialize date range picker
            $('#userDatePicker').daterangepicker(dateRangeOptions, (start, end) => changeDatePickerText('#userDatePicker span', start, end));

            // Set initial date range text and load data
            changeDatePickerText('#userDatePicker span', start, end);
            usersChart(start, end);

            // Update on date range change
            $('#userDatePicker').on('apply.daterangepicker', (event, picker) => usersChart(picker.startDate, picker.endDate));

            // Ban reason modal handler
            $('.viewReasonBtn').on('click', function() {
                let modal = $('#reasonModal');
                let reason = $(this).data('reason');
                modal.find('.reason-text').text(reason || 'No reason provided');
                modal.modal('show');
            });

            // ========== USERS BY COUNTRY SECTION ==========
            
            // Store country chart data globally
            let currentCountryData = {
                dates: [],
                series: [] // Array of {name: 'Country', data: [...]}
            };

            let currentCountryChartType = 'line';
            let countryChart = null;
            let selectedCustomCountries = []; // Store selected countries from modal

            // Country table pagination variables
            let countryTableData = [];
            let countryCurrentPage = 1;
            let countryRowsPerPage = 10;

            // Generate dynamic colors for countries
            function getCountryColor(index) {
                const colors = [
                    '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6f42c1',
                    '#fd7e14', '#20c997', '#e83e8c', '#6610f2', '#007bff'
                ];
                return colors[index % colors.length];
            }

            // Initialize Country Chart
            function initializeCountryChart(type = 'line') {
                console.log('Initializing country chart with type:', type);
                console.log('Current country data:', currentCountryData);
                
                if (countryChart) {
                    countryChart.destroy();
                }

                // Validate data
                if (!currentCountryData.dates || currentCountryData.dates.length === 0) {
                    console.error('No dates available for chart');
                    $('#countryChartArea').html('<div class="alert alert-warning text-center">No date data available</div>');
                    return;
                }

                if (!currentCountryData.series || currentCountryData.series.length === 0) {
                    console.error('No series data available for chart');
                    $('#countryChartArea').html('<div class="alert alert-warning text-center">No country data available</div>');
                    return;
                }

                // Add colors to series
                let coloredSeries = currentCountryData.series.map((series, index) => ({
                    ...series,
                    color: getCountryColor(index)
                }));

                console.log('Colored series:', coloredSeries);

                try {
                    if (type === 'line' || type === 'bar') {
                        let chartConfig = {
                            chart: { 
                                type: type === 'line' ? 'spline' : 'column',
                                height: 450
                            },
                            title: { text: null },
                            xAxis: { 
                                categories: currentCountryData.dates,
                                crosshair: true
                            },
                            yAxis: { 
                                title: { text: 'Number of Users' },
                                min: 0
                            },
                            tooltip: {
                                shared: true,
                                valueSuffix: ' users'
                            },
                            plotOptions: {},
                            series: coloredSeries,
                            credits: { enabled: false },
                            legend: {
                                enabled: true,
                                align: 'center',
                                verticalAlign: 'bottom'
                            },
                            exporting: {
                                enabled: true,
                                buttons: {
                                    contextButton: {
                                        menuItems: ['downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadSVG']
                                    }
                                }
                            }
                        };

                        if (type === 'line') {
                            chartConfig.plotOptions.spline = {
                                marker: { enabled: false }
                            };
                        } else {
                            chartConfig.plotOptions.column = {
                                pointPadding: 0.2,
                                borderWidth: 0,
                                borderRadius: 5
                            };
                        }

                        countryChart = Highcharts.chart('countryChartArea', chartConfig);
                    } else if (type === 'pie') {
                        // Percentage Area Chart (Stacked Area with 100% stacking)
                        countryChart = Highcharts.chart('countryChartArea', {
                            chart: { 
                                type: 'area',
                                height: 450
                            },
                            title: { text: null },
                            xAxis: { 
                                categories: currentCountryData.dates,
                                tickmarkPlacement: 'on',
                                title: { enabled: false }
                            },
                            yAxis: { 
                                title: { text: 'Percentage (%)' },
                                labels: {
                                    format: '{value}%'
                                },
                                min: 0,
                                max: 100
                            },
                            tooltip: {
                                shared: true,
                                formatter: function() {
                                    let tooltipText = '<b>' + this.x + '</b><br/>';
                                    
                                    this.points.forEach(point => {
                                        // Get actual count from original data
                                        let dateIndex = currentCountryData.dates.indexOf(this.x);
                                        let seriesData = currentCountryData.series.find(s => s.name === point.series.name);
                                        let actualCount = seriesData ? seriesData.data[dateIndex] : 0;
                                        
                                        tooltipText += '<span style="color:' + point.color + '">\u25CF</span> ' + 
                                                      point.series.name + ': <b>' + point.y.toFixed(1) + '%</b> (' + actualCount + ' users)<br/>';
                                    });
                                    
                                    return tooltipText;
                                }
                            },
                            plotOptions: {
                                area: {
                                    stacking: 'percent',
                                    lineColor: '#ffffff',
                                    lineWidth: 1,
                                    marker: {
                                        enabled: false,
                                        symbol: 'circle',
                                        radius: 2,
                                        states: {
                                            hover: {
                                                enabled: true
                                            }
                                        }
                                    },
                                    fillOpacity: 0.7
                                }
                            },
                            series: coloredSeries,
                            credits: { enabled: false },
                            legend: {
                                enabled: true,
                                align: 'center',
                                verticalAlign: 'bottom'
                            },
                            exporting: {
                                enabled: true,
                                buttons: {
                                    contextButton: {
                                        menuItems: ['downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadSVG']
                                    }
                                }
                            }
                        });
                    }
                } catch (error) {
                    console.error('Error creating chart:', error);
                    $('#countryChartArea').html('<div class="alert alert-danger text-center">Error creating chart: ' + error.message + '</div>');
                }
            }

            // Populate Country Table
            function populateCountryTable() {
                // Build country data structure with totals for sorting
                let countries = currentCountryData.series.map(s => s.name);
                let dates = currentCountryData.dates;

                // Calculate country totals for sorting
                countryTableData = [];
                countries.forEach(country => {
                    let series = currentCountryData.series.find(s => s.name === country);
                    let countryTotal = 0;
                    let countryDataByDate = {};
                    
                    dates.forEach((date, index) => {
                        let value = series ? series.data[index] : 0;
                        countryDataByDate[date] = value;
                        countryTotal += value;
                    });
                    
                    countryTableData.push({
                        country: country,
                        dataByDate: countryDataByDate,
                        total: countryTotal
                    });
                });

                // Sort countries by total in descending order (highest to lowest)
                countryTableData.sort((a, b) => b.total - a.total);
                
                renderCountryPaginatedTable();
            }

            // Render Country Paginated Table
            function renderCountryPaginatedTable() {
                const totalCountries = countryTableData.length;
                const dates = currentCountryData.dates;
                
                // Determine pagination display based on number of countries (rows)
                if (totalCountries <= 10) {
                    // No pagination needed
                    $('#countryTablePaginationTop').hide();
                    $('#countryTablePaginationBottom').hide();
                    $('#countryTableContainer').css('max-height', 'none').css('overflow-y', 'visible');
                } else if (totalCountries <= 50) {
                    // Show pagination
                    $('#countryTablePaginationTop').show();
                    $('#countryTablePaginationBottom').show();
                    $('#countryTableContainer').css('max-height', 'none').css('overflow-y', 'visible');
                } else {
                    // Make scrollable
                    $('#countryTablePaginationTop').hide();
                    $('#countryTablePaginationBottom').hide();
                    $('#countryTableContainer').css('max-height', '500px').css('overflow-y', 'auto');
                }

                if (totalCountries <= 10) {
                    // Show all rows without pagination
                    renderCountryTableRows(countryTableData, dates);
                } else if (totalCountries <= 50) {
                    // Paginate countries (rows)
                    const startIndex = (countryCurrentPage - 1) * countryRowsPerPage;
                    const endIndex = Math.min(startIndex + countryRowsPerPage, totalCountries);
                    const paginatedCountries = countryTableData.slice(startIndex, endIndex);
                    
                    renderCountryTableRows(paginatedCountries, dates);
                    renderCountryPagination(totalCountries, startIndex, endIndex);
                } else {
                    // Show all rows in scrollable container
                    renderCountryTableRows(countryTableData, dates);
                }
            }

            // Render Country Table Rows (Transposed: Countries as rows, Dates as columns)
            function renderCountryTableRows(countriesData, dates) {
                // Build table header with dates
                let headerHtml = '<tr><th class="text-center sticky-column">Country</th>';
                dates.forEach(date => {
                    headerHtml += `<th class="text-center">${date}</th>`;
                });
                headerHtml += '<th class="text-center fw-bold">Total</th></tr>';
                $('#countryTableContainer thead').html(headerHtml);

                // Build table body with countries as rows
                let tbody = $('#countryTableBody');
                tbody.empty();

                let dateTotals = {};
                dates.forEach(date => dateTotals[date] = 0);
                let grandTotal = 0;

                countriesData.forEach(countryRow => {
                    let rowHtml = `<tr><td class="text-center fw-bold sticky-column" style="background-color: #f8f9fa;">${countryRow.country}</td>`;
                    
                    dates.forEach(date => {
                        let count = countryRow.dataByDate[date] || 0;
                        dateTotals[date] += count;
                        grandTotal += count;
                        rowHtml += `<td class="text-center">${count.toLocaleString()}</td>`;
                    });
                    
                    rowHtml += `<td class="text-center fw-bold">${countryRow.total.toLocaleString()}</td></tr>`;
                    tbody.append(rowHtml);
                });

                // Build table footer with date totals
                let footerHtml = '<tr class="fw-bold"><td class="text-center sticky-column" style="background-color: #f8f9fa;">Total</td>';
                dates.forEach(date => {
                    footerHtml += `<td class="text-center">${dateTotals[date].toLocaleString()}</td>`;
                });
                footerHtml += `<td class="text-center">${grandTotal.toLocaleString()}</td></tr>`;
                $('#countryTableContainer tfoot').html(footerHtml);
            }

            // Render Country Pagination
            function renderCountryPagination(totalRows, startIndex, endIndex) {
                // Update info text
                $('#countryShowingFrom').text(startIndex + 1);
                $('#countryShowingTo').text(endIndex);
                $('#countryTotalRows').text(totalRows);

                // Generate pagination controls
                const totalPages = Math.ceil(totalRows / countryRowsPerPage);
                let paginationHtml = '';

                // Previous button
                paginationHtml += `
                    <li class="page-item ${countryCurrentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-country-page="${countryCurrentPage - 1}">
                            <i class="las la-angle-left"></i>
                        </a>
                    </li>
                `;

                // Page numbers (show first, last, and pages around current)
                for (let i = 1; i <= totalPages; i++) {
                    if (i === 1 || i === totalPages || (i >= countryCurrentPage - 1 && i <= countryCurrentPage + 1)) {
                        paginationHtml += `
                            <li class="page-item ${i === countryCurrentPage ? 'active' : ''}">
                                <a class="page-link" href="#" data-country-page="${i}">${i}</a>
                            </li>
                        `;
                    } else if (i === countryCurrentPage - 2 || i === countryCurrentPage + 2) {
                        paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    }
                }

                // Next button
                paginationHtml += `
                    <li class="page-item ${countryCurrentPage === totalPages ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-country-page="${countryCurrentPage + 1}">
                            <i class="las la-angle-right"></i>
                        </a>
                    </li>
                `;

                $('#countryPaginationControls').html(paginationHtml);
            }

            // Country Pagination click handler
            $(document).on('click', '#countryPaginationControls a', function(e) {
                e.preventDefault();
                if (!$(this).parent().hasClass('disabled') && !$(this).parent().hasClass('active')) {
                    countryCurrentPage = parseInt($(this).data('country-page'));
                    renderCountryPaginatedTable();
                    $('#countryTableView').get(0).scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });

            // Country rows per page change handler
            $('#countryRowsPerPage').on('change', function() {
                countryRowsPerPage = parseInt($(this).val());
                countryCurrentPage = 1;
                renderCountryPaginatedTable();
            });

            // Fetch Country Chart Data
            const countriesChart = (startDate, endDate) => {
                let data = {
                    start_date: startDate.format('YYYY-MM-DD'),
                    end_date: endDate.format('YYYY-MM-DD')
                };
                
                // Check if custom countries are selected
                if (selectedCustomCountries.length > 0) {
                    data.countries = selectedCustomCountries;
                } else {
                    data.limit = $('#countryLimitFilter').val() || 10;
                }
                
                const url = @json(route('admin.order.report.country.statistics'));

                // Show loading indicator
                $('#countryChartArea').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading chart data...</p></div>');

                $.get(url, data, function(response, status) {
                    if (status === 'success') {
                        console.log('Country Chart Data:', response); // Debug log
                        
                        // Store data globally
                        currentCountryData.dates = response.dates || [];
                        currentCountryData.series = response.data || [];

                        // Check if we have data
                        if (currentCountryData.dates.length === 0 || currentCountryData.series.length === 0) {
                            $('#countryChartArea').html('<div class="alert alert-info text-center">No data available for the selected date range.</div>');
                            return;
                        }

                        // Render based on current chart type
                        if (currentCountryChartType === 'table') {
                            $('#countryChartArea').html('');
                            populateCountryTable();
                        } else {
                            initializeCountryChart(currentCountryChartType);
                        }
                    }
                }).fail(function(xhr, status, error) {
                    console.error('Country Chart Error:', error, xhr.responseText);
                    $('#countryChartArea').html('<div class="alert alert-danger text-center">Failed to load chart data. Please try again.</div>');
                });
            };

            // Country Chart type toggle buttons handler
            $('button[data-country-chart-type]').on('click', function() {
                let chartType = $(this).data('country-chart-type');
                currentCountryChartType = chartType;

                // Update active button
                $('button[data-country-chart-type]').removeClass('active');
                $(this).addClass('active');

                // Toggle visibility
                if (chartType === 'table') {
                    $('#countryChartArea').hide();
                    $('#countryTableView').show();
                    populateCountryTable();
                } else {
                    $('#countryChartArea').show();
                    $('#countryTableView').hide();
                    initializeCountryChart(chartType);
                }
            });

            // Initialize country date range picker
            $('#countryDatePicker').daterangepicker(dateRangeOptions, (start, end) => changeDatePickerText('#countryDatePicker span', start, end));

            // Set initial date range text and load country data
            changeDatePickerText('#countryDatePicker span', start, end);
            countriesChart(start, end);

            // Update on country date range change
            $('#countryDatePicker').on('apply.daterangepicker', (event, picker) => countriesChart(picker.startDate, picker.endDate));

            // Country limit filter change handler
            $('#countryLimitFilter').on('change', function() {
                let selectedValue = $(this).val();
                
                if (selectedValue === 'custom') {
                    // Show the modal
                    let modal = new bootstrap.Modal(document.getElementById('countrySelectorModal'));
                    modal.show();
                    
                    // Fetch all countries and populate the multi-select if not already populated
                    if ($('#specificCountries option').length <= 1) {
                        loadAllCountries();
                    }
                } else {
                    // Clear custom country selection
                    selectedCustomCountries = [];
                    
                    // Load data with the selected limit
                    let picker = $('#countryDatePicker').data('daterangepicker');
                    countriesChart(picker.startDate, picker.endDate);
                }
            });

            // Update selection count
            $('#specificCountries').on('change', function() {
                let count = $(this).val() ? $(this).val().length : 0;
                if (count > 0) {
                    $('#countrySelectionCount').text(`${count} ${count === 1 ? 'country' : 'countries'} selected`);
                } else {
                    $('#countrySelectionCount').text('');
                }
            });

            // Clear country selection
            $('#clearCountryFilter').on('click', function() {
                $('#specificCountries').val([]).trigger('change');
            });

            // Load all countries for multi-select
            function loadAllCountries() {
                const url = @json(route('admin.order.report.country.statistics'));
                let picker = $('#countryDatePicker').data('daterangepicker');
                
                // Show loading state
                $('#specificCountries').html('<option disabled>Loading countries...</option>');
                
                $.get(url, {
                    start_date: picker.startDate.format('YYYY-MM-DD'),
                    end_date: picker.endDate.format('YYYY-MM-DD'),
                    limit: 0 // Get all countries
                }, function(response) {
                    if (response && response.data) {
                        let countries = response.data.map(item => item.name).sort();
                        let options = '';
                        
                        if (countries.length === 0) {
                            options = '<option disabled>No countries found</option>';
                        } else {
                            countries.forEach(country => {
                                options += `<option value="${country}">${country}</option>`;
                            });
                        }
                        
                        $('#specificCountries').html(options);
                    }
                }).fail(function() {
                    $('#specificCountries').html('<option disabled>Error loading countries</option>');
                });
            }

            // Apply specific country filter
            $('#applyCountryFilter').on('click', function() {
                let selectedCountries = $('#specificCountries').val(); // Array of selected countries
                
                if (!selectedCountries || selectedCountries.length === 0) {
                    // Show a nicer notification instead of alert
                    $('#countrySelectionCount').html('<i class="las la-exclamation-circle"></i> Please select at least one country').css('color', '#dc3545');
                    return;
                }
                
                // Show loading state
                const $btn = $(this);
                const originalHtml = $btn.html();
                $btn.prop('disabled', true).html('<i class="las la-spinner la-spin"></i> Loading...');
                
                // Store selected countries globally
                selectedCustomCountries = selectedCountries;
                
                // Fetch data for selected countries
                let picker = $('#countryDatePicker').data('daterangepicker');
                countriesChart(picker.startDate, picker.endDate);
                
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('countrySelectorModal')).hide();
                
                // Reset button state
                setTimeout(function() {
                    $btn.prop('disabled', false).html(originalHtml);
                }, 500);
            });

        })(jQuery);
    </script>
@endpush
