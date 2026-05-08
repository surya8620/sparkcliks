@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="form-group mb-4">
        <label class="form--label" for="referralURL">@lang('Referral Link')</label>
        <div class="input-group">
            <input class="form-control form--control" id="referralURL" type="text" value="{{ route('home') }}?reference={{ $user->username }}" readonly>
            <button class="input-group-text copytext text--base px-3" id="copyBoard"> <i class="fa fa-copy"></i></button>
        </div>
        <small class="form--label text--base" style="font-size: 0.8rem;">@lang('Share this link to get 10% Commission on all purchases made by your friends.')</small>
    </div>
    <div class="table-responsive">
        <table class="table--responsive--lg table">
            <thead>
                <tr>
                    <th>@lang('Full Name')</th>
                    <th>@lang('User Name')</th>
                    <th>@lang('Email')</th>
                    <th>@lang('Mobile')</th>
                    <th>@lang('Plan')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($refUsers as $log)
                    <tr>
                        <td>{{ __($log->fullname) }}</td>
                        <td>{{ __($log->username) }}</td>
                        <td>{{ $log->email }}</td>
                        <td>{{ $log->mobile }}</td>
                        <td>{{ __($log->plan ? $log->plan->name : 'No Plan') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center" colspan="100%"> {{ __($emptyMessage) }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-4">
        {{ paginateLinks($refUsers) }}
    </div>
@endsection
@push('style')
    <style type="text/css">
        .copytextDiv {
            border: 1px solid #00000021;
            cursor: pointer;
        }

        #referralURL {
            border-right: 1px solid #00000021;
        }

        .bg-success-custom {
            background-color: #28a7456e !important;
        }

        .brd-success-custom {
            border: 1px dashed #28a745;
        }
    </style>
@endpush
@push('script')
    <script type="text/javascript">
        (function($) {
            "use strict";
            $('#copyBoard').on("click", function() {
                var copyText = document.getElementById("referralURL");
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                /*For mobile devices*/
                document.execCommand("copy");
                iziToast.success({
                    message: "Copied: " + copyText.value,
                    position: "topRight"
                });
            });
        })(jQuery);
    </script>
@endpush
