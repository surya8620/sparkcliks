@extends($activeTemplate . 'layouts.master')

@section('content')
<div class="pt-60 pb-60 bg--light">
    <div class="invoice-container">

        @include('partials.invoice')

    </div>
</div>
@endsection

@push('style')
    <style>
        .card {
            border: none;
        }
        .card .table thead tr {
            border: 1px solid hsl(var(--dark));
        }
        .table thead tr {
            background: none;
        }
        .btn--dark:hover {
            background: hsl(var(--dark)) !important;
            color: hsl(var(--white)) !important;
        }
        :disabled {
            cursor: no-drop;
        }
        .invoice-container {
            margin: 15px auto;
            padding: 70px;
            max-width: 850px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .invoice-container td.total-row {
            background-color: #f8f8f8;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }
        .invoice-container .invoice-status {
            margin: 20px 0 0 0;
            text-transform: uppercase;
            font-size: 24px;
            font-weight: 700;
        }
        @media (max-width: 767px) {
            .invoice-container {
            padding: 20px;
        }
        @media (max-width: 575px) {
            .invoice-container {
            padding: 10px;
        }
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.gateway').on('change', function() {
                var gateway = $(this).val();

                var resource = $('select[name=payment] option:selected').data('gateway');

                if (gateway == 'wallet') {
                    $('.payBtn').prop('disabled', false);
                } else if (gateway && gateway != 'wallet') {
                    $('input[name=currency]').val(resource.currency);
                    $('input[name=method_code]').val(resource.method_code);
                    $('.payBtn').prop('disabled', false);
                } else {
                    $('.payBtn').prop('disabled', true);
                }
            });
        })(jQuery);
    </script>
@endpush
