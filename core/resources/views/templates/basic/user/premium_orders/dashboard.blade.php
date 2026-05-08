@extends($activeTemplate . 'layouts.app')
@section('panel')

@if($widget['premium_ack'] == 0)
<div class="container">
    <h1 class="text-center">Disclaimer</h1>
    <form method="POST" action="{{ route('user.premium.accept') }}">
        @csrf
        <div class="form-group">
            <label for="terms">Use at Your Own Risk:</label>
            <textarea class="form-control" rows="6" id="terms" disabled>
All information in the Service is provided “as is”, with no guarantee of completeness, accuracy, timeliness, or of the results obtained from the use of this information, and without warranty of any kind, express or implied, including, but not limited to warranties of performance, merchantability, and fitness for a particular purpose.

The Company will not be liable to You or anyone else for any decision made or action taken in reliance on the information given by the Service or for any consequential, special or similar damages, even if advised of the possibility of such damages.

By using this service, you will be liable for the Termination or Suspension of AdSense or any Ad Network Account Closure. 
            </textarea>
        </div>

        <div class="form-group">
            <label for="terms">No Responsibility Disclaimer</label>
            <textarea class="form-control" rows="4" id="terms" disabled>
In no event shall the Company be liable for any special, incidental, indirect, or consequential damages whatsoever arising out of or in connection with your access or use or inability to access or use the Service.
            </textarea>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="acceptTerms" name="acceptTerms" required>
            <label class="form-check-label" for="acceptTerms">
                I accept the disclaimer policy
            </label>
        </div>
        <button type="submit" class="btn btn--primary btn-center">Accept</button>
    </form>
</div>
@else
<div class="row mb-none-30">
    <div class="col-xl-6 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--primary b-radius--10 box-shadow ">
            <div class="icon">
                <i class="la la-coins"></i>
            </div>
            <div class="details">
                <div class="numbers">
                        <span class="amount">{{ getamount($widget['premium_credit']) }}</span>
                </div>
                <div class="desciption">
                    <span class="text--medium">@lang('Credits')</span>
                </div>
            </div>
        </div>
    </div><!-- dashboard-w1 end -->

    <div class="col-xl-6 col-lg-4 col-sm-6 mb-30">
    <a href="{{ route('user.premium.completed') }}">
        <div class="dashboard-w1 bg--red b-radius--10 box-shadow ">
            <div class="icon">
                <i class="las la-shopping-cart"></i>
            </div>
            <div class="details">
                <div class="numbers">
                        <span class="amount">{{$widget['total_order']}}</span>
                </div>
                <div class="desciption">
                    <span class="text--medium">@lang('Total Campaigns')</span>
                </div>
            </div>
        </div>
    </div><!-- dashboard-w1 end --></a>
</div><!-- row end-->
<br>
<div class="row mt-50 mb-none-30">
    <div class="col-xl-3 col-sm-6 mb-30">
        <a href="{{ route('user.premium.completed') }}">
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

    <div class="col-xl-3 col-sm-6 mb-30">
        <a href="{{ route('user.premium.processing') }}">
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

    <div class="col-xl-3 col-sm-6 mb-30">
        <a href="{{ route('user.premium.pending') }}">
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

    <div class="col-xl-3 col-sm-6 mb-30">
        <a href="{{ route('user.premium.cancelled') }}">
            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                <i class="las la-times-circle overlay-icon text--pink"></i>
                <div class="widget-two__icon b-radius--5 bg--secondary">
                    <i class="la la-times-circle"></i>
                </div>
                <div class="widget-two__content">
                <h2 class="">{{$widget['cancelled_order']}}</h2>
                    <p>@lang('Cancelled')</p>
                </div>
            </div><!-- widget-two end -->
        </a>
    </div>
<hr>
            <div class="col-xl-12 mb-30">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">@lang('Total Visits Delivered') (@lang('Last 30 Days'))</h5>
                        <div id="apex-line"></div>
                    </div>
                </div>
            </div>
