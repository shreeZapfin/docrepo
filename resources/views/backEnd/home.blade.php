@extends('backEnd.layout')
@section('headerInclude')
    <title>{{ trans('backLang.control') }} | Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="{{ URL::to("public/backEnd/assets/styles/flags.css") }}"/>
   <style>
       label{min-height:10px;padding-left:20px;margin-bottom:0;font-weight:400;cursor:pointer}
       label{color:#3c763d}
       label{color:#8a6d3b}
       label{background-color: red}

       /* width */
       ::-webkit-scrollbar {
           width: 5px;
       }

       /* Track */
       ::-webkit-scrollbar-track {
           background: #ffffff;
       }

       /* Handle */
       ::-webkit-scrollbar-thumb {
           background: #9ee7dd;
       }

       /* Handle on hover */
       ::-webkit-scrollbar-thumb:hover {
           background: #0cbba4;
       }
   </style>
@endsection
@section('content')
    <div class="padding p-b-0" style="padding: 1.5rem;">
        <div class="margin">
            <h5 class="m-b-0 _300">{{ trans('backLang.hi') }} <span class="text-primary">{{ Auth::user()->name }}</span>, {{ trans('backLang.welcomeBack') }}
            </h5>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-5 col-lg-4">
                <div class="row">
                        <div class="col-xs-6">
                            <div class="box p-a" style="cursor: pointer"
                                 onclick="location.href='{{ route('customers') }}'">
                                <a href="{{ route('customers') }}">
                                    <div class="pull-left m-r">
                                        <i class="material-icons  text-2x text-info m-y-sm">wc</i>
                                    </div>
                                    <div class="clear">
                                        <div class="text-muted">Company</div>
                                        <h4 class="m-a-0 text-md _600">{{$customer_count}}</h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <div class="col-xs-6">
                        <div class="box p-a" style="cursor: pointer"
                             onclick="location.href='{{ route('pickups') }}'">
                            <a href="{{ route('pickups') }}">
                                <div class="pull-left m-r">
                                    <i class="material-icons  text-2x text-danger m-y-sm">access_time</i>
                                </div>
                                <div class="clear">
                                    <div class="text-muted">Pickups</div>
                                    <h4 class="m-a-0 text-md _600">{{$Pickup_count}}</h4>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="box p-a" style="cursor: pointer"
                             onclick="location.href='{{ route('agents') }}'">
                            <a href="{{ route('agents') }}">
                                <div class="pull-left m-r">
                                    <i class="material-icons  text-2x text-success m-y-sm">accessibility</i>
                                </div>
                                <div class="clear">
                                    <div class="text-muted">{{ trans('backLang.fc') }}</div>
                                    <h4 class="m-a-0 text-md _600">{{$Agent_count}}</h4>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="box p-a" style="cursor: pointer"
                             onclick="location.href='{{ url('products') }}'">
                            <a href="{{ url('products') }}">
                                <div class="pull-left m-r">
                                    <i class="material-icons  text-2x text-accent m-y-sm">import_export</i>
                                </div>
                                <div class="clear">
                                    <div class="text-muted">Products</div>
                                    <h4 class="m-a-0 text-md _600">{{$document_count}}</h4>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="row-col box-color text-center primary">
                            <div class="row-cell p-a">
                               Active {{ trans('backLang.fc') }}
                                <h4 class="m-a-0 text-md _600"><a href>{{ $active_agent }}</a></h4>
                            </div>
                            <div class="row-cell p-a dker">
                               InActive {{ trans('backLang.fc') }}
                                <h4 class="m-a-0 text-md _600"><a href>{{ $Inactive_agent }}</a></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-7 col-lg-8">
                <div class="row-col box bg">
                    <div id="chart"></div>
                    <div class="col-sm-4 dker">
                        <div class="box-header">
                            <h3>{{ trans('backLang.reports') }}</h3>
                        </div>
                        <div class="box-body">
                            <div class="box p-a" style="cursor: pointer"
                                 onclick="location.href='{{ route('pickups','Accepted') }}'">
                                <a href="{{ route('pickups',['status'=>'Accepted']) }}">
                                    <div class="pull-left m-r">
                                        <i class="material-icons  text-2x text-warning m-y-sm">beenhere

                                        </i>
                                    </div>
                                    <div class="clear">
                                        <div class="text-muted"> Accepted Pickups</div>
                                        <h4 class="m-a-0 text-md _600">{{$accepted_pickups}}</h4>
                                    </div>
                                </a>
                            </div>
                            <div class="box p-a" style="cursor: pointer"
                                 onclick="location.href='{{ route('pickups','Published') }}'">
                                <a href="{{ route('pickups',['status'=>'Published']) }}">
                                    <div class="pull-left m-r">
                                        <i class="material-icons  text-2x text-success m-y-sm">flash_off</i>
                                    </div>
                                    <div class="clear">
                                        <div class="text-muted">Published Pickups</div>
                                        <h4 class="m-a-0 text-md _600">{{$assigned_pickups}}</h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        </div>
        <div class="row">
            <?php
            $col_count = 0;
            if (Helper::GeneralWebmasterSettings("inbox_status")) {
                if (Auth::user()->permissionsGroup->inbox_status) {
                    $col_count++;
                }
            }
            if (Helper::GeneralWebmasterSettings("calendar_status")) {
                if (Auth::user()->permissionsGroup->calendar_status) {
                    $col_count++;
                }
            }
            if (Helper::GeneralWebmasterSettings("newsletter_status")) {
                if (Auth::user()->permissionsGroup->newsletter_status) {
                    $col_count++;
                }
            }
            $col_width = 12;
            if ($col_count > 0) {
                $col_width = 12 / $col_count;
            }
            ?>

            @if(Helper::GeneralWebmasterSettings("inbox_status"))
                @if(@Auth::user()->permissionsGroup->inbox_status)
                    <div class="col-md-12 col-xl-{{$col_width}}" id="pickup_div">
                        <div class="box m-b-0 " style="height: 370px;overflow-y: scroll">
                            <div class="box-header">
                                <h3>Latest {{ trans('backLang.pickups') }}</h3>

                            </div>

                            @if(count($Pickups) == 0)
                                <div class="text-center m-t-1" style="color:#bbb">
                                    <h1><i class="material-icons">&#xe156;</i></h1>
                                    {{ trans('backLang.noData') }}</div>
                            @else
                                <ul class="list-group no-border">
                                    @foreach($Pickups as $pickup)
                                        <?php
                                        $s4ds_current_date = date('Y-m-d', $_SERVER['REQUEST_TIME']);
                                        $day_mm = date('Y-m-d', strtotime($pickup->pickup_date));
                                        $dtformated = date('d M Y', strtotime($pickup->pickup_date));
                                        try {
                                            $groupColor = $Webmail->webmailsGroup->color;
                                            $groupName = $Webmail->webmailsGroup->name;
                                        } catch (Exception $e) {
                                            $groupColor = "";
                                            $groupName = "";
                                        }

                                        $fontStyle = "";
                                        $unreadIcon = "&#xe151;";
                                        $unreadbg = "";
                                        $unreadText = "";
                                        if ($pickup->status == 'Accepted') {
                                            $fontStyle = "_700";
                                            $unreadIcon = "&#xe0be;";
                                            $unreadbg = "style=\"background: $groupColor \"";
                                            $unreadText = "style=\"color: $groupColor \"";
                                        }
                                        ?>
                                        <li class="list-group-item">
                                            <div class="pull-right">
                                                <small>{{ $dtformated }}</small>
                                            </div>
                                            <a href="{{ route("pickups.edit",["id"=>$pickup->id]) }}"
                                               class="pull-sm-right w-50 m-r">
                                                <label style="background-color: #EF6F6C;" class="label label-danger">{{ $pickup->status }}</label>
                                            </a>
                                            <div class="clear" style="overflow: visible;">
                                                <a href="{{ route("pickups.edit",["id"=>$pickup->id]) }}"
                                                   class="_500 block">{{ $pickup->pickup_person     }}</a>
                                                <small class="text-muted text-ellipsis">{{ $pickup->address }}</small>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
            @if(Helper::GeneralWebmasterSettings("calendar_status"))
                @if(@Auth::user()->permissionsGroup->calendar_status)
                    <div class="col-md-12 col-xl-{{$col_width}}">
                        <div class="box m-b-0" style="min-height: 370px;overflow-y: scroll">
                            <div class="box-header">
                                <h3>Latest {{ trans('backLang.decline_pickup') }}</h3>
                            </div>

                            @if(count($DeclinePickups) == 0)
                                <div class="text-center m-t-1" style="color:#bbb">
                                    <h1><i class="material-icons">&#xe1b7;</i></h1>
                                    {{ trans('backLang.noData') }}</div>
                            @else
                                <ul class="list-group no-border">
                                    <?php
                                    $ii = 1;
                                    ?>
                                    @foreach($DeclinePickups as $id)

                                        @if($ii<=4)
                                            <li class="list-group-item">
                                                <?php
                                                $i2 = 0;
                                                $v0 = "";
                                                $v1 = "";
                                                $v2 = 0;
                                                $v3 = 0;
                                                ?>
                                                @foreach($id as $key => $val)

                                                    @if($i2 == 0)
                                                        <?php $v0 = $val; ?>
                                                    @endif
                                                    @if($i2 == 1)
                                                        <?php $v1 = $val; ?>
                                                    @endif
                                                    @if($i2 == 2)
                                                        <?php $v2 = $val; ?>
                                                    @endif
                                                    @if($i2 == 3)
                                                        <?php $v3 = $val; ?>
                                                    @endif
                                                    <?php
                                                    $i2++;
                                                    ?>
                                                @endforeach
                                                <?php
                                                $flag = "";
                                                $country_code = strtolower($v1);
                                                if ($country_code != "unknown") {
                                                    $flag = "<div class='flag flag-$country_code' style='display: inline-block'></div> ";
                                                }
                                                ?>
                                                <a href="{{ route("pickups.edit",["id"=>$v3]) }}" class="list-left">
                                                <span class="w-40 rounded dker">
                                                    <span>{{$v0}}</span>
                                                </span>
                                                </a>
                                                <div class="list-body">
                                                    <div> {{$v1}}</div>
                                                    <small class="text-muted text-ellipsis">
                                                        {{ trans('backLang.comments') }} : {{ $v2 }}
                                                        {{--{{ trans('backLang.visitors') }} : {{ $v2 }},--}}
                                                        {{--{{ trans('backLang.pageViews') }} : {{ $v3 }}--}}
                                                    </small>
                                                </div>
                                            </li>
                                        @endif
                                        <?php $ii++;?>
                                    @endforeach
                                </ul>
                            @endif

                        </div>
                    </div>
                @endif
            @endif
                @if(Helper::GeneralWebmasterSettings("newsletter_status"))
                    @if(@Auth::user()->permissionsGroup->newsletter_status)
                        <div class="col-md-12 col-xl-{{$col_width}}">
                            <div class="box m-b-0" style="height: 370px;overflow-y: scroll">
                                <div class="box-header">
                                    <h3>Latest {{ trans('backLang.fc') }}</h3>
                                </div>

                                @if(count($Agents) == 0)
                                    <div class="text-center m-t-1" style="color:#bbb">
                                        <h1><i class="material-icons">&#xe7ef;</i></h1>
                                        {{ trans('backLang.noData') }}</div>
                                @else
                                    <ul class="list-group no-border">
                                        @foreach($Agents as $Agent)
                                            <li class="list-group-item">
                                                <a href="{{ route("agents.show",["id"=>$Agent->id]) }}"
                                                   class="list-left">
                                                <span class="w-40 avatar">
                                                    @if($Agent->profile_pic!="")
                                                        <img  style="height: 37px;" src="{{ URL::to('public/uploads/agents/'.$Agent->profile_pic) }}"
                                                             >
                                                    @else
                                                        <img src="{{ URL::to('public/uploads/agents/profile.jpg') }}"
                                                              style="opacity: 0.5">
                                                    @endif
                                                </span></a>
                                                <div class="list-body">
                                                    <div>
                                                        <a href="{{ route("agents.show",["id"=>$Agent->id]) }}">{{ $Agent->name }}</a>
                                                    </div>
                                                    <small class="text-muted text-ellipsis"><span
                                                                dir="ltr">{{ trans('agent.mobile') }}:{{ $Agent->mobile }}</span></small>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    @endif
                @endif
        </div>
    </div>
@endsection
@section('footerInclude')
    <script src="https://cdn.jsdelivr.net/npm/frappe-charts@1.1.0/dist/frappe-charts.min.iife.js"></script>
    <!-- or -->
    <script src="https://unpkg.com/frappe-charts@1.1.0/dist/frappe-charts.min.iife.js"></script>
    <script type="text/javascript">
        var pickups_date = {!! json_encode($pickups_dates) !!}
        var pickupPublish = {!! json_encode($PickupPublish) !!}
            var completed_publish =  {!! json_encode($Pickupcompleted) !!}
        let chart = new frappe.Chart( "#chart", { // or DOM element
            data: {
                labels: pickups_date,
                datasets: [
                    { name: "Publish Pickup", values:pickupPublish },
                    { name: "Completed Pickup", values: completed_publish }
                ],
            },
            title: "Last 7 Days Of Pickups",
            type: 'axis-mixed', // or 'bar', 'line', 'pie', 'percentage'
            height: 300,
            colors: ['blue', '#0cb8a4'],

            tooltipOptions: {
                formatTooltipX: d => (d + '').toUpperCase(),
            formatTooltipY: d => d + ' Pickups',
        }
        });
    </script>


@endsection


