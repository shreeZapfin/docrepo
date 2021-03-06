@extends('customer.layout')

{{-- page level styles --}}

@section('headerInclude')
    <title xmlns="http://www.w3.org/1999/html">{{ trans('backLang.control') }} | Pickups</title>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">

    <link rel="stylesheet" href="{{ URL::to('public/css/datatables.css') }}">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css" rel="stylesheet" />
     <link href="https://codeseven.github.io/toastr/build/toastr.min.css" rel="stylesheet"/>

    {{--DateRangePicker--}}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        .daterangepicker td.active, .daterangepicker td.active:hover{
            background-color: #0cc2aa;
        }
        table th{
            width: 5% !important;
            max-width: 5%;
            padding: 1% 1%;
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
                <div class="table-responsive">
                    <div class="row">
                        <div class="form-group col-md-12 col-sm-4">
                            <h3>Pickups</h3>
                        </div>
                        <div class="form-group col-md-12 col-sm-4 text-right" style="margin-top: -4%;">
                            <a   class="btn btn-primary"  onclick="ExportExcel()" id="export_excel" value="Submit">Export Excel</a>
                            <button type="submit" class="btn btn-danger" data-toggle="modal" data-target="#CustomerMailModal">Mail To Customer</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 col-sm-3">
                            <strong>Filter By Company</strong>
                            <select id="company" name="company" class="form-control" >
                                @if(session()->get('customer')->customer_id != -1)
                                    <option value="{{$company_names->email}}">{{$company_names->company_name}}</option>
                                    @else
                                    <option value="-1" selected >All</option>
                                    @foreach($company_names as $company_name)
                                        <option value="{{$company_name->id.'/'.$company_name->email}}">{{$company_name->company_name}}</option>
                                    @endforeach
                                    @endif
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-3">
                            <strong>Filter By Status</strong>
                            <select id="status" name="status" class="form-control" >
                                <option value="-1" selected >All</option>
                                <option value="Unpublished"  >Unpublished</option>
                                <option value="Published">Published</option>
                                <option value="Document Submited">Document Submited</option>
                                <option value="Accepted">Accepted</option>
                                <option value="Completed">Completed</option>
                                <option value="Unassigned">Unassigned</option>
                            </select>
                        </div>

                        <div class="col-md-3 col-sm-3">
                            <strong>Filter By City</strong>
                            <select id="city" name="city" class="form-control" >
                                <option value="-1" selected >All</option>
                                @foreach($city_names as $city)
                                    <option value="{{$city}}">{{$city}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 col-sm-3">
                            <label class="ui-check m-a-0">
                                <input id="check_datefilter"  type="checkbox"><i></i><strong style="margin-left: 0px;">Apply Date Range Filter</strong>
                            </label>
                            <input type="text" name="daterange" id="daterange" class="form-control" value="" />
                        </div>
                    </div>

                    <table class="table table-striped table-bordered" id="pickup_table">

                        <thead>

                        <tr class="filters">

                            <th>Application Id</th>

                            <th>Company</th>

                            <th>FC</th>

                            <th>Product</th>



                            <th>Pickup Person</th>

                            <th>Pickup Date</th>

                            <th>City</th>

                            <th>Pincode</th>

                            <th>Status</th>
                            <th>Pdf Link</th>
                           
                           



                        </tr>

                        </thead>

                        <tbody>

                        </tbody>

                    </table>



                </div>

            </div>

        </div>    <!-- row-->

    </section>
    <div class="modal fade" id="CustomerMailModal" tabindex="-1" role="dialog" aria-labelledby="user_delete_confirm_title" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3><i class="material-icons">
                            &#xe02e;</i> {{ trans('backLang.compose') }}
                    </h3>
                </div>
                <div class="modal-body">
                    <div class="box">
                        <div class="box-body">
                            {{Form::open(['route'=>['customer.pickups.webmailSend'],'method'=>'POST', 'files' => true ])}}
                            <div class="form-group row">
                                <input type="hidden" name="filter_status" id="filter_status" value="">
                                <input type="hidden" name="filter_company"id="filter_company" value="">
                                <input type="hidden" name="filter_city" id="filter_city" value="">
                                <input type="hidden" name="filter_daterange" id="filter_daterange" value="">

                                <label for="title_en"
                                       class="col-sm-2 form-control-label">{!!  trans('backLang.sendTo') !!}
                                </label>
                                <div class="col-sm-9" id="mail_id">
                                    <select id="to_email"  class="form-control select">
                                        @if(session()->get('customer')->customer_id != -1)
                                            <option value="{{$company_names->email}}">{{$company_names->company_name}}</option>
                                        @else
                                            <option value="-1" selected >All</option>
                                        @foreach($company_names as $company_name)
                                                <option value="{{$company_name->id}}">{{$company_name->company_name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-sm-1">
                                    <small>
                                        <a onclick="document.getElementById('bcc').style.display='block'">{!!  trans('backLang.sendBcc') !!}</a>
                                    </small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="title_en"
                                       class="col-sm-2 form-control-label">Email Id
                                </label>
                               <span style="margin-left: 12px;"> <strong>Note:</strong> For Multiple Email Id's Use Comma(,)</span>
                                <div class="col-sm-10 emailInput">
                                        {{--<div id="email_label"></div>--}}
                                    {{--<input name="emailids" id="hidden_email" class="form-control" value="" type="hidden">--}}
                                    <input name="emailids"  class="form-control" id="emailids" >
                                </div>
                            </div>

                            <div id="cc"  class="form-group row">
                                <label for="title_en"
                                       class="col-sm-2 form-control-label">{!!  trans('backLang.sendCc') !!}
                                </label>
                                <div class="col-sm-10">
                                    @if(session()->get('customer')->customer_id != -1)
                                    <input  type="text" id="to_cc" name="to_cc" class="form-control" value="{{$company_names->email_cc}}">
                                        @else
                                        <input  type="text" id="to_cc" name="to_cc" class="form-control" value="">
                                    @endif
                                </div>
                            </div>
                            <div id="bcc" style="display: none" class="form-group row">
                                <label for="title_en"
                                       class="col-sm-2 form-control-label">{!!  trans('backLang.sendBcc') !!}
                                </label>
                                <div class="col-sm-9">
                                    {!! Form::email('to_bcc','', array('placeholder' => '','class' => 'form-control','id'=>'to_bcc')) !!}
                                </div>
                                <div class="col-sm-1">
                                    <a onclick="document.getElementById('to_bcc').value='';document.getElementById('bcc').style.display='none'"><i
                                                class="material-icons md-18">×</i></a>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="title_en"
                                       class="col-sm-2 form-control-label">Subject
                                </label>
                                <div class="col-sm-10">
                                    {!! Form::text('title','Status Update', array('placeholder' => '','class' => 'form-control','id'=>'title','required'=>'')) !!}
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12" >
                                    <textarea name="details" ui-jp="summernote" class="form-control" style="height:200px">Please find attached Daily Report </textarea>

                                    {{--{!! Form::textarea('details','', array('ui-jp'=>'summernote','placeholder' => '','class' => 'form-control','ui-options'=>'{height: 250}')) !!}--}}
                                </div>
                            </div>
                            <div class="form-group row m-t-md">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-primary m-t"><i class="material-icons">&#xe31b;</i> {!! trans('backLang.send') !!}</button>
                                    <a data-dismiss="modal" class="btn btn-default m-t"><i class="material-icons">&#xe5cd;</i> {!! trans('backLang.cancel') !!}</a>
                                    </div>
                                </div>
                            {{Form::close()}}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection



{{-- page level scripts --}}

@section('footerInclude')

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://codeseven.github.io/toastr/build/toastr.min.js"></script>

    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"></script>


    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>



    <script>
        //For DateRangePicker
        $('#daterange').attr('value','');
        $(function() {
            $('input[name="daterange"]').daterangepicker({
                opens: 'left',
                locale: {
                    format: 'DD-MM-YYYY',

                },
            }, function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
        });
        $(function () {

            $('body').on('hidden.bs.modal', '.modal', function () {

                $(this).removeData('bs.modal');

            });

        });

        $('.select2-selection__choice__remove').click(function(){
            $(this).hide();
        })

//        $('.emailInput').on('keyup','#emailids',function(e){
//            if (e.which == 188 || e.which == 13) {
//                $email = $(this).val();
//                showData ='<label class="select2-selection__choice"  style="color: #555; background: #fff;border: 1px solid #ccc;border-radius: 4px;cursor: default;float: left;margin: 5px 0 0 6px;padding: 0 6px;line-height: 1.428571429;list-style: none;" title='+$email.replace(',','')+'>'+
//                        '<span  style="cursor: pointer;" class="select2-selection__choice__remove"  role="presentation">×</span>'+$email.replace(',','')+'</label>';
//                $('#email_label').append(showData);
//                var ids =  $('#hidden_email').val() + $email;
//                $('#hidden_email').attr('value',ids);
//                $(this).val('');
//            }
//        });


       $('#to_email').on('change',function(){
           if($('#to_email').val() == -1){
               var admin_email = 'mesanketshah@gmail.com';
               $('#emailids').attr('value',admin_email);
           }else{
               $.ajax({

                   url: '{!! route('customer.pickups.set_mailId') !!}',

                   data: {customer_id: $('#to_email').val()},

                   type: 'get',

                   datatype: 'json',

                   success: function (res) {
                       var $data = JSON.parse(res);
                       if($data){
                           $('#emailids').attr('value',$data.email);
                           $('#to_cc').attr('value',$data.email_cc);
                       }
                   }
               });
           }
       });


        //Onclick Event On Expor Excel
        function ExportExcel(){
            var status =  $('#status').val();
            var city = $('#city').val();
            var company = $('#filter_company').val();
            var daterange = $('#filter_daterange').val();

            if(status != -1 || city != -1 || company != '' || daterange != ''){
                var url = '{!! route('customer.pickups.allPickupExport',['status','city','company','date']) !!}';
                var url1 = url.replace('status', status);
                var url2 = url1.replace('city', city);
                if(company != ''){
                    var url3 = url2.replace('company', company);
                }else{
                    var url3 = url2.replace('company', -1);
                }
                if(daterange != ''){
                    var url4 = url3.replace('date', daterange);
                }else{
                    var url4 = url3.replace('date', -1);
                }
                $('#export_excel').attr('href',url4);

            }else{
                var url = '{!! route('customer.pickups.allPickupExport',['-1','-1','-1','-1']) !!}';
               $('#export_excel').attr('href',url);

            }
        }

        //Set Emial Id accoding To Change On MailBox
        {{--var to_email = null;--}}
        {{--$('#to_email').on('change',function(){--}}
            {{--to_email = $(this).val();--}}
            {{--if(to_email == -1){--}}
                {{--var admin_email = 'mesanketshah@gmail.com';--}}
                {{--$('#emailids').attr('value',admin_email);--}}
            {{--}else{--}}
                {{--$('#emailids').attr('value',to_email);--}}
            {{--}--}}

        {{--});--}}
        var session_value = {!! session()->get('customer')->customer_id !!};
        if(session_value != -1){
            $('#emailids').attr('value', $('#to_email').val());
        }else{
            if($('#to_email').val() == -1){
                var admin_email = 'mesanketshah@gmail.com';
                $('#emailids').attr('value',admin_email);
            }
        }


        $(function() {
            var status = null;
            var city = null;
            var company = null;
            var company_email = null;
            var daterange = null;

            $('#status').on('change',function(){
                status = $(this).val();
                $('#filter_status').attr('value',status);
                table.draw();
            });

            $('#city').on('change',function(){
                city = $(this).val();
                $('#filter_city').attr('value',city);
                table.draw();
            });

             $('#daterange').on('change',function(){
                if ($('#check_datefilter').is(':checked')) {
                    daterange = $(this).val();
                    $('#filter_daterange').attr('value',daterange);
                    table.draw();
                } else {
                    toastr.error('Please Select Checkbox To Apply Filter');
                }
            });
            //For Datatable Refresh On Unchecked Checkbox
            $('#check_datefilter').on('change',function() {
                if ($('#check_datefilter').is(':checked')) {
                } else {
                    daterange = null;
                    $('#filter_daterange').attr('value','');
                    table.ajax.reload();
                }
            });

            $('#company').on('change',function(){
                var data = $(this).val();
                 var  split_data  = data.split('/');
                company = split_data[0];
                company_email = split_data[1];
                $('#filter_company').attr('value',company);
               if(company == -1){
                   var admin_email = 'mesanketshah@gmail.com';
                   $('#emailids').attr('value',admin_email);
               }else{
                   $('#emailids').attr('value',company_email);
               }
                $("div#mail_id select.select option").each(function(){
                    if($(this).val()== company_email){
                        $(this).attr("selected","selected");
                    }
                });
                table.draw();
            });
            var table = $('#pickup_table').DataTable( {

            dom:'<"toolbar">frtlip',

        processing: true,

        serverSide: true,

        "lengthMenu": [[10, 20, 40, 100], [10, 20, 40, 100]],

        ajax: {
            url: '{!! route('customer.pickups.data') !!}',
            data: function (d) {
                d.status = status;
                d.city = city;
                d.company = company;
                d.daterange = daterange;
            }
            },

        order: [[0, 'asc']],

        columns: [

        { data: 'application_id', name: 'application_id' },

        { data: 'company_name', name: 'company_name' },

        { data: 'agent_name', name: 'agent_name' },

        { data: 'job_title', name: 'job_title' },



        { data: 'pickup_person', name: 'pickup_person' },

        { data: 'pickup_date', name: 'pickup_date'},

        { data: 'city', name: 'city' },

            { data: 'pincode', name: 'pincode' },

            { data: 'status', name: 'status' },
            { data: 'actions', name: 'actions' },
           
           




        ],

        });

        } );

    </script>

@endsection

