@extends('backEnd.layout')
{{-- page level styles --}}
@section('headerInclude')
    <title>{{ trans('backLang.control') }} | @lang('backLang.questions')</title>
    {{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="{{ URL::to('css/datatables.css') }}">
    <link href="{{ asset('public/js/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
    {{--Checkbox css--}}
    <link rel="stylesheet" href="{{asset('public/css/bootstrap_checkbox.css')}}"/>
    <style>
        .span_class{
            font-size: 13px;
            color: indianred;
        }

        .ui-check input:checked + i:before{
            margin-top: -2px;
            margin-left: -2px;
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            margin: 0;
        }
    </style>
@endsection
{{-- Page content --}}
@section('content')
    <div class="padding">
        <div class="box">
            <div class="box-header dker">
                <h3> Add @lang('backLang.new_question')</h3>
                <small>
                    <a href="">Home</a> /
                    <a href="">@lang('backLang.questions')</a> /
                    <a href="">Create @lang('backLang.questions')</a>
                </small>
            </div>
            <div class="row p-a pull-right" style="margin-top: -70px;">
                <div class="col-sm-12">
                    <a class="btn btn-warning" id="back_url" onclick="BackURL()">
                        Back
                    </a>
                </div>
            </div>
            <div class="box-body">
                @if(session()->has('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <p>{{session('error')}} </p>
                    </div>
                @endif
                {{Form::open(['route'=>['questions.store'],'method'=>'POST','id'=>'QuestionForm'])}}
                {{csrf_field()}}
                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="customer" class="form-control-label">{!!  trans('backLang.company') !!}</label>
                        <select id="customer_id" name="customer_id" class="form-control"  style="">
                            <option value="-1">Not Selected</option>
                            @foreach($companys as $company)
                                <option @if($company_id == $company->id) selected @endif value="{{$company->id}}">{{$company->company_name}}</option>
                            @endforeach
                        </select>
                        <span class="span_class" id="customer_span" style="display: none">Please Select Comapany</span>
                    </div>
                    <div class="col-md-6">
                        <label for="job" class="form-control-label">{!!  trans('backLang.products') !!}</label>
                        <input id="product"  type="hidden" value="{{$product_id}}">
                        <select id="product_id" name="product_id" class="form-control" style="">
                            <option value="-1">Not Selected</option>

                        </select>
                        <span class="span_class" id="product_span" style="display: none">Please Select Product</span>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12" style="border: 5px solid #0cc2aa; border-radius: 25px; padding: 10px 0px;">
                        <p class="col-md-12" style="font-weight: 500; font-size: 20px; margin-bottom: 0px;">Set Dynamic Fields</p>
                        <div class="col-md-3" style="float: left; padding-top: 20px;">
                            <div class="radio" style="font-size: 18px;">
                                <label class="ui-check ui-check-md" style="padding-right: 15px;">
                                    <input type="radio" id="status1" class="has-value" value="Text" checked>
                                    <i class="dark-white" style="width: 20px; height: 20px;"></i>
                                    {{ trans('backLang.test_field') }}
                                </label>
                                <label class="ui-check ui-check-md">
                                    <input type="radio" id="status2" class="has-value" value="Link">
                                    <i class="dark-white" style="width: 20px; height: 20px;"></i>
                                    {{ trans('backLang.link_field') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3"  style="margin-top:-21px;">
                            <label for="column" class="form-control-label" >Select column</label>
                            <select id="column_id"  name="column_id" class="form-control">
                                <option value="-1">Not Selected</option>
                                @foreach($pickup_columns as $pickup_column)
                                    <option value="{{$pickup_column->column}}">{{$pickup_column->column}}</option>
                                @endforeach
                            </select>
                            <span class="span_class" id="customer_span" style="display: none">Please Select Comapany</span>


                        </div>
 
                            <div class="col-sm-3 p-a pull-right" style="bottom: 22px;">
                                <a onclick="insertText($('#column_id').val())"
                               class="btn btn-success m-t">Add</a>
                            </div>

                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <label for="column" class="form-control-label">Enter Sequence</label>
                            <input type="number" name="sequence"  id="sequence"  value="{{$latest_sequence}}" class="form-control">
                            <span class="span_class" id="sequence_span" style="display: none">Please Enter Sequence</span>
                        </div>
                        <div class="col-md-6" style="font-size: 16px; padding-top: 30px;">
                            <label class="container">Picture will be required if checked
                                <input type="checkbox" name="question_image" value="1">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class=" col-md-12">
                    <label for="customer" class="form-control-label">{!!  trans('backLang.questions') !!}</label>
                    <textarea class="form-control" rows="4" name="question" id="question"></textarea>
                    <span class="span_class" id="question_span" style="display: none">Please Enter Question</span>



                    </div>

                </div>
                <div class="form-group row" id="document_table" style="display: none;">
                    <div class="row" style="left: 15px;position: relative" style="padding: 10px">
                        <div class="col-md-4">
                            <label for="column" class="form-control-label" >Enter Name</label>

                            </div>
                        <div class="col-md-4">
                            <label for="column" class="form-control-label" >Enter Link</label>

                            </div>

                        </div>
                </div>
                <div class="form-group row m-t-md " >
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary m-t"><i class="material-icons">
                                &#xe31b;</i> {!! trans('backLang.add') !!}</button>
                        <a onclick="Cancel()" id="cancel_url"
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
        function Cancel(){
            var url = '{{ URL::to("questions/?company_id=:company_id,product_id=:product_id") }}';
            url = url.replace(':product_id',$('#product_id').val());
            var url2 = url.replace(':company_id',$('#customer_id').val());
            var final_url = url2.replace(',','&');
            $('#cancel_url').attr('href',final_url);
        }

        function BackURL(){
            var url = '{{ URL::to("questions/?company_id=:company_id,product_id=:product_id") }}';
            url = url.replace(':product_id',$('#product_id').val());
            var url2 = url.replace(':company_id',$('#customer_id').val());
            var final_url = url2.replace(',','&');
            $('#back_url').attr('href',final_url);
        }
        //Products On Company Onchange
//        if customer_id has value then product is selected
       if($('#customer_id').val()!= -1 || $('#customer_id').val() != ''){
           var company_id = $('#customer_id').val();
           var product_id = $('#product').val();

           $.ajax({
               url:'{{url('/questions/getProducts')}}',
               type: 'get',
               data:{company_id: company_id},
               success: function(data){
                   if(data.length > 0){
                       selectProduct = '<option value="-1">Not Selected</option>';
                       $.each(data,function(key,product){
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
        $('#customer_id').on('change',function(){
            var company_id = null;
            $.ajax({
                url:'{{url('/questions/getProducts')}}',
                type: 'get',
                data:{company_id: $(this).val()},
                success: function(data){
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
        $('input:radio').click(function() {
            $('input:radio').not(this).prop('checked', false);
        });

        function  insertText(value){

            if(value != -1){
                var fieldvalue = $("input[class='has-value']:checked").val();
                var dyanmic_field = '[['+ fieldvalue +'_'+ value +']]';
                if(fieldvalue != 'Text'){

                    var row  = '<div class="row" style="left: 15px;position: relative" style="padding: 10px">'+
                                '<div class="col-md-4">'+
                                '<label for="column" class="form-control-label" style="display: none">Enter Name</label>'+
                                '<input type="text" name="name[]" class="form-control" required>'+
                                '<span class="span_class" id="name_span" style="display: none">Please Enter Name</span>'+
                                '</div>'+
                                '<div class="col-md-4">'+
                                '<label for="column" class="form-control-label" style="display: none">Enter Link</label>'+
                                '<input type="text" name="link[]" class="form-control"  readonly value="'+dyanmic_field+'" required>'+
                                '<span class="span_class" id="link_span" style="display: none">Please Enter Link</span>'+
                                '</div>'+
                                '<div class="col-sm-4 p-a pull-right" style="bottom:16px; !important;">'+
                                '<button class="btn btn-danger"  >Remove</button>'+
                                '</div>'+
                                '</div>';

                    $('#document_table').append(row).show();
                }else{
                    var $txt = jQuery("#question");
                    var caretPos = $txt[0].selectionStart;
                    var textAreaTxt = $txt.val();
                    var txtToAdd = dyanmic_field;
                    $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
                }


            }

        }
        $(function () {
            $('body').on('hidden.bs.modal', '.modal', function () {
                $(this).removeData('bs.modal');
            });
        });
        //Remove Link Field Row
        $('.row').on('click','.btn-danger',function(){
            $(this).parent().parent().remove();
        });
        //Validation for Form
        $("#question").keyup(function(){
            $('#question_span').hide();
        });
        $("#sequence").keyup(function(){
            $('#sequence_span').hide();
        });
        $('#customer_id').change(function(){
            if($('#customer_id').val() != -1){
                $('#customer_span').hide();
            }else{

                $('#customer_span').show();
            }
        });
        $('#product_id').change(function(){
            if($('#product_id').val() != -1){
                $('#product_span').hide();
            }else{
                $('#product_span').show();
            }
        });
        $('#QuestionForm').on('submit', function(e) {
            if ($('#product_id').val() != '-1' && $('#customer_id').val() != '-1' && $('#question').val() != '' && $('#sequence').val() != '') {
                $('#QuestionForm').submit();
            }else{

                if ($('#product_id').val() == -1) {
                    if ($("#product_id").parent().next(".validation").length == 0) // only add if not added
                    {
                        $('#product_span').show();
                    }
                    $('#product_id').focus();
                    focusSet = true;

                } else {
                    $("#product_id").parent().next(".validation").remove(); // remove it
                }
                if ($('#customer_id').val() == -1) {
                    if ($("#customer_id").parent().next(".validation").length == 0) // only add if not added
                    {
                        $('#customer_span').show();
                    }
                    $('#customer_id').focus();
                    focusSet = true;

                } else {
                    $("#customer_id").parent().next(".validation").remove(); // remove it
                }
                if (!$('#question').val()) {
                    if ($("#question").parent().next(".validation").length == 0) // only add if not added
                    {
                        $('#question_span').show();
                    }
                    $('#question').focus();
                    focusSet = true;

                } else {
                    $("#question").parent().next(".validation").remove(); // remove it
                }
                if (!$('#sequence').val()) {
                    if ($("#sequence").parent().next(".validation").length == 0) // only add if not added
                    {
                        $('#sequence_span').show();
                    }
                    $('#sequence').focus();
                    focusSet = true;

                } else {
                    $("#sequence").parent().next(".validation").remove(); // remove it
                }
                e.preventDefault();
            }
        });
    </script>
@endsection
