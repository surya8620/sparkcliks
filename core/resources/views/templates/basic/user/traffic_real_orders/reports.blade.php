@extends($activeTemplate . 'layouts.app')
@section('panel')

    <div class="row">
        <div class="col-lg-12">

            <div class="card b-radius--10 mb-4">
                    <div class="card-body p-0">
                        <div class="table-responsive--sm table-responsive">

                            <table class="table table--light tabstyle--two custom-data-table">
                                    <thead>
        <tr>
            <th>Date</th>
            <th>Visitors Count</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($hits as $item)
        <tr>
            <td>{{ showMonth($item->date) }}</td>
            <td>{{ $item->count }}</td>
        </tr>
        @endforeach
    </tbody>
                            </table><!-- table end -->
                        </div>
                    </div>

                </div><!-- card end -->

        </div>
    </div>
@endsection
@push('breadcrumb-plugins')
<label class="form-control-label font-weight-bold">Timezone - UTC</label>
@endpush


@push('style')
    <style>
        .break_line{
            white-space: initial !important;
        }
    </style>
@endpush
