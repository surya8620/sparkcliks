@extends($activeTemplate . 'layouts.app')
@section('panel')
<div class="row mb-none-30">
    <div class="col-xl-2 col-lg-2 col-sm-4 mb-15">
        <div class="dashboard-w1 bg--18 b-radius--10 box-shadow">
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
    <div class="col-xl-2 col-lg-2 col-sm-4 mb-15">
        <div class="dashboard-w1 bg--indigo b-radius--10 box-shadow">
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

    <div class="col-xl-2 col-lg-2 col-sm-4 mb-15">
        <div class="dashboard-w1 bg--deep-purple b-radius--10 box-shadow">
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

    <div class="col-xl-2 col-lg-2 col-sm-4 mb-15">
        <div class="dashboard-w1 bg--pink b-radius--10 box-shadow">
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

    <div class="col-xl-2 col-lg-2 col-sm-4 mb-15">
        <div class="dashboard-w1 bg--5 b-radius--10 box-shadow">
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

    <div class="col-xl-2 col-lg-2 col-sm-4 mb-15">
        <div class="dashboard-w1 bg--14 b-radius--10 box-shadow">
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
</div><br>

            <div class="col-xl-12 mb-30">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">@lang('Total Visits - All Campaigns') (@lang('Last 30 Days'))</h5>
                        <div id="apex-line"></div>
                    </div>
                </div>
            </div>

<div class="row mb-none-30">
    <div class="col-xl-6 col-sm-6 mb-30">
        <a href="{{ route('user.web.history') }}">
            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                <i class="las la-shopping-cart overlay-icon text--primary"></i>
                <div class="widget-two__icon b-radius--5 bg--primary">
                    <i class="las la-shopping-cart"></i>
                </div>
                <div class="widget-two__content">
                    <h2 class="">{{$widget['total_order']}}</h2>
                    <p>@lang('Total')</p>
                </div>
            </div><!-- widget-two end -->
        </a>
    </div>
    <div class="col-xl-6 col-sm-6 mb-30">
        <a href="{{ route('user.web.pending') }}">
            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                <i class="las la-exclamation-triangle overlay-icon text--warning"></i>
                <div class="widget-two__icon b-radius--5 bg--warning">
                    <i class="las la-exclamation-triangle"></i>
                </div>
                <div class="widget-two__content">
                    <h2 class="">{{$widget['pending_order']}}</h2>
                    <p>@lang('Needs Action')</p>
                </div>
            </div><!-- widget-two end -->
        </a>
    </div>

    <div class="col-xl-6 col-sm-6 mb-30">
        <a href="{{ route('user.web.completed') }}">
            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                <i class="las la-check-circle overlay-icon text--green"></i>
                <div class="widget-two__icon b-radius--5 bg--green">
                    <i class="las la-check-circle"></i>
                </div>
                <div class="widget-two__content">
                    <h2 class="">{{$widget['completed_order']}}</h2>
                    <p>@lang('Completed')</p>
                </div>
            </div><!-- widget-two end -->
        </a>
    </div>

    <div class="col-xl-6 col-sm-6 mb-30">
        <a href="{{ route('user.web.processing') }}">
            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                <i class="la la-refresh overlay-icon text--teal"></i>
                <div class="widget-two__icon b-radius--5 bg--teal">
                    <i class="la la-refresh"></i>
                </div>
                <div class="widget-two__content">
                    <h2 class="">{{$widget['processing_order']}}</h2>
                    <p>@lang('Active')</p>
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
                        <h6 class="card-title font-weight-bold">A single Nano pack allows you to create a campaign with up to 6000 hits per month</h6>
                    </div>
                    <div class="form-group text-center"><small class="font-weight-bold text-danger">IMPORTANT: We don't allow 3rd level domains, shorteners, adfly or any similar services for free traffic campaigns,
                            neither can you use the domains that are already in our system, use only paid campaigns in this case.</small>
                    </div>
                </div>
                <div class="form-group text-center">
                    @if($widget['nano_exp'] < $widget['time']) <button type="submit" class="btn btn--primary">@lang('Get Now')</button>
                        @else
                        <label class="text--small badge font-weight-normal badge--danger">
                            Next Credit will be available after {{ showDateTime($widget['nano_exp']) }}
                        </label>
                        @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('breadcrumb-plugins')
@if($widget['nano'] == 0)
<div class="button" align="right">
    <input type="button" id="trial" value="Get Free Nano Credit" class="btn btn-sm btn--primary" data-bs-toggle="modal">
</div>
@else
@endif
@endpush

@push('script')
<script src="{{ asset('assets/spadmin/js/vendor/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/spadmin/js/vendor/chart.js.2.8.0.js') }}"></script>
<script>
    "use strict";
    //Show Trial modal
    $('#trial').on('click', function() {
        $('#trialModal').modal('show');
        $(document).ready(function() {
            $('.deposit').on('click', function() {
                var id = $(this).data('id');
                var nano = $(this).data('nano ');
            });
        });
    });
</script>
<script>
var options = {
  chart: {
    height: 450,
    type: "area",
    toolbar: {
      show: false
    },
    dropShadow: {
      enabled: true,
      enabledSeries: [0],
      top: -2,
      left: 0,
      blur: 10,
      opacity: 0.08
    },
    animations: {
      enabled: true,
      easing: 'linear',
      dynamicAnimation: {
        speed: 1000
      }
    },
  },
  dataLabels: {
    enabled: false
  },
  series: [
    {
      name: "Total Visits Delivered",
      data: @json($counts)
    },
  ],
  fill: {
    type: "gradient",
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.7,
      opacityTo: 0.9,
      stops: [0, 90, 100]
    }
  },
  xaxis: {
    categories: @json($dates)
  },
  grid: {
    padding: {
      left: 5,
      right: 5
    },
    xaxis: {
      lines: {
        show: false
      }
    },
    yaxis: {
      lines: {
        show: false
      }
    },
  },
};
var chart = new ApexCharts(document.querySelector("#apex-line"), options);
chart.render();
</script>

@endpush