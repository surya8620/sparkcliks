@extends($activeTemplate . 'layouts.master')
@section('content')
@if($widget['nano'] == 0)
<div class="dashboard-table__header d-flex justify-content-end pt-0 px-0">
    <div class="dashboard-table__btn">
        <input type="button" id="trial" value="Get Free Nano Credit/Free Trial" class="btn btn-sm btn--base" data-bs-toggle="modal">
    </div>
</div>
@else
@endif

<div class="row gy-4 mb-none-30">
    <div class="col-xl-2 col-lg-2 col-sm-4 mb-3 mb-lg-0">
        <div class="dashboard-w1 bg--18 b-radius--10 box-shadow">
            <a class="item-link" href="#"></a>
            <div class="icon">
                <i class="fa-solid fa fa-coins"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="amount">{{$widget['nano']}}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Nano')</span>
                </div>
            </div>
        </div>
    </div><!-- dashboard-w1 end -->
    <div class="col-xl-2 col-lg-2 col-sm-4 mb-3 mb-lg-0">
        <div class="dashboard-w1 bg--indigo b-radius--10 box-shadow">
            <a class="item-link" href="#"></a>
            <div class="icon">
                <i class="fa-solid fa fa-coins"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="amount">{{$widget['mini']}}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Mini')</span>
                </div>
            </div>
        </div>
    </div><!-- dashboard-w1 end -->

    <div class="col-xl-2 col-lg-2 col-sm-4 mb-3 mb-lg-0">
        <div class="dashboard-w1 bg--deep-purple b-radius--10 box-shadow">
            <a class="item-link" href="#"></a>
            <div class="icon">
                <i class="fa-solid fa fa-coins"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="amount">{{$widget['small']}}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Small')</span>
                </div>
            </div>
        </div>
    </div><!-- dashboard-w1 end -->

    <div class="col-xl-2 col-lg-2 col-sm-4 mb-3 mb-lg-0">
        <div class="dashboard-w1 bg--pink b-radius--10 box-shadow">
            <a class="item-link" href="#"></a>
            <div class="icon">
                <i class="fa-solid fa fa-coins"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="amount">{{$widget['medium']}}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Medium')</span>
                </div>
            </div>
        </div>
    </div><!-- dashboard-w1 end -->

    <div class="col-xl-2 col-lg-2 col-sm-4 mb-3 mb-lg-0">
        <div class="dashboard-w1 bg--5 b-radius--10 box-shadow">
            <a class="item-link" href="#"></a>
            <div class="icon">
                <i class="fa-solid fa fa-coins"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="amount">{{$widget['large']}}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Large')</span>
                </div>
            </div>
        </div>
    </div><!-- dashboard-w1 end -->

    <div class="col-xl-2 col-lg-2 col-sm-4 mb-3 mb-lg-0">
        <div class="dashboard-w1 bg--14 b-radius--10 box-shadow">
            <a class="item-link" href="#"></a>
            <div class="icon">
                <i class="fa-solid fa fa-coins"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="amount">{{$widget['ultimate']}}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Ultimate')</span>
                </div>
            </div>
        </div>
    </div><!-- dashboard-w1 end -->
</div>
<br><br>
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
<br><br>
<div class="row mb-none-30">
    <div class="col-xl-6 col-sm-6 mb-3 mb-lg-0">
        <a href="{{ route('user.web.history') }}" class="d-block text-decoration-none" style="color: inherit;">
            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                <i class="las la-shopping-cart overlay-icon text--primary"></i>
                <div class="widget-two__icon b-radius--5 bg--primary">
                    <i class="las la-shopping-cart text-white"></i>
                </div>
                <div class="widget-two__content">
                    <h3 class="">{{$widget['total_order']}}</h3>
                    <p>@lang('Total')</p>
                </div>
            </div><!-- widget-two end -->
        </a>
    </div>
    <div class="col-xl-6 col-sm-6 mb-3 mb-lg-0">
        <a href="{{ route('user.web.pending') }}" class="d-block text-decoration-none" style="color: inherit;">
            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                <i class="las la-exclamation-triangle overlay-icon text--warning"></i>
                <div class="widget-two__icon b-radius--5 bg--warning">
                    <i class="las la-exclamation-triangle text-white"></i>
                </div>
                <div class="widget-two__content">
                    <h3 class="">{{$widget['pending_order']}}</h3>
                    <p>@lang('Needs Action')</p>
                </div>
            </div><!-- widget-two end -->
        </a>
    </div>
