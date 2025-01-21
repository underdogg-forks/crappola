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
    <table class=" table-bordered table-striped table-hover datatable" id="dt-clients">
        <thead>
        <tr>
            <th>&nbsp;</th>
            <th>Name</th>
            <th>Contact</th>
            <th>Email</th>
            <th>ID Number</th>
            <th>Custom Client 1</th>
            <th>Last Login</th>
            <th>Balance</th>
            <th>Action</th>
        </tr>
        </thead>
    </table>

    {{--<div class="relative flex flex-col min-w-0 rounded break-words border bg-white border-1 border-grey-light">
    <div class="py-3 px-6 mb-0 bg-grey-lighter border-b-1 border-grey-light text-grey-darkest">
        Clients
    </div>
    <div class="flex-auto p-6">
        <div class="block w-full overflow-auto scrolling-touch">
            <table
                class="w-full max-w-full mb-4 bg-transparent table-bordered table-striped table-hover datatable datatable-cameraimage"
            >
                <thead>
                    <tr>
                        <th></th>
                        <th>{{ trans("cruds.images.fields.project") }}</th>
                        <th>{{ trans("cruds.images.fields.kentekennr") }}</th>
                        <th>{{ trans("cruds.images.fields.cameraname") }}</th>
                        <th>{{ trans("cruds.images.fields.poort") }}</th>
                        <th>{{ trans("cruds.images.fields.vehicle_direction") }}</th>
                        <th>{{ trans("cruds.images.fields.recognition_date") }}</th>
                        <th>{{ trans("cruds.images.fields.image") }}</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>--}}
@endsection

@push('scripts')
<script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#dt-clients').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            pageLength: 10,
            ajax: '{{ route('api.clients.datatable') }}',
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
                { data: 'contact', name: 'contact' },
                { data: 'email', name: 'email' },
                { data: 'id_number', name: 'id_number' },
                { data: 'custom_client_1', name: 'custom_client_1' },
                { data: 'last_login', name: 'last_login' },
                { data: 'balance', name: 'balance' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });
   });
</script>
@endpush
