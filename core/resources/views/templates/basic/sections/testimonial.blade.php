@php
    $testimonialContent = getContent('testimonial.content', true);
    $testimonialElements = getContent('testimonial.element');
    $testimonialElements = $testimonialElements->chunk(round($testimonialElements->count() / 2));
@endphp


<section class="testimonials pt-170 pb-85">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="section-heading style-center text-center">
                    <span class="section-heading__subtitle"> {{ __(@$testimonialContent->data_values->section_title) }}
                    </span>
                    <h2 class="section-heading__title" s-break="-2" s-color="bg--base text-white">{{ __(@$testimonialContent->data_values->heading) }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid g-0">
        @foreach ($testimonialElements as $testimonialElement)
            <div class="@if ($loop->first) testimonial-slider @else testimonial-slider-rtl @endif"
                @if (!$loop->first) dir="rtl" @endif>
                @foreach ($testimonialElement as $testimonialElementLtr)
                    <div class="testimonails-card">
                        <div class="testimonial-item">
                            <div class="testimonial-item__content">
                                <div class="testimonial-item__rating">
                                    @php
                                        $review = @$testimonialElementLtr->data_values->review;
                                        $noReview = 5 - $review;
                                    @endphp
                                    <ul class="rating-list">
                                        @for ($i = 1; $i <= $review; $i++)
                                            <li class="rating-list__item"><i class="fas fa-star"></i></li>
                                        @endfor

                                        @for ($i = 1; $i <= $noReview; $i++)
                                            <li class="rating-list__item"><i class="far fa-star"></i></li>
                                        @endfor
                                    </ul>
                                </div>
                                <p class="testimonial-item__desc">
                                    “{{ __(@$testimonialElementLtr->data_values->comment) }}”</p>
                                <div class="testimonial-item__info">
                                    <div class="testimonial-item__thumb">
                                        <img class="fit-image"
                                            src="{{ frontendImage('testimonial', @$testimonialElementLtr->data_values->image, '60x60') }}"
                                            alt="testimonial image">
                                    </div>
                                    <div class="testimonial-item__details">
                                        <h5 class="testimonial-item__name">
                                            {{ __(@$testimonialElementLtr->data_values->name) }} </h5>
                                        <span class="testimonial-item__designation">
                                            {{ __(@$testimonialElementLtr->data_values->designation) }} </span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</section>

@push('script')
    <script>
        (function($) {
            "use strict";



            // testimonial slider js
            $(".testimonial-slider").slick({
                dots: false,
                arrows: false,
                infinite: false,
                slidesToShow: 3,
                slidesToScroll: 1,
                speed: 12000,
                cssEase: "linear",
                autoplay: true,
                autoplaySpeed: 0,
                adaptiveHeight: false,
                autoplay: true,
                pauseOnDotsHover: false,
                pauseOnHover: true,
                pauseOnFocus: true,
                responsive: [{
                        breakpoint: 1400,
                        settings: {
                            slidesToShow: 3,
                        },
                    },
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 2,
                        },
                    },
                    {
                        breakpoint: 576,
                        settings: {
                            slidesToShow: 1,
                        },
                    },
                ],
            });

            // testimonial slider js
            $(".testimonial-slider-rtl").slick({
                dots: false,
                arrows: false,
                slidesToShow: 3,
                slidesToScroll: 1,
                speed: 12000,
                cssEase: "linear",
                autoplaySpeed: 0,
                autoplay: true,
                pauseOnHover: true,
                pauseOnFocus: true,
                infinite: true,
                rtl: true,
                responsive: [{
                        breakpoint: 1400,
                        settings: {
                            slidesToShow: 3,
                        },
                    },
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 2,
                        },
                    },
                    {
                        breakpoint: 576,
                        settings: {
                            slidesToShow: 1,
                        },
                    },
                ],
            });

        })(jQuery);
    </script>
@endpush


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
