@extends('backEnd.layout')
@section('headerInclude')
    <title>{{ trans('backLang.control') }} | Documents</title>
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
            <div class="box">
                <div class="box-header dker">
                    <h3>Documents</h3>
                    <small>
                        <a href="">{{ trans('backLang.home') }}</a> /
                        <a href="{{route('documents')}}">Documents</a>/
                        <a href="{{route('documents')}}">Import Documents</a>
                    </small>
                </div>
                <div class="row p-a pull-right" style="margin-top: -70px;">
                    <div class="col-sm-12">
                        <a class="btn btn-warning" href="{{ url()->previous() }}">
                            Back
                        </a>
                    </div>
                </div>
                <div class="padding">
                    <div class="row">
                        <div class="col-lg-6" style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 10px;">
                            <div class="panel-heading">
                                <h4 class="panel-title"> <i class="livicon" data-name="upload-alt" data-size="18" data-loop="true" data-c="#fff" data-hc="white"></i>
                                    Import Documents
                                </h4>
                            </div>
                            <div class="panel-body">
                                <h5>Download Documents File Templates</h5><br />
                                <a href=""><button class="btn btn-success">Download Excel xls</button></a>
                                <a href=""><button class="btn btn-success">Download Excel xlsx</button></a>
                                <a href=""><button class="btn btn-success">Download CSV</button></a>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 10px;" action="" class="form-horizontal" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="panel-body">
                                    <label for="input-file-1">Select File To Import</label>
                                    <input type="file" id="file" name="file" required>
                                    <h4><strong>Import File -</strong> It will add Documents or update if already added</h4>
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