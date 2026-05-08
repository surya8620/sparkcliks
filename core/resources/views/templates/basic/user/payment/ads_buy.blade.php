@extends($activeTemplate . 'layouts.app')
@section('panel')
<div class="form-group  justify-content-center" id="gatewayModal">
            <div class="form-group">
                <div class="card card-deposit text-center">
                    <div class="card-body card-body-deposit">
                <form action="{{ route('user.billing.insert') }}" method="POST">
                @csrf
                    <div class="form-group text-center">
                    <h4 class="form-label font-weight-bold"><strong>@lang('Choose Your Payment Method')</strong></h4><hr>
                    <div class="form-check form-check-inline">
			<h4 class="form-label font-weight-bold"><img src="{{ getImage(getFilePath('payments') . '/cards-logos.png') }}" alt="Card Payments" width="200"></h4><hr>
                        <label>
                            <input type="radio" name="method_code" value="110" crx="USD" checked>
                            <img src="{{ getImage(getFilePath('payments') . '/razorpay.jpg') }}" alt="RazorPay" width="120">
                        </label>
                        <label>
                        <input type="radio" name="method_code" value="114" crx="USD">
                        <img src="{{ getImage(getFilePath('payments') . '/stripe.jpg') }}" alt="Stripe" width="120">
                        </label><br>
                        <h4 class="form-label font-weight-bold"><img src="{{ getImage(getFilePath('payments') . '/CoinPayments.jpg') }}" alt="CointPayments" width="200"></h4><hr>
                        <label>
                            <input type="radio" name="method_code" value="503" crx="USDC.TRC20">
                            <img src="{{ getImage(getFilePath('payments') . '/USDCTR20.png') }}" alt="USD Coin (USDC.TRC20)" width="120">
                        </label>
                        <label>
                            <input type="radio" name="method_code" value="503" crx="USDT.TRC20">
                            <img src="{{ getImage(getFilePath('payments') . '/USDTTR20.png') }}" alt="Tether USD (USDT.TRC20)" width="120">
                        </label>
                        </div>
                    </div>
                    <div class="form-group text-center" style="max-width:600px; margin:0 auto;">
			<input type="hidden" name="currency" class="edit-currency">
                        <input type="hidden" name="credits" class="edit-credits" value="">
                        <input type="hidden" name="amount" class="edit-amount" value="">          
                        <div class="table-responsive--sm table-responsive">
                            <p for="plans" class="font-weight-bold">@lang('Choose Your Pack')</p>
                            <div class="input-group mb-3">
                                    <span class="input-group">
                                        <select name="plans" id="plans" required="" class="form-control" placeholder="Select">
                                            <option>Choose Pack...</option>
                                            <option value="21">Unblocked Traffic - 25K Pack</option>
                                            <option value="22">Unblocked Traffic - 50K Pack</option>
                                            <option value="23">Unblocked Traffic - 100K Pack</option>
                                            <option value="24">Unblocked Traffic - 250K Pack</option>
                                            <option value="25">Unblocked Traffic - 500K Pack</option>
                                            <option value="26">Unblocked Traffic - 1000K Pack</option>
                                        </select>
                                    </span>
                            </div>    
                                <table class="table table--light style--two">
                                    <thead>
                                        <tr>
                                            <th scope="col">@lang('Details')</th>
                                            <th scope="col">@lang('Credits')</th>
                                            <th scope="col">@lang('Price')</th>
                                            <th scope="col">@lang('Total')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td data-label="@lang('Details')" class="text-left"><span id="pack_name"></span></td>
                                            <td data-label="@lang('Credit')" class="text-center"><span id="plan_credit"></span></td>
                                            <td data-label="@lang('Price')" class="text-center">$<span id="pack_price"></span></td>
                                            <td data-label="@lang('Total')">$<span id="plan_price"></span></td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>VAT <span id="cart_vat_rate">0</span>%</td>
                                            <td>$<span id="cart_vat_value">0</span></td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td><b>To Pay:</b></td>
                                            <td><b>$<span id="plan_price_total"></span></b></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div><br>
                            <p class="font-weight-bold text--warning"><strong>@lang('Please Note: 1 Credit = 1000 Worldwide Visits / 2 Credits = 1000 Geo Target Visits')</strong></p><br>
			<hr>
			<div class="alert alert-danger" role="alert" style="font-size: 12px;">
  				Please Note: Unblocked Traffic service is non-refundable. If you wish to try the service, please activate the Trial pack from the Unblocked Traffic Dashboard. Once you have subscribed, refunds will not be provided under any circumstances.
			<br><br>*CoinPayments are manually verifed and processed during Support hours.
			</div>
                        <div class="form-group">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="agree" value="1" required>
                                <span class="custom-control-label text-uppercase"><strong>I have read and agree to comply with the <a href="https://www.sparkcliks.com/terms-of-use/">Terms of USE</a>, <a href="https://www.sparkcliks.com/disclaimer/">DISCLAIMER</a>, <a href="https://www.sparkcliks.com/delivery-policy/">Delivery Policy</a>, <a href="https://www.sparkcliks.com/refund-policy/">Refund Policy</a>, and <a href="https://www.sparkcliks.com/privacy-policy/">Privacy Policy</a></strong></span>
                            </label>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn--primary" disabled="">@lang('Buy Now')</button>
                        </div>
                    </div>
                </form>   
            </div>
        </div>
    </div>
