<style>
    @page {
        size: 8.27in 11.7in;
        margin: .5in;
    }

    body {
        font-family: "Maven Pro", sans-serif;
        font-size: 14px;
        line-height: 1.5;
        color: #023047;
    }

    /* Typography */
    .strong {
        font-weight: 700;
    }

    .fw-md {
        font-weight: 500;
    }

    .text-base {
        color: #{{ $general->base_color }};
    }

    .bg-base {
        background: #{{ $general->base_color }};
    }

    h1,
    .h1 {
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 67px;
        line-height: 1.2;
        font-weight: 500;
    }

    h2,
    .h2 {
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 50px;
        line-height: 1.2;
        font-weight: 500;
    }

    h3,
    .h3 {
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 38px;
        line-height: 1.2;
        font-weight: 500;
    }

    h4,
    .h4 {
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 28px;
        line-height: 1.2;
        font-weight: 500;
    }

    h5,
    .h5 {
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 20px;
        line-height: 1.2;
        font-weight: 500;
    }

    h6,
    .h6 {
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 16px;
        line-height: 1.2;
        font-weight: 500;
    }

    .text-uppercase {
        text-transform: uppercase;
    }

    .text-end {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    /* List Style */
    ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    /* Utilities */
    .d-block {
        display: block;
    }

    .mt-0 {
        margin-top: 0;
    }

    .m-0 {
        margin: 0;
    }

    .mt-3 {
        margin-top: 16px;
    }

    .mt-4 {
        margin-top: 24px;
    }

    .mb-3 {
        margin-bottom: 16px;
    }

    /* Title */
    .title {
        display: inline-block;
        letter-spacing: 0.05em;
    }

    /* Table Style */
    table {
        width: 7.27in;
        caption-side: bottom;
        border-collapse: collapse;
        border: 1px solid #eafbff;
        color: #023047;
        vertical-align: top;
    }

    table td {
        padding: 5px 15px;
    }

    table th {
        padding: 5px 15px;
    }

    table th:last-child {
        text-align: right !important;
    }

    .table> :not(caption)>*>* {
        padding: 12px 24px;
        background-color: #023047;
        border-bottom-width: 1px;
        box-shadow: inset 0 0 0 9999px #023047;
    }

    .table>tbody {
        vertical-align: inherit;
        border: 1px solid #eafbff;
    }

    .table>thead {
        vertical-align: bottom;
        background: #219ebc;
        color: white;
    }

    .table>thead th {
        text-align: left;
        font-size: 16px;
        letter-spacing: 0.03em;
        font-weight: 500;
    }

    .table td:last-child {
        text-align: right;
    }

    .table th:last-child {
        text-align: right;
    }

    .table> :not(:first-child) {
        border-top: 0;
    }

    .table-sm> :not(caption)>*>* {
        padding: 5px;
    }

    .table-bordered> :not(caption)>* {
        border-width: 1px 0;
    }

    .table-bordered> :not(caption)>*>* {
        border-width: 0 1px;
    }

    .table-borderless> :not(caption)>*>* {
        border-bottom-width: 0;
    }

    .table-borderless> :not(:first-child) {
        border-top-width: 0;
    }

    .table-striped>tbody>tr:nth-of-type(even)>* {
        background: #eafbff;
    }

    .mt-30 {
        margin-top: 30px;
    }

    .text-danger {
        color: red;
    }

    .text-success {
        color: green;
    }

    /* Logo */

    .logo-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .info {
        display: flex;
        justify-content: space-between;
        padding-top: 15px;
        padding-bottom: 15px;
        border-top: 1px solid #023047;
        border-bottom: 1px solid #023047;
    }

    .address {
        padding-top: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #023047;
    }

    header {
        padding-top: 15px;
        padding-bottom: 15px;
    }

    .body {
        padding-top: 30px;
        padding-bottom: 30px;
    }

    footer {
        padding-bottom: 15px;
    }

    .badge {
        display: inline-block;
        padding: 3px 15px;
        font-size: 10px;
        line-height: 1;
        border-radius: 15px;
    }

    .badge--success {
        color: white;
        background: #02c39a;
    }

    .badge--warning {
        color: white;
        background: #ffb703;
    }

    .align-items-center {
        align-items: center;
    }

    .footer-link {
        text-decoration: none;
        color: #219ebc;
    }

    .footer-link:hover {
        text-decoration: none;
        color: #219ebc;
    }

    .list--row {
        overflow: auto
    }

    .list--row::after {
        content: '';
        display: block;
        clear: both;
    }

    .float-left {
        float: left;
    }

    .float-right {
        float: right;
    }

    .d-block {
        display: block;
    }

    .d-inline-block {
        display: inline-block;
    }

    .table tbody tr td {
        font-family: ui-monospace;
    }

    /* //////////////////////////// */

    .table {
        border-color: #dee2e670;
    }

    .table>thead {
        vertical-align: bottom;
        background-color: hsl(var(--base)) !important;
        color: white;
    }

    .table> :not(caption)>*>* {
        background-color: transparent !important;
        box-shadow: none !important;
    }

    .table tbody tr td {
        border-width: 0;
        font-family: ui-monospace;
    }

    .table thead tr th {
        padding: 10px 15px;
    }

    .border--top {
        border-top: 1px solid #dee2e670;
    }

    .table tbody tr td {
        padding: 12px 15px;
    }

    .text-center {
        align-items: center !important;
    }

    .logo img {
        width: 165px;
        height: 35px;
    }

    @media (max-width: 575px) {
        .logo img {
            height: 30px;
        }
    }

    .badge--danger {
        background-color: rgba(234, 84, 85, 0.1);
        border: 1px solid #ea5455;
        color: #ea5455;
    }

    .badge--success {
        background-color: rgba(40, 199, 111, 0.1);
        border: 1px solid #28c76f;
        color: #28c76f;
    }

    .badge--warning {
        background-color: rgba(255, 159, 67, 0.1);
        border: 1px solid #ff9f43;
        color: #ff9f43;
    }

    .badge--dark {
        background-color: rgba(0, 0, 0, 0.1);
        border: 1px solid #000000;
        color: #000000;
    }

    .bg--dark {
        background-color: #081f30 !important;
        color: #fff;
        font-weight: 700;
    }

    tr.even {
        background-color: #{{ $general->base_color }}08 !important;
    }

    .table tbody tr:nth-child(even) {
        background: unset;
    }

    td:nth-child(2) {
        max-width: 200px !important;
    }

    .body {
        overflow-x: auto;
    }

    .custom-left-align {
        text-align: left !important;
    }
</style>

<header>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="list--row">
                    <div class="logo float-left">
                        <img src="{{ siteLogo() }}" style="width: 20%; height: 10%;" alt="SparkCliks"
                            class="sidebar-logo__link" />
                    </div>
                    <div class="address-form float-left">
                        <ul class="text">
                            <li>
                                <h5 class="primary-text d-block fw-md text-base fw-bold">@lang('SPARKCLIKS')</h5>
                            </li>
                            <li>
                                <span class="strong">@lang('No.1, 2nd Cross,')</span>
                            </li>
                            <li>
                                <span class="strong">@lang('Chikkanna Layout, Hennur,')</span>
                            </li>
                            <li>
                                <span class="strong">@lang('Bengaluru, Karnataka, IN 560043')</span>
                            </li>
                            <li>
                                <span class="strong">@lang('GSTIN: 29GAOPS7068N1ZC')</span>
                            </li>
                        </ul>
                    </div>
                    <div class="address-form float-right">
                        <ul class="text-end">
                            <li>
                                <h3 class="primary-text d-block fw-md text-base">@lang('TAX INVOICE')</h3>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<main>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="info list--row d-block">
                    <div class="info-left float-left">
                        <div class="list list--row">
                            <span class="strong">@lang('Invoice #') :</span>
                            <span>{{ $invoice->getInvoiceNumber }}</span>
                        </div>
                        <div class="list list--row">
                            <span class="strong">@lang('Invoice Date') :</span>
                            <span> {{ showDateTime($invoice->created_at, 'd/m/Y') }} </span>
                        </div>
                        <div class="list list--row">
                            <span class="strong">@lang('Transaction #') :</span>
                            <span> {{ $invoice->trx }} </span>
                        </div>
                        <div class="list list--row">
                            <span class="strong">@lang('VAT/GST Number') :</span>
                            <span> {{ $invoice->vat_num }} </span>
                        </div>
                        <div class="list list--row">
                            <span class="strong">@lang('Company Name') :</span>
                            <span> {{ $invoice->company }} </span>
                        </div>
                    </div>
                    <div class="info-right float-right">
                        <div class="list list--row">
                            <span class="strong">@lang('Invoice') :</span>
                            <span>{{ $invoice->getInvoiceNumber }}</span>
                        </div>
                    </div>
                </div>
                <div class="address list--row">
                    <div class="address-to float-left">
                        <span class="text-base d-block fw-md">@lang('BILLED TO')</span>
                        <h6 class="text-uppercase fw-bold">{{ __(@$user->fullname) }}</h6>
                        <ul class="list" style="--gap: 0.3rem">
                            <li>
                                <div class="list list--row" style="--gap: 0.5rem">
                                    <span>{{ $address['address'] ?? __('') }}</span><br>
                                </div>
                            </li>
                            <li>
                                <div class="list list--row" style="--gap: 0.5rem">
                                    <span>{{ $address['city'] ?? __('') }}</span>,
                                    <span>{{ $address['zip'] ?? __('') }}</span>
                                </div>
                            </li>
                            <li>
                                <div class="list list--row" style="--gap: 0.5rem">
                                    <span>{{ $address['state'] ?? __('') }}</span>
                                </div>
                            </li>
                            <li>
                                <div class="list list--row" style="--gap: 0.5rem">
                                    <span>{{ $address['country'] ?? __('') }}</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="m-0 float-right text-center">
                        @php echo @$invoice->showStatus; @endphp
                        @if ($invoice->status == 2)
                            <div class="mt-2">
                                @lang('Due Date'): {{ showDateTime($invoice->due_date, 'd/m/Y') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="body">
                    <div class="text-center mt-2 mb-3">
                        <div class="title-inset">
                            <h6 class="title m-0 text-uppercase">@lang('Items')</h6>
                        </div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr class="bg-base">
                                <th class="text-white">@lang('Description')</th>
                                <th></th>
                                <th class="text-white text-center">@lang('Credits')</th>
                                <th class="text-white text-center">@lang('Price')</th>
                                <th class="text-white text-center">@lang('Discount')</th>
                                <th class="text-white text-end"> @lang('Amount')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-base">
                                <td class="fw-bold custom-left-align">{{ __(@$invoice->plan_name) }}</td>
                                <td></td>
                                <td class="fw-bold text-center">{{ $invoice->credits }}</td>
                                <td class="fw-bold text-center">{{ showAmt2($invoice->price / $invoice->credits) }}
                                </td>
                                <td class="fw-bold text-center">{{ number_format($invoice->discount_percentage, 2) }}%
                                </td>
                                <td class="fw-bold text-end">{{ showAmt2($invoice->amount) }}</td>
                            </tr>
                            <tr class="text-base">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="fw-bold text-end text-uppercase fs-6">
                                    <small>@lang('Discount:')</small>
                                    <br>
                                    <small>@lang('Processing Fee:')</small>
                                    <br>
                                    <small>@lang('GST:')</small>
                                    <br>
                                    <span class="d-block mt-1 fs-6">@lang('Total:')</span>
                                </td>
                                <td colspan="2">
                                    <small>{{ showAmt2($invoice->discount_amount) }}</small>
                                    <br>
                                    <small>{{ showAmt2($invoice->charge) }}</small>
                                    <br>
                                    <small>{{ showAmt2($invoice->vat) }}</small>
                                    <br>
                                    <span
                                        class="h6 fw-bold fs-6">{{ showAmt2($invoice->amount + $invoice->charge + $invoice->vat) }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>


                    <table class="table table-striped mt-30">
                        <thead>
                            <tr class="bg-base">
                                <td class="text-white custom-left-align">@lang('Transaction Date')</td>
                                <td class="text-white">@lang('Gateway')</td>
                                <td class="text-white">@lang('Payment ID')</td>
                                <td class="text-white">@lang('Amount')</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-base">{{ showDateTime($invoice->created_at, 'd/m/Y') }}</td>
                                <td>{{ __(@$invoice->gateway->name) }}</td>
                                <td>{{ $invoice->payment_id ?? '' }}</td>
                                <td>
                                    {{ showAmt2(@$invoice->final_amo) }} {{ __($invoice->method_currency) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
