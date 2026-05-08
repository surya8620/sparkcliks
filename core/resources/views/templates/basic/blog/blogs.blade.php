@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="blog py-120">
        <div class="container">
            <div class="row gy-4 justify-content-center">
                @foreach ($blogs as $blog)
                    <div class="col-lg-4 col-md-6">
                        <div class="blog-item">
                            <div class="blog-item__thumb">
                                <a class="blog-item__thumb-link" href="{{ route('blog.details', $blog->slug) }}">
                                    <img class="fit-image" src="{{ frontendImage('blog', 'thumb_' . @$blog->data_values->image, '435x300') }}"
                                        alt="blog image">
                                </a>
                            </div>
                            <div class="blog-item__content">
                                <ul class="text-list flex-align gap-3">
                                    <li class="text-list__item fs-16 text--dark"> <span class="text-list__item-icon fs-12 text--base me-1"></span><i
                                            class="far fa-clock"></i> {{ showDateTime($blog->created_at, 'd M Y') }} </li>
                                </ul>
                                <h5 class="blog-item__title"><a class="blog-item__title-link border-effect"
                                        href="{{ route('blog.details', $blog->slug) }}"> {{ __($blog->data_values->title) }} </a></h5>
                                <p class="blog-item__desc">
                                    @php echo  strLimit(strip_tags($blog->data_values->description),100) @endphp
                                </p>
                                <a class="btn--simple border-effect" href="{{ route('blog.details', $blog->slug) }}"> @lang('Read More') <span
                                        class="btn--simple__icon"><i class="las la-angle-right"></i></span></a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($blogs->hasPages())
                <div class="mt-4">
                    {{ paginateLinks($blogs) }}
                </div>
            @endif

        </div>
    </section>

    @if (@$sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif

@endsection