</div>
<br>
<div class="row mb-none-30">
    <div class="col-xl-4 col-sm-6 mb-3 mb-lg-0">
        <a href="{{ route('user.web.completed') }}" class="d-block text-decoration-none" style="color: inherit;">
            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                <i class="las la-check-circle overlay-icon text--teal"></i>
                <div class="widget-two__icon b-radius--5 bg--teal">
                    <i class="las la-check-circle text-white"></i>
                </div>
                <div class="widget-two__content">
                    <h3 class="">{{$widget['completed_order']}}</h3>
                    <p>@lang('Completed')</p>
                </div>
            </div><!-- widget-two end -->
        </a>
    </div>

    <div class="col-xl-4 col-sm-6 mb-3 mb-lg-0">
        <a href="{{ route('user.web.processing') }}" class="d-block text-decoration-none" style="color: inherit;">
            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                <i class="la la-refresh overlay-icon text--teal"></i>
                <div class="widget-two__icon b-radius--5 bg--green">
                    <i class="la la-refresh text-white"></i>
                </div>
                <div class="widget-two__content">
                    <h3 class="">{{$widget['processing_order']}}</h3>
                    <p>@lang('Active')</p>
                </div>
            </div><!-- widget-two end -->
        </a>
    </div>
    <div class="col-xl-4 col-sm-6 mb-3 mb-lg-0">
        <a href="{{ route('user.web.paused') }}" class="d-block text-decoration-none" style="color: inherit;">
            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                <i class="la la-pause overlay-icon text--teal"></i>
                <div class="widget-two__icon b-radius--5 bg--red">
                    <i class="la la-pause text-white"></i>
                </div>
                <div class="widget-two__content">
                    <h3 class="">{{$widget['paused_order']}}</h3>
                    <p>@lang('Paused')</p>
                </div>
            </div><!-- widget-two end -->
        </a>
    </div>
</div>
<br>
<hr style="height:2px;border-width:0;color:gray;background-color:gray"><br>
<div class="modal fade" id="trialModal" tabindex="-1" role="dialog" aria-labelledby="trialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center" id="trialModalLabel"><strong>@lang('Get Free Nano Credit')</strong></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('user.web.nano')}}" method="post" class="resetForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group text-center">
                        <input type="hidden" name="nano" class="edit-amount" value="1">
                        <div class="form-group text-center">
                            <div class="card card-deposit text-center">
                                <div class="card-body card-body-deposit">
                                        <div class="text-bold h5 text--primary">NANO</div>
                                        <div class="card-title font-weight-bold text--primary"><small>upto <strong> 6,000</strong> page views</small></div>
                                        @if($widget['nano_exp'] > $widget['time'])
                                        <strong class="text--small font-weight-normal text--danger">
                                            Next Credit will be available after {{ showDateTime($widget['nano_exp']) }}
                                        </strong>
                                        @endif
                                </div>
                                <div class="card-body text--primary">
                                    <div class="row">
                                        <div class="col-8 text-start">Unique Visitors</div>
                                        <div class="col-4 text-center"><strong>2,000</strong></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-8 text-start">Maximum URLs</div>
                                        <div class="col-4 text-center"><strong>3</strong></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-8 text-start">Up to pages per visit</div>
                                        <div class="col-4 text-center"><strong>3</strong></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-8 text-start">Countries Geo Targeting</div>
                                        <div class="col-4 text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" style="fill: red;" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-8 text-start">Google Analytics 4 Engagement</div>
                                        <div class="col-4 text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" style="fill: red;" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-8 text-start">Google Analytics 4 Natural Events</div>
                                        <div class="col-4 text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" style="fill: red;" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-8 text-start">Automatic Website Crawler</div>
                                        <div class="col-4 text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" style="fill: red;" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-8 text-start">Content Engagement</div>
                                        <div class="col-4 text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" style="fill: red;" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-8 text-start">Random Engagement Time</div>
                                        <div class="col-4 text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" style="fill: red;" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-8 text-start">Random Session Time</div>
                                        <div class="col-4 text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" style="fill: red;" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-8 text-start">Random Scroll & Clicks</div>
                                        <div class="col-4 text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" style="fill: red;" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-8 text-start">Referral, Organic, Social Traffic Types</div>
                                        <div class="col-4 text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" style="fill: red;" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-8 text-start">Shorteners bit.ly and cutt.ly</div>
                                        <div class="col-4 text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" style="fill: red;" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-8 text-start">Random Time on Page</div>
                                        <div class="col-4 text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" style="fill: red;" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-8 text-start">Up to 30 seconds on every visit</div>
                                        <div class="col-4 text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="25" fill="currentColor" class="bi bi-check color--primary" viewBox="0 0 16 16">
                                                <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small class="card-title font-weight-bold">*Nano Credit can be redeemed once every 30days.</small>
                    </div>
                </div>
                <div class="form-group text-center">
                    @if($widget['nano_exp'] < $widget['time']) <button type="submit" class="btn btn--base btn-sm">@lang('Get Now')</button>
                        @else
                        @endif
                </div>
            </form>
        </div>
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
    fetch("{{ route('user.web.chart') }}")
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
    // Function to fetch data from Laravel function and update chart
    function updateChart() {
        $.ajax({
            url: "{{ route('user.web.realtime') }}",
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
                        name: 'Users',
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
                $('#realtimetotal').html('<span class="dot"></span>&nbsp; USERS IN LAST 30 MINUTES: ' + totalVisits);
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