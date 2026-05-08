@extends($activeTemplate.'layouts.frontend')
@section('content')
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3>{{ __($pageTitle) }}</h3>
                <hr>
                <div>
                    @php echo $policy->data_values->details @endphp
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
