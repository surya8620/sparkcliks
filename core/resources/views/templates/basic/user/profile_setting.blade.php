@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="dashboard-card custom--card">
                <div class="dashboard-card-body">
                    <h5 class="form-label font-weight-bold mb-4 text-center"><strong>@lang('Manage Your Profile')</strong></h5>
                    <form class="dashboard-form register" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row"><hr>
                        <h6 class="form-label font-weight-bold mb-4"><strong>@lang('Personal Details')</strong></h6>
                            <div class="form-group col-sm-6">
                                <label class="form--label" style="font-size: 14px;">@lang('First Name')</label>
                                <input type="text" class="form--control" name="firstname" value="{{ $user->firstname }}"
                                    required>
                            </div>
                            <div class="form-group col-sm-6">
                                <label class="form--label" style="font-size: 14px;">@lang('Last Name')</label>
                                <input type="text" class="form--control" name="lastname" value="{{ $user->lastname }}"
                                    required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label class="form--label" style="font-size: 14px;">@lang('E-mail Address')</label>
                                <input class="form--control" value="{{ $user->email }}" readonly>
                            </div>
                            <div class="form-group col-sm-6">
                                <label class="form--label" style="font-size: 14px;">@lang('Mobile Number')</label>
                                <input class="form--control" value="{{ $user->mobile }}" readonly>
                            </div>
                        </div><br>
                        <h6 class="form-label font-weight-bold"><strong>@lang('Billing Address')</strong></h6>
						<span class=" form-label font-weight-bold text-danger small">@lang('Note: A valid billing address is required to purchase credits.')</span>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label class="form--label" style="font-size: 14px;">@lang('Address')</label>
                                <input type="text" class="form--control" name="address" value="{{ @$user->address }}" required>
                            </div>
                            <div class="form-group col-sm-6">
                                <label class="form--label" style="font-size: 14px;">@lang('State')</label>
                                <input type="text" class="form--control" name="state" value="{{ @$user->state }}">
                            </div>
                        </div>


                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label class="form--label" style="font-size: 14px;">@lang('Zip Code')</label>
                                <input type="text" class="form--control" name="zip" value="{{ @$user->zip }}" required>
                            </div>

                            <div class="form-group col-sm-4">
                                <label class="form--label" style="font-size: 14px;">@lang('City')</label>
                                <input type="text" class="form--control" name="city" value="{{ @$user->city }}" required>
                            </div>

                            <div class="form-group col-sm-4">
                                <label class="form--label" style="font-size: 14px;">@lang('Country')</label>
                                <input class="form--control" value="{{ @$user->country }}" disabled>
                            </div>
                        </div>
                        <br>
                        <h6 class="form-label font-weight-bold mb-4"><strong>@lang('Company')</strong></h6>

                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label class="form--label" style="font-size: 14px;">@lang('Company Name')</label>
                                <input type="text" class="form--control" name="org" value="{{ @$user->org }}" placeholder="@lang('Company Name')">
                            </div>

                            <div class="form-group col-sm-6">
                                <label class="form--label" style="font-size: 14px;">@lang('VAT/GST Number')</label>
                                <input type="text" class="form--control" name="vat" value="{{ @$user->vat }}" placeholder="@lang('VAT/GST Number')">
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn--base w-100" "form--label">@lang('Update')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <br>
        <div class="col-md-8 py-4">
            <div class="dashboard-card custom--card">
                <div class="dashboard-card-body">
                    <h6 class="form-label font-weight-bold mb-4 text-center">
                        <strong>@lang('Close Your Account')</strong>
                    </h6>

                    <label class="form--label" style="font-size: 14px;">
                        @lang('If you would like to stop using SparkCliks, you can close your account. If you are experiencing any issues, we strongly encourage you to reach out to our support team, we will do everything we can to make your experience better!')
                    </label>

                    <div class="row g-3">   <!-- NEW wrapper -->
                        <div class="col-12 col-sm-6">
                            <a class="btn btn--sm btn-outline--base w-100" href="{{ route('ticket.open') }}"> <i class="fa fa-comments" aria-hidden="true"></i> @lang('Get Help')</a>
                        </div>

                        <div class="col-12 col-sm-6">
                            <a href="javascript:void(0)" class="btn btn--primary ml-1 deleteBtn btn--sm w-100"" title="Delete Account" data-original-title="@lang('Delete Account')" data-toggle="tooltip" data-url="{{ route('user.profile.delete', $user->id) }}">
                            @lang('Close My Account')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        {{-- Delete MODAL --}}
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">@lang('Close Account')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="" id="from-prevent-multiple-submits">
                @csrf
                <input type="hidden" name="delete_id" id="delete_id" class="delete_id" value="0">
                <div class="modal-body">
                    <h6>@lang('Would you like to close your account?')</h6>
                    <label class="form--label" style="font-size: 14px;">@lang('All active campaigns will be terminated. Your account will be disabled immediately, and you will lose access to all services. The account will be automatically deleted within 30 days.')</label>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--base btn--sm" data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary btn--sm" id="btn-save">@lang('Close')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    (function($) {
        "use strict";
        $('.deleteBtn').on('click', function() {
            var modal = $('#deleteModal');
            var url = $(this).data('url');

            modal.find('form').attr('action', url);
            modal.modal('show');
        });
        $('#from-prevent-multiple-submits').on('submit', function() {
            $("#btn-save", this)
                .html("Disabling...")
                .attr('disabled', 'disabled');
            return true;
        })
    })(jQuery);
</script>
@endpush