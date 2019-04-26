@extends('backEnd.layout')
{{-- page level styles --}}

@section('headerInclude')
    <title>{{ trans('backLang.control') }} | Category</title>
    <link rel="stylesheet" href="{{ URL::to('css/user_profile/user_profile.css') }}">
    <link href="{{ URL::to('css/user_profile/jasny-bootstrap.css') }}" rel="stylesheet"/>
    <link href="{{ URL::to('css/user_profile/bootstrap-editable.css') }}" rel="stylesheet"/>
    <link href="https://codeseven.github.io/toastr/build/toastr.min.css" rel="stylesheet"/>
@endsection

{{-- Page content --}}
@section('content')
    <div class="padding">

        <div class="box">
            <div class="box-header dker">
                <h3>Category Detail</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ trans('backLang.home') }}</a> / <a href="{{route('jobs')}}">Category</a> /
                    <a href="{{route('jobs.show',$job_profile->id)}}">Category Detail</a>
                </small>
            </div>
            <div class="row p-a pull-right" style="margin-top: -70px;">
                <div class="col-sm-12">
                    <a class="btn btn-warning" href="{{ route('jobs')}}">
                        Back
                    </a>
                </div>
            </div>
            <div class="box-body">
                <!-- Main content -->
                <section class="content paddingleft_right15">
                    <div class="row">

                        <div class="col-lg-12">
                            <div class="col-lg-12">
                                <div class="row" style="font-family: 'Open Sans', Arial, sans-serif; font-weight: 900; line-height: 1.1em; color: #0cbaa4;">
                                    <h4>Category Detail </h4>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="row" style="color: #656565; border-bottom: 1px solid rgba(0, 0, 0, 0.1); padding: 10px 0; font-family: 'Open Sans', Arial, sans-serif;    font-size: 14px;    line-height: 1.6em;">
                                    <div class="col-lg-3" style="font-weight: 600;">
                                        Category Name :
                                    </div>
                                    <div class="col-lg-9">
                                        {{ $job_profile->name }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="row" style="color: #656565; border-bottom: 1px solid rgba(0, 0, 0, 0.1); padding: 10px 0; font-family: 'Open Sans', Arial, sans-serif;    font-size: 14px;    line-height: 1.6em;">
                                    <div class="col-lg-3" style="font-weight: 600;">
                                        @lang('backLang.customer') :
                                    </div>
                                    <div class="col-lg-9">
                                        {{ $job_profile->customer_name }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="row" style="color: #656565; border-bottom: 1px solid rgba(0, 0, 0, 0.1); padding: 10px 0; font-family: 'Open Sans', Arial, sans-serif;    font-size: 14px;    line-height: 1.6em;">
                                    <div class="col-lg-3" style="font-weight: 600;">
                                        @lang('backLang.price') :
                                    </div>
                                    <div class="col-lg-9">
                                        <i class="fa fa-inr fa-fw" aria-hidden="true"></i><span id="total">{{ $job_profile->price }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="row" style="color: #656565; border-bottom: 1px solid rgba(0, 0, 0, 0.1); padding: 10px 0; font-family: 'Open Sans', Arial, sans-serif;    font-size: 14px;    line-height: 1.6em;">
                                    <div class="col-lg-3" style="font-weight: 600;">
                                        @lang('backLang.created_by') :
                                    </div>
                                    <div class="col-lg-9">
                                        {{ $job_profile->user_name }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="row" style="color: #656565; border-bottom: 1px solid rgba(0, 0, 0, 0.1); padding: 10px 0; font-family: 'Open Sans', Arial, sans-serif;    font-size: 14px;    line-height: 1.6em;">
                                    <div class="col-lg-3" style="font-weight: 600;">
                                        @lang('agent.created_at') :
                                    </div>
                                    <div class="col-lg-9">
                                        {{ $job_profile->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="row" style="font-family: 'Open Sans', Arial, sans-serif; font-weight: 900; line-height: 1.1em; color: #0cbaa4; margin-top: 20px;">
                                    <h4><Category>Document</Category> Details</h4>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="row">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>Document</th>
                                            <th>Sequence</th>
                                            <th>Question</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($job_profile->job_details as $job_detail)
                                        <tr>
                                            <td>
                                                @if($job_detail->document_name != null)
                                                {{ $job_detail->document_name }}
                                                    @else
                                                NA
                                                    @endif
                                            </td>
                                            <td>
                                                {{ $job_detail->sequence }}
                                            </td>
                                            <td>
                                                {{ $job_detail->question }}
                                            </td>
                                        </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@stop

{{-- page level scripts --}}
@section('footerInclude')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://codeseven.github.io/toastr/build/toastr.min.js"></script>
@endsection
