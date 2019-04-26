@extends('backEnd.layout')
{{-- page level styles --}}
@section('headerInclude')
    <title>{{ trans('backLang.control') }} | Documents</title>
    {{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="{{ URL::to('css/datatables.css') }}">
@endsection


{{-- Page content --}}
@section('content')
        <!-- Main content -->
    <section class="content paddingleft_right15">
        <div class="padding">
            <div class="box">
                <div class="box-header dker">
                    <h3>Documents</h3>
                    <small>
                        <a href="{{route('adminHome')}}">{{ trans('backLang.home') }}</a> /
                        <a href="{{route('documents')}}">Documents</a>
                    </small>
                </div>
                {{--<div class="row p-a pull-right" style="margin-top: -70px;">--}}
                    {{--<div class="col-sm-12">--}}
                        {{--<a class="btn btn-fw info" href="{{route("admin.documents.import")}}">--}}
                            {{--<i class="material-icons">&#xe03b;</i>--}}
                            {{--&nbsp; {{ trans('backLang.import') }}--}}
                        {{--</a>--}}
                    {{--</div>--}}
                {{--</div>--}}
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="document_table">
                        <thead>
                        <tr class="filters">
                            <th>Document Id</th>
                            <th>Document Name</th>
                            <th>Created By</th>
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
                dom:'<"m-t-10 pull-right"f>rti<"m-t-10 pull-right"p><"m-t-10 pull-bottom"l>',
                processing: true,
                serverSide: true,
                "lengthMenu": [[10, 20, 40, 100], [10, 20, 40, 100]],
                ajax: '{!! route('documents.data') !!}',
                order: [[0, 'asc']],
                columns: [
                    { data: 'id',name:'id'},
                    { data: 'name',name:'name'},
                    { data: 'user_name',name:'user_name'},

                ],
            });
        } );
    </script>
@endsection
