@extends('backEnd.layout')

{{-- page level styles --}}

@section('headerInclude')

    <title>{{ trans('backLang.control') }} | @lang('backLang.questions')</title>

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

                @if(session()->has('success'))

                <div class="alert alert-success alert-dismissible">

                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

                    <p>{{session('success')}} </p>

                </div>

                @endif

                @if(session()->has('error'))

                    <div class="alert alert-danger alert-dismissible">

                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

                        <p>{{session('error')}} </p>

                    </div>

                @endif



            <div class="box">

                <div class="box-header dker">

                    <h3>@lang('backLang.questions')</h3>

                    <small>

                        <a href="{{route('adminHome')}}">{{ trans('backLang.home') }}</a> /

                        <a href="">@lang('backLang.questions')</a>

                    </small>



                </div>

                <div class="row p-a pull-right" style="margin-top: -70px;">

                    <div class="col-sm-12">

                        <a class="btn btn-fw primary" href="{{route("questions.create")}}" id="create_button">

                            <i class="material-icons">&#xe7fe;</i>

                            &nbsp; {{ trans('backLang.new_question') }}

                        </a>

                    </div>

                </div>

                <div class="table-responsive">

                    <div class="col-md-4 col-sm-4">

                        <strong>Filter By Company</strong>

                        <select id="company_id" name="status" class="form-control" >

                            @foreach($companys as $company)

                                <option @if($company_id == $company->id ) selected @endif value="{{$company->id}}">{{$company->company_name}}</option>

                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-4 col-sm-4">

                        <strong>Filter By @lang('backLang.products')</strong>
                        <input id="product"  type="hidden" value="{{$product_id}}">
                        <select id="product_id" name="status" class="form-control" >

                            @foreach($products as $product)

                            <option @if($product_id == $product->id ) selected @endif value="{{$product->id}}">{{$product->name}}</option>

                            @endforeach

                        </select>

                    </div>

                    <table class="table table-striped table-bordered" id="table">

                        <thead>

                        <tr class="filters">

                            <th>Company</th>

                            <th>@lang('backLang.products')</th>

                            <th>Question</th>

                            <th>Links</th>

                            <th>Sequence</th>

                            <th>Image Required</th>

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



    <div class="modal fade" id="delete_question" tabindex="-1" role="dialog" aria-labelledby="user_delete_confirm_title" aria-hidden="true">

        <div class="modal-dialog" id="animate">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">{{ trans('backLang.confirmation') }}</h5>

                </div>

                <div class="modal-body text-center p-lg">

                    <p>

                        {{ trans('backLang.confirmationDeleteMsg') }}

                        <br>

                        <strong>[ Question ]</strong>

                    </p>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn dark-white p-x-md" data-dismiss="modal">{{ trans('backLang.no') }}</button>

                    <a href="{{route('questions.deleteQuestion',':id')}}" id="question_delete_id" class="btn danger p-x-md">{{ trans('backLang.yes') }}</a>

                </div>

            </div>

        </div>

    </div>

@endsection







{{-- page level scripts --}}

@section('footerInclude')

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>

    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>



    <script>



        function setId($id) {

            var delete_link = $('#question_delete_id').attr('href');

            url = delete_link.replace(':id',$id);

            $('#question_delete_id').attr('href',url);

        }


        //        if customer_id has value then product is selected
        if($('#product').val()!= -1 || $('#customer_id').val() != ''){
            var company_id = $('#company_id').val();
            var product_id = $('#product').val();

            $.ajax({
                url:'{{url('/questions/getProducts')}}',
                type: 'get',
                data:{company_id: company_id},
                success: function(data){
                    console.log($('#product').val());
                    if(data.length > 0){
                        selectProduct = '<option value="-1">Not Selected</option>';
                        $.each(data,function(key,product){
                            console.log(product_id);
                            if(product_id == product.id){
                                selectProduct = selectProduct + '<option  selected  value="'+product.id+'">'+product.name+'</option>';
                            }else{
                                selectProduct = selectProduct + '<option    value="'+product.id+'">'+product.name+'</option>';
                            }
                        });
                    }else{
                        selectProduct = '<option value="-1">No Products Available</option>';
                    }

                    $('#product_id').html(selectProduct);
                }
            })
        }
        //Products On Company Onchange
        $('#company_id').on('change',function(){

            var company_id = null;


            $.ajax({

                url:'{{url('/questions/getProducts')}}',

                type: 'get',

                data:{company_id: $(this).val()},

                success: function(data){

                    $('#product_id').empty();

                    if(data.length > 0){

                        selectProduct = '<option value="-1">Not Selected</option>';

                        $.each(data,function(key,product){

                            selectProduct = selectProduct + '<option value="'+product.id+'">'+product.name+'</option>';

                        });

                    }else{

                        selectProduct = '<option value="-1">No Products Available</option>';

                    }



                    $('#product_id').html(selectProduct);

                }

            })

        });

        $(function () {

            $('body').on('hidden.bs.modal', '.modal', function () {

                $(this).removeData('bs.modal');

            });

        });
    //Set Option Selected When Redirect To Page



        $(function() {

            var product_id = null;
            var company_id = null;
            //set product_id  To Filter
            product_id = $('#product_id').val();

            //set company_id  To Filter
            company_id =  $('#company_id').val();

            if($('#product').val() != ''){
                product_id = $('#product').val();
            }
            //for Company Id
            var url = '{{ URL::to("questions/create?company=:company_id,product=:product_id") }}';
            url = url.replace(':product_id',product_id);
            var url2 = url.replace(':company_id',company_id);
            var final_url = url2.replace(',','&');
            $('#create_button').attr('href',final_url);

            //onChange Product and CompanyId pass To Filter
            $('#product_id').on('change',function(){
                product_id = $(this).val();
                //Select Option when Redirect Page
                var url = '{{ URL::to("questions/create?company=:company_id,product=:product_id") }}';
                url = url.replace(':product_id',product_id);
                var url2 = url.replace(':company_id',company_id);
                var final_url = url2.replace(',','&');
                $('#create_button').attr('href',final_url);
                table.draw();

            });

            $('#company_id').on('change',function(){

                company_id = $(this).val();

                //Select Option when Redirect Page
                {{--var url = '{{ route('questions.create',['company_id'=>':company_id'])}}';--}}
                var url = '{{ URL::to("questions/create?company=:company_id,product=:product_id") }}';
                url = url.replace(':product_id',product_id);
                var url2 = url.replace(':company_id',company_id);
                var final_url = url2.replace(',','&');
                $('#create_button').attr('href',final_url);
                console.log(final_url);
                table.draw();

            });

            var table =  $('#table').DataTable( {

                dom:'<"m-t-10 pull-right"f>rti<"m-t-10 pull-right"p><"m-t-10 pull-bottom"l>',

                processing: true,

                serverSide: true,

                "lengthMenu": [[10, 20, 40, 100], [10, 20, 40, 100]],

                   ajax: {

                       url: '{!! route('questions.data') !!}',

                       data: function (d) {

                           d.product_id = product_id;

                           d.company_id = company_id;

                       }

                   },

                order: [[4, 'asc']],

                columns: [

                    { data: 'company_name', name: 'company_name' },

                    { data: 'product_name', name: 'product_name' },

                    { data: 'question', name: 'question' },

                    { data: 'links', name: 'links' },

                    { data: 'sequence', name: 'sequence' },

                    { data: 'question_image', name: 'question_image' },

                    { data: 'actions', name: 'actions', orderable: false, searchable: false }





                ],

            });

        } );

    </script>

@endsection