</div>
<br>
<hr style="height:2px;border-width:0;color:gray;background-color:gray"><br>
<div class="modal fade" id="trial" tabindex="-1" role="dialog" aria-labelledby="trialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5 text-center" id="trialModalLabel"><strong>@lang('Activate Trial')</strong>
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
            </div>
            <form action="{{route('user.billing.insert')}}" method="post" class="resetForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group text-center">
                        <input type="hidden" name="currency" class="edit-currency" value="USD">
                        <input type="hidden" name="credits" class="edit-credits" value="1">
                        <input type="hidden" name="plans" class="edit-plans" value="27">
                        <input type="hidden" name="amount" class="edit-amount" value="2">
                    </div>
                    <div class="form-group text-center">
                        <h4><strong>@lang('Premium Traffic - Trial Pack')</strong></h4><br>
                        <h5><strong>@lang('1000 Real Visits - $2<br><br> Just $0.002/visit')</strong></h5><br>
                        <h5><strong>@lang('Real Traffic from Real People!')</strong></h5><br>
                        <p class="p-t-10"><strong>@lang('Choose a Payment Method')</strong></p><br>
                        <label>
                            <input type="radio" name="method_code" value="114" required="" checked>
                            <img src="{{ getImage(getFilePath('payments') . '/stripe.jpg') }}" alt="Stripe" width="120">
                        </label>
                        <label>
                            <input type="radio" name="method_code" value="110" required="">
                            <img src="{{ getImage(getFilePath('payments') . '/razorpay.jpg') }}" alt="RazorPay" width="120">
                        </label>
                    </div>
                </div>
                <div class="form-group text-center" id="credit_cards">
                    <img src="{{ getImage(getFilePath('payments') . '/cards-logos.png') }}" id="visa" width="150"><br><br>
                    <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="agree" value="1" required>
                        <span class="custom-control-label text-uppercase"><strong>I agree to comply with the <a href="https://www.sparkcliks.com/terms-of-use/">Terms of USE</a> and have read the <a href="https://www.sparkcliks.com/disclaimer/">Disclaimer</a>, <a href="https://www.sparkcliks.com/refund-policy/">Refund Policy</a>, <a href="https://www.sparkcliks.com/privacy-policy/">Privacy Policy</a> </strong></span>
                    </label>
                    <button type="submit" class="btn btn--primary" disabled>@lang('Buy Now')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('breadcrumb-plugins')
@if($widget['premium_trial'] == 0 && $widget['premium_ack'] == 1)
<button type="button" align="right" class="btn btn--primary" data-bs-toggle="modal" data-bs-target="#trial">
Activate Trial
</button>
@else
@endif
@endpush

@push('script')
<script src="{{ asset('assets/spadmin/js/vendor/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/spadmin/js/vendor/chart.js.2.8.0.js') }}"></script>
<script>
    "use strict";
    //Show Trial modal
    $(document).ready(function() {
    @if($widget['premium_trial'] == 0)
    $('#trialModal').modal('show');
    @endif
    });

    $('#trial').on('click', function() {
        $('#trialModal').modal('show');
        $(document).ready(function() {
            $('.deposit').on('click', function() {
                var id = $(this).data('id');
                var result = $(this).data('resource');
                var minAmount = $(this).data('min_amount');
                var maxAmount = $(this).data('max_amount');
                var baseSymbol = "{{__($general->cur_text)}}";
                var fixCharge = $(this).data('fix_charge');
                var plans = $(this).data('plans');
                var credits = $(this).data('credits');
                var percentCharge = $(this).data('percent_charge');

                var depositLimit = `@lang('Deposit Limit'): ${minAmount} - ${maxAmount}  ${baseSymbol}`;
                $('.depositLimit').text(depositLimit);
                var depositCharge = `@lang('Payment Gateway Charges'): ${fixCharge} ${baseSymbol}  ${(0 < percentCharge) ? ' + ' +percentCharge + ' % ' : ''}`;
                $('.depositCharge').text(depositCharge);
                $('.method-name').text(`@lang('Payment By ') ${result.name}`);
                $('.currency-addon').text(baseSymbol);
                $('.edit-currency').val(result.currency);
                $('.edit-method-code').val(result.method_code);
                $('#trialModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
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
      name: "Web Reports",
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