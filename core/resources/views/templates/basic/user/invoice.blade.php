@extends($activeTemplate . 'layouts.master')

@section('content')
<div class="bg--light">
    <div class="container">
        <div class="dashboard-table__header d-flex justify-content-end pt-0 px-0">
            <div class="dashboard-table__btn">
                <button class="btn btn--sm btn--base" id="downloadPdf" data-trx="{{ $invoice->trx }}"> @lang('Download')</a>
            </div>
        </div>
    </div>
    <div class="invoice-container">

        @include('partials.invoice')

    </div>
</div>
@endsection

@push('style')
<style>
    .ribbon-wrapper {
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        overflow: hidden;
        z-index: 10; /* Ensure it appears above other elements */
    }

    .ribbon {
        position: absolute;
        top: 10px;
        /* Ensures it stays inside the container */
        right: -40px;
        /* Moves it inward to align properly */
        background: green;
        color: white;
        width: 150px;
        text-align: center;
        padding: 10px 0;
        font-size: 14px;
        font-weight: bold;
        transform: rotate(45deg);
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
    }

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    document.getElementById('downloadPdf').addEventListener('click', function() {
        var element = document.querySelector('.invoice-container'); // Select only the invoice part

        // Get the transaction ID from the button's data attribute
        var trx = this.getAttribute('data-trx');

        // Ensure full invoice is visible before conversion
        element.style.display = 'block';
        element.style.transform = 'scale(0.95)'; // Reduce size by 20%
        element.style.transformOrigin = 'top left'; // Keep it aligned properly

        html2pdf().set({
            margin: [5, 5, 5, 5], // Reduce margins for better fit
            filename: 'invoice_' + trx + '.pdf',
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            html2canvas: {
                scale: 3, // Increase scale for better resolution
                useCORS: true, // Enable cross-origin image loading
                logging: true, // Debugging
                scrollY: 0 // Prevent cropping issue
            },
            jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: 'portrait'
            }
        }).from(element).save();
    });
</script>
@endpush