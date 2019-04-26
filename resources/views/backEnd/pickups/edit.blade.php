@extends('backEnd.layout')

{{-- page level styles --}}



@section('headerInclude')

<title>{{ trans('backLang.control') }} | Pickups</title>

    <link rel="stylesheet" href="{{ URL::to('public/css/user_profile/user_profile.css') }}">

    <link href="{{ URL::to('public/css/user_profile/jasny-bootstrap.css') }}" rel="stylesheet"/>

    <link href="{{ URL::to('public/css/user_profile/bootstrap-editable.css') }}" rel="stylesheet"/>

    <link href="{{  URL::to('public/js/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">

    <link href="http://codeseven.github.io/toastr/build/toastr.min.css" rel="stylesheet"/>

    <style>

        .container {

            padding-bottom:10px;

            overflow: hidden;

            position: relative;

            float: left;

            display: inline-block;

            cursor: pointer;

        }



        .child {

            height: 100%;



            background-size: cover;

            background-repeat: no-repeat;

            -webkit-transition: all .5s;

            -moz-transition: all .5s;

            -o-transition: all .5s;

            transition: all .5s;

        }



        .child img {

            -webkit-transition: 0.6s ease;

            transition: 0.6s ease;

            -transition: all 1s ease; /* Safari and Chrome */

            -moz-transition: all 1s ease; /* Firefox */

            -o-transition: all 1s ease; /* IE 9 */

            -ms-transition: all 1s ease; /* Opera */



        }



        .container:hover .child img {

            -webkit-transform: scale(1.2);

            kit-transform:scale(1.25); /* Safari and Chrome */

            -moz-transform:scale(1.25); /* Firefox */

            -ms-transform:scale(1.25); /* IE 9 */

            -o-transform:scale(1.25); /* Opera */

            transform:scale(1.25);

            filter: alpha(opacity=30);



        }
        .col-md-3{
            margin-top: -2px;
        }

    </style>



@endsection



{{-- Page content --}}

