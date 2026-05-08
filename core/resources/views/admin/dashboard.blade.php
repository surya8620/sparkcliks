@extends('admin.layouts.app')
@section('panel')

        {{--Users Sections--}}
    <div class="row gy-4">
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.users.all') }}" icon="las la-users" title="Total"
                value="{{ $widget['total_users'] }}" bg="primary" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.users.active') }}" icon="las la-user-check" title="Active"
                value="{{ $widget['verified_users'] }}" bg="success" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.users.email.unverified') }}" icon="lar la-envelope"
                title="Email Unverified" value="{{ $widget['email_unverified_users'] }}" bg="danger" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.users.profile.incomplete') }}" icon="las la-comment-slash"
                title="Profile Incomplete" value="{{ $widget['profile_pending'] }}" bg="warning" />
        </div>
    </div>
    <hr>
    <div class="row mb-none-30 mt-30">
        <div class="col-xl-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                        <h5 class="card-title">@lang('')</h5>
                        <div id="userDatePicker" class="border p-1 cursor-pointer rounded">
                            <i class="la la-calendar"></i>&nbsp;
                            <span></span> <i class="la la-caret-down"></i>
                        </div>
                    </div>
                    <div id="usersChartArea"></div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    {{--Reports Sections--}}
        <div class="row mb-none-30 mt-30">
            <div class="col-xl-6 mb-30">
                <div class="card">
                    <div class="card-body">
		    	<div id="loader" class="loader"></div>
                    	<div id="chartContainer" style="height: 400px;"></div>
                    	<p id="totalVisits" style="font-weight: bold;"></p>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 mb-30">
                <div class="card">
                    <div class="card-body">
		    	<div id="loader2" class="loader"></div>
                    	<div id="chartContainer2" style="height: 400px;"></div>
                    	<p  id="realtimetotal" style="font-weight: bold;"></p>
                    </div>
                </div>
            </div>
        </div>
    <hr>
        {{--API Cron Status--}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0">@lang('Cron Status')</h6>
    </div>
    <div class="row gy-3">
        <div class="col-xxl-3 col-sm-6">
            <div class="card border-0 bg--primary bg-opacity-10">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-white small">Add API</p>
                            <h6 class="mb-0 text-white">{{ gs('api_process_cron') ? diffForHumans(gs('api_process_cron')) : 'Never' }}</h6>
                        </div>
                        <i class="las la-plus-circle fs-2 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card border-0 bg--info bg-opacity-10">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-white small">Update API</p>
                            <h6 class="mb-0 text-white">{{ gs('api_update_cron') ? diffForHumans(gs('api_update_cron')) : 'Never' }}</h6>
                        </div>
                        <i class="las la-sync fs-2 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card border-0 bg--warning bg-opacity-10">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-white small">Stop API</p>
                            <h6 class="mb-0 text-white">{{ gs('api_stop_cron') ? diffForHumans(gs('api_stop_cron')) : 'Never' }}</h6>
                        </div>
                        <i class="las la-stop-circle fs-2 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card border-0 bg--success bg-opacity-10">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-white small">Resume API</p>
                            <h6 class="mb-0 text-white">{{ gs('api_resume_cron') ? diffForHumans(gs('api_resume_cron')) : 'Never' }}</h6>
                        </div>
                        <i class="las la-play-circle fs-2 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card border-0 bg--danger bg-opacity-10">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-white small">Invoice</p>
                            <h6 class="mb-0 text-white">{{ gs('inv_cron') ? diffForHumans(gs('inv_cron')) : 'Never' }}</h6>
                        </div>
                        <i class="las la-list fs-2 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card border-0 bg--secondary bg-opacity-10">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-white small">Currency</p>
                            <h6 class="mb-0 text-white">{{ gs('currency_cron') ? diffForHumans(gs('currency_cron')) : 'Never' }}</h6>
                        </div>
                        <i class="las la-dollar-sign fs-2 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card border-0 bg--dark bg-opacity-10">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-white small">Expired</p>
                            <h6 class="mb-0 text-white">{{ gs('exp_cron') ? diffForHumans(gs('exp_cron')) : 'Never' }}</h6>
                        </div>
                        <i class="las la-clock fs-2 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card border-0 bg--success bg-opacity-10">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-white small">Completed</p>
                            <h6 class="mb-0 text-white">{{ gs('completed_cron') ? diffForHumans(gs('completed_cron')) : 'Never' }}</h6>
                        </div>
                        <i class="las la-check-circle fs-2 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    {{--Campaigns Sections--}}
    <h5 class="card-title">@lang('Campaigns')</h5>
        <div class="row gy-4 mt-2">
        <div class="col-xxl-4 col-sm-6">
            <x-widget value="{{ $widget['total_order'] }}" title="Total Campaigns" style="6"
                link="#" icon="las la-shopping-cart" bg="primary" outline="true" />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget value="{{ $widget['processing_order'] }}" title="Active Campaigns" style="6"
                link="#" icon="la la-refresh" bg="primary" outline="true" />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget value="{{ $widget['completed_order'] }}" title="Completed Campaigns" style="6"
                link="#" icon="las la-check-circle" bg="success" outline="true" />
        </div>
    </div>
    <div class="row mb-none-30 mt-30">
        <div class="col-xl-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                        <h5 class="card-title">@lang('')</h5>
                        <div id="campaignDatePicker" class="border p-1 cursor-pointer rounded">
                            <i class="la la-calendar"></i>&nbsp;
                            <span></span> <i class="la la-caret-down"></i>
                        </div>
                    </div>
                    <div id="campaignsChartArea"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row gy-4 mt-2">
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.serp.index') }}" icon="fa fa-search-plus" title="SERP"
                value="{{ $widget['total_serp_order'] }}" bg="primary" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.wt.index') }}" icon="fa fa-line-chart" title="Website Traffic"
                value="{{ $widget['total_wt_order'] }}" bg="success" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.rt.index') }}" icon="fa fa-user-secret"
                title="Realistic Traffic" value="{{ $widget['total_rt_order'] }}" bg="secondary" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.tb.index') }}" icon="fa fa-bug"
                title="Traffic Bot" value="{{ $widget['total_tb_order'] }}" bg="danger" />
        </div>
    </div>
    <hr>
    {{--Payments Sections--}}
    <h5 class="card-title">@lang('Payments')</h5>
        <div class="row mb-none-30 mt-30">
        <div class="col-xl-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                        <h5 class="card-title">@lang('Transactions')</h5>

                        <div id="trxDatePicker" class="border p-1 cursor-pointer rounded">
                            <i class="la la-calendar"></i>&nbsp;
                            <span></span> <i class="la la-caret-down"></i>
                        </div>
                    </div>

                    <div id="transactionChartArea"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row gy-4 mt-2">
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.deposit.list') }}" title="Total"
                icon="fas fa-hand-holding-usd" value="{{ showAmount($deposit['total_deposit_amount']) }}" bg="success" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.deposit.pending') }}" title="Current Year"
                icon="fas fa-hand-holding-usd" value="{{ showAmount($deposit['total_deposit_amount_cy']) }}" bg="warning" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.deposit.rejected') }}" title="Last Month"
                icon="fas fa-hand-holding-usd" value="{{ showAmount($deposit['total_deposit_amount_lm']) }}" bg="danger" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.deposit.list') }}" title="Current Month"
                icon="fas fa-hand-holding-usd" value="{{ showAmount($deposit['total_deposit_amount_cm']) }}" bg="primary" />
        </div>
    </div>


    @if (gs('cron_status'))
        @include('admin.partials.cron_modal')
    @endif
@endsection

@if (gs('cron_status'))
    @push('breadcrumb-plugins')
        <button class="btn btn-outline--primary btn-sm" data-bs-toggle="modal" data-bs-target="#cronModal">
            <i class="las la-server"></i>@lang('Cron Setup')
        </button>
    @endpush
@endif

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/charts.js') }}"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('style')
<style>
/* Define the dot */
.dot {
    height: 10px;
    width: 10px;
    background-color: green;
    border-radius: 50%;
    display: inline-block;
    animation: growShrink 2s infinite alternate;
}
.loader {
    border: 8px solid #41C1BA; /* Light grey */
    border-top: 8px solid #365B6D; /* Blue */
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 2s linear infinite;
    display: none; /* Initially hidden */
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
        .active {
            background-color: green;
        }
        .dead {
            background-color: red;
        }


 .apexcharts-menu {
    min-width: 120px !important;
 }
</style>
@endpush

@push('script')
    <script>
        "use strict";

        const start = moment().subtract(14, 'days');
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
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                    'month')],
                'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
            },
            maxDate: moment()
        }

        const changeDatePickerText = (element, startDate, endDate) => {
            $(element).html(startDate.format('MMMM D, YYYY') + ' - ' + endDate.format('MMMM D, YYYY'));
        }

        let dwChart = barChart(
            document.querySelector("#dwChartArea"),
            @json(__(gs('cur_text'))),
            [{
                    name: 'Payments',
                    data: []
                }
            ],
            [],
        );

        let trxChart;

        document.addEventListener('DOMContentLoaded', () => {
            trxChart = Highcharts.chart('transactionChartArea', {
                chart: { type: 'spline' },
                title: { text: '' },
                xAxis: { categories: [] },
                yAxis: { title: { text: 'Transactions' } },
                plotOptions: {
                    spline: {
                        marker: {
                            enabled: false // hides the dots
                        }
                    }
                },
                series: [
                    { name: 'Completed', data: [], color: '#28a745' }, // green
                    { name: 'Refunded', data: [], color: '#dc3545' },  // amber/yellow
                    { name: 'Cancelled', data: [], color: '#ffc107' }  // red ffc107
                ]
            });
        });

        let userChart;

        document.addEventListener('DOMContentLoaded', () => {
            userChart = Highcharts.chart('usersChartArea', {
                chart: { type: 'spline' },
                title: { text: 'User Statistics' },
                xAxis: { categories: [] },
                yAxis: { title: { text: 'Users' } },
                plotOptions: {
                    spline: {
                        marker: {
                            enabled: false // hides the dots
                        }
                    }
                },
                series: [
                    { name: 'Total', data: [] },
                    { name: 'New', data: [] },
                    { name: 'Banned', data: [] },
                    { name: 'Unverified', data: [] }
                ]
            });
        });

        let campaignChart;

        document.addEventListener('DOMContentLoaded', () => {
            campaignChart = Highcharts.chart('campaignsChartArea', {
                chart: { type: 'spline' },
                title: { text: 'Campaign Statistics' },
                xAxis: { categories: [] },
                yAxis: { title: { text: 'Campaigns' } },
                plotOptions: {
                    spline: {
                        marker: {
                            enabled: false // hides the dots
                        }
                    }
                },
                series: [
                    { name: 'Total', data: [] },
                    { name: 'Completed', data: [] },
                    { name: 'SERP', data: [] },
                    { name: 'Website Traffic', data: [] },
                    { name: 'Realistic Traffic', data: [] },
                    { name: 'Traffic Bot', data: [] }
                ]
            });
        });


        const depositWithdrawChart = (startDate, endDate) => {
            const data = {
                start_date: startDate.format('YYYY-MM-DD'),
                end_date: endDate.format('YYYY-MM-DD')
            }
            const url = @json(route('admin.chart.deposit.withdraw'));
            $.get(url, data,
                function(data, status) {
                    if (status == 'success') {
                        dwChart.updateSeries(data.data);
                        dwChart.updateOptions({
                            xaxis: {
                                categories: data.created_on,
                            }
                        });
                    }
                }
            );
        }

        const transactionChart = (startDate, endDate) => {
            const data = {
                start_date: startDate.format('YYYY-MM-DD'),
                end_date: endDate.format('YYYY-MM-DD')
            };
            const url = @json(route('admin.chart.transaction'));

            $.get(url, data, function(response, status) {
                if (status === 'success') {
                    // Update x-axis categories
                    trxChart.xAxis[0].setCategories(response.created_on);

                    // Update series data (assumes same order: Total Users, Active Users)
                    if (trxChart.series.length >= 2 && response.data.length >= 2) {
                        trxChart.series[0].setData(response.data[0].data);
                        trxChart.series[1].setData(response.data[1].data);
                        trxChart.series[2].setData(response.data[2].data);
                    } else {
                        console.warn("Mismatch in expected series structure.");
                    }
                }
            });
        };

        const usersChart = (startDate, endDate) => {
            const data = {
                start_date: startDate.format('YYYY-MM-DD'),
                end_date: endDate.format('YYYY-MM-DD')
            };
            const url = @json(route('admin.chart.users'));

            $.get(url, data, function(response, status) {
                if (status === 'success') {
                    // Update x-axis categories
                    userChart.xAxis[0].setCategories(response.created_on);

                    // Update series data (assumes same order: Total Users, Active Users)
                    if (userChart.series.length >= 2 && response.data.length >= 2) {
                        userChart.series[0].setData(response.data[0].data);
                        userChart.series[1].setData(response.data[1].data);
                        userChart.series[2].setData(response.data[2].data);
                        userChart.series[3].setData(response.data[3].data);
                    } else {
                        console.warn("Mismatch in expected series structure.");
                    }
                }
            });
        };

        const campaignsChart = (startDate, endDate) => {
            const data = {
                start_date: startDate.format('YYYY-MM-DD'),
                end_date: endDate.format('YYYY-MM-DD')
            };
            const url = @json(route('admin.chart.campaigns'));

            $.get(url, data, function(response, status) {
                if (status === 'success') {
                    // Update x-axis categories
                    campaignChart.xAxis[0].setCategories(response.created_on);

                    // Update series data (assumes same order: Total Users, Active Users)
                    if (campaignChart.series.length >= 2 && response.data.length >= 2) {
                        campaignChart.series[0].setData(response.data[0].data);
                        campaignChart.series[1].setData(response.data[1].data);
                        campaignChart.series[2].setData(response.data[2].data);
                        campaignChart.series[3].setData(response.data[3].data);
                        campaignChart.series[4].setData(response.data[4].data);
                    } else {
                        console.warn("Mismatch in expected series structure.");
                    }
                }
            });
        };

        $('#dwDatePicker').daterangepicker(dateRangeOptions, (start, end) => changeDatePickerText('#dwDatePicker span',
            start, end));
        $('#trxDatePicker').daterangepicker(dateRangeOptions, (start, end) => changeDatePickerText('#trxDatePicker span',
            start, end));
        $('#userDatePicker').daterangepicker(dateRangeOptions, (start, end) => changeDatePickerText('#userDatePicker span',
            start, end));
        $('#campaignDatePicker').daterangepicker(dateRangeOptions, (start, end) => changeDatePickerText('#campaignDatePicker span',
            start, end));

        changeDatePickerText('#dwDatePicker span', start, end);
        changeDatePickerText('#trxDatePicker span', start, end);
        changeDatePickerText('#userDatePicker span', start, end);
        changeDatePickerText('#campaignDatePicker span', start, end);

        depositWithdrawChart(start, end);
        transactionChart(start, end);
        usersChart(start, end);
        campaignsChart(start, end);

        $('#dwDatePicker').on('apply.daterangepicker', (event, picker) => depositWithdrawChart(picker.startDate, picker
            .endDate));
        $('#trxDatePicker').on('apply.daterangepicker', (event, picker) => transactionChart(picker.startDate, picker
            .endDate));
        $('#userDatePicker').on('apply.daterangepicker', (event, picker) => usersChart(picker.startDate, picker
            .endDate));
        $('#campaignDatePicker').on('apply.daterangepicker', (event, picker) => campaignsChart(picker.startDate, picker
            .endDate));
    </script>
