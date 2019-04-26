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
        <h3> Past Pickups</h3>
        <small>
            <a href="">{{ trans('customer.home') }}</a> /
            <a href="{{route('agents')}}">Past Pickups</a>
        </small>
    </div>

    <!-- Main content -->
    <section class="content paddingleft_right15">
        <div class="padding">
            <div class="box">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="pickup_table">
                        <thead>
                        <tr class="filters">
                            <th>Agent Name</th>
                            <th>price</th>
                            <th>Pickup person</th>
                            <th>Job Title</th>
                            <th>Pickup Date</th>
                            <th>City</th>
                            <th>Address</th>
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
            $('#pickup_table').DataTable( {
                dom:'<"m-t-10 pull-left"l><"m-t-10 pull-right"f>rti<"m-t-10 pull-left"B><"m-t-10 pull-right"p>',
                processing: true,
                serverSide: true,
                "lengthMenu": [[10, 20, 40, 100], [10, 20, 40, 100]],
                ajax: '{!! route('customer.pickups.past_pickup_data') !!}',
                order: [[0, 'asc']],
                columns: [
                    { data: 'agent_name', name: 'agent_name' },
                    { data: 'price', name: 'price' },
                    { data: 'pickup_person', name: 'pickup_person' },
                    { data: 'name', name: 'name' },
                    { data: 'pickup_date', name: 'pickup_date' },
                    { data: 'city', name: 'city' },
                    { data: 'address', name: 'address' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
            });
        } );
    </script>
@endsection