@section('content')

    <div class="padding">

        @if(session()->has('message'))

            <div class="alert alert-success alert-dismissible" id="alert_message" style="text-align: center">

                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

                <strong>{{ session()->get('message') }}</strong>

            </div>

        @endif

        <div class="box m-b-0">

            <div class="box-header dker">

                <h3>{{$Pickups->pickup_person}}

                    &nbsp;<label style="font-size: 14px;"><strong>{{\Carbon\Carbon::parse($Pickups->pickup_date)->format('d-m-Y')}} Start time: <span style="font-weight: lighter">{{\Carbon\Carbon::parse($Pickups->preferred_start_time)->format('h:i A')}}</span> End Time: <span style="font-weight: lighter">{{\Carbon\Carbon::parse($Pickups->preferred_end_time)->format('h:i A')}}</span></strong></label>

                </h3>

                <small>

                    <a href="{{ route('adminHome') }}">{{ trans('backLang.home') }}</a> /

                    <a href="{{ route('pickups.edit',$Pickups->id) }}">Edit Pickup</a> /

                    <a href="{{ route('pickups.edit',$Pickups->id) }}">{{$Pickups->pickup_person}}</a>

                </small>



            </div>

            <div class="box-tool">

                <ul class="nav">

                    <li class="nav-item inline ">

                        <a  class="btn btn-md white btn-addon default"   style="background-color: orange;color: white" data-toggle="modal" title="Reschedule  Pickup" value="" data-target="#reschedule_pickup"> {!! trans('backLang.reschedule') !!}</a>
                         @if($link_availble > 0)
                        <a  class="btn btn-md white btn-addon default"   style="background-color: #673ab7;color: white" data-toggle="modal" title="Mail  Links" value="" data-target="#mail_link">Mail Links</a>
                        @endif
                        @if($Pickups->status == 'UnPublished')
                            <a class="btn btn-md white btn-addon success"  id="published"  onclick="SetPublish({{$Pickups->id}});">
                                {!! trans('backLang.Published') !!}</a>
                        @endif
                        @if($Pickups->status == 'Completed' ||  $Pickups->status == 'Accepted' ||  $Pickups->status == 'Document Submited')
                            <a class="btn btn-md white btn-addon warning" id="unpublished" onclick="SetPublish({{$Pickups->id}});">
                                Republish</a>
                        @endif


                    </li>

                </ul>

            </div>

        </div>

        <div class="box nav-active-border b-info">

            <ul class="nav nav-md">

                <li class="nav-item inline active" >

                    <a data-toggle="tab" class="nav-link @if(session()->get('document')!=1)) active @endif" href="#pickup_screem" >

                            <span class="text-md"><i class="material-icons">

                                    &#xe31e;</i> {{ trans('backLang.topicTabDetails') }}</span>

                    </a>

                </li>



                <li class="nav-item inline">

                    <a data-toggle="tab" class="nav-link @if(session()->get('document')!=0)) active @endif" href="#document">

                        <span class="text-md"><i class="material-icons">

                                &#xe0b9;</i> {{ trans('backLang.questions') }}

                        </span></a>

                </li>

                <li class="nav-item inline">

                    <a data-toggle="tab" class="nav-link" href="#submited_document">

                        <span class="text-md"><i class="material-icons">

                            </i> {{ trans('backLang.submit_document') }}

                        </span></a>

                </li>

                <li class="nav-item inline">

                    <a data-toggle="tab" class="nav-link" href="#delivery_details">

                        <span class="text-md"><i class="material-icons">

                                </i> {{ trans('backLang.delivery_details') }}

                        </span></a>

                </li>

                <li class="nav-item inline">

                    <a data-toggle="tab" class="nav-link" href="#pickup_reschedule">

                        <span class="text-md"><i class="material-icons">

                                </i> Schedule History

                        </span></a>

                </li>

            </ul>

            <div class="tab-content clear b-t">

                <div id="pickup_screem" class="tab-pane fade @if(session()->get('document')!=1) in active @endif">

                    <div class="box-body" style="margin-top: -20px;">

                        {{Form::open(['route'=>['pickups.update',$Pickups->id],'method'=>'POST'])}}

                    {{csrf_field()}}

                        <div class="form-group row">

                            <div class="col-md-3">

                            <label for="pickup_person"

                                   class=" form-control-label">{!!  trans('backLang.pickup_person') !!}

                            </label>

                            {!! Form::text('pickup_person',$Pickups->pickup_person, array('placeholder' => '','class' => 'form-control','id'=>'pickup_person','required'=>'')) !!}

                            </div>

                            <div class="col-md-3">

                                <label for="pickup_email" class=" form-control-label">Pickup Email</label>

                                {!! Form::text('pickup_email',$Pickups->pickup_email, array('placeholder' => '','class' => 'form-control','id'=>'pickup_email','required'=>'')) !!}

                            </div>

                            <div class="col-md-3">

                                <label for="application_id"

                                       class=" form-control-label">{!!  trans('backLang.application_id') !!}

                                </label>

                                {!! Form::text('application_id',$Pickups->application_id, array('placeholder' => '','class' => 'form-control','id'=>'application_id','required'=>'')) !!}

                            </div>

                            <div class="col-md-3">

                                <label for="mobile"  class=" form-control-label">{!!  trans('backLang.mobile') !!}</label>

                                {!! Form::text('mobile',$Pickups->mobile, array('placeholder' => '','class' => 'form-control','id'=>'mobile','required'=>'')) !!}

                            </div>
                             <div class="col-md-3">

                                <label for="status"  class="form-control-label">{!!  trans('backLang.status') !!}</label>

                                <select id="status" name="status" class="form-control" style="">

                                    <option value="UnPublished" @if($Pickups->status == 'UnPublished' ) selected  @endif>UnPublished</option>
                                    <option value="Published" @if($Pickups->status == 'Published' ) selected  @endif>Published</option>

                                    <option value="Document Submited" @if($Pickups->status == 'Document Submited' ) selected  @endif>Document Submited</option>

                                    <option value="Accepted" @if($Pickups->status == 'Accepted' ) selected  @endif>Accepted</option>

                                    <option value="Completed" @if($Pickups->status == 'Completed' ) selected  @endif>Completed</option>

                                </select>

                            </div>
                            <div class="col-md-3">

                                <label for="mobile"  class=" form-control-label">Cheque Amt</label>

                                {!! Form::text('cheque_amt',$Pickups->cheque_amt, array('placeholder' => '','class' => 'form-control','id'=>'cheque_amt')) !!}

                            </div>
                            <div class="col-md-3">

                                <label for="mobile"  class=" form-control-label">Loan Amt</label>

                                {!! Form::text('loan_amt',$Pickups->loan_amt, array('placeholder' => '','class' => 'form-control','id'=>'loan_amt')) !!}

                            </div>
                            <div class="col-md-3">

                                <label for="mobile"  class=" form-control-label">pod_number</label>
                                {!! Form::text('pod_number',$Pickups->pod_number, array('placeholder' => '','class' => 'form-control','id'=>'pod_number')) !!}

                            </div>


                            <div class="row"><label style="margin-top: 8px;" ><strong style="margin-left: 22px;font-size: 18px;">{!!  trans('backLang.home_address') !!}</strong></label></div><hr  style="margin-top: 0rem;margin-bottom: 0rem;">
                            <div class="col-md-3" >

                                <label for="address" class=" form-control-label">{!!  trans('backLang.home_address') !!}</label>

                                {!! Form::text('home_address',$Pickups->home_address, array('placeholder' => '','class' => 'form-control','id'=>'home_address','required'=>'')) !!}

                            </div>

                            <div class="col-md-3">

                                <label for="city"

                                       class=" form-control-label">{!!  trans('backLang.city') !!}

                                </label>

                                {!! Form::text('city',$Pickups->city, array('placeholder' => '','class' => 'form-control','id'=>'city','required'=>'')) !!}

                            </div>

                            <div class="col-md-3">

                                <label for="state"

                                       class="form-control-label">{!!  trans('backLang.state') !!}

                                </label>

                                {!! Form::text('state',$Pickups->state, array('placeholder' => '','class' => 'form-control','id'=>'state','required'=>'')) !!}

                            </div>

                            <div class="col-md-3">

                                <label for="pincode" class=" form-control-label">{!!  trans('backLang.pincode') !!}</label>

                                {!! Form::text('pincode',$Pickups->pincode, array('placeholder' => '','class' => 'form-control','id'=>'pincode','required'=>'')) !!}

                            </div>
                            <div class="row"><label style="margin-top: 8px;" ><strong style="margin-left: 22px;font-size: 18px;">{!!  trans('backLang.office_address') !!}</strong></label> <label class="ui-check ">
                                    <input id="check_adress" type="checkbox" class="has-value"><i></i> Check If office address same as home address
                                </label></div><hr  style="margin-top: 0rem;margin-bottom: 0rem;">
                            <div class="col-md-3">

                                <label for="address" class=" form-control-label">{!!  trans('backLang.office_address') !!}</label>

                                {!! Form::text('office_address',$Pickups->office_address, array('placeholder' => '','class' => 'form-control','id'=>'office_address','required'=>'')) !!}

                            </div>

                            <div class="col-md-3">

                                <label for="city"

                                       class=" form-control-label">{!!  trans('backLang.office_city') !!}

                                </label>

                                {!! Form::text('office_city',$Pickups->office_city, array('placeholder' => '','class' => 'form-control','id'=>'office_city','required'=>'')) !!}

                            </div>

                            <div class="col-md-3">

                                <label for="state"

                                       class="form-control-label">{!!  trans('backLang.office_state') !!}

                                </label>

                                {!! Form::text('office_state',$Pickups->office_state, array('placeholder' => '','class' => 'form-control','id'=>'office_state','required'=>'')) !!}

                            </div>

                            <div class="col-md-3">

                                <label for="pincode" class=" form-control-label">{!!  trans('backLang.office_pincode') !!}</label>

                                {!! Form::text('office_pincode',$Pickups->office_pincode, array('placeholder' => '','class' => 'form-control','id'=>'office_pincode','required'=>'')) !!}

                            </div>

                            <div class="col-md-3">

                                <label for="price"  class=" form-control-label">{!! trans('backLang.price') !!}</label>

                                {!! Form::text('price',$Pickups->price, array('placeholder' => '','class' => 'form-control','id'=>'price','required'=>'')) !!}

                            </div>

                            <div class="col-md-3">

                                <label for="customer"  class="form-control-label">{!!  trans('backLang.company') !!}</label>

                                <select id="customer" name="customer" class="form-control" style="">

                                    <option value="-1">Not Selected</option>

                                    @foreach($customers as $customer)

                                        <option value="{{$customer->id}}" @if($Pickups->customer_id == $customer->id ) selected  @endif>{{$customer->name}}</option>

                                    @endforeach

                                </select>

                            </div>

                            <div class="col-md-3">

                                <label for="job"  class=" form-control-label">{!!  trans('backLang.products') !!}</label>

                                <select id="product_id" name="product" class="form-control" style="">

                                    <option value="-1">Not Selected</option>

                                    @foreach($products as $product)

                                        <option value ="{{$product->id}}" @if($Pickups->product_id == $product->id ) selected  @endif>{{$product->name}}</option>

                                    @endforeach

                                </select>

                            </div>

                            <div class="col-md-3">

                                <label for="agent" class=" form-control-label">{!!  trans('backLang.fc') !!}

                                </label>

                                <select id="agent" name="agent" class="form-control" style="">

                                    <option value="-1">Not Selected</option>

                                    @foreach($agents as $agent)

                                        <option value="{{$agent->id}}" @if($Pickups->agent_id == $agent->id ) selected  @endif>{{$agent->name}}</option>

                                    @endforeach

                                </select>

                            </div>

                            <div class="col-md-3">

                                <label for="status"  class="form-control-label">{!!  trans('backLang.status') !!}</label>

                                <select id="status" name="status" class="form-control" style="">
                                     <option value="UnPublished" @if($Pickups->status == 'UnPublished' ) selected  @endif>UnPublished</option>
                         

                                    <option value="Published" @if($Pickups->status == 'Published' ) selected  @endif>Published</option>

                                    <option value="Document Submited" @if($Pickups->status == 'Document Submited' ) selected  @endif>Document Submited</option>

                                    <option value="Accepted" @if($Pickups->status == 'Accepted' ) selected  @endif>Accepted</option>

                                    <option value="Completed" @if($Pickups->status == 'Completed' ) selected  @endif>Completed</option>

                                </select>

                            </div>

                            <div class="form-group row  m-t-md ">

                                <div class="col-sm-offset-5 col-sm-10" style="padding-top: 15px;">

                                    <button type="submit" class="btn btn-primary m-t"><i class="material-icons">

                                            &#xe31b;</i> {!! trans('backLang.update') !!}</button>

                                    <a href="{{ route('pickups') }}"

                                       class="btn btn-danger m-t"><i class="material-icons">

                                            &#xe5cd;</i> {!! trans('backLang.cancel') !!}</a>

                                </div>
                            </div>

                        </div>

                        {{Form::close()}}

                    </div>

                </div>



                <div id="document" class="tab-pane fade @if(session()->get('document')!=0) in active @endif">

                    <div class="box-body">

                        <div id="edit_document_table">

                            @if($pickup_documents->count() !=0)

                                {{Form::open(['route'=>['pickups'],'method'=>'post'])}}

                                <div class="row">

                                    <table class="table table-striped  b-t">

                                        <thead>

                                        <tr>

                                            <th>{{ trans('backLang.sequence') }}</th>

                                            <th>{{ trans('backLang.question') }}</th>
                                            <th>Links</th>

                                            <th>{{ trans('backLang.topicComment') }}</th>

                                            <th>Document Pictures</th>

                                            <th>{{ trans('backLang.options') }}</th>

                                        </tr>

                                        </thead>

                                    <tbody>

                                    @foreach($submited_documents->documents as $documents)

                                        <tr>

                                            <td>
                                                <input type="hidden" value="{{$documents->id}}" name="edit_document[]">
                                                {{ $documents->sequence}}
                                            </td>

                                            <td>
                                                {{ $documents->question}}
                                            </td>

                                            <td>
                                                <ol style="margin-bottom: 0px; padding-left: 15px;">
                                                @foreach($documents->links as $link)
                                                        <li><a  target="new" href="{!! $link->link !!}">{{ $link->name }}</a></li>
                                                @endforeach
                                                </ol>
                                            </td>

                                            <td>
                                                @if($documents->comments !=null){{ $documents->comments}} @else  @endif
                                            </td>

                                            <td>
                                                @foreach($documents->pictures as $document_picture)

                                                    <div class="col-md-3">

                                                        @if($document_picture->filename != null)

                                                        <a href="{{ URL::to('public/uploads/pickups/'.$document_picture->filename) }}" target="new">

                                                        <img src="{{ URL::to('public/uploads/pickups/'.$document_picture->filename) }}"

                                                             alt="{{ $document_picture->filename }}" title="{{ $document_picture->filename  }}"

                                                             style="height: 60px;"

                                                             class="img-responsive"></a>

                                                            @else <i class="fa fa-times text-danger inline"></i> @endif

                                                    </div>

                                                @endforeach
                                            </td>

                                            <td class="text-center">
                                                <a class="btn btn-sm success"

                                                   onclick="Editdocument({{$documents->id}});">

                                                    <small>{{ trans('backLang.edit') }}</small>

                                                </a>

                                                @if(@Auth::user()->permissionsGroup->delete_status)

                                                    <button class="btn btn-sm warning" data-toggle="modal"

                                                            data-target="#mc-{{ $documents->id }}"

                                                            ui-toggle-class="bounce"

                                                            ui-target="#animate">

                                                        <small> {{ trans('backLang.delete') }}

                                                        </small>

                                                    </button>

                                                @endif
                                            </td>

                                        </tr>

                                        <!-- .modal -->

                                        <div id="mc-{{ $documents->id }}" class="modal fade" data-backdrop="true">

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

                                                        <button type="button" class="btn dark-white p-x-md"

                                                                data-dismiss="modal">{{ trans('backLang.no') }}</button>

                                                        <a href="{{ route("pickups.document_delete",$documents->id) }}"

                                                           class="btn danger p-x-md">{{ trans('backLang.yes') }}</a>

                                                    </div>

                                                </div><!-- /.modal-content -->

                                            </div>

                                        </div>

                                    @endforeach

                                    </tbody>

                                </table>

                                </div>

                                {{Form::close()}}

                            @else

                        @endif

                    </div>

                        <div class="form-group row" id="edit_document_div">

                        </div>

                    </div>

                </div>

                <div id="submited_document" class="tab-pane fade">

                    <div class="box-body">

                        <div id="edit_document_table">

                            {{Form::open(['route'=>['pickups'],'method'=>'post'])}}

                                <div class="row">

                                    <table class="table table-striped  b-t">

                                        <thead>

                                        <div class="col-md-6">

                                            <label><strong>Document Submited Date</strong></label>:

                                            @if($document_submit_date != null)

                                                <span>{{\Carbon\Carbon::parse($document_submit_date->created_at)->format('d-m-Y')}}</span>

                                            @else

                                                <span>Not Submited</span>

                                            @endif

                                        </div>

                                        <div class="col-md-6">

                                            <label><strong>POD Completed Date</strong></label>:

                                            @if($Pickups->completed_at != null)

                                                <span>{{\Carbon\Carbon::parse($Pickups->completed_at)->format('d-m-Y')}}</span>

                                            @else

                                                <span>Not Submited</span>

                                            @endif

                                        </div>

                                        <tr>

                                            <th>{{ trans('backLang.sequence') }}</th>

                                            <th>Document Picture</th>

                                        </tr>

                                        </thead>

                                        <tbody>

                                        @foreach($submited_documents->documents as $submited_document)

                                            <tr>

                                                <td>
                                                    <input type="hidden" value="{{$submited_document->id}}" name="edit_document[]">
                                                    {{ $submited_document->sequence}}

                                                </td>

                                                <td>

                                                        @foreach($submited_document->pictures as $document_pictures)

                                                            @if($document_pictures->count() > 0)

                                                                <div class="container" onclick="">

                                                                <div class="col-sm-2 child childimage" >

                                                                    <a href="{{ URL::to('public/uploads/pickups/'.$document_pictures->filename) }}" target="new">

                                                                        <img src="{{ URL::to('public/uploads/pickups/'.$document_pictures->filename) }}"

                                                                             alt="{{ $document_pictures->filename }}" title="{{ $document_pictures->filename  }}"

                                                                             style="height: 60px;"

                                                                             class="img-responsive">

                                                                    </a>

                                                                </div>

                                                                </div>

                                                     @else

                                                    Not Submited

                                                    @endif

                                                        @endforeach

                                                    </td>

                                            </tr>

                                            <!-- .modal -->

                                            <div id="mc-{{ $submited_document->id }}" class="modal fade" data-backdrop="true">

                                                <div class="modal-dialog" id="animate">

                                                    <div class="modal-content">

                                                        <div class="modal-header">

                                                            <h5 class="modal-title">{{ trans('backLang.confirmation') }}</h5>

                                                        </div>

                                                        <div class="modal-body text-center p-lg">

                                                            <p>

                                                                {{ trans('backLang.confirmationDeleteMsg') }}

                                                                <br>



                                                                <strong>[ {!! $submited_document->document_name !!} ]</strong>

                                                            </p>

                                                        </div>

                                                        <div class="modal-footer">

                                                            <button type="button" class="btn dark-white p-x-md"

                                                                    data-dismiss="modal">{{ trans('backLang.no') }}</button>

                                                            <a href="{{ route("pickups.document_delete",$submited_document->id) }}"

                                                               class="btn danger p-x-md">{{ trans('backLang.yes') }}</a>

                                                        </div>

                                                    </div><!-- /.modal-content -->

                                                </div>

                                            </div>

                                            <!-- / .modal -->

                                        @endforeach

                                        </tbody>

                                    </table>

                                </div>

                            {{Form::close()}}

                        </div>

                    </div>

                </div>

                <div id="delivery_details" class="tab-pane fade">

                    <div class="box-body">

                        <div id="edit_document_table">

                            <div class="row">

                                <table class="table table-striped  b-t">

                                    <thead>

                                    <tr>

                                        <th>{{ trans('backLang.delivery_number') }}</th>

                                        <th>{{ trans('backLang.pod_number') }}</th>

                                    </tr>

                                    </thead>

                                    <tbody>

                                    <td>@if($Pickups->delivery_number !=null || $Pickups->delivery_number != '')

                                            {{$Pickups->delivery_number}}

                                        @else

                                            NA

                                        @endif

                                    </td>

                                    <td>

                                        @if($Pickups->pod_number !=null )

                                            <div class="col-sm-2">

                                                <img src="{{ URL::to('public/uploads/pickups/'.$Pickups->pod_number) }}"

                                                     alt="{{ $Pickups->pod_number }}" title="{{ $Pickups->pod_number  }}"

                                                     style="height: 60px;"

                                                     class="img-responsive">

                                            </div>

                                        @else

                                            NA

                                        @endif

                                    </td>

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

                <div id="pickup_reschedule" class="tab-pane fade">

                    <div class="box-body">

                        <div id="edit_document_table">

                            <div class="row">

                                <table class="table table-striped  b-t">

                                    <thead>

                                    <tr>

                                        <th>{{ trans('backLang.pickup_date') }}</th>
                                        <th>{{ trans('backLang.preferred_start_time') }}</th>
                                        <th>{{ trans('backLang.preferred_end_time') }}</th>
                                        <th>{{ trans('backLang.comments') }}</th>
                                        <th>Updated By</th>
                                        <th>Updated At</th>

                                    </tr>

                                    </thead>

                                    <tbody>

                                    @foreach($Pickupschedules as $Pickupschedule)

                                        <tr>

                                            <td>{{\Carbon\Carbon::parse($Pickupschedule->pickup_date)->format('d-m-Y')}}</td>
                                            <td>{{\Carbon\Carbon::parse($Pickupschedule->pickup_startime)->format('h:i A')}}</td>
                                            <td>{{\Carbon\Carbon::parse($Pickupschedule->pickup_endtime)->format('h:i A')}}</td>
                                            <td>{{$Pickupschedule->comments}}</td>
                                            <td>{{$Pickupschedule->created_by}}</td>
                                            <td>{{\Carbon\Carbon::parse($Pickupschedule->created_at)->diffForHumans()}}</td>

                                        </tr>

                                    @endforeach

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@stop



