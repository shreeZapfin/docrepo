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
                <h3> Edit Question</h3>
                <small>
                    <a href="">Home</a> /
                    <a href="">@lang('backLang.questions')</a> /
                    <a href="">Edit Question</a>
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
                {{Form::open(['route'=>['questions.updatequestion',$question->id],'method'=>'POST','id'=>'QuestionForm'])}}
                {{csrf_field()}}
                <div class="form-group row">
                    <div class="col-md-6">
                        <label  for="customer" class="form-control-label" style="font-size: 20px;">{!!  trans('backLang.company') !!}</label>
                        <input type="hidden" value="{{$question->company->id}}" name="customer_id" id="customer_id">
                        <label style="font-size: 20px;"><strong> : {{$question->company->company_name}}</strong></label>
                    </div>
                    <div class="col-md-6">
                        <label  for="customer" class="form-control-label" style="font-size: 20px;">{!!  trans('backLang.products') !!}</label>
                        <input type="hidden" value="{{$question->product->id}}" name="product_id" id="product_id">
                        <label style="font-size: 20px;"><strong> : {{$question->product->name}}</strong></label>
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
                            <label for="column" class="form-control-label">Select column</label>
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
                            <input type="number" name="sequence" id="sequence" value="{{$question->sequence}}" class="form-control">
                            <span class="span_class" id="sequence_span" style="display: none">Please Enter Sequence</span>
                        </div>
                        <div class="col-md-6" style="font-size: 16px; padding-top: 30px;">
                            <label class="container">Picture will be required if checked
                                <input type="checkbox" name="question_image" value="1" @if($question->question_image == 1) checked @endif>
                                <span class="checkmark"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class=" col-md-12">
                        <label for="customer" class="form-control-label">{!!  trans('backLang.questions') !!}</label>
                        <textarea class="form-control" rows="4" name="question" id="question">{{$question->question}}</textarea>
                        <span class="span_class" id="question_span" style="display: none">Please Enter Question</span>
                    </div>
                </div>
                {{--<div class="form-group row" id="document_table">--}}
                    {{--@foreach($pickup_links as $link)--}}
                    {{--<div class="row" style="left: 15px;position: relative">--}}
                        {{--<div class="col-md-4">--}}
                            {{--<label for="column" class="form-control-label">Enter Name</label>--}}
                            {{--<input type="text" name="name[]" class="form-control" required value="{!! $link->name !!}">--}}
                            {{--<span class="span_class" id="name_span" style="display: none">Please Enter Name</span>--}}
                            {{--</div>--}}
                        {{--<div class="col-md-4">--}}
                            {{--<label for="column" class="form-control-label">Enter Link</label>--}}
                            {{--<input type="text" name="link[]" class="form-control" value="{!! $link->link !!}"  readonly required>--}}
                            {{--<span class="span_class" id="link_span" style="display: none">Please Enter Link</span>--}}
                            {{--</div>--}}
                        {{--<div class="col-sm-4 p-a pull-right" style="top: 15px; !important;">--}}
                            {{--<button class="btn btn-danger"  >Remove</button>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--@endforeach--}}
                {{--</div>--}}

                <div class="col-lg-12">

                    <div class="row">

                        <table class="table table-striped" id ="link_table" @if(count($pickup_links) <= 0 ) style="display:none" @endif>

                            <thead>

                            <tr>

                                <th>Enter Name</th>

                                <th>Enter Link</th>

                                <th></th>

                            </tr>

                            </thead>

                            <tbody>

                            @if($pickup_links->isEmpty())


                            @else

                                @foreach($pickup_links as $link)

                                    <tr id="product_{{$link->id}}">

                                        <td>

                                            <input type="text" name="name[]" class="form-control" required value="{!! $link->name !!}">

                                        </td>

                                        <td>

                                            <input type="text" name="link[]" class="form-control" value="{!! $link->link !!}"  readonly required>

                                        </td>

                                        <td>
                                            <a id="{{$link->id}}"  class="btn btn-danger"  onclick="removeLink(this.id)" >Remove</a>
                                        </td>


                                    </tr>

                                @endforeach

                            @endif

                            </tbody>

                        </table>

                    </div>

                </div>

                <div class="form-group row m-t-md " >
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary m-t"><i class="material-icons">
                                &#xe31b;</i> {!! trans('backLang.update') !!}</button>
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
        $(function () {
            $('body').on('hidden.bs.modal', '.modal', function () {
                $(this).removeData('bs.modal');
            });
        });
        $('input:radio').click(function() {
            $('input:radio').not(this).prop('checked', false);
        });
        var rowCount = $('#link_table tr').length;
        function  insertText(value){
            rowCount++;
            if(value != -1){
                var fieldvalue = $("input[class='has-value']:checked").val();
                var dyanmic_field = '[['+ fieldvalue +'_'+ value +']]';
                if(fieldvalue != 'Text'){
                    var row ='<tr id="product_'+rowCount+'">'+
                            '<td>'+
                            '<input type="text" name="name[]" class="form-control" required>'+
                            '<span class="span_class" id="name_span" style="display: none">Please Enter Name</span>'+
                            '</td>'+
                            '<td>'+
                            '<input type="text" name="link[]" class="form-control"  readonly value="'+dyanmic_field+'" required>'+
                            '<span class="span_class" id="link_span" style="display: none">Please Enter Link</span>'+
                            '</td>'+
                            '<td>'+
                            '<a class="btn btn-danger"  onclick="removeLink('+rowCount+')">Remove</a>'+
                            '</td><tr>';
                        $('#link_table').append(row).show();
                }else{
                    var $txt = jQuery("#question");
                    var caretPos = $txt[0].selectionStart;
                    var textAreaTxt = $txt.val();
                    var txtToAdd = dyanmic_field;
                    $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
                }

            }

        }
        //Remove Link Field Row
        function removeLink(id){
            if($('#link_table tr').length != 2){
                $('#product_'+id).remove();
                var last = $('#link_table tr:last').attr('id');

            }
        }

        //Validation for Form
        $("#question").keyup(function(){
            $('#question_span').hide();
        });
        $("#sequence").keyup(function(){
            $('#sequence_span').hide();
        });
        $('#QuestionForm').on('submit', function(e) {
            if ($('#question').val() != '' && $('#sequence').val() != '') {
                $('#QuestionForm').submit();
            }else{
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
