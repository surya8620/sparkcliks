@extends($activeTemplate . 'layouts.app')
@section('panel')
    <div class="dashboard position-relative">
        <div class="dashboard__inner flex-wrap">
            @include($activeTemplate . 'partials.dashboard_sidebar')
            <div class="dashboard__right">
                @include($activeTemplate . 'partials.dashboard_header')
                <div class="dashboard-body">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('form').on('submit', function() {
                if ($(this).valid()) {
                    $(':submit', this).attr('disabled', 'disabled');
                }
            });

            var inputElements = $('[type=text],[type=password],select,textarea');
            $.each(inputElements, function(index, element) {
                element = $(element);
                if (element.hasClass('exclude')) {
                    return false;
                }
                element.closest('.form-group').find('label').attr('for', element.attr('name'));
                element.attr('id', element.attr('name'))
            });

            $.each($('input:not([type=checkbox]):not([type=hidden]), select, textarea'), function(i, element) {

                if (element.hasAttribute('required')) {
                    $(element).closest('.form-group').find('label').addClass('required');
                }

            });

            $('.showFilterBtn').on('click', function() {
                $('.responsive-filter-card').slideToggle();
            });

            Array.from(document.querySelectorAll('table')).forEach(table => {
                let heading = table.querySelectorAll('thead tr th');
                Array.from(table.querySelectorAll('tbody tr')).forEach((row) => {
                    Array.from(row.querySelectorAll('td')).forEach((colum, i) => {
                        colum.setAttribute('data-label', heading[i].innerText)
                    });
                });
            });

            let disableSubmission = false;
            $('.disableSubmission').on('submit', function(e) {
                if (disableSubmission) {
                    e.preventDefault()
                } else {
                    disableSubmission = true;
                }
            });

            $.each($('.select2'), function() {
                $(this)
                    .wrap(`<div class="position-relative"></div>`)
                    .select2({
                        width: "100%",
                        dropdownParent: $(this).parent()
                    });
            });


        })(jQuery);
    </script>
@endpush