<script>
	document.getElementById('loader').style.display = 'block';
        // Fetch data from the endpoint
        fetch('{{ route('admin.chart.reports.daily') }}')
            .then(response => response.json())
            .then(data => {
                // Configure and render the chart
		const maxDataValue = Math.max(...data.visit);
		const yAxisMax = Math.max(maxDataValue, 10);
		const tickInterval = Math.ceil(yAxisMax / 5);
		const minPadding = (yAxisMax > 10) ? 0 : 1 - (yAxisMax / 10);
                Highcharts.chart('chartContainer', {
                    chart: {
                        type: 'spline'
                    },
                    title: {
                        text: 'Traffic Statistics',
                    },
                    xAxis: {
                        categories: data.dates.map(date => formatDate(date)), // Format dates
                        labels: {
                            rotation: -45, // Rotate labels by 45 degrees for better readability
                            style: {
                                fontSize: '12px', // Adjust font size
                            }
                        },
                        tickInterval: 2 // Display labels for every other day
                    },
                    yAxis: {
                        title: {
                            text: null 
                        }
                    },
                    series: [{
                        name: 'Visits',
                        data: data.visit,
			color: '#41C1BA',
                        marker: {
                        enabled: false // Disable data point markers
                    }
                    }],
                    credits: {
                        text: 'All Campaigns - Last 30 Days',
                        href: '',
                        position: {
                            align: 'right',
                            x: -10,
                            verticalAlign: 'bottom',
                            y: -10
                        },
                        style: {
                            color: '#365B6D',
                            fontSize: '12px',
                            fontWeight: 'normal'
                        }
                    }
                });

                // Calculate total visits
                var totalVisits = data.visit.reduce((a, b) => a + b, 0);

                // Display total visits
                document.getElementById('totalVisits').textContent = 'TOTAL VISITS DELIVERED: ' + totalVisits;
        	// Hide loader after data is fetched
        	document.getElementById('loader').style.display = 'none';
            });

        // Function to format dates as "01 Jan"
        function formatDate(dateString) {
            var date = new Date(dateString);
            return date.getDate() + ' ' + monthNames[date.getMonth()];
        }

        // Array of month names
        var monthNames = [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
        ];
