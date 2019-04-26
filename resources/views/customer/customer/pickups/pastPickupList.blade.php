@extends('customer.layout')
{{-- page level styles --}}

@section('headerInclude')

    <link rel="stylesheet" href="{{ URL::to('css/user_profile/user_profile.css') }}">
    <link href="{{ URL::to('css/user_profile/jasny-bootstrap.css') }}" rel="stylesheet"/>
    <link href="{{ URL::to('css/user_profile/bootstrap-editable.css') }}" rel="stylesheet"/>
@endsection

{{-- Page content --}}
@section('content')
    <div class="box-header dker">
        <h3>Pickup List</h3>
        <small>
            <a href="">{{ trans('customer.home') }}</a> /
            <a></a>Pickups
        </small>
        <div class="right" style="text-align: right">
            <a style="margin-top: -57px;" href="{{ url()->previous() }}" >Back</a>
        </div>
        <p>Pickup Details Of <strong>{{$pickup_details->pickup_person}}</strong></p>
    </div>
    <!-- Main content -->
    <section class="content paddingleft_right15">
        <div class="row">
            <div class="col-lg-12" >

                <div  class="tab-content mar-top">
                    <div id="tab1" class="tab-pane fade active in">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="panel">
                                    <div class="panel-body">
                                        <div class="col-md-8">
                                            <div class="panel-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped" id="users">
                                                        <tr>
                                                            <td>POD Number</td>
                                                            <td>
                                                                <p>{{ $pickup_details->pod_number }}</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Mobile</td>
                                                            <td>
                                                                <p>{{ $pickup_details->mobile }}</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Delivery Number</td>
                                                            <td>
                                                                <p> {{ $pickup_details->delivery_number }}</p>
                                                            </td>
                                                        </tr>
                                                        <tr><td>
                                                                <p>@lang('customer.status')</p>
                                                            </td>
                                                            <td>
                                                                <p>{{ $pickup_details->status }}</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('customer.created_at')</td>
                                                            <td>
                                                                <p> {!! $pickup_details->created_at->diffForHumans() !!}</p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="panel">
                                    <div class="panel-body">
                                        <div class="col-md-8">
                                            <div class="panel-body">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

{{-- page level scripts --}}
@section('footerInclude')

@endsection
