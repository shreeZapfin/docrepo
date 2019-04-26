@extends('backEnd.layout')
@section('headerInclude')
    <title>{{ trans('backLang.control') }} | Pickups</title>
    @stop
@section('content')
    <style>
        .btn-success {
            color: #fff;
            background-color: #01BC8C;
            border-color: #01BC8C;
        }
    </style>
    <section class="content paddingleft_right15">

        <div class="padding">
            @if($data !=null)
                <div class="alert alert-success alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    @if($excel_data['success'] !=null)<p>{{$excel_data['success']}} Pickup Import Successfully</p>@endif
                    @if($excel_data['failed'] !=null)<p>{{$excel_data['failed']}} Pickup Import Failed,Please Try Again!!</p> @endif
                    @if($data !=null)
                        <form  action="{{ route('pickups.downloadExcel') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input  type="hidden" id="data" value="{{$data}}" name="data">
                  <span class="pull-right">
                <button type="submit" class="btn btn-primary"  style="margin-top: -39px" type="submit" name="action" value="import">Download Report</button>
                </span>
                        </form>
                    @endif
                </div>
            @endif
            @if(session()->has('message'))
                <div class="alert alert-success alert-dismissible" style="text-align: center">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>{{ session()->get('message') }}</strong>
                </div>
            @endif
                @if(session()->has('error'))
                    <div class="alert alert-danger alert-dismissible" style="text-align: center">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>{{ session()->get('error') }}</strong>
                    </div>
                @endif
            <div class="box">
                <div class="box-header dker">
                    <h3>Pickups</h3>
                    <small>
                        <a href="">{{ trans('backLang.home') }}</a> /
                        <a href="{{route('pickups')}}">Pickups</a> /
                        <a href="{{route('pickups.import')}}">Import Pickups</a>
                    </small>
                </div>
                <div class="row p-a pull-right" style="margin-top: -70px;">
                    <div class="col-sm-12">
                        <a class="btn btn-warning" href="{{route('pickups')}}">
                            Back
                        </a>
                    </div>
                </div>
                <div class="padding">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel-body">
                                <h5>Download Import Pickups File Templates</h5>
                                <a href="{{ URL::to('public/backEnd/downloadExcel/import_pickup_template.xls') }}"><button class="btn btn-success">Download Excel xls</button></a>
                                <a href="{{ URL::to('public/backEnd/downloadExcel/import_pickup_template.xlsx') }}"><button class="btn btn-success">Download Excel xlsx</button></a>
                                <a href="{{ URL::to('public/backEnd/downloadExcel/import_pickup_template.csv') }}"><button class="btn btn-success">Download CSV</button></a>
                            </div><br>
                            <form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 10px;" action="{{ route('pickups.saveExcel') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="panel-body">
                                    <h4><strong>Select File To Import</strong></h4>
                                    <input type="file" id="file" name="file" required><br>
                                    <button class="btn btn-primary" style="margin-top: 15px;" type="submit" name="action" value="delete">Import File</button>
                                    {{--<button class="btn btn-danger" type="button" style="margin-top: 15px;" data-toggle="modal" data-target="#deleteModal">Delete Previous & Import</button>--}}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endsection