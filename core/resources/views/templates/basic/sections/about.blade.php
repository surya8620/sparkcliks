@php
    $aboutContent = getContent('about.content', true);
    $aboutElements = getContent('about.element', orderById: true, limit: 4);
@endphp

<section class="about pt-170 pb-85">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-6">
                <div class="section-heading mb-0">
                    <span class="section-heading__subtitle"> {{ __(@$aboutContent->data_values->section_title) }} </span>
                    <h2 class="section-heading__title" s-break='4' s-color="bg--yellow">{{ __(@$aboutContent->data_values->heading) }}</h2>
                    <p class="section-heading__desc"> {{ __(@$aboutContent->data_values->subheading) }} </p>
                </div>
                <div class="about__content mt-4">
                    <div class="row">
                        @foreach ($aboutElements as $aboutElement)
                            <div class="col-sm-6">
                                <div class="about-item">
                                    <div class="about-item__icon">
                                        <img src="{{ frontendImage('about', @$aboutElement->data_values->icon_image, '40x40') }}">
                                    </div>
                                    <h5 class="about-item__title">
                                        {{ __(@$aboutElement->data_values->title) }}
                                    </h5>
                                    <p class="about-item__subtitle">
                                        {{ __(@$aboutElement->data_values->sub_title) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach

                    </div>
                    <div class="about-btn">
                        <a href="{{ @$aboutContent->data_values->button_link }}" class="btn btn--base mt-85">
                            {{ __(@$aboutContent->data_values->button) }} </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about__thumb">
                    <img src="{{ frontendImage('about', @$aboutContent->data_values->image, '805x650') }}" alt="about_image">
                </div>
            </div>
        </div>
    </div>
</section>
