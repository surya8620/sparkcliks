@extends($activeTemplate.'layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-sm-8 col-lg-8 col-xl-6">
            <div class="card custom--card">
                <form class="dashboard-form"  method="post" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label class="form--label">@lang('Username')</label>
                        <input type="text" name="username" class="form-control form--control checkUser">
                        <small class="text-danger usernameExist"></small>
                    </div>
                    <div class="form-group">
                        <label class="form--label">@lang('Amount') <small class="text--success">( @lang('Charge'): {{ getAmount(gs('bt_fixed')) }} {{__(gs('cur_text'))  }} + {{ getAmount(gs('bt_percent')) }}% )</small></label>
                        <div class="input-group">
                            <input type="number" step="any" name="amount" value="{{ old('username') }}" class="form-control form--control">
                            <span class="input-group-text">{{ __(gs('cur_text'))  }}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form--label">@lang('Amount Will Cut From Your Account')</label>
                        <div class="input-group">
                            <input type="text" class="form-control form--control calculation" readonly>
                            <span class="input-group-text border-0">{{ __(gs('cur_text'))  }}</span>
                        </div>
                    </div>
                    <div class="pt-4">
                        <button type="submit" class="btn btn--base btn--lg w-100">@lang('Transfer')</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script type="text/javascript">
    $('input[name=amount]').on('input',function(){
        var amo = parseFloat($(this).val());
        var calculation = amo + ( parseFloat({{ gs('bt_fixed') }}) + ( amo * parseFloat({{ gs('bt_percent') }}) ) / 100 );
        $('.calculation').val(calculation);
    });

    $('.checkUser').on('focusout',function(e){
        var url = '{{ route('user.checkUser') }}';
        var value = $(this).val();
        var token = '{{ csrf_token() }}';
        var data = {username:value,_token:token}
        $.post(url,data,function(response) {
            if(response.data != false){
                $(`.${response.type}Exist`).text('');
            }else{
                $(`.${response.type}Exist`).text(`${response.type} not found`);
            }
        });
    });
</script>
@endpush
