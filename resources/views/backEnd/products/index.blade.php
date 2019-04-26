@extends('backEnd.layout')

{{-- page level styles --}}

@section('headerInclude')

    <title>{{ trans('backLang.control') }} | @lang('backLang.products')</title>

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
            @if(session()->has('message'))
                <div class="alert alert-success alert-dismissible" style="text-align: center">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>{{ session()->get('message') }}</strong>
                </div>
            @endif
            <div class="box">

                <div class="box-header dker">

                    <h3>@lang('backLang.products')</h3>

                    <small>

                        <a href="{{route('adminHome')}}">{{ trans('backLang.home') }}</a> /

                        <a href="{{url('/products')}}">@lang('backLang.products')</a>

                    </small>

                </div>

                <div class="row p-a pull-right" style="margin-top: -70px;">
                    <div class="col-sm-12">
                        <a class="btn btn-fw primary" href="{{route("products.create")}}">
                            <i class="material-icons">&#xe7fe;</i>
                            &nbsp; New Product
                        </a>
                    </div>
                </div>

                <div class="table-responsive">

                    <div class="col-md-4 col-sm-4">

                        <strong>Filter By Company</strong>

                        <select id="company_id" name="status" class="form-control" >

                            @foreach($companys as $company)

                                <option value="{{$company->id}}">{{$company->company_name}}</option>

                            @endforeach

                        </select>

                    </div>



                    <table class="table table-striped table-bordered" id="table">

                        <thead>

                        <tr class="filters">

                            <th>Company</th>

                            <th>@lang('backLang.products')</th>

                            <th>Product Code</th>

                            <th>Price</th>
                           
                            <th>Questions</th>

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
    <div class="modal fade" id="delete_product" tabindex="-1" role="dialog" aria-labelledby="user_delete_confirm_title" aria-hidden="true">
        <div class="modal-dialog" id="animate">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('backLang.confirmation') }}</h5>
                </div>
                <div class="modal-body text-center p-lg">
                    <p>
                        {{ trans('backLang.confirmationDeleteMsg') }}
                        <br>
                        <strong>[ Product ]</strong>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark-white p-x-md" data-dismiss="modal">{{ trans('backLang.no') }}</button>
                    <a href="{{route('products.delete',':id')}}" id="product_delete_id" class="btn danger p-x-md">{{ trans('backLang.yes') }}</a>
                </div>
            </div>
        </div>
    </div>


    <script>

        $(function () {

            $('body').on('hidden.bs.modal', '.modal', function () {

                $(this).removeData('bs.modal');

            });

        });

        $(function() {



             var company_id = $('#company_id').val();





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

                       url: '{!! route('products.data') !!}',

                       data: function (d) {

                           d.company_id = company_id;

                       }

                   },

                order: [[0, 'asc']],

                columns: [

                    { data: 'company_name', name: 'company_name' },

                    { data: 'name', name: 'name' },

                    { data: 'product_code', name: 'product_code' },

                    { data: 'price', name: 'price' },

                      { data: 'question_count', name: 'question_count' },

                    { data: 'actions', name: 'actions', orderable: false, searchable: false }





                ],

            });

        } );




        function setId($id) {
            var delete_link = $('#product_delete_id').attr('href');
            url = delete_link.replace(':id',$id);
            $('#product_delete_id').attr('href',url);
        }
    </script>

@endsection

