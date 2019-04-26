@extends('backEnd.layout')
{{-- page level styles --}}
@section('headerInclude')
    {{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="{{ URL::to('css/datatables.css') }}">
    <link href="{{ asset('js/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
    <style>


    </style>
@endsection
{{-- Page content --}}
@section('content')
    <div class="padding">
        <div class="box">
            <div class="box-header dker">
                <h3>Pickups</h3>
                <small>
                    <a href="">Home</a> /
                    <a href="">Pickups</a> /
                    <a href="">Create Pickups</a>
                </small>
            </div>
            <div class="row p-a pull-right" style="margin-top: -70px;">
                <div class="col-sm-12">
                    <a class="btn btn-warning" href="{{ url()->previous() }}">
                         Back
                    </a>
                </div>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['admin.pickups.store'],'method'=>'POST'])}}
                {{csrf_field()}}
                <div class="form-group row">
                    <div class="col-md-4">
                        <label for="pickup_person" style="margin-bottom: -11px;" class="col-sm-2 form-control-label">{!!  trans('backLang.pickup_person') !!}</label>
                        {!! Form::text('pickup_person','', array('placeholder' => '','class' => 'form-control','id'=>'pickup_person','required'=>'')) !!}
                    </div>
                    <div class="col-md-4">
                        <label for="mobile" style="margin-bottom: -11px;" class="col-sm-2 form-control-label">{!!  trans('backLang.mobile') !!}</label>
                        {!! Form::text('mobile','', array('placeholder' => '','class' => 'form-control','id'=>'mobile','required'=>'')) !!}
                    </div>
                    <div class="col-md-4">
                        <label for="pickup_date"  style="margin-bottom: -11px;" class="col-sm-2 form-control-label">{!!  trans('backLang.pickup_date') !!}</label>
                        {!! Form::text('pickup_date','', array('placeholder' => '','autocomplete'=>'off','class' => 'form-control','id'=>'pickup_date','required'=>'')) !!}
                    </div>
                    <div class="col-md-4">
                        <label for="address" style="margin-bottom: -11px;" class="col-sm-2 form-control-label">{!!  trans('backLang.address') !!}</label>
                        {!! Form::text('address','', array('placeholder' => '','class' => 'form-control','id'=>'address','required'=>'')) !!}
                    </div>
                    <div class="col-md-4">
                        <label for="city" style="margin-bottom: -11px;" class="col-sm-2 form-control-label">{!!  trans('backLang.city') !!}</label>
                        {!! Form::text('city','', array('placeholder' => '','class' => 'form-control','id'=>'city','required'=>'')) !!}
                    </div>
                    <div class="col-md-4">
                        <label for="state" style="margin-bottom: -11px;" class="col-sm-2 form-control-label">{!!  trans('backLang.state') !!}</label>
                        {!! Form::text('state','', array('placeholder' => '','class' => 'form-control','id'=>'state','required'=>'')) !!}
                    </div>
                    <div class="col-md-4">
                        <label for="pincode" style="margin-bottom: -11px;" class="col-sm-2 form-control-label">{!!  trans('backLang.pincode') !!}</label>
                        {!! Form::text('pincode','', array('placeholder' => '','class' => 'form-control','id'=>'pincode','required'=>'')) !!}
                    </div>
                    <div class="col-md-4">
                        <label for="start_time" style="margin-bottom: -11px;" class="col-sm-2 form-control-label">{!!  trans('backLang.preferred_start_time') !!}</label>
                        {!! Form::text('start_time','', array('placeholder' => '','class' => 'form-control','id'=>'start_time','required'=>'')) !!}
                    </div>
                    <div class="col-md-4">
                        <label for="end_time" style="margin-bottom: -11px;" class="col-sm-2 form-control-label">{!!  trans('backLang.preferred_end_time') !!}</label>
                        {!! Form::text('end_time','', array('placeholder' => '','class' => 'form-control','id'=>'end_time','required'=>'')) !!}
                    </div>
                    <div class="col-md-4">
                        <label for="price" style="margin-bottom: -11px;" class="col-sm-2 form-control-label">{!! trans('backLang.price') !!}</label>
                        {!! Form::text('price','', array('placeholder' => '','class' => 'form-control','id'=>'price','required'=>'')) !!}
                    </div>
                    <div class="col-md-4">
                        <label for="pod_number" style="margin-bottom: -11px;" class="col-sm-2 form-control-label">{!!  trans('backLang.pod_number') !!}</label>
                        {!! Form::text('pod_number','', array('placeholder' => '','class' => 'form-control','id'=>'pod_number','required'=>'')) !!}
                    </div>
                    <div class="col-md-4">
                        <label for="delivery_number" style="margin-bottom: -11px;" class="col-sm-2 form-control-label">{!!  trans('backLang.delivery_number') !!}</label>
                        {!! Form::text('delivery_number','', array('placeholder' => '','class' => 'form-control','id'=>'delivery_number','required'=>'')) !!}
                    </div>
                    <div class="col-md-4">
                        <label for="job" style="margin-bottom: -11px;" class="col-sm-2 form-control-label">{!!  trans('backLang.jobs') !!}</label>
                        <select id="job" name="job" class="form-control" style="">
                            <option value="-1">All</option>
                            @foreach($jobs as $job)
                                <option value="{{$job->id}}">{{$job->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="customer" style="margin-bottom: -11px;" class="col-sm-2 form-control-label">{!!  trans('backLang.customers') !!}</label>
                        <select id="customer" name="customer" class="form-control"  style="">
                            <option value="-1">All</option>
                            @foreach($customers as $customer)
                                <option value="{{$customer->id}}">{{$customer->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="table-responsive" id="document_table" >
                </div>
                <div class="form-group row m-t-md " >
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary m-t"><i class="material-icons">
                                &#xe31b;</i> {!! trans('backLang.add') !!}</button>
                        <a href="{{ route('admin.pickups') }}"
                           class="btn btn-danger m-t"><i class="material-icons">
                                &#xe5cd;</i> {!! trans('backLang.cancel') !!}</a>
                    </div>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
@endsection
{{-- page level scripts --}}
@section('footerInclude')

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>

    <script>

        $(function () {
            $('body').on('hidden.bs.modal', '.modal', function () {
                $(this).removeData('bs.modal');
            });
        });
        $("#start_time").datetimepicker({
            format: 'LT'
        }).parent().css("position :relative");
        $("#end_time").datetimepicker({
            format: 'LT'
        }).parent().css("position :relative");

        $("#pickup_date").datetimepicker({

            format: 'DD/MM/YYYY'
        }).parent().css("position :relative");



        $('#job').on('change',function(){
            job_id = $(this).val();
            selectDocument();
        });
        $('#customer').on('change',function(){
            customer_id = $(this).val();
            selectDocument();
        });



        function selectDocument(){
            showData = '';
            var job_id = $('#job').val();
            if(job_id!= -1){
                var customer_id = $('#customer').val();
                $.ajax({
                    url: '{!! route('admin.pickups.getdocument') !!}',
                    data: {customer_id: customer_id,job_id:job_id},
                    type: 'get',
                    datatype: 'json',
                    success: function (res) {
                        var $data = JSON.parse(res);
                        if($data.status !=0){
                            $('#document_table').empty();
                            showData += '<table class="table table-bordered width100" style="border: solid" >'+
                                    '<thead>'+
                                    '<tr class="filters" style="border: solid" >'+
                                    '<th style="border:2px solid darkgrey;">Document</th>'+
                                    '<th style="border:2px solid darkgrey;">Sequence</th>'+
                                    '<th style="border: 2px solid darkgrey;">Question</th>'+
                                    '<th style="border: 2px solid darkgrey;">Comments</th>'+
                                    '</tr>'+
                                    '</thead>'+
                                    '<tbody>';
                            $data.documents.forEach(function ($document) {
                                showData +=  '<tr>' +
                                        '<td style="border: 2px solid darkgrey; ">' +
                                        '<input id="document" name="document[]" type="text" class="form-control required" value=' + $document.document_name + ' required/>' +
                                        '<td style="border: 2px solid darkgrey;">' +
                                        '<input id="sequence" name="sequence[]" type="number" placeholder="Sequence" class="form-control required" value=' + $document.sequence + ' required/>' +
                                        '</td>' +
                                        '<td style="border: 2px solid darkgrey; ">' +
                                        '<input id="question" name="question[]" type="text"placeholder="Question" class="form-control required" value=' + $document.question + ' required/>' +
                                        '</td>' +
                                        '<td style="border: 2px solid darkgrey; ">' +
                                        '<textarea id="comments" name="comments[]" type="text"placeholder="Comments" class="form-control required" value="" required/></textarea>' +
                                        '</td>' +
                                        '</tr>';
                            });
                            showData +=  '</tbody>'+
                                    '</table>';
                            $('#document_table').append(showData);
                            $('#document_table').show();

                        }else {
                            $('#document_table').hide();
                        }
                    }
                });
            }else{
                alert('Please Select Job');
            }

        }



    </script>
@endsection
