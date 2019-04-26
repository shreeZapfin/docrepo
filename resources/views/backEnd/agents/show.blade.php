@extends('backEnd.layout'){{-- page level styles --}}@section('headerInclude')    <title>{{ trans('backLang.control') }} | Agents </title>    <link rel="stylesheet" href="{{ URL::to('public/css/user_profile/user_profile.css') }}">    <link href="{{ URL::to('public/css/user_profile/jasny-bootstrap.css') }}" rel="stylesheet"/>    <link href="{{ URL::to('public/css/user_profile/bootstrap-editable.css') }}" rel="stylesheet"/>    <link href="https://codeseven.github.io/toastr/build/toastr.min.css" rel="stylesheet"/>@endsection{{-- Page content --}}@section('content')    <div class="padding">        <div class="box">            <div class="box-header dker">                <h3>{{ trans('backLang.fc') }} Profile</h3>                <small>                    <a href="{{ route('adminHome') }}">{{ trans('backLang.home') }}</a> / <a href="{{route('agents')}}">{{ trans('backLang.fc') }}</a> /                    <a href="{{route('agents.show',$agent_profile->id)}}">{{ trans('backLang.fc') }} Profile</a>                </small>            </div>            <div class="row p-a pull-right" style="margin-top: -70px;">                <div class="col-sm-12">                    <a class="btn btn-warning" href="{{ route('agents')}}">                        Back                    </a>                </div>            </div>            <div class="box-body">                <!-- Main content -->                <section class="content paddingleft_right15">                    <div class="row">                        <div class="col-lg-4">                            <div class="row">                                <div class="col-xs-6 col-sm-4 col-md-8" style="margin-left: 47px;">                                    <div class="box p-a-xs">                                        <img src="{{ URL::to('public/uploads/agents/'.$agent_profile->profile_pic) }}"                                             alt="{{ $agent_profile->profile_pic }}" title="{{ $agent_profile->profile_pic  }}"                                             style="height: 150px;width: auto;"                                             class="img-responsive">                                        <div class="p-a-xs">                                            <div class="text-ellipsis">                                                <a style="display: block;overflow: hidden;"                                                   href="{{ URL::to('public/uploads/agents/'.$agent_profile->profile_pic) }}"                                                   target="_blank">                                                </a>                                            </div>                                        </div>                                    </div>                                </div>                            </div>                            <div class="row">                                <div class="col-lg-12 col-md-12">                                    @if($agent_profile->status !='Approved')                                    <a style="width: 100%" id="approve_agent" onclick="AgentApprove({{$agent_profile->id}});" type="button" class="btn btn-md white btn-addon primary"                                            data-dismiss="modal">Approve Agent</a>                                    @else                                        <a style="width: 100%" id="inactive_agent" onclick="AgentApprove({{$agent_profile->id}});" type="button" class="btn btn-md white btn-addon warning"                                       data-dismiss="modal">Disable Agent</a>                                    @endif                                </div>                            </div>                        </div>                        <div class="col-lg-8">                            <div class="col-lg-12">                                <div class="row" style="font-family: 'Open Sans', Arial, sans-serif; font-weight: 900; line-height: 1.1em; color: #0cbaa4;">                                    <h4>{{ trans('backLang.fc') }} Details</h4>                                </div>                            </div>                            <div class="col-lg-12">                                <div class="row" style="color: #656565; border-bottom: 1px solid rgba(0, 0, 0, 0.1); padding: 10px 0; font-family: 'Open Sans', Arial, sans-serif;    font-size: 14px;    line-height: 1.6em;">                                    <div class="col-lg-3" style="font-weight: 600;">                                      Id :                                    </div>                                    <div class="col-lg-9">                                        {{ $agent_profile->id }}                                    </div>                                </div>                            </div>                            <div class="col-lg-12">                                <div class="row" style="color: #656565; border-bottom: 1px solid rgba(0, 0, 0, 0.1); padding: 10px 0; font-family: 'Open Sans', Arial, sans-serif;    font-size: 14px;    line-height: 1.6em;">                                    <div class="col-lg-3" style="font-weight: 600;">                                        @lang('agent.name') :                                    </div>                                    <div class="col-lg-9">                                        {{ $agent_profile->name }}                                    </div>                                </div>                            </div>                            <div class="col-lg-12">                                <div class="row" style="color: #656565; border-bottom: 1px solid rgba(0, 0, 0, 0.1); padding: 10px 0; font-family: 'Open Sans', Arial, sans-serif;    font-size: 14px;    line-height: 1.6em;">                                    <div class="col-lg-3" style="font-weight: 600;">                                        @lang('agent.wallet_amt') :                                    </div>                                    <div class="col-lg-9">                                        <i class="fa fa-inr fa-fw" aria-hidden="true"></i><span id="total">{{ $agent_profile->wallet_amt }}                                    </div>                                </div>                            </div>                            <div class="col-lg-12">                                <div class="row" style="color: #656565; border-bottom: 1px solid rgba(0, 0, 0, 0.1); padding: 10px 0; font-family: 'Open Sans', Arial, sans-serif;    font-size: 14px;    line-height: 1.6em;">                                    <div class="col-lg-3" style="font-weight: 600;">                                        @lang('agent.mobile') :                                    </div>                                    <div class="col-lg-9">                                        {{ $agent_profile->mobile }}                                    </div>                                </div>                            </div>                            <div class="col-lg-12">                                <div class="row" style="color: #656565; border-bottom: 1px solid rgba(0, 0, 0, 0.1); padding: 10px 0; font-family: 'Open Sans', Arial, sans-serif;    font-size: 14px;    line-height: 1.6em;">                                    <div class="col-lg-3" style="font-weight: 600;">                                        @lang('agent.dob') :                                    </div>                                    <div class="col-lg-9">                                        {{ \Carbon\Carbon::parse($agent_profile->dob)->format('d-m-Y')}}                                    </div>                                </div>                            </div>                            <div class="col-lg-12">                                <div class="row" style="color: #656565; border-bottom: 1px solid rgba(0, 0, 0, 0.1); padding: 10px 0; font-family: 'Open Sans', Arial, sans-serif;    font-size: 14px;    line-height: 1.6em;">                                    <div class="col-lg-3" style="font-weight: 600;">                                        @lang('agent.email') :                                    </div>                                    <div class="col-lg-9">                                        {{ $agent_profile->email }}                                    </div>                                </div>                            </div>                            <div class="col-lg-12">                                <div class="row" style="color: #656565; border-bottom: 1px solid rgba(0, 0, 0, 0.1); padding: 10px 0; font-family: 'Open Sans', Arial, sans-serif;    font-size: 14px;    line-height: 1.6em;">                                    <div class="col-lg-3" style="font-weight: 600;">                                        @lang('agent.gender') :                                    </div>                                    <div class="col-lg-9">                                        {{ $agent_profile->gender }}                                    </div>                                </div>                            </div>                            <div class="col-lg-12">                                <div class="row" style="color: #656565; border-bottom: 1px solid rgba(0, 0, 0, 0.1); padding: 10px 0; font-family: 'Open Sans', Arial, sans-serif;    font-size: 14px;    line-height: 1.6em;">                                    <div class="col-lg-3" style="font-weight: 600;">                                        @lang('agent.address') :                                    </div>                                    <div class="col-lg-9">                                        {{ $agent_profile->address1 }},{{ $agent_profile->address2 }}                                    </div>                                </div>                            </div>                            <div class="col-lg-12">                                <div class="row" style="color: #656565; border-bottom: 1px solid rgba(0, 0, 0, 0.1); padding: 10px 0; font-family: 'Open Sans', Arial, sans-serif;    font-size: 14px;    line-height: 1.6em;">                                    <div class="col-lg-3" style="font-weight: 600;">                                        @lang('agent.city') :                                    </div>                                    <div class="col-lg-9">                                        {{ $agent_profile->city }}                                    </div>                                </div>                            </div>                            <div class="col-lg-12">                                <div class="row" style="color: #656565; border-bottom: 1px solid rgba(0, 0, 0, 0.1); padding: 10px 0; font-family: 'Open Sans', Arial, sans-serif;    font-size: 14px;    line-height: 1.6em;">                                    <div class="col-lg-3" style="font-weight: 600;">                                        @lang('agent.state') :                                    </div>                                    <div class="col-lg-9">                                        {{ $agent_profile->state }}                                    </div>                                </div>                            </div>                            <div class="col-lg-12">                                <div class="row" style="color: #656565; border-bottom: 1px solid rgba(0, 0, 0, 0.1); padding: 10px 0; font-family: 'Open Sans', Arial, sans-serif;    font-size: 14px;    line-height: 1.6em;">                                    <div class="col-lg-3" style="font-weight: 600;">                                        @lang('agent.pincode') :                                    </div>                                    <div class="col-lg-9">                                        {{ $agent_profile->pincode }}                                    </div>                                </div>                            </div>                            <div class="col-lg-12">                                <div class="row" style="color: #656565; border-bottom: 1px solid rgba(0, 0, 0, 0.1); padding: 10px 0; font-family: 'Open Sans', Arial, sans-serif;    font-size: 14px;    line-height: 1.6em;">                                    <div class="col-lg-3" style="font-weight: 600;">                                        @lang('agent.status') :                                    </div>                                    <div class="col-lg-9">                                        {{ $agent_profile->status }}                                    </div>                                </div>                            </div>                            <div class="col-lg-12">                                <div class="row" style="color: #656565; border-bottom: 1px solid rgba(0, 0, 0, 0.1); padding: 10px 0; font-family: 'Open Sans', Arial, sans-serif;    font-size: 14px;    line-height: 1.6em;">                                    <div class="col-lg-3" style="font-weight: 600;">                                        @lang('agent.created_at') :                                    </div>                                    <div class="col-lg-9">                                        {{ $agent_profile->created_at->diffForHumans() }}                                    </div>                                </div>                            </div>                            <div class="col-lg-12">                                <div class="row" style="font-family: 'Open Sans', Arial, sans-serif; font-weight: 900; line-height: 1.1em; color: #0cbaa4; margin-top: 20px;">                                    <h4>Education Details</h4>                                </div>                            </div>                            <div class="col-lg-12">                                <div class="row">                                    <table class="table table-striped">                                        <thead>                                        <tr>                                            <th>Degree</th>                                            <th>College</th>                                            <th>Year</th>                                        </tr>                                        </thead>                                        <tbody>                                        @foreach($agent_profile->agenteducation as $education)                                        <tr>                                            <td>                                                {{ $education->degree }}                                            </td>                                            <td>                                                {{ $education->college }}                                            </td>                                            <td>                                                {{ $education->year }}                                            </td>                                        </tr>                                        @endforeach                                        </tbody>                                    </table>                                </div>                            </div>                            <div class="col-lg-12">                                <div class="row" style="font-family: 'Open Sans', Arial, sans-serif; font-weight: 900; line-height: 1.1em; color: #0cbaa4; margin-top: 20px;">                                    <h4>Bank Details</h4>                                </div>                            </div>                            <div class="col-lg-12">                                <div class="row">                                    <table class="table table-striped">                                        <thead>                                        <tr>                                            <th>Bank Name</th>                                            <th>Ifsc Code</th>                                            <th>Account Number	</th>                                            <th>Account Type</th>                                        </tr>                                        </thead>                                        <tbody>                                       @if($agent_profile->agentbankdetails->isEmpty())                                           <tr>                                               <td>                                                   Not Submited                                               </td>                                               <td>                                                   Not Submited                                               </td>                                               <td>                                                   Not Submited                                               </td>                                               <td>                                                   Not Submited                                               </td>                                           </tr>                                       @else                                            @foreach($agent_profile->agentbankdetails as $bankdetail)                                                <tr>                                                    <td>                                                        {{ $bankdetail->bank_name}}                                                    </td>                                                    <td>                                                        {{ $bankdetail->ifsc_code }}                                                    </td>                                                    <td>                                                        {{ $bankdetail->account_number }}                                                    </td>                                                    <td>                                                        {{ $bankdetail->account_type }}                                                    </td>                                                </tr>                                            @endforeach                                       @endif                                        </tbody>                                    </table>                                </div>                            </div>                            <div class="col-lg-12">                                <div class="row" style="font-family: 'Open Sans', Arial, sans-serif; font-weight: 900; line-height: 1.1em; color: #0cbaa4; margin-top: 20px;">                                    <h4>Documents</h4>                                </div>                            </div>                            @if($agent_profile->agentdocument->isEmpty())                                <p>Not Submitted</p>                            @else                            @foreach($agent_profile->agentdocument as $document)                                <div class="col-xs-6 col-sm-4 col-md-3">                                    <div class="box p-a-xs">                                        <img src="{{ URL::to('public/uploads/agents/Documents/'.$document->filename) }}"                                             alt="{{ $document->type  }}" title="{{ $document->type  }}"                                             style="height: 150px"                                             class="img-responsive">                                        <div class="p-a-sm">                                            <div class="text-ellipsis">                                                <a style="display: block;overflow: hidden;"                                                   href="{{ URL::to('public/uploads/agents/Documents/'.$document->filename) }}"                                                   target="_blank">                                                    <small>{{ ($document->type !="") ? $document->type:$document->filename  }}</small>                                                </a>                                            </div>                                        </div>                                    </div>                                </div>                            @endforeach                                @endif                        </div>                    </div>                </section>            </div>        </div>    </div>@stop{{-- page level scripts --}}@section('footerInclude')    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>    <script src="https://codeseven.github.io/toastr/build/toastr.min.js"></script>    <script>        function AgentApprove(agent_id) {            var agent_id = agent_id;            var url = '{{ route("agents.show", ":id") }}';            url = url.replace(':id', agent_id);            $.ajax({                url: '{!! route('agents.approve_agent') !!}',                data: {agent_id: agent_id},                type: 'get',                datatype: 'json',                success: function (res) {                    var $data = JSON.parse(res);                    if($data.status !=0){                        toastr.success($data.message);                        var redirect = setTimeout(function(){                            location.href= url                        }, 500);                    }else{                        toastr.success($data.message);                        var redirect = setTimeout(function(){                            location.href = url                        }, 500);                    }                }            });        }    </script>@endsection