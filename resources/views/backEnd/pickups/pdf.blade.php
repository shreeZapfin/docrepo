<html>

<head>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="{{asset('/public/outletcontrol.css')}}">
    {{--<iframe src="http://docs.google.com/gview?url=http://path.com/to/your/pdf.pdf&embedded=true" style="width:600px; height:500px;" frameborder="0"></iframe>--}}
    <style>
        .td_class{
            padding: 8px;
            line-height: 1.42857143;
            vertical-align: top;
            border-top: 1px solid #ddd;
        }
        .panel-title{
            margin-top: 0;
            margin-bottom: 0;
            font-size: 16px;
            color: inherit;
        }
    </style>

</head>
<body>
<!--content area start-->
<div id="content" class="container-fluid full-width-container blank" >
    <div class="pmd-card-body">
         <div class="row" style="width: 100%">
            <div class="col-md-8 col-sm-5 col-xs-5" style="width:45%; border-right: 2px solid black;
            height: auto;">
                <img src="{{asset('/public/DB2.png') }}" style="height: auto;width: 200px">
                <h3 style="margin-top: -37px;padding-left: 54px;">DocBoyz</h3>
            </div>
            <div class="col-md-3 col-sm-2 col-xs-5" style="width:45%;">
                <p>Zapfin Technologies pvt. ltd.<br>
                    Aditya Business Center S N 1 A B Wing<br>
                    3rd Floor Above ICICI Bank Kondhwa<br>
                    Pune- 411048.</p>
                <a target="new" href="www.docboyz.in">www.docboyz.in</a>
            </div>
        </div>

        <div class="row" style="width: 100%">
            <div class="col-md-6 col-sm-5 col-xs-5" style="width:45%;">
                <div class="panel panel-success" >
                    <div class="panel-heading ">
                        <h3 class="panel-title">Pickup Details</h3>
                    </div>
                    <div class="table-responsive" style="padding:8px">
                        <table>
                            <tbody>
                            <tr>
                                <td class="title"><strong>Application ID :</strong>{{$Pickups->application_id}}</td>
                            </tr>
                            <tr>
                                <td class="title"><strong>Pickup Person :</strong>{{$Pickups->pickup_person}}</td>
                            </tr>
                            <tr style="border-bottom: 0">
                                <td class="title" style="border-bottom: 0"> <strong>Pincode :</strong>{{$Pickups->pincode}}</td>
                            </tr>
                            <tr>
                                <td class="title"><strong>Address </strong>: {{$Pickups->home_address}}</td>
                            </tr>
                            <tr>
                                <td class="title"><strong>City </strong>: {{$Pickups->city}}</td>
                            </tr>
                            <tr>
                                <td class="title"><strong>State </strong>: {{$Pickups->state}}</td>
                            </tr>
                            <tr>
                                <td class="title"><strong>Status :</strong>{{$Pickups->status}}</td>
                            </tr>
                            <tr>
                                <td class="title"><strong>Document Submited Date :@if($document_submit_date !=null)</strong>{{\Carbon\Carbon::parse($document_submit_date->created_at)->format('d-m-Y')}} @else Not Submitted @endif</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-5 col-xs-5" style="width:45%;">
                <div class="panel panel-success" >
                    <div class="panel-heading ">
                        <h3 class="panel-title">FC Details</h3>
                    </div>
                    <div class="table-responsive" style="padding:8px">
                        <table>
                            <tbody>
                            <tr style="">
                                <td class="title"><strong>FC Number :</strong>{{$agents->id}}</td>
                            </tr>
                            <tr style="">
                                <td class="title"><strong>FC Name :</strong>{{$agents->name}}</td>
                            </tr>
                            <tr>
                                <td class="title"><strong>Email  :</strong> {{$agents->email}}</td>
                            </tr>
                            <tr style="border-bottom: 1px!important;">
                                <td class="title"><strong>Mobile  :</strong>{{$agents->mobile}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel panel-success" >
                    <div class="panel-heading ">
                        <h3 class="panel-title">POD Details</h3>
                    </div>
                    <div class="table-responsive" style="padding:8px">
                        <table>
                            <tbody>
                            <tr>
                                <td class="title"><strong>POD No :</strong>@if($Pickups->delivery_number != null){{$Pickups->delivery_number}}< @else Not Submited @endif</td>
                            </tr>
                            <tr>
                                <td class="title"><strong>POD Completed Date :</strong>@if($Pickups->completed_at != null){{\Carbon\Carbon::parse($Pickups->completed_at)->format('d-m-Y')}}< @else Not Submited @endif</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if($submited_documents->count() !=0)
            <table>
                <tbody>
                @if(count($submited_documents) > 0)
                    @foreach($submited_documents as $documents)
                        <tr>
                            <td class="panel-title td_class" style="">
                                <strong>Question{{$documents->sequence}}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="background-color: #dff0d8;" class="td_class">{{ $documents->question }}</td>
                        </tr>
                        @if( $documents->comments != null)
                            <tr>
                                <td class="panel-title td_class" style=""><strong>Answer</strong>&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="td_class" style="background-color: #dff0d8">{{ $documents->comments }}</td>
                            </tr>
                        @endif
                        @if(count($documents->pictures) > 0 )
                            @foreach($documents->pictures as $document_picture)
                                <tr>
                                    <td class="td_class">
                                        @if($document_picture->filename != null)
                                            <a style="" href="{{ URL::to('public/uploads/pickups/'.$document_picture->filename) }}" target="new">
                                                <img src="{{ URL::to('public/uploads/pickups/'.$document_picture->filename) }}"
                                                     alt="{{ $document_picture->filename }}" title="{{ $document_picture->filename  }}"
                                                     style="width: 100%;height: auto" ></a><br>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                @endif
                <tr>
                    <td class="panel-title td_class" style="">
                        <strong>POD Details</strong>
                    </td>
                </tr>
                <tr>
                    <td style="background-color: #dff0d8" class="td_class">POD Number:@if($Pickups->delivery_number != null){{$Pickups->delivery_number}} @else Not Submited @endif</td>
                </tr>
                <tr>
                    <td>
                        @if($Pickups->pod_number != null)
                            <a href="{{ URL::to('public/uploads/pickups/'.$Pickups->pod_number) }}" target="new">
                                <img src="{{ URL::to('public/uploads/pickups/'.$Pickups->pod_number) }}"
                                     alt="{{$Pickups->pod_number }}" title="{{$Pickups->pod_number  }}"
                                     style="width: 200px;padding-top: 10px" ></a><br>
                        @else
                            Not Submited
                        @endif
                    </td>
                </tr>
                </tbody>
            </table>
        @endif
    </div>
</div>



<!-- content area end -->



</body>
</html>