{{-- page level scripts --}}

@section('footerInclude')

    <script src="{{URL::to('public/backEnd/scripts/app.html.js')}}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>

    <script src="http://codeseven.github.io/toastr/build/toastr.min.js"></script>

    <script src="{{URL::to('public/js/pickup_schedule.js')}}"></script>



    <div class="modal fade" id="reschedule_pickup" tabindex="-1" role="dialog" aria-labelledby="user_delete_confirm_title" aria-hidden="true">

        <div class="modal-dialog">

            <div class="modal-content">

                {{Form::open(['route'=>'pickups.ReschedulePickup','method'=>'post','id'=>'RescheduleForm'])}}

                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                    <h4 class="modal-title" style="line-height: 1;" id="user_delete_confirm_title">@lang('backLang.reschedule')</h4>

                </div>

                <div class="modal-body">

                    <div class="row" >

                        <div class="col-md-6">

                            <label for="start_time"

                                   class="form-control-label">{!!  trans('backLang.preferred_start_time') !!}

                            </label>

                            {!! Form::text('start_time',$Pickups->preferred_start_time, array('placeholder' => '','class' => 'form-control','id'=>'start_time','required'=>'')) !!}

                        </div>

                        <div class="col-md-6">

                            <label for="end_time"

                                   class="form-control-label">{!!  trans('backLang.preferred_end_time') !!}

                            </label>

                            {!! Form::text('end_time',$Pickups->preferred_end_time, array('placeholder' => '','class' => 'form-control','id'=>'end_time','required'=>'')) !!}

                        </div>

                        <div class="col-md-12">

                            <label for="pickup_date"

                                   class=" form-control-label">{!!  trans('backLang.pickup_date') !!}

                            </label>

                            <?php  $pickup_date = str_replace('-', '/', $Pickups->pickup_date);

                            $pickup_date = \Carbon\Carbon::parse($Pickups->pickup_date)->format('d-m-Y');

                            ?>

                            {!! Form::text('pickup_date',$pickup_date, array('placeholder' => '','class' => 'form-control','id'=>'pickup_date','required'=>'')) !!}

                        </div>

                        <div class="col-md-12">

                            <label for="pickup_date"

                                   class=" form-control-label">Comments

                            </label>

                            {!! Form::textarea('comments','', array('placeholder' => '','class' => 'form-control','id'=>'comments','rows'=>'4')) !!}
                            <span class="span_class" id="comments_span" style="display: none;font-size: 13px;color: indianred;">Please Enter Comments</span>
                        </div>

                        <input id="pickup_id" name="pickup_id" value="{{$Pickups->id}}" hidden>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

                    <button type="submit" class="btn btn-success">Reschedule</button>

                </div>

                {{Form::close()}}

            </div>

        </div>

    </div>
    {{--Mail link model--}}
    <div class="modal fade" id="mail_link" tabindex="-1" role="dialog" aria-labelledby="user_delete_confirm_title" aria-hidden="true">

        <div class="modal-dialog">

            <div class="modal-content">

                {{Form::open(['route'=>'pickups.SendLinkMail','method'=>'post','id'=>'sendlinkmail'])}}

                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    @if(count($submited_documents->documents) > 0)
                        <span style="float: right;margin-right: 24px;">Check All</span>

                    <label class="ui-check m-a-0" style="float: right">
                        <input id="checkAll" type="checkbox"><i></i>
                    </label>
                    @endif
                    <h4 class="modal-title" style="line-height: 1;" id="user_delete_confirm_title">Mail @lang('backLang.links') To {{$submited_documents->pickup_person}}</h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        @if(count($submited_documents->documents) > 0)
                            @foreach($submited_documents->documents as $documents)
                                @foreach($documents->links as $document_link)
                                    <div class="col-md-6">
                                        <label class="ui-check m-a-0">
                                            <input type="checkbox" name="ids[]" class="checkBoxClass" value="{{$document_link->id}}"><i class="dark-white" style="position: initial;"></i>
                                            <input type="hidden" name="document_ids[]" value="{{$document_link->id}}">
                                        </label>
                                        <label for="start_time" class="form-control-label">{{$document_link->name}}</label>
                                    </div>
                                    <input id="pickup_id" name="pickup_id" value="{{$Pickups->id}}" hidden>
                                @endforeach
                            @endforeach
                            @else
                            <p style="text-align: center">Not submitted</p>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Send</button>
                </div>

                {{Form::close()}}

            </div>

        </div>

    </div>

    <script>
        //Onclick Checkbox Copy Address
        $('#check_adress').click(function(){
            if($('#check_adress').is(':checked')){
                $('#office_address').attr('value',$('#home_address').val());
                $('#office_city').attr('value',$('#city').val());
                $('#office_state').attr('value',$('#state').val());
                $('#office_pincode').attr('value',$('#pincode').val());
            }else{
                $('#office_address').attr('value','');
                $('#office_city').attr('value','');
                $('#office_state').attr('value','');
                $('#office_pincode').attr('value','');
            }
        })
        //Start Time

        $("#start_time").datetimepicker({

            format: 'LT'

        }).parent().css("position :relative");

        $("#end_time").datetimepicker({

            format: 'LT'

        }).parent().css("position :relative");



        $("#pickup_date").datetimepicker({

            format: 'DD-MM-YYYY'

        }).parent().css("position :relative");



        //Set time To Alert Message

        setTimeout(function(){

            $('#alert_message').hide();

        }, 5000);



        //Set Firstly Active Tab

        $(".nav-link").click(function () {

            $(".nav-link").removeClass("active");

            $(".tab").addClass("active"); // instead of this do the below

        });

        //Edit Document through Ajax

        function Editdocument(document) {

            var $document_id = document;

            showData = '';

            $.ajax({

                url: '{!! route("pickups.document_edit") !!}',

                type: 'get',

                data: {document_id: $document_id},

                datatype: 'json',

                success: function (res) {

                    var $data = JSON.parse(res);

                    if($data.status !=0){

                        $('#edit_document_table').hide();

                        $('#edit_document_div').empty();

                        $('#edit_document_div').show();

                        showData +='{{Form::open(['route'=>['pickups.updateDocument'],'method'=>'POST' ])}}'+

                                '{{csrf_field()}}'+

                                '<div class="form-group row">'+

                                '<input type="hidden" value='+$data.edit_documents.id+' name="document_id">'+

                                '<input type="hidden" value= '+$data.edit_documents.pickup_id+' name="pickup_id">';

                        showData +='</div><div class="form-group row">'+

                                '<label for="email"class="col-sm-2 form-control-label">{!!  trans('backLang.sequence') !!}</label>'+

                                '<div class="col-sm-10">'+

                                '<input id="sequence" name="sequence" type="text"  class="form-control" value="'+$data.edit_documents.sequence+'" />'+

                                '</div>'+

                                '</div>'+

                                '<div class="form-group row">'+

                                '<label for="email"class="col-sm-2 form-control-label">{!!  trans('backLang.question') !!}'+

                                '</label>'+

                                '<div class="col-sm-10">'+

                                '<input id="question" name="question" type="text" placeholder="Question" class="form-control required" value="'+ $data.edit_documents.question +'" required/>' +

                                '</div>'+

                                '</div>'+
                                '<div class="form-group row">'+

                                '<label for="email"class="col-sm-2 form-control-label">{!!  trans('backLang.comments') !!}'+

                                '</label>'+

                                '<div class="col-sm-10">';
                                        if($data.edit_documents.comments != null){
                                            showData += '<textarea class="col-sm-12 form-control-label" rows="4" style="border-color: #e4e6e8;" name="comment">'+$data.edit_documents.comments+'</textarea>';
                                        }else{
                                            showData += '<textarea class="col-sm-12 form-control-label" rows="4" style="border-color: #e4e6e8;" name="comment"></textarea>';
                                        }
                        showData += '</div>'+

                                '</div>'+
                                '<div class="form-group row">'+
                                '<label for="email" class="col-sm-2 form-control-label">{!!  trans('backLang.links') !!}</label>';

                        if($data.edit_documents.links.length <= 0){

                            showData += '<div class="col-sm-10">Not Submited</div>';

                        }



                            showData += '<div class="row">';
                        $data.edit_documents.links.forEach(function ($doc_link){
                            var url = '{{ URL::to("uploads/pickups", "document_pic") }}';
                            url = url.replace('document_pic', $doc_link.filename);
                            showData+='<div class="col-sm-4">'+
                                            '<input name="link_id[]" value="'+ $doc_link.id +'" hidden>'+
                                    '<label>'+ $doc_link.name +'</label>'+
                                    '<input id="link" name="link_'+ $doc_link.id +'" type="text" placeholder="Link" class="form-control required" value="'+ $doc_link.link +'" required/></div>';
                                    });
                        showData+='</div>';




                        showData+='</div><div class="form-group row">'+

                                    '<label for="comment" class="col-sm-2 form-control-label">Pictures</label>';

                                $data.edit_documents.pictures.forEach(function ($doc_picture){

                                    var url = '{{ URL::to("uploads/pickups", "document_pic") }}';

                                    url = url.replace('document_pic', $doc_picture.filename);

                                    showData += '<div class="col-sm-1">'+

                                        '<img src='+url+' alt='+$doc_picture.filename+' title='+$doc_picture.filename+' style="height: 60px;width: 65px;"class="img-responsive"></div>';

                                });



                        showData+='</div><div class="form-group row  m-t-md ">'+

                                    '<div class="col-sm-offset-2 col-sm-10">'+

                                        '<button type="submit" class="btn btn-primary m-t"><i class="material-icons">'+

                                            '&#xe31b;</i> {!! trans('backLang.update') !!}</button>'+

                                        '<a  style ="margin-left:5px;" onclick="CancelDocument();" class="btn btn-danger m-t"><i class="material-icons">'+

                                            '&#xe5cd;</i> {!! trans('backLang.cancel') !!}</a>'+

                                    '</div>'+

                               '</div>'+



                                '{{Form::close()}}';

                        $('#edit_document_div').append(showData);

                    }

                }

            });

        }



        function CancelDocument(){

            $('#edit_document_table').show();

            $('#edit_document_div').hide();

        }

        //Set Publish

        function SetPublish(pickup_id){

            var pickup_id = pickup_id;

            var url = '{{ route("pickups.edit", ":id") }}';

            url = url.replace(':id', pickup_id);

            $.ajax({

                url:  '{!! route("pickups.setPublish") !!}',

                type: 'GET',

                data:{pickup_id:pickup_id},

                datatype: 'json',

                success: function (response) {

                    var $data = JSON.parse(response);

                    if($data.status !=0 && $data.status !=2){

                        toastr.success($data.message);

                        var redirect = setTimeout(function(){

                            location.href= url

                        }, 500);



                    }else if($data.status !=1 && $data.status !=2){

                        toastr.success($data.message);

                        var redirect = setTimeout(function(){

                            location.href= url

                        }, 500);



                    }else{

                        toastr.success($data.message);

                        var redirect = setTimeout(function(){

                            location.href= url

                        }, 500);

                    }





                }

            });

        }



        //Products On Company Onchange

        $('#customer').on('change',function(){

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

        //Reschedule Validation On Submit

        $("#comments").keyup(function(){
            $('#comments_span').hide();
        });

        $('#RescheduleForm').on('submit', function(e) {
            var start_time = $('#start_time').val();
            var end_time = $('#end_time').val();
            start_time = start_time.split(" ");
            var time = start_time[0].split(":");
            var stime = time[0];
            if(start_time[1] == "PM" && stime<12) stime = parseInt(stime) + 12;
            start_time = stime + ":" + time[1] + ":00";

            end_time = end_time.split(" ");
            var time1 = end_time[0].split(":");
            var etime = time1[0];
            if(end_time[1] == "PM" && etime<12) etime = parseInt(etime) + 12;
            end_time = etime + ":" + time1[1] + ":00";

//            if (start_time != '' && end_time != '') {
//                if (end_time <= start_time) {
//                    toastr.success('select valid time');
//
//                }
//            }

            if ($('#comments').val() != '' && end_time > start_time){
                $('#RescheduleForm').submit();
            }else{
                if (!$('#comments').val()) {
                    if ($("#comments").parent().next(".validation").length == 0) // only add if not added
                    {
                        $('#comments_span').show();
                    }
                    $('#comments').focus();
                    focusSet = true;

                } else {
                    $("#comments").parent().next(".validation").remove(); // remove it
                }
                if(end_time <= start_time){
                    toastr.error('select valid time');
                }
                e.preventDefault();
            }

        });


        //checkbox check all
        $("#checkAll").click(function () {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
    </script>



@endsection

