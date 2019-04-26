@extends('backEnd.layout')
{{-- page level styles --}}

@section('headerInclude')
    <title>{{ trans('backLang.control') }} | Company</title>
    {{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="{{ URL::to('public/css/datatables.css') }}">
@endsection

{{-- Page content --}}
@section('content')
        <!-- Main content -->
    <section class="content paddingleft_right15">
        <div class="padding">
            <div class="box">
                <div class="box-header dker">
                    <h3>Company</h3>
                    <small>
                        <a href="{{route('adminHome')}}">{{ trans('backLang.home') }}</a> /
                        <a href="{{route('customers')}}">{{ trans('backLang.company') }}</a>
                    </small>
                </div>
                <div class="row p-a pull-right" style="margin-top: -70px;">
                    <div class="col-sm-12">
                        <a class="btn btn-fw info" href="{{route("customers.import")}}">
                            <i class="material-icons">&#xe03b;</i>
                            &nbsp; {{ trans('backLang.import') }}
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="table">
                        <thead>
                        <tr class="filters">
                            <th>id</th>
                            <th>Name</th>
                            <th>Company Name</th>
                            <th>Mobile</th>
                            <th>Email </th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>    <!-- row-->
    </section>
@stop

{{-- page level scripts --}}
@section('footerInclude')
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
    <script>
        $(function () {
            $('body').on('hidden.bs.modal', '.modal', function () {
                $(this).removeData('bs.modal');
            });
        });
        $(function() {
            $('#table').DataTable( {
                dom:'<"m-t-10 pull-right"f>rti<"m-t-10 pull-right"p><"m-t-10 pull-bottom"l>',
                processing: true,
                serverSide: true,
                "lengthMenu": [[10, 20, 40, 100], [10, 20, 40, 100]],
                ajax: '{!! route('customers.data') !!}',
                order: [[0, 'asc']],
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'company_name', name: 'company_name' },
                    { data: 'mobile', name: 'mobile' },
                    { data: 'email', name: 'email' },
                    { data: 'status', name: 'status' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                responsive: true,
            });
        } );
    </script>
@endsection
