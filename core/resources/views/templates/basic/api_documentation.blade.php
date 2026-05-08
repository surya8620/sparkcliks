@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="ptb-80">
        <div class="container">
            <div class="bodywrapper__inner">
                <div class="row">
                    <div class="d-flex justify-content-end mb-3">
                        <a class="submit-btn text--small" href="{{ asset('assets/example.txt') }}" target="_blank">
                            <i class="las la-code"></i> @lang('Example PHP Code')</a>
                    </div>
                    <div class="col-lg-12">
                        <div class="api-docs">
                            <ul class="api-docs-list">
                                <li class="api-docs-list__item"> <strong class="text"> @lang('API URL') </strong> <span
                                        class="text">{{ route('api.v1') }}</span>
                                </li>
                                <li class="api-docs-list__item"> <strong class="text"> @lang('Response Format')</strong> <span
                                        class="text">@lang('JSON')</span>
                                </li>
                                <li class="api-docs-list__item"> <strong class="text"> @lang('HTTP Method')</strong> <span
                                        class="text"> @lang('POST')</span>
                                </li>
                                <li class="api-docs-list__item"> <strong class="text"> @lang('Api Key')</strong> <span
                                        class="text">@lang('Your api key')</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="faq-wrapper">

                            <div class="faq-item">
                                <h3 class="faq-title"><span class="title">@lang('Service List')
                                    </span><span class="right-icon"></span></h3>
                                <div class="faq-content">
                                    <div class="card">
                                        <div class="card-body">
                                            <b>@lang('Required parameters')</b>

                                            <div id="type_0">
                                                <ul>
                                                    <li><b width="20%">key</b> - @lang('Your API Key')</li>
                                                    <li><b>action</b> - "services"</li>
                                                </ul>
                                            </div>
                                            <br>
                                            <b>@lang('Success response') :</b>
                                            <pre>[
    {<em>
     "service": 1,
     "name": "YouTube Livestream Viewers ",
     "rate": "0.33000000",
     "min": 1000,
     "max": 200000,
     "category": "Live Stream [ Low ConCurrent | Less Price ] [ 30 Minutes to 24 Hours]"</em>
    },
    {<em>
      "service": 2,
      "name": "YouTube Livestream Viewers ~ ",
      "rate": "2.10000000",
      "min": 1000,
      "max": 200000,
      "category": "Live Stream [ Low ConCurrent | Less Price ] [ 30 Minutes to 24 Hours]" </em>
    }
]</pre>
                                            <br>
                                            <b>@lang('Error response') :</b>
                                            <pre>
{<em>"@lang('error')" : "@lang('The action field is required')" </em>}
{<em>"@lang('error')" : "@lang('The api key field is required')" </em>}
{<em>"@lang('error')" : "@lang('Invalid api key')" </em>}
{<em>"@lang('error')" : "@lang('Invalid action')" </em>}
</pre>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="faq-item">
                                <h3 class="faq-title"><span class="title">@lang('Place New Order')
                                    </span><span class="right-icon"></span></h3>
                                <div class="faq-content">

                                    <div class="card">
                                        <div class="card-body">
                                            <b>@lang('Required parameters')</b>

                                            <div id="type_0">
                                                <ul>
                                                    <li><b width="20%">key</b> - @lang('Your API Key')</li>
                                                    <li><b>action</b> - "add"</li>
                                                    <li><b>service</b> - @lang('Service ID')</li>
                                                    <li><b>link</b> - @lang('Link to page')</li>
                                                    <li><b>quantity</b> - @lang('Quantity to be delivered')</li>
                                                    <li><b>runs(optional) </b> - @lang('Runs to deliver')</li>
                                                    <li><b>interval(optional) </b> - @lang('Interval in minutes')</li>
                                                </ul>
                                            </div>
                                            <br>
                                            <b>@lang('Success response') :</b>
                                            <pre>
{<em>
  "order" : "1242"</em> 
}
</pre>

                                            <br>
                                            <b>@lang('Error response') :</b>
                                            <pre>
{<em>"@lang('error')" : "@lang('The action field is required')"</em>}
{<em>"@lang('error')" : "@lang('The api key field is required')"</em>}
{<em>"@lang('error')" : "@lang('Invalid api key')"</em>}
{<em>"@lang('error')" : "@lang('Invalid Service Id')"</em>}
{<em>"@lang('error')" : "@lang('The link field is required')"</em>}
{<em>"@lang('error')" : "@lang('The quantity field is required')"</em>}
{<em>"@lang('error')" : "@lang('Please follow the limit')"</em>}
{<em>"@lang('error')" : "@lang('Insufficient balance')"</em>}

</pre>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="faq-item">
                                <h3 class="faq-title"><span class="title">
                                        @lang('Order Status')
                                    </span><span class="right-icon"></span></h3>
                                <div class="faq-content">

                                    <div class="card">
                                        <div class="card-body">
                                            <b>@lang('Required parameters')</b>

                                            <div id="type_0">
                                                <ul>
                                                    <li><b width="20%">key</b> - @lang('Your API Key')</li>
                                                    <li><b>action</b> - "status"</li>
                                                    <li><b>order</b> - @lang('Order ID')</li>
                                                </ul>
                                            </div>
                                            <br>
                                            <b>@lang('Success response') :</b>
                                            <pre>
{<em>
  "status" : "Pending",
  "start_count" : "1000",
  "remains" : "500",
  "currency" : {{ gs()->cur_text }}
</em>
}</pre>
                                            <br>
                                            <b>@lang('Available status')</b>
                                            <ul>
                                                <li><span class="text--warning">Pending</span></li>
                                                <li><span class="text--info">Processing</span></li>
                                                <li><span class="text--success">Complete</span></li>
                                                <li><span class="text--danger">Order Cancelled</span></li>
                                                <li><span class="text--dark">Refunded</span></li>
                                            </ul>
                                            <br>
                                            <b>@lang('Error response') :</b>
                                            <pre>
{<em>"@lang('error')" : "@lang('The action field is required')"</em>}
{<em>"@lang('error')" : "@lang('The api key field is required')"</em>}
{<em>"@lang('error')" : "@lang('Invalid api key')"</em>}
{<em>"@lang('error')" : "@lang('Invalid request')"</em>}
{<em>"@lang('error')" : "@lang('The order field is required')"</em>}
{<em>"@lang('error')" : "@lang('Invalid Order Id')"</em>}
 </pre>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="faq-item">
                                <h3 class="faq-title"><span class="title">
                                        @lang('Get Balance')
                                    </span><span class="right-icon"></span></h3>
                                <div class="faq-content">

                                    <div class="card">
                                        <div class="card-body">
                                            <b>@lang('Required parameters')</b>

                                            <div id="type_0">
                                                <ul>
                                                    <li><b width="20%">key</b> - @lang('Your API Key')</li>
                                                    <li><b>action</b> - "balance"</li>

                                                </ul>
                                            </div>
                                            <br>
                                            <b>@lang('Success response') :</b>
                                            <pre>
{<em>"balance" : "100.84292"</em>
<em>"currency" : {{ gs()->cur_text }}</em>
}
  </pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
