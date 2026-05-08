@php
    $counterContent = getContent('counter.content', true);
    $counterElements = getContent('counter.element', false, limit: 4);
    $classes = ['sub text--primary-two', 'text--base-two', 'text--green-two', 'text--orange'];
@endphp

<div class="counter-area section-bg py-60">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="style-center mb-5 text-center">
                    <h2 class="section-heading__title" s-break-counter="-3">{{ __(@$counterContent->data_values->heading) }}</h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="row justify-content-center gy-4">
                    @foreach ($counterElements as $key => $counterElement)
                        <div class="col-lg-3 col-sm-6">
                            <div class="counterup-item">
                                <div class="counterup-item__icon">
                                    <i class="icon-user-1"></i>
                                </div>
                                <div class="counterup-item__content">
                                    <div class="counterup-item__number">
                                        <h2 class="counterup-item__title mb-0">
                                            <span class="odometer" data-odometer-final="{{ @$counterElement->data_values->number }}">0</span>

                                            <sup class="{{ $classes[$key] }}">{{ @$counterElement->data_values->abbreviation }}</sup>
                                        </h2>
                                    </div>
                                    <p class="counterup-item__text mb-0"> {{ __(@$counterElement->data_values->title) }} </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('style-lib')
    <link href="{{ asset($activeTemplateTrue . 'css/odometer.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/odometer.min.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            // counter up
            $(".counterup-item").each(function() {
                $(this).isInViewport(function(status) {
                    if (status === "entered") {
                        for (var i = 0; i < document.querySelectorAll(".odometer").length; i++) {
                            var el = document.querySelectorAll('.odometer')[i];
                            el.innerHTML = el.getAttribute("data-odometer-final");
                        }
                    }
                });
            });

        })(jQuery);
    </script>
@endpush
