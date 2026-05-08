@php
    $brandElements = getContent('brand.element');
@endphp

<div class="client section-bg py-60">
    <div class="container">
        <div class="client-logos client-slider">
            @foreach ($brandElements as $brandElement)
                <img src="{{ frontendImage('brand', @$brandElement->data_values->image, '140x40') }}" alt="brand image">
            @endforeach
        </div>
    </div>
</div>

@if (!app()->offsetExists('slick_style'))
    @push('style-lib')
        <link href="{{ asset($activeTemplateTrue . 'css/slick.css') }}" rel="stylesheet">
    @endpush
    @php app()->offsetSet('slick_style',true) @endphp
@endif

@if (!app()->offsetExists('slick_script'))
    @push('script-lib')
        <script src="{{ asset($activeTemplateTrue . 'js/slick.min.js') }}"></script>
    @endpush
    @php app()->offsetSet('slick_script',true) @endphp
@endif

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.client-slider').slick({
                slidesToShow: 7,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 1000,
                pauseOnHover: true,
                speed: 2000,
                dots: false,
                arrows: false,
                prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-long-arrow-alt-left"></i></button>',
                nextArrow: '<button type="button" class="slick-next"><i class="fas fa-long-arrow-alt-right"></i></button>',
                responsive: [{
                        breakpoint: 1199,
                        settings: {
                            slidesToShow: 6,
                        }
                    },
                    {
                        breakpoint: 991,
                        settings: {
                            slidesToShow: 5
                        }
                    },
                    {
                        breakpoint: 767,
                        settings: {
                            slidesToShow: 4
                        }
                    },
                    {
                        breakpoint: 400,
                        settings: {
                            slidesToShow: 3
                        }
                    }
                ]
            });

        })(jQuery);
    </script>
@endpush
