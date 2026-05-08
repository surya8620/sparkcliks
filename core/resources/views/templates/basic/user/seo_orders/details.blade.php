@extends($activeTemplate . 'layouts.master')
@section('content')
    @if ($order->status == 0)
        <div class="alert alert-danger alert-dismissible fade show d-flex" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>@lang('ERROR: ') {{ $order->error }} </strong></span>
        </div>
    @endif
    <div class="card custom--card">
        <div class="card-header">
            <div class="row mb-none-30">
                <!-- First Section -->
                <div class="form-group col-md-4">
                    <div class="dashboard-w1 bg--primary b-radius--10 box-shadow">
                        <div class="icon">
                            <i class="fa-solid fa-qrcode"></i>
                        </div>
                        <div class="details">
                            <div class="desciption">
                                <span class="text--medium">@lang('Campaign') <strong>(ID:
                                        {{ $order->id }})</strong></span>
                            </div>
                            <div class="numbers">
                                <span class="amount">{{ $order->service->name }}</span>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Second Section -->
                <div class="form-group col-md-4 text-center">
                    <div class="dashboard-w1 bg--teal b-radius--10 box-shadow ">
                        <div class="icon">
                            <i class="fa-solid fa-poo-storm"></i>
                        </div>
                        <div class="details">
                            <div class="desciption">
                                <span class="text--medium">@lang('Status')</span>
                            </div>
                            <div class="numbers">
                                @if ($order->status == 1)
                                    <span class="amount">
                                        @lang('ACTIVE')
                                    </span>
                                @elseif($order->status == 0)
                                    <span class="amount">
                                        @lang('ERROR')
                                    </span>
                                @elseif($order->status == 2)
                                    <span class="amount">
                                        @lang('COMPLETED')
                                    </span>
                                @elseif($order->status == 3)
                                    <span class="amount">
                                        @lang('DENIED')
                                    </span>
                                @elseif($order->status == 4)
                                    <span class="amount">
                                        @lang('CANCELLED')
                                    </span>
                                @elseif($order->status == 5)
                                    <span class="amount">
                                        @lang('EXPIRED')
                                    </span>
                                @elseif($order->status == 6)
                                    <span class="amount">
                                        @lang('PAUSED')
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Third Section -->
                <div class="form-group col-md-4">
                    <div class="dashboard-w1 bg--red b-radius--10 box-shadow ">
                        <div class="icon">
                            <i class="fa-solid fa-globe"></i>
                        </div>
                        <div class="details">
                            <div class="desciption">
                                <span class="text--medium">@lang('Geo-Target')</span>
                            </div>
                            <div class="numbers">
                                <span class="amount">{{ $order->country }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Fourth Section -->
                <div class="form-group col-md-2">
                    <div class="dashboard-w1 bg--1 b-radius--10 box-shadow ">
                        <div class="icon">
                            <i class="fa-solid fa-chart-bar"></i>
                        </div>
                        <div class="details">
                            <div class="desciption">
                                <span class="text--medium">@lang('Quantity')</span>
                            </div>
                            <div class="numbers">
                                <span class="amount">{{ $order->quantity }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Fifth Section -->
                <div class="form-group col-md-2">
                    <div class="dashboard-w1 bg--1 b-radius--10 box-shadow ">
                        <div class="icon">
                            <i class="fa-solid fa-check-circle"></i>
                        </div>
                        <div class="details">
                            <div class="desciption">
                                <span class="text--medium">@lang('IP Qualitiy')</span>
                            </div>
                            <div class="numbers">
                                <span class="amount">{{ $order->quality }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Sixth Section -->
                <div class="form-group col-md-2">
                    <div class="dashboard-w1 bg--1 b-radius--10 box-shadow ">
                        <div class="icon">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </div>
                        <div class="details">
                            <div class="desciption">
                                <span class="text--medium">@lang('No of Clicks/day')</span>
                            </div>
                            <div class="numbers">
                                <span class="amount">{{ $order->clicks }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Seventh Section -->
                <div class="form-group col-md-2">
                    <div class="dashboard-w1 bg--1 b-radius--10 box-shadow ">
                        <div class="icon">
                            <i class="fa-solid fa-flag-checkered"></i>
                        </div>
                        <div class="details">
                            <div class="desciption">
                                <span class="text--medium">@lang('Completed')</span>
                            </div>
                            <div class="numbers">
                                <span class="amount">{{ $order->start_counter }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Eight Section -->
                <div class="form-group col-md-2">
                    <div class="dashboard-w1 bg--1 b-radius--10 box-shadow ">
                        <div class="icon">
                            <i class="fa-solid fa-hourglass-half"></i>
                        </div>
                        <div class="details">
                            <div class="desciption">
                                <span class="text--medium">@lang('Remaining')</span>
                            </div>
                            <div class="numbers">
                                <span class="amount">{{ $order->remain }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Ninth Section -->
                <div class="form-group col-md-2">
                    <div class="dashboard-w1 bg--1 b-radius--10 box-shadow ">
                        <div class="icon">
                            <i class="fa-solid fa-person-walking"></i>
                        </div>
                        <div class="details">
                            <div class="desciption">
                                <span class="text--medium">@lang('Attempts')</span>
                            </div>
                            <div class="numbers">
                                <span class="amount">{{ $order->attempt }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row gy-4 mb-none-30">
                <div class="col-xl-12 mb-30">
                    <div class="card">
                        <div class="card-body">
                            <div id="loader" class="loader"></div>
                            <div id="chartContainer" style="height: 400px;"></div>
                            <p class="text--small" id="totalVisits" style="font-weight: bold;"></p>
                        </div>
                    </div>
                </div>
            </div>
            <hr style="height:2px;border-width:0;color:gray;background-color:gray">
            <form class="dashboard-form" role="form" method="POST"
                action="{{ route('user.seo.update', $order->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form--label" style="font-size: 14px;"><strong>@lang('Campaign Name')</strong></label>
                        <span data-bs-toggle="tooltip" title="@lang('Keep it Short and Catchy')" class="vat-fee-info"><i
                                class="las la-info-circle"></i> </span>
                        <input type="text" name="title" class="form--control form--control-lg"
                            value="{{ $order->name }}" placeholder="Keep it Short and Catchy">
                    </div>
                    <div class="form-group col-md-8">
                        <div class="form-group">
                            <label class="form--label font-weight-bold" for="url"
                                style="font-size: 14px;"><strong>@lang('Your URL')</strong>
                                <span data-bs-toggle="tooltip" title="@lang('URL of your website, eg: https://www.website.com')" class="vat-fee-info"><i
                                        class="las la-info-circle"></i> </span>
                                <br><small>@lang('Clickers look for your website available on Search Engine Result Page.')</small></label>
                            <input type="text" name="link" value="{{ urldecode($order->link) }}"
                                class="form--control form--control-lg"
                                placeholder="URL of your website, eg: https://www.website.com">
                        </div>
                    </div>
                    <div class="form-group col-md-8">
                        <label class="form--label font-weight-bold" for="url"
                            style="font-size: 14px;">@lang('Next URL')
                            <span data-bs-toggle="tooltip" title="@lang('URL of your website, eg: https://website.com/page1 or https://www.website.com/page1')" class="vat-fee-info"><i
                                    class="las la-info-circle"></i> </span>
                            <br><small>@lang('Clickers navigate to this page after visiting the first page.')</small></label>
                        <input type="text" name="link2" value="{{ urldecode($order->link2) }}"
                            class="form--control form--control-lg"
                            placeholder="URL of your website, eg: https://website.com/page1 or https://www.website.com/page1">
                    </div>
                    <div class="form-group col-md-8">
                        <label class="form--label font-weight-bold" for="keyword"
                            style="font-size: 14px;">@lang('Keywords')
                            <span data-bs-toggle="tooltip" title="@lang('Clickers search for your keywords in the Search Engine., Comma Separated or One per line')" class="vat-fee-info"><i
                                    class="las la-info-circle"></i> </span>
                            <br><small>@lang('Clickers navigate to this page after visiting the first page.')</small></label>
                        <textarea name="keyword" class="form-control form--control" rows="5"
                            placeholder="Clickers search for your keywords in the Search Engine., Comma Separated or One per line.">{{ str_replace(',', "\n", $order->keyword) }}</textarea>
                        <small class="form-text text-muted">@lang('In Google Search Console, you can see which keywords your domain is referenced on Google for a given location and It must be ranked in top 100 on the keyword.')</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form--label font-weight-bold" for="url"
                            style="font-size: 14px;">@lang('No of Clicks per day.')
                            <span data-bs-toggle="tooltip" title="@lang('No of Clickers who visit your website daily.')" class="vat-fee-info"><i
                                    class="las la-info-circle"></i> </span>
                            <br><small>@lang('No of Clickers who visit your website daily.')</small></label>
                        <input type="number" class="form-control form--control" id="clicks" name="clicks"
                            value="{{ $order->clicks }}" min="1"
                            max="{{ $order->quantity - $order->start_counter }}">
                    </div>

                    @if ($order->country != 'Worldwide')
                        <div class="form-group col-md-4">
                            <label class="form-label font-weight-bold" for="countries"
                                style="font-size: 14px;"><strong>@lang('Countries Geo-Targeting')</strong></label>
                            <span data-bs-toggle="tooltip" title="@lang('Worldwide: Traffic comes from random countries. Geo Target: Choose specific countries to receive traffic from.')" class="vat-fee-info"><i
                                    class="las la-info-circle"></i> </span>
                            <select name="country" id="country" class="form--control form--control-lg"
                                value="{{ $order->country }}">
                                @include('partials.country_seo_2')
                            </select>
                            <label class="form-label font-weight-bold justify-content-end" for="behaviour"></label>
                        </div>
                    @endif
                    @if ($order->status == 0 || $order->status == 1)
                        <hr style="height:2px;border-width:0;color:gray;background-color:gray">
                        <div class="form-group col-md-12">
                            <button type="submit" class="btn btn--base btn--lg w-100"
                                id="from-prevent-multiple-submits">@lang('Update')</button>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
    <hr style="height:2px;border-width:0;color:gray;background-color:gray">
    <div class="col-lg-12">
        <div class="card b-radius--10 mb-4">
            <div class="table-responsive mb-4">
                <h4 class="my-3 text-center">Click History</h4>
                <table class="table--responsive--lg table-bordered table-striped table">
                    <thead>
                        <tr>
                            <th class="text-center" style="font-size: 14px;">@lang('Click #')</th>
                            <th class="text-center" style="font-size: 14px;">@lang('IP')</th>
                            <th class="text-center" style="font-size: 14px;">@lang('Region')</th>
                            <th class="text-center" style="font-size: 14px;">@lang('Country')</th>
                            <th class="text-center" style="font-size: 14px;">@lang('Timestamp(UTC)')</th>
                        </tr>
                    </thead>
                    <tbody id="click-history-container">
                        @forelse ($clicks as $index => $click)
                            <tr>
                                <td class="text-center" style="font-size: 14px;" data-label="@lang('Click #')">
                                    {{ $clicks->total() - ($clicks->firstItem() + $index) + 1 }}</td>
                                <td class="text-center" style="font-size: 14px;" data-label="@lang('IP')">
                                    {{ $click->clicker_ip }}</td>
                                <td class="text-center" style="font-size: 14px;" data-label="@lang('Region')">
                                    {{ $click->clicker_region }}</td>
                                <td class="text-center" style="font-size: 14px;" data-label="@lang('Country')">
                                    {{ $click->clicker_country }}</td>
                                <td class="text-center" style="font-size: 14px;" data-label="@lang('Timestamp(UTC)')">
                                    {{ $click->created_at->format('Y-m-d H:i:s') }}
                                    <br>{{ diffForHumans($click->created_at) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4 mb-4" id="pagination-links">
                    {{ $clicks->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection


@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            $(document).on('click', '#pagination-links a', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    success: function(response) {
                        // Replace both click history and pagination section
                        $('#click-history-container').html($(response).find(
                            '#click-history-container').html());
                        $('#pagination-links').html($(response).find('#pagination-links')
                            .html());

                        // Smooth scroll to maintain focus
                        $('html, body').animate({
                            scrollTop: $('#click-history-container').offset().top
                        }, 600);
                    },
                    error: function() {
                        alert('Could not load data. Please try again.');
                    }
                });
            });
        });
    </script>
    <script>
        (function($) {
            "use strict";


            // Optional: when number input changes, update range slider
            input.addEventListener('input', () => {
                if (parseInt(input.value) >= parseInt(range.min) && parseInt(input.value) <= parseInt(range
                        .max)) {
                    range.value = input.value;
                }
            });

            $('#from-prevent-multiple-submits').on('submit', function() {
                $("#btn-save", this)
                    .html("Please wait...")
                    .attr('disabled', 'disabled');
                return true;
            })
        })(jQuery);
    </script>

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script>
        document.getElementById('loader').style.display = 'block';
        // Fetch data from the endpoint
        fetch("{{ route('user.seo_campaign.chart', $order->id) }}")
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
                        name: 'SERP CLICKS',
                        data: data.visit,
                        color: '#41C1BA',
                        marker: {
                            enabled: false // Disable data point markers
                        }
                    }],
                    credits: {
                        text: 'Last 30 days',
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
@endpush