</script>
<script>
	document.getElementById('loader3').style.display = 'block';
        // Fetch data from the endpoint
        fetch('{{ route('admin.chart.reports.payments') }}')
            .then(response => response.json())
            .then(data => {
                Highcharts.chart('paymentsChart', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Payments'
                    },
                    xAxis: {
                        categories: data.months.map(month => formatMonth(month)),
                        labels: {
                            rotation: -45, // Rotate labels by 45 degrees for better readability
                            style: {
                                fontSize: '12px', // Adjust font size
                            }
                        },
                    },
                    yAxis: {
                        title: {
                            text: null 
                        }
                    },
                    series: [{
                        name: 'Amount',
                        data: data.amount,
			color: '#41C1BA',
                        marker: {
                        enabled: false // Disable data point markers
                    }
                    }],
                    credits: {
                        text: '',
                        href: '',
                        position: {
                            align: 'right',
                            x: -10,
                            verticalAlign: 'bottom',
                            y: -10
                        },
                        style: {
                            color: '#365B6D',
                            fontSize: '12px',
                            fontWeight: 'normal'
                        }
                    }
                });

                // Calculate total visits
                var totalAmount = data.amount.reduce((a, b) => a + b, 0);

                // Display total visits
                document.getElementById('totalAmount').textContent = 'Total Amount: ' + totalAmount;
        	// Hide loader after data is fetched
        	document.getElementById('loader3').style.display = 'none';
            });

    function formatMonth(month) {
        // Parse the month string and format it
        const [year, monthNum] = month.split('-');
        const formattedMonth = new Date(year, monthNum - 1).toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
        return formattedMonth;
    }
