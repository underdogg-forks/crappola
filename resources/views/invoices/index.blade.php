@extends('layouts.header')

@section('head')
    @parent

    <script src="{{ asset('/js/select2.min.js') }}" type="text/javascript"></script>
    <link href="{{ asset('/css/select2.css') }}" rel="stylesheet" type="text/css"/>

@stop

@push('styles')
<link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css" rel="stylesheet" />
@endpush

@section('content')
    <table class=" table-bordered table-striped table-hover datatable" id="dt-invoices">
        <thead>
        <tr>
            <th>&nbsp;</th>
            <th>Name</th>
            <th>Action</th>
        </tr>
        </thead>
    </table>
@endsection

@push('scripts')
<script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#dt-invoices').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            pageLength: 10,
            ajax: '{{ route('api.invoices.datatable') }}',
            'columnDefs': [
                {
                    'targets': 0,
                    'checkboxes': {
                        'selectRow': true
                    }
                }
            ],
            'select': {
                'style': 'multi'
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false},
                { data: 'name', name: 'name' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });
   });
</script>
@endpush
