@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php $banner = getContent('banner.content', true); @endphp
    <section class="banner-section">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-6">
                    <div class="banner-content">
                        <div class="banner-content__subtitle"><span> {{ __(@$banner->data_values->title) }} </span>
                            <img src="{{ frontendImage('banner', @$banner->data_values->image_one, '40x40') }}">
                        </div>
                        <h2 class="banner-content__title" s-break="-1" s-color="bg--green px-1">{{ __(@$banner->data_values->heading) }}</h2>
                        <p class="banner-content__desc"> {{ __(@$banner->data_values->subheading) }} </p>
                        <div class="banner-content__button d-flex align-items-center gap-3">
                            <a class="btn btn--base" href="{{ @$banner->data_values->button_link_one }}"> {{ __(@$banner->data_values->button_one) }} </a>
                            <a class="btn btn-outline--base" href="{{ @$banner->data_values->button_link_two }}">
                                {{ __(@$banner->data_values->button_two) }} </a>
                        </div>
                        <div class="banner-content__client">
                            <div class="thumb">
                                <img src="{{ frontendImage('banner', @$banner->data_values->image_three, '140x50') }}">
                            </div>
                            <div class="number">
                                <p> <span>{{ @$banner->data_values->customer_count }} </span> {{ __(@$banner->data_values->customer_title) }} </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="banner-content-thumb">
                        <img src="{{ frontendImage('banner', @$banner->data_values->image_two, '805x650') }}" alt="banner">
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if (@$sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
    
@endsection