</div>
    <div class="modal fade" id="trialModal" tabindex="-1" role="dialog" aria-labelledby="trialModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title d-flex justify-content-center text-center" id="trialModalLabel" ><strong>@lang('Free Credit')</strong>
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{route('user.web.nano')}}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group text-center">
                        <h3 class="card-title font-weight-bold">Nano Credit</h3>
                            <input type="hidden" name="nano" class="edit-amount" value="1"><br>
			    <h6 class="card-title font-weight-bold">A single Nano pack allows you to create a campaign with up to 6000 hits per month</h6>
                        </div>
                    <div class="form-group text-center"><small class="font-weight-bold text-danger">IMPORTANT: We don't allow 3rd level domains, adult sites, shorteners, adfly or any similar services for free traffic campaigns,
                            neither can you use the domains that are already in our system, use only paid campaigns in this case.</small>
                    </div>
                    </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn--primary">@lang('Buy Now')</button>
                        </div>
                </form>
            </div>
        </div>
    </div>

        
@stop

@push('script')
    <script>
        "use strict";

        $(document).ready(function(){
            $('.deposit').on('click', function () {
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
            });
            $('select').on("change", function() {
                var modal = $('#gatewayModal');
                    var planSelected = $('#plans').val()                
                    const planPriceMapping = {
                        '21':18.99,
                        '22':35.99,
                        '23':67.99,
                        '24':159.99,
                        '25':299.99,
                        '26':559.99
                    }
                    var total_price = planPriceMapping[planSelected] || ''
                    modal.find('input[name=amount]').val(total_price);
                    document.getElementById("plan_price").innerHTML = total_price;
                    document.getElementById("plan_price_total").innerHTML = total_price;
                    const planCreditMapping = {
                        '21':25,
                        '22':50,
                        '23':100,
                        '24':250,
                        '25':500,
                        '26':1000,
                }
                    var planCredits = planCreditMapping[planSelected] || ''
                    modal.find('input[name=credits]').val(planCredits);
                    document.getElementById("plan_credit").innerHTML = planCredits;
                    const creditMapping = {
                        '21':19.99,
                        '22':39.99,
                        '23':79.99,
                        '24':199.99,
                        '25':399.99,
                        '26':799.99
                    }
                    var packCredits = creditMapping[planSelected] || ''
                    modal.find('input[name=price_per_credit]').val(packCredits);
                    document.getElementById("pack_price").innerHTML = packCredits;
                    const planMapping = {
                        '21':'Unblocked Traffic - 25K + 5% OFF',
                        '22':'Unblocked Traffic - 50K + 10% OFF',
                        '23':'Unblocked Traffic - 100K + 15% OFF',
                        '24':'Unblocked Traffic - 250K + 20% OFF',
                        '25':'Unblocked Traffic - 500K + 25% OFF',
                        '26':'Unblocked Traffic - 1000K + 30% OFF',
                    }
                    var packName = planMapping[planSelected] || ''
                    document.getElementById("pack_name").innerHTML = packName;
                });
        });
        //Show Trial modal
        $('#trial').on('click', function() {
        $('#trialModal').modal('show');
        $(document).ready(function(){
            $('.deposit').on('click', function () {
                var id = $(this).data('id');
                var nano = $(this).data('nano ');
		});
            });
        });

    </script>
<script>
    // Function to set the currency value based on the crx attribute of the selected radio button
    function setCurrency() {
        var selectedRadio = document.querySelector('input[name="method_code"]:checked');
        var currencyValue = selectedRadio.getAttribute('crx');

        // Set the value of the hidden input field with class "edit-currency"
        document.querySelector('.edit-currency').value = currencyValue;
    }

    // Call the function initially to set the currency value based on the default checked radio button
    setCurrency();

    // Add event listener to all radio buttons with name "method_code" to dynamically update the currency value
    document.querySelectorAll('input[name="method_code"]').forEach(function(radio) {
        radio.addEventListener('change', setCurrency);
    });
</script>

@endpush
