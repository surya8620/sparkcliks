@extends($activeTemplate . 'layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>

                                <tr>
                                    <th scope="row">@lang('API URL')</th>
                                    <td>{{ route('api.v1') }}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th scope="row">@lang('Response format')</th>
                                    <td>@lang('JSON')</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th scope="row">@lang('HTTP Method')</th>
                                    <td>@lang('POST')</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th scope="row">@lang('Your API key')</th>
                                    <td>{{ auth()->user()->api_key }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline--primary confirmationBtn" data-question="@lang('Your current api key will removed. Are you sure to generate new api key?')" data-action="{{ route('user.api.generateKey') }}">
                                            @lang('Generate New Key')
                                        </button>
                                    </td>
                                </tr>

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
            </div><!-- card end -->
        </div>
        <div class="col-lg-12 mb-30">
            <div class="accordion cmn-accordion" id="accordionExample">
                <div class="card">
                    <div class="card-header" id="headingOne">
                        <button class="acc-btn collapsed" data-bs-toggle="collapse" data-bs-target="#collapseOne" type="button" aria-expanded="false" aria-controls="collapseOne">
                            <span class="text">@lang('Service List')</span>
                            <span class="plus-icon"></span>
                        </button>
                    </div>

                    <div class="collapse" id="collapseOne" data-parent="#accordionExample" aria-labelledby="headingOne">
                        <div class="card-body">
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
{<em>"@lang('error')" : "@lang('Invalid action')"</em>}

</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="headingTwo">
                        <button class="acc-btn collapsed" data-bs-toggle="collapse" data-bs-target="#collapseTwo" type="button" aria-expanded="false" aria-controls="collapseTwo">
                            <span class="text">@lang('Place New Order')</span>
                            <span class="plus-icon"></span>
                        </button>
                    </div>
                    <div class="collapse" id="collapseTwo" data-parent="#accordionExample" aria-labelledby="headingTwo">
                        <div class="card-body">
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
  "order" : 1242</em> 
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
                </div>
                <div class="card">
                    <div class="card-header" id="headingThree">
                        <button class="acc-btn collapsed" data-bs-toggle="collapse" data-bs-target="#collapseThree" type="button" aria-expanded="false" aria-controls="collapseThree">
                            <span class="text">@lang('Order Status')</span>
                            <span class="plus-icon"></span>
                        </button>
                    </div>
                    <div class="collapse" id="collapseThree" data-parent="#accordionExample" aria-labelledby="headingThree">
                        <div class="card-body">
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
{<em>"@lang('error')" : "@lang('Invalid action')"</em>}
{<em>"@lang('error')" : "@lang('The order field is required')"</em>}
{<em>"@lang('error')" : "@lang('Invalid Order Id')"</em>}
 </pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="headingSix">
                        <button class="acc-btn collapsed" data-bs-toggle="collapse" data-bs-target="#collapseSix" type="button" aria-expanded="false" aria-controls="collapseSix">
                            <span class="text">@lang('User Balance')</span>
                            <span class="plus-icon"></span>
                        </button>
                    </div>
                    <div class="collapse" id="collapseSix" data-parent="#accordionExample" aria-labelledby="headingSix">
                        <div class="card-body">
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
{
    <em> "status": "100.84292"</em>
    <em>"currency" :" {{ gs()->cur_text }}"</em>
}
  </pre>
                                    <b>@lang('Error response') :</b>
                                    <pre>
{<em>"@lang('error')" : "@lang('The action field is required')" </em>}
{<em>"@lang('error')" : "@lang('The api key field is required')" </em>}
{<em>"@lang('error')" : "@lang('Invalid api key')" </em>}
 </pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a class="btn btn-sm btn-outline--primary text--small" href="{{ asset('assets/example.txt') }}" target="_blank">
        <i class="las la-code"></i>
        @lang('Example PHP Code')</a>
@endpush