</script>

<script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script>
        // Function to fetch data from Laravel function and update chart
        function updateChart() {
            $.ajax({
                url: "{{ route('admin.chart.reports.realtime') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                // Calculate total visits in the last 30 minutes
                var totalVisits = response.visit.reduce((total, visit) => total + visit, 0);

                var chartData = {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: '<p><span class="dot"></span>Realtime</p>'
                    },
                    xAxis: {
                        categories: response.timestamps,
                        crosshair: true
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: null
                        }
                    },
                    credits: {
                        text: '',
                        href: '',
                        position: {
                            align: 'right',
                            x: -10,
                            verticalAlign: 'bottom',
                            y: -10
                        },
                        style: {
                            color: '#666666',
                            fontSize: '12px',
                            fontWeight: 'normal'
                        }
                    },
                    series: [{
                        name: 'Users',
                        data: response.visit,
                        color: '#41C1BA' // Specify the color for the bars here
                    }]
                };

                    // Update or create the chart
                    if (window.chart) {
                        window.chart.update(chartData);
                    } else {
                        document.getElementById('chartContainer2').style.display = 'block'; // Show the chart container
                        window.chart = Highcharts.chart('chartContainer2', chartData, function() {
                            // Callback function to hide the loaders after the chart is rendered
                            document.getElementById('loader2').style.display = 'none';
                        });
                    }
                // Update the total visits container
                $('#realtimetotal').html('<span class="dot"></span>&nbsp; LAST 30 MINUTES: ' + totalVisits);
                },
        error: function(xhr, status, error) {
            console.error("Error fetching data:", error); // Log any errors
        }
                });
                }

        // Call updateChart function initially and then every 10 seconds
        document.addEventListener('DOMContentLoaded', function() {
            // Show loader
            document.getElementById('loader2').style.display = 'block';
            updateChart();
            // Call updateChart every 10 seconds
            setInterval(updateChart, 60000);
        });    
</script>

@endpush
