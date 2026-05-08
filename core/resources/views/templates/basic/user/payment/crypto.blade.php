@extends($activeTemplate.'layouts.master')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-deposit text-center">
                    <div class="card-body card-body-deposit text-center">
                        <h4 class="my-2"> @lang('PLEASE SEND EXACTLY') <span class="text--success"> {{ $data->amount }}</span>
                            {{ __($data->currency) }}</h4>
                        <h5 class="mb-2">@lang('TO') <span class="text--success"> {{ $data->sendto }}</span></h5>
                        <img src="{{$data->img}}" alt="@lang('Image')">
                        <h6 class="mb-2 text--success">@lang('*You bear the network fee.')</h6>
                        <h5 class="mb-2 text--danger">@lang('PLEASE ENSURE YOU SEND THE EXACT AMOUNT OF CRYPTO AS RECEIVERS FUNDS.')</h5><br>
                        <h6 class="mb-2 text--danger">@lang('Please note that crypto payments may experience a delay in processing after being transferred. They usually take up to 20 minutes to process, but in some cases, it may take longer. Once the payment is successfully received, it will be automatically confirmed. Kindly refrain from attempting the transaction again until confirmation is received. Ensure that the funds are transferred within 60 minutes of initiation.')</h6><br>
                        <h6 class="mb-2 text--success">@lang('Track your transaction status')<a href="https://www.coinpayments.net/supwiz-buyer" class= "font-weight-bold" data-original-title="@lang(' here.')"> here</a></h6><br>
                        <h6 class="form-label font-weight-bold"><strong>@lang('Need Crypto? You can purchase USDT from')@lang(' BINANCE ')<br><a href="https://p2p.binance.com/en-IN/quickTrade/buy/USDT/" class="btn btn--primary btn-block custom-success deposit orderBtn font-weight-bold" data-original-title="@lang('Buy Crypto')">
                                                Buy Crypto
                                            </a>
                        <br>@lang('After purchasing, you can transfer the crypto to the above address.')</strong></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
