@extends('customer.layout')
{{-- page level styles --}}
@section('headerInclude')
    {{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="{{ URL::to('css/datatables.css') }}">
@endsection


{{-- Page content --}}
@section('content')
    <div class="box-header dker">
        <h3>Documents</h3>
        <small>
            <a href="">{{ trans('customer.home') }}</a> /
            <a href="{{route('agents')}}">Documents</a>
        </small>
    </div>

    <!-- Main content -->
    <section class="content paddingleft_right15">
        <div class="padding">
            <div class="box">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="document_table">
                        <thead>
                        <tr class="filters">
                            <th>Job Type</th>
                            <th>Document Name</th>
                            <th>Price</th>
                            <th>Customer Name</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>    <!-- row-->
    </section>
@endsection

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
            $('#document_table').DataTable( {
                dom:'<"m-t-10 pull-left"l><"m-t-10 pull-right"f>rti<"m-t-10 pull-left"B><"m-t-10 pull-right"p>',
                processing: true,
                serverSide: true,
                "lengthMenu": [[10, 20, 40, 100], [10, 20, 40, 100]],
                ajax: '{!! route('customer.documents.data') !!}',
                order: [[0, 'asc']],
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'document_name',name:'document_name'},
                    { data: 'price',name:'price'},
                    { data: 'customer_name', name: 'customer_name' },

                ],
            });
        } );
    </script>
@endsection
