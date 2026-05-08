@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="dashboard-table">
                @if (!blank($orders))
                    @include(@$activeTemplate . 'partials.seo_order_list')

                    @if ($orders->hasPages())
                        {{ paginateLinks($orders) }}
                    @endif
                @else
                    @include($activeTemplate . 'partials.empty', [
                        'message' => ' No Campaigns Found!',
                    ])
                @endif
            </div>

        </div>
    </div>

@endsection

@push('style')
    <style>
        .break_line {
            white-space: initial !important;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            font-size: 14px !important;
            padding-left: 0 !important;
            line-height: 1 !important;
        }

        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            padding: 0 !important;
            height: auto !important;
            border: 0;
        }

        .select2-container:has(.select2-selection--single, .select2-selection--multiple) {
            height: 21px;
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 0px !important;
            width: auto !important;
            ;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow:after {
            right: 0 !important;
            top: -3px !important;
        }
    </style>
@endpush

@push('style-lib')
    <link href="{{ asset('assets/global/css/datepicker.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/datepicker.en.js') }}"></script>
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            'use strict';

            $('.select2').select2();

            if (!$('.datepicker-here').val()) {
                $('.datepicker-here').datepicker();
            }

            $('[name=category_id], [name=status]').on('change', function() {
                $('.form').submit();
            })
        });
    </script>
@endpush
