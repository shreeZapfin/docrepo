@extends('backEnd.layout')
{{-- page level styles --}}
@section('headerInclude')
    <title>{{ trans('backLang.control') }} | Category</title>
    {{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">

    <link rel="stylesheet" href="{{ URL::to('public/css/datatables.css') }}">
    <style>
        td {
            text-align: center;
        }
        th{
            text-align: center;
        }
    </style>
    @endsection


    {{-- Page content --}}
    @section('content')
            <!-- Main content -->
    <section class="content paddingleft_right15">
        <div class="padding">
            <div class="box">
                <div class="box-header dker">
                    <h3>Category</h3>
                    <small>
                        <a href="{{route('adminHome')}}">{{ trans('backLang.home') }}</a> /
                        <a href="{{route('jobs')}}">Category</a>
                    </small>
                </div>

                <div class="table-responsive">
                    <div class="col-md-4 col-sm-4">
                        <strong>Filter By Category</strong>
                        <select id="job_id" name="status" class="form-control" >
                            <option value="-1" selected >All</option>
                            @foreach($jobs as $job)
                            <option value="{{$job->id}}" >{{$job->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <strong>Filter By Company</strong>
                        <select id="company_id" name="status" class="form-control" >
                            <option value="-1" selected >All</option>
                            @foreach($companies as $companie)
                            <option value="{{$companie->id}}">{{$companie->company_name}}</option>
                          @endforeach
                        </select>
                    </div>
                    <table class="table table-striped table-bordered" id="table">
                        <thead>
                        <tr class="filters">
                            <th>Category Id</th>
                            <th>Category</th>
                            <th>Company</th>
                            <th>Document</th>
                            <th>Price</th>
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
            var job_id = null;
            var company_id = null;

            $('#job_id').on('change',function(){
                job_id = $(this).val();
                table.draw();
            });
            $('#company_id').on('change',function(){
                company_id = $(this).val();
                table.draw();
            });
           var table =  $('#table').DataTable( {
               dom:'<"m-t-10 pull-right"f>rti<"m-t-10 pull-right"p><"m-t-10 pull-bottom"l>',
                processing: true,
                serverSide: true,
                "lengthMenu": [[10, 20, 40, 100], [10, 20, 40, 100]],
                   ajax: {
                       url: '{!! route('jobs.data') !!}',
                       data: function (d) {
                           d.job_id = job_id;
                           d.company_id = company_id;
                       }
                   },
                order: [[0, 'asc']],
                columns: [
                    { data: 'job_id', name: 'job_id' },
                    { data: 'job', name: 'job' },
                    { data: 'company_name', name: 'company_name' },
                    { data: 'document', name: 'document' },
                    { data: 'price', name: 'price' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }


                ],
            });
        } );
    </script>
@endsection
