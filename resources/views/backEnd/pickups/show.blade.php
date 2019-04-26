<html>

<head>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="{{asset('outletcontrol.css')}}">
    {{--<iframe src="http://docs.google.com/gview?url=http://path.com/to/your/pdf.pdf&embedded=true" style="width:600px; height:500px;" frameborder="0"></iframe>--}}
    <style>
        td.titlee {
            font-family: "Times New Roman", Times, serif;
        }
    </style>

</head>
<body>
<!--content area start-->
<div id="content" class="container-fluid full-width-container blank" >
    <div class="pmd-card-body">
        {{--<div class="pmd-card-title text-center">--}}
        {{--<img src="{{ URL::to('backEnd/assets/images/logo.png') }}" alt="logo" >--}}
        {{--<h3 class="upperhead" style="display: inline;padding-bottom: 1px;">DocBoys</h3>--}}
        {{--</div>--}}


        <div class="row" style="width: 100%">
            <a href="{{url('/pickups/pdfview',$Pickups->id)}}">Download</a>
            <div class="col-md-6 col-sm-5 col-xs-5" style="width:45%;">
                <div class="panel panel-success" >
                    <div class="panel-heading ">
                        <h3 class="panel-title">Pickup Details</h3>
                    </div>
                    <div class="table-responsive" style="padding:8px">
                        <table>
                            <tbody>
                            <tr>
                                <td class="title"><strong>Pickup Person :</strong>{{$Pickups->pickup_person}}</td>
                            </tr>
                            <tr>
                                <td class="title"><strong>Status :</strong>{{$Pickups->status}}</td>
                            </tr>
                            <tr>
                                <td class="title"><strong>Pickup date </strong>: {{$Pickups->pickup_date}}</td>
                            </tr>
                            <tr>
                                <td class="title"><strong>Completed date </strong>: {{$Pickups->completed_at}}</td>
                            </tr>
                            <tr>
                                <td class="title"><strong>Address </strong>: {{$Pickups->address}}</td>
                            </tr>
                            <tr>
                                <td class="title"><strong>City </strong>: {{$Pickups->city}}</td>
                            </tr>
                            <tr>
                                <td class="title"><strong>State </strong>: {{$Pickups->state}}</td>
                            </tr>
                            <tr style="border-bottom: 0">
                                <td class="title" style="border-bottom: 0"> <strong>Pincode :</strong>{{$Pickups->pincode}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-5 col-xs-5" style="width:45%;">
                <div class="panel panel-success" >
                    <div class="panel-heading ">
                        <h3 class="panel-title">Agent Details</h3>
                    </div>
                    <div class="table-responsive" style="padding:8px">
                        {{--<table>--}}
                            {{--<tbody>--}}
                            {{--<tr style="">--}}
                                {{--<td class="title"><strong>Agent Name :</strong>{{$agents->name}}</td>--}}
                            {{--</tr>--}}
                            {{--<tr>--}}
                                {{--<td class="title"><strong>Email  :</strong> {{$agents->email}}</td>--}}
                            {{--</tr>--}}
                            {{--<tr style="border-bottom: 1px!important;">--}}
                                {{--<td class="title"><strong>Mobile  :</strong>{{$agents->mobile}}</td>--}}
                            {{--</tr>--}}
                            {{--<tr>--}}
                                {{--<td class="title"><strong>Status :</strong>{{$agents->status}}</td>--}}
                            {{--</tr>--}}
                            {{--</tbody>--}}
                        {{--</table>--}}
                    </div>
                </div>
            </div>
        </div>

        @if($submited_documents->count() !=0)
            <table class="table pmd-table table-sm summary-table mb0">
                <tbody>
                @if(count($submited_documents) > 0)
                    @foreach($submited_documents as $documents)
                        <tr>
                            <td class="panel-title">
                                <strong>Question{{$documents->sequence}}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="background-color: #dff0d8">{{ $documents->question }}</td>
                        </tr>
                        @if( $documents->comments != null)
                            <tr>
                                <td class="panel-title" style=""><strong>Answer</strong>&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="background-color: #dff0d8">{{ $documents->comments }}</td>
                            </tr>
                        @endif
                            @if(count($documents->pictures) > 0 )
                                <tr>
                                    <td>
                                        @foreach($documents->pictures as $document_picture)
                                            @if($document_picture->filename != null)
                                                <a href="{{ URL::to('uploads/pickups/'.$document_picture->filename) }}" target="new">
                                                    <img src="{{ URL::to('uploads/pickups/'.$document_picture->filename) }}"
                                                         alt="{{ $document_picture->filename }}" title="{{ $document_picture->filename  }}"
                                                         style="width: 200px;padding-top: 10px" ></a><br>
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                            @endif
                    @endforeach
                @endif
                </tbody>
            </table>
        @endif
    </div>
</div>



<!-- content area end -->


</body>
</html>
