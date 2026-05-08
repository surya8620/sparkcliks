@php
    $featureContent = getContent('features.content', true);
    $featureElements = getContent('features.element', orderById: true, limit: 8);
@endphp

<section class="features pt-85 pb-85">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-8">
                <div class="section-heading">
                    <span class="section-heading__subtitle"> {{ __(@$featureContent->data_values->section_title) }} </span>
                    <h2 class="section-heading__title" s-break="-2" s-color="bg--base-two text-white">{{ __(@$featureContent->data_values->heading) }}
                    </h2>
                </div>
            </div>
            <div class="col-lg-6 col-md-4">
                <div class="section-btn text-md-end">
                    <a href="{{ @$featureContent->data_values->button_link }}" class="btn btn--base"> {{ __(@$featureContent->data_values->button) }}
                    </a>
                </div>
            </div>
        </div>

        <div class="row gy-4">
            @foreach ($featureElements as $featureElement)
                <div class="col-xl-3 col-lg-4 col-sm-6">
                    <div class="features-item section-bg">
                        <div class="features-item__icon">
                            <img src="{{ frontendImage('features', @$featureElement->data_values->icon_image, '60x60') }}">
                        </div>
                        <h5 class="features-item__title"> {{ __(@$featureElement->data_values->title) }} </h5>
                        <p class="features-item__desc"> {{ __(@$featureElement->data_values->content) }} </p>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</section>
