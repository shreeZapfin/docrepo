@extends('backEnd.layout')

{{-- page level styles --}}

@section('headerInclude')
    <title>{{ trans('backLang.control') }} | Products</title>

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

                <h3>Products</h3>

                <small>

                    <a href="">Home</a> /

                    <a href="">Products</a> /

                    <a href="">Create Product</a>

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

                {{Form::open(['route'=>['products.update',$product->id],'method'=>'PATCH'])}}

                {{csrf_field()}}

                <div class="form-group row">

                    <div class="col-md-6">

                        <label for="name" style="" class="form-control-label">{!!  trans('backLang.name') !!}</label>

                        {!! Form::text('name',$product->name, array('placeholder' => '','class' => 'form-control','id'=>'name','required'=>'')) !!}

                    </div>

                    <div class="col-md-6">

                        <label for="product_code"  class="form-control-label">Product Code</label>
                        <span style="color: lightgray;">Ex:(Home Loan as HL)</span>
                        {!! Form::text('product_code',$product->product_code, array('placeholder' => '','class' => 'form-control','id'=>'product_code','required'=>'')) !!}

                    </div>

                    <div class="col-md-6">

                        <label for="price"  class="form-control-label">{!!  trans('backLang.price') !!}</label>

                        {!! Form::text('price',$product->price, array('placeholder' => '','class' => 'form-control','id'=>'price','required'=>'')) !!}

                    </div>

                    <div class="col-md-6">

                        <label for="company"  class="form-control-label">{!!  trans('backLang.company') !!}</label>

                        <select id="company" name="company" class="form-control" style="">

                            <option value="-1">All</option>

                            @foreach($companys as $company)

                                <option value="{{$company->id}}" @if($product->company_id == $company->id) selected @endif>{{$company->company_name}}</option>

                            @endforeach

                        </select>

                    </div>

                </div>

                <div class="table-responsive" id="document_table" >

                </div>

                <div class="form-group row m-t-md " >

                    <div class="col-sm-offset-2 col-sm-10">

                        <button type="submit" class="btn btn-primary m-t"><i class="material-icons">

                                &#xe31b;</i> {!! trans('backLang.update') !!}</button>

                        <a href="{{ url()->previous() }}"

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

                    url: '{!! url('pickups.getdocument') !!}',

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

