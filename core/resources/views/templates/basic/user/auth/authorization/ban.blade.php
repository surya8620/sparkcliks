@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $banContent = getContent('ban_page.content', true);
    @endphp
    <section class="pt-120 pb-120">
    <div class="section bg--light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 col-md-8 col-12 text-center">
                    <div class="ban-section">
                            <img src="{{ asset('assets/images/blocked.webp') }}" class="img-fluid" style="width: 40%;"
                            alt="Locked Out">
                        <div class="mt-3">
                            <p>{{ $user->ban_reason }}</p>
                        </div><br>
                        <a href="{{ route('user.logout') }}" class="btn btn--base">
                            <i class="las la-undo"></i>
                            @lang('Logout')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </section>
@endsection
