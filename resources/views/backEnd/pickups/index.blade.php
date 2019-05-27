@extends('backEnd.layout')
{{-- page level styles --}}
@section('headerInclude')
    <title>{{ trans('backLang.control') }} | Pickups</title>
    {{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="{{ URL::to('public/css/datatables.css') }}">
    <link href="https://codeseven.github.io/toastr/build/toastr.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css" rel="stylesheet" />

    {{--DateRangePicker--}}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        .daterangepicker td.active, .daterangepicker td.active:hover{
            background-color: #0cc2aa;
        }
        #Agent_selct_box .dropdown-menu{
            height: 250px; !important;
        }
        .bootstrap-select.btn-group .dropdown-menu li a {
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        .dropdown-menu>.active>a, .dropdown-menu>.active>a:hover, .dropdown-menu>.active>a:focus {
            color: #fff;
            text-decoration: none;
            background-color: #428bca;
            outline: 0;
        }
        .dropdown-menu>li>a {
            display: block;
            padding: 3px 20px;
            clear: both;
            font-weight: 400;
            line-height: 1.42857143;
            color: #333;
            white-space: nowrap;
        }
        .hidden {
            display: none!important;
        }

      .table > tbody > tr > td{
            padding-left: 5px;!important;
            padding-right: 5px;!important;
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
                        <h3>Pickups</h3>
                        <small>
                            <a href="{{route('adminHome')}}">Home</a> /
                            <a href="{{route('pickups')}}">Pickups</a>
                        </small>
                    </div>
                    <div class="row p-a pull-right" style="margin-top: -70px;">
                        <div class="col-sm-12">
                            {{--<a class="btn btn-fw primary" href="{{route("admin.pickups.create")}}">--}}
                                {{--<i class="material-icons">&#xe7fe;</i>--}}
                                {{--&nbsp; {{ trans('backLang.create_pickups') }}--}}
                            {{--</a>--}}
                            <a class="btn btn-fw info" href="{{route("pickups.import")}}">
                                <i class="material-icons">&#xe03b;</i>
                                &nbsp; {{ trans('backLang.import') }}
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        {{Form::open(['route'=>'pickups.updateStatus','method'=>'post','id'=>'UpdateStatusForm'])}}
                        <div class="row">
                            <div class="col-md-3 col-sm-2" style="padding-top: 13px;">
                                 <strong>Filter By Company Name</strong>
                                 <!--Here we use foreach because we need Company -->
                                    <select name="company_name" class="form-control" id="company_name">
                                        <option disable="true" selected="true" value="-1" >All</option>
                                       <?php foreach ($getCustomersList as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>"><?php
                                                echo ucwords($value['company_name']);
                                                ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                          </div>
                          <div class="col-md-3 col-sm-2" style="padding-top: 13px;">
                              <strong>Filter By Product Name</strong>
                              <!--Here we called product list -->
                                    <select name="product_name" class="form-control" id="product_name">
                                        <option disable="true" selected="true" value="-1" >All</option>
                                    </select>
                          </div>
                            <div class="col-md-3 col-sm-2">
                                <label class="ui-check m-a-0">
                                    <input id="check_datefilter"  type="checkbox"><i></i><strong style="margin-left: 0px;">Apply Date Range Filter</strong>
                                </label>
                                <input type="text" name="daterange" id="daterange" class="form-control" value="" />
                            </div>
                            <div class="col-md-3 col-sm-2" style="padding-top: 13px;">
                                <strong>Filter By Status</strong>
                                <select id="status" name="status" class="form-control" >
                                    <option value="-1" selected >All</option>
                                    <option value="Unpublished"  >Unpublished</option>
                                    <option value="Published" @if($status == "Published") selected @endif>Published</option>
                                    <option value="Document Submited">Document Submited</option>
                                    <option value="Accepted" @if($status == "Accepted") selected @endif>Accepted</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Unassigned">Unassigned</option>
                                </select>
                            </div>
                         </div>
                        <div class="col-md-3 col-sm-3" style="padding-top: 10px;padding-bottom: 10px;margin-left: -13px;">
                            <strong>Update Status</strong>
                            <select name="action" id="action"  class="form-control"
                                    required>
                                <option value="-1">Not Selected</option>
                                <option value="Unpublished"  >Unpublished</option>
                                <option value="Published">Published</option>
                            </select>
                        </div>
                        {{--Hidden Field For Status Redirect From Dashboard --}}
                        <input type="hidden" id="status_type" value="{{$status}}">
                        <div class="col-md-2 col-sm-2">
                                <a  style="margin-top: 21px;" class="btn btn-fw warning" onclick="updateStatus()" >
                                    {{ trans('backLang.apply') }}</a>
                        </div>

                        <table class="table table-striped table-bordered" id="pickup_table">
                            <thead>
                            <tr class="filters">
                                <th style="width:20px;">
                                    <label class="ui-check m-a-0">
                                        <input id="checkAll" type="checkbox"><i></i>
                                    </label>
                                </th>
                                <th>Application Id</th>
                                <th>Company</th>
                                <th>FC</th>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Pickup person</th>
                                <th>Pickup Date</th>
                                <th>City</th>
                                <th>Pincode</th>
                                <th>Status</th>
                                <th style="width:12%">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table><br>
                        {{Form::close()}}
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
    <script src="https://codeseven.github.io/toastr/build/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    {{--Make Pickup Pdf--}}
    <div class="modal fade" id="generate_pdf" tabindex="-1" role="dialog" aria-labelledby="user_delete_confirm_title" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(['route'=>'pickups.pdfview','method'=>'post','id'=>'GeneratePdf'])}}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" style="line-height: 1;">Generate PDF</h4>
                </div>
                <div class="modal-body">
                    <div class="row" >

                        <div class="col-md-6">
                            <label style=""><strong>Select Status</strong></label>
                        </div>
                        <div class="col-md-6">
                            <input id="pickup_pdf_id" name="pickup_pdf_id" value="" hidden>
                            <label class="ui-check m-a-0">
                                <input id="check_customer_mail" type="checkbox" name="check_customer_mail" class="has-value" value="1"><i></i>
                            </label>Check,if Want to mail Customer
                        </div>

                        <div class="col-md-12" id="">
                            <select class="form-control selectpicker" id="pickup_status" name="pickup_status" data-live-search="true">
                                <option value="-1">Not Selected</option>
                                <option value="Document Submited">Document Submited</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Generate</button>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
    <div class="modal fade" id="assign_agent" tabindex="-1" role="dialog" aria-labelledby="user_delete_confirm_title" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(['route'=>'pickups.assignAgent','method'=>'post','id'=>'assignAgentForm'])}}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" style="line-height: 1;" id="user_delete_confirm_title">Assign FC</h4>
                </div>
                <div class="modal-body">
                    <div class="row" >
                       <input id="pickup_id" name="pickup_id" value="" hidden>
                        <div class="col-md-12">
                            <label style=""><strong>Select FC</strong></label>
                        </div>
                        <div class="col-md-12" id="Agent_selct_box">
                            <select class="form-control selectpicker" id="assign_agent_id" name="assign_agent" data-live-search="true">
                                <option value="-1">Not Selected</option>
                                @foreach($agents as $agent)
                                    <option value="{{$agent->id}}">{{$agent->name}}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Assign</button>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
    <div class="modal fade" id="delete_pickup" tabindex="-1" role="dialog" aria-labelledby="user_delete_confirm_title" aria-hidden="true">
        <div class="modal-dialog" id="animate">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('backLang.confirmation') }}</h5>
                </div>
                <div class="modal-body text-center p-lg">
                    <p>
                        {{ trans('backLang.confirmationDeleteMsg') }}
                        <br>
                        <strong>[ Pickup ]</strong>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark-white p-x-md" data-dismiss="modal">{{ trans('backLang.no') }}</button>
                    <a href="{{route('pickups.delete',':id')}}" id="pickup_delete_id" class="btn danger p-x-md">{{ trans('backLang.yes') }}</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
          $("#company_name").change(function () {
                 var addCompanyNameId = $('#company_name option:selected').val();
                 if (addCompanyNameId !== '' && typeof addCompanyNameId !== 'undefined' && addCompanyNameId !== 'null') {
                     getProductListByCompanyId();
                 }
           });
          
          //OnChange Company List Display Particular Product Name
            function getProductListByCompanyId() {
                var customerDropDownId = $('#company_name option:selected').val();
                if (customerDropDownId !== '' && typeof customerDropDownId !== 'undefined') {
                  $.ajax({
                    type: "get",
                    url: "getProductListByCompanyId/" + customerDropDownId,
                    dataType: "json",
                    success: function (responseData) {
                        $('#product_name').empty();
                        $('#product_name').append($('<option>').text('All').attr('value', '')).trigger('change');
                        $.each(responseData, function (productindex, productname) {
                            if (productname.toLowerCase() == customerDropDownId.toLowerCase()) {
                                var newProductDropdownOptionSelected = new Option(productname, productindex, true, true);
                            } else {
                                var newProductDropdownOptionSelected = new Option(productname, productindex, false, false);
                            }
                            $('#product_name').append(newProductDropdownOptionSelected).trigger('change');
                        });
                    }
                 });
                }
             }          
        });

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
        $(function() {
            $('.selectpicker').selectpicker();
        });
        $(function () {
            $('body').on('hidden.bs.modal', '.modal', function () {
                $(this).removeData('bs.modal');
            });
        });
        $(function() {
            var status = null;
            var company_name = null;
            var product_name = null;
            var select_status = $('#status').val();
            var daterange = null;
            var status_type = $('#status_type').val();
            
            $('#company_name').on('change',function(){
                company_name = $(this).val();
                table.draw();
            });
            $('#product_name').on('change',function(){
                product_name = $(this).val();
                table.draw();
            });
            $('#status').on('change',function(){
                status = $(this).val();
                table.draw();
            });
            $('#daterange').on('change',function(){
                if ($('#check_datefilter').is(':checked')) {
                    daterange = $(this).val();
                    table.draw();
                } else {
                    toastr.error('Please Select Checkbox To Apply Filter');
                }
            })

            //For Datatable Refresh On Unchecked Checkbox
            $('#check_datefilter').on('change',function() {
                if ($('#check_datefilter').is(':checked')) {
                } else {
                    daterange = null;
                    table.ajax.reload();
                }
            });
             var table = $('#pickup_table').DataTable( {
                   // dom:'<"m-t-10 pull-left"l><"m-t-10 pull-right"f>rti<"m-t-10 pull-left"B><"m-t-10 pull-right"p>',
                  dom:'<"toolbar">frtlip',
                processing: true,
                serverSide: true,
                "lengthMenu": [[10, 20, 40, 100], [10, 20, 40, 100]],
                ajax: {
                    url: '{!! route('pickups.data') !!}',
                    data: function (d) {
                        d.status = status;
                        d.company_name = company_name;
                        d.product_name = product_name;
                        d.select_status = select_status;
                        d.daterange = daterange;
                        d.status_type = status_type;
                    }
                },
                //order: [[0, 'asc']],
                columns: [
                    { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false  },
                    { data: 'application_id', name: 'application_id' },
                    { data: 'company_name', name: 'company_name' },
                    { data: 'agent_name', name: 'agent_name' },
                    { data: 'job', name: 'job' },
                    { data: 'price', name: 'price' },
                    { data: 'pickup_person', name: 'pickup_person' },
                    { data: 'pickup_date', name: 'pickup_date' },
                    { data: 'city', name: 'city' },
                    { data: 'pincode', name: 'pincode' },
                    { data: 'status', name: 'status' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
            });
        } );

        //Validation on Checkbox Form
        function updateStatus() {
            var update_status = $('#action').val();
            if(update_status != -1){
                if($('.checkBoxClass').is(':checked')){
                    document.getElementById("UpdateStatusForm").submit();
                }else{
                    toastr.error('Please Select Atleast One Checkbox');
                }
            }else{
                toastr.error('Please Select Atleast One Status');

            }
        }

        //Validation On Assign agent
        $('#assignAgentForm').on('submit', function(e){
            if($('#assign_agent_id').val() != -1){
                $('#assesmentForm').submit();
            }else{
                alert("Please select value for Assign Agent");
                e.preventDefault();
            }
        });
        //Script For All Checkbox Check
        $("#checkAll").click(function () {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
        
        function setId($id) {
         $('#pickup_id').attr('value',$id);
            $('#pickup_pdf_id').attr('value',$id);
            var delete_link = $('#pickup_delete_id').attr('href');
            url = delete_link.replace(':id',$id);
            $('#pickup_delete_id').attr('href',url);
        }
        //Validation On Generate Pdf
        $('#GeneratePdf').on('submit', function(e){
            if($('#pickup_status').val() != -1){
                $('#GeneratePdf').submit();
            }else{
                alert("Please select Status for Generating Pdf");
                e.preventDefault();
            }
        });
    </script>
@endsection
