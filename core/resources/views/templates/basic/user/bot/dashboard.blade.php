@extends($activeTemplate . 'layouts.master')
@section('content')
    @if($widget['bot_ack'] == 0)

        <div class="row justify-content-center mx-0">
            <div class="col-lg-7 col-md-9">
                <div class="disclaimer-box">
                    <div class="disclaimer-header">
                        <a href="#" class="disclaimer-logo-link">
                            <img src="{{ asset('assets/images/STB-3.webp') }}" alt="Sparky Traffic Bot" class="disclaimer-logo">
                        </a>
                    </div>
                    
                    <div class="disclaimer-content">
                        <form method="POST" action="{{ route('user.bot.accept') }}">
                            @csrf
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" value="1" id="acceptTerms" name="acceptTerms" required>
                                <label class="form-check-label" for="acceptTerms">
                                    I accept the disclaimer policy, and agree to activate my trial.
                                </label>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn--primary">
                                    <i class="las la-check-circle"></i> Activate Trial
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .disclaimer-box {
                background: #ffffff;
                border-radius: 10px;
                box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                margin: 2rem auto;
                max-width: 600px;
            }

            .disclaimer-header {
                padding: 0;
                text-align: center;
                overflow: hidden;
            }

            .disclaimer-logo-link {
                display: block;
                text-decoration: none;
                line-height: 0;
            }

            .disclaimer-logo {
                width: 100%;
                height: auto;
                display: block;
            }

            .disclaimer-content {
                padding: 1.5rem;
            }

            .disclaimer-content .alert {
                border-radius: 8px;
                font-size: 0.9rem;
                margin-bottom: 1rem;
                padding: 1rem;
            }

            .disclaimer-content .alert p {
                margin: 0;
                line-height: 1.6;
            }

            .disclaimer-content .alert-info {
                background-color: #e7f3ff;
                border: 1px solid #b3d9ff;
                color: #004085;
            }

            .disclaimer-content .alert-danger {
                background-color: #ffe7e7;
                border: 1px solid #ffb3b3;
                color: #721c24;
            }

            .disclaimer-content .alert-success {
                background-color: #e7ffe7;
                border: 1px solid #b3ffb3;
                color: #155724;
            }

            .disclaimer-content .form-check {
                background: #f8f9fa;
                padding: 1rem 3rem;
                border-radius: 8px;
                border: 2px solid #e9ecef;
                margin-bottom: 1.5rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            .disclaimer-content .form-check-input {
                width: 20px;
                height: 20px;
                margin-top: 0.15rem;
                cursor: pointer;
                flex-shrink: 0;
            }

            .disclaimer-content .form-check-label {
                cursor: pointer;
                font-weight: 500;
                margin-left: 0;
                flex: 1;
            }

            .disclaimer-content .text-center {
                margin-bottom: 0;
            }

            .disclaimer-content .btn {
                padding: 0.75rem 2rem;
                font-weight: 600;
                border-radius: 25px;
            }

            @media (max-width: 768px) {
                .disclaimer-box {
                    margin: 1rem auto;
                }

                .disclaimer-header {
                    padding: 1.25rem;
                }

                .disclaimer-header i {
                    font-size: 2rem;
                }

                .disclaimer-content {
                    padding: 1.25rem;
                }

                .disclaimer-content .btn {
                    width: 100%;
                }
            }
        </style>
    
    @else
    @php
        session()->forget('coupon');
    @endphp
    <div class="row mb-none-30">
        <div class="col-xl-12 col-sm-6 mb-30">
            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                <i class="fab la-servicestack overlay-icon text--warning"></i>
                <div class="widget-two__icon b-radius--5 bg--green">
                    <i class="fab la-servicestack text-white"></i>
                </div>
                <div class="widget-two__content">
                    <h3 class="fw-bold">
                        @if ($widget['mem_type'] == 121)
                            <span class="amount">@lang('TRIAL')</span>
                        @elseif($widget['mem_type'] == 122)
                            <span class="amount">@lang('LITE')</span>
                        @elseif($widget['mem_type'] == 123)
                            <span class="amount">@lang('BASIC')</span>
                        @elseif($widget['mem_type'] == 124)
                            <span class="amount">@lang('BRONZE')</span>
                        @elseif($widget['mem_type'] == 125)
                            <span class="amount">@lang('SILVER')</span>
                        @elseif($widget['mem_type'] == 126)
                            <span class="amount">@lang('GOLD')</span>
                        @elseif($widget['mem_type'] == 127)
                            <span class="amount">@lang('PLATINUM')</span>
                        @elseif($widget['mem_type'] == 128)
                            <span class="amount">@lang('DIAMOND')</span>
                        @elseif($widget['mem_type'] == 129)
                            <span class="amount">@lang('Custom')</span>
                        @else
                            <span class="amount">@lang('INACTIVE')</span>
                        @endif
                    </h3>
                    <p class="text--medium fw-semibold">@lang('Subscription Plan')</p>
                </div>
            </div><!-- widget-two end -->
        </div>
    </div><br>
    <div class="row mb-none-30">
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-4">
            <div class="dashboard-w1 bg--primary b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="la la-coins"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{ getamount($widget['mem_credit']) }}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--medium fw-semibold">@lang('Total Browsers')</span>
                    </div>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-4">
            <div class="dashboard-w1 bg--green b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="la la-play-circle"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{ getamount($widget['mem_used']) }}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--medium fw-semibold">@lang('Active Browsers')</span>
                    </div>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-4">
            <div class="dashboard-w1 bg--teal b-radius--10 box-shadow">
                <div class="icon">
                    <i class="la la-hourglass-half"></i>
                </div>
                <div class="details">
                    <div class="text--medium fw-bold">
                        @if ((is_null($widget['mem_exp']) || $widget['mem_exp'] == null) && $widget['mem_status'] == 0)
                            <span class="amount">@lang('Inactive')</span>
                        @elseif (($widget['cur_time'] > $widget['mem_exp']) && $widget['mem_status'] != 1)
                            <span class="amount">@lang('Expired')</span>
                        @elseif(($widget['cur_time'] < $widget['mem_exp']) && $widget['mem_status'] != 0)
                            <span class="amount">@lang('Active')</span>
                        @else
                            <span class="amount">@lang('Inactive')</span>
                        @endif
                    </div>
                    <div class="desciption">
                        <span class="text--medium fw-semibold">@lang(' Status')</span>
                    </div>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-4">
            <div class="dashboard-w1 bg--red b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="la la-clock-o"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{ showMonthTime($widget['mem_exp']) }}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--medium fw-semibold">@lang(' Validity until')</span>
                    </div>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
    </div><!-- row end-->
    <br>
    <div class="row gy-4 mb-none-30">
        <div class="col-xl-8 mb-3 mb-lg-0">
            <div class="card">
                <div class="card-body">
                    <div id="loader" class="loader"></div>
                    <div id="chartContainer" style="height: 400px;"></div>
                    <p class="text--small" id="totalVisits" style="font-weight: bold;"></p>
                </div>
            </div>
        </div>

        <div class="col-xl-4 mb-3 mb-lg-0">
            <div class="card">
                <div class="card-body">
                    <div id="loader2" class="loader"></div>
                    <div id="realtime" style="height: 400px;"></div>
                    <p class="text--small" id="realtimetotal" style="font-weight: bold;"></p>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row mb-none-30">
        <div class="col-xl-6 col-sm-6 mb-3 mb-lg-0">
            <a href="{{ route('user.bot.history') }}" class="d-block text-decoration-none" style="color: inherit;">
                <div class="widget-two box--shadow2 b-radius--5 bg--white">
                    <i class="las la-clipboard-list overlay-icon text--primary"></i>
                    <div class="widget-two__icon b-radius--5 bg--primary">
                        <i class="las la-clipboard-list text-white"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="">{{$widget['total_order']}}</h3>
                        <p class="fw-semibold">@lang('Total')</p>
                    </div>
                </div><!-- widget-two end -->
            </a>
        </div>
        <div class="col-xl-6 col-sm-6 mb-3 mb-lg-0">
            <a href="{{ route('user.bot.processing') }}" class="d-block text-decoration-none" style="color: inherit;">
                <div class="widget-two box--shadow2 b-radius--5 bg--white">
                    <i class="la la-refresh overlay-icon text--teal"></i>
                    <div class="widget-two__icon b-radius--5 bg--green">
                        <i class="la la-refresh text-white"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="">{{$widget['processing_order']}}</h3>
                        <p class="fw-semibold">@lang('Active')</p>
                    </div>
                </div><!-- widget-two end -->
            </a>
        </div>
    </div>
    <br>
    <div class="row mb-none-30">
        <div class="col-xl-4 col-sm-6 mb-3 mb-lg-0">
            <a href="{{ route('user.bot.completed') }}" class="d-block text-decoration-none" style="color: inherit;">
                <div class="widget-two box--shadow2 b-radius--5 bg--white">
                    <i class="las la-check-circle overlay-icon text--teal"></i>
                    <div class="widget-two__icon b-radius--5 bg--teal">
                        <i class="las la-check-circle text-white"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="">{{$widget['completed_order']}}</h3>
                        <p class="fw-semibold">@lang('Completed')</p>
                    </div>
                </div><!-- widget-two end -->
            </a>
        </div>

        <div class="col-xl-4 col-sm-6 mb-3 mb-lg-0">
            <a href="{{ route('user.bot.paused') }}" class="d-block text-decoration-none" style="color: inherit;">
                <div class="widget-two box--shadow2 b-radius--5 bg--white">
                    <i class="la la-pause overlay-icon text--teal"></i>
                    <div class="widget-two__icon b-radius--5 bg--red">
                        <i class="la la-pause text-white"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="">{{$widget['paused_order']}}</h3>
                        <p class="fw-semibold">@lang('Paused')</p>
                    </div>
                </div><!-- widget-two end -->
            </a>
        </div>
        <div class="col-xl-4 col-sm-6 mb-3 mb-lg-0">
            <a href="{{ route('user.bot.cancelled') }}" class="d-block text-decoration-none" style="color: inherit;">
                <div class="widget-two box--shadow2 b-radius--5 bg--white">
                    <i class="la la-times overlay-icon text--secondary"></i>
                    <div class="widget-two__icon b-radius--5 bg--secondary">
                        <i class="la la-times text-white"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="">{{$widget['cancelled_order']}}</h3>
                        <p class="fw-semibold">@lang('Cancelled')</p>
                    </div>
                </div><!-- widget-two end -->
            </a>
        </div>
    </div>
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

        .chart-container {
            position: relative;
            /* Ensure proper positioning */
            overflow: hidden;
            /* Hide any overflow content during transitions */
        }

        .chart {
            transition: all 0.3s ease;
            /* Apply smooth transition to all properties */
        }

        .loading-overlay {
            position: absolute;
            /* Position the overlay */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            /* Semi-transparent white background */
            display: flex;
            /* Center the loading spinner */
            justify-content: center;
            align-items: center;
            z-index: 1000;
            /* Ensure the overlay is above other content */
            transition: opacity 0.3s ease;
            /* Apply smooth transition to opacity */
            opacity: 0;
            /* Initially hidden */
            pointer-events: none;
            /* Allow interactions with underlying content */
        }

        .loading-overlay.active {
            opacity: 1;
            /* Show the overlay */
        }

        /* Define the animation */
        @keyframes growShrink {
            0% {
                transform: scale(1);
            }

            100% {
                transform: scale(1.5);
            }
        }

        .loader {
            border: 8px solid #41C1BA;
            /* Light grey */
            border-top: 8px solid #365B6D;
            /* Blue */
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 2s linear infinite;
            display: none;
            /* Initially hidden */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endif
@endsection

@push('script')
<script>
    "use strict";

    // Show Trial modal
    $('#trial').on('click', function() {
        $('#trialModal').modal('show');
    });

    // Handle deposit button click (event delegation)
    $(document).on('click', '.deposit', function() {
        var id = $(this).data('id');
        var nano = $(this).data('nano'); // Fix the space issue
        //console.log("ID:", id, "Nano:", nano);
    });
</script>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
    document.getElementById('loader').style.display = 'block';
    // Fetch data from the endpoint
    fetch("{{ route('user.bot.chart') }}")
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
                    text: null
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
                    name: 'Sessions',
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
            document.getElementById('totalVisits').textContent = 'TOTAL SESSIONS COMPLETED: ' + totalVisits;
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
    // Function to fetch data from Laravel function and update chart
    function updateChart() {
        $.ajax({
            url: "{{ route('user.bot.realtime') }}",
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
                        text: '<p><span class="dot"></span></p>'
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
                        name: 'Sessions',
                        data: response.visit,
                        color: '#41C1BA' // Specify the color for the bars here
                    }]
                };

                // Update or create the chart
                if (window.chart) {
                    window.chart.update(chartData);
                } else {
                    document.getElementById('realtime').style.display = 'block'; // Show the chart container
                    window.chart = Highcharts.chart('realtime', chartData, function() {
                        // Callback function to hide the loaders after the chart is rendered
                        document.getElementById('loader2').style.display = 'none';
                    });
                }
                // Update the total visits container
                $('#realtimetotal').html('<span class="dot"></span>&nbsp; SESSIONS IN LAST 30 MINUTES: ' + totalVisits);
            }
        });
    }

    // Call updateChart function initially and then every 10 seconds
    document.addEventListener('DOMContentLoaded', function() {
        // Show loader
        document.getElementById('loader2').style.display = 'block';
        updateChart();
        // Call updateChart every 10 seconds
        setInterval(updateChart, 10000);
    });
</script>

@endpush
