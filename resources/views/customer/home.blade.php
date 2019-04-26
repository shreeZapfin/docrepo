@extends('customer.layout')
@section('headerInclude')
    <link rel="stylesheet" type="text/css" href="{{ URL::to("backEnd/assets/styles/flags.css") }}"/>
@endsection
@section('content')
    <div class="padding p-b-0">
        <div class="margin">

        </div>
        <div class="row">
            <div class="col-sm-12 col-md-5 col-lg-4">
                <div class="row">

                    @foreach($GeneralWebmasterSections as $headerWebmasterSection)


                    @endforeach
                    <div class="col-xs-12">
                        <div class="row-col box-color text-center primary">
                            <div class="row-cell p-a">
                                {{ trans('backLang.visitors') }}
                                <h4 class="m-a-0 text-md _600"><a href>100</a></h4>
                            </div>
                            <div class="row-cell p-a dker">
                                {{ trans('backLang.pageViews') }}
                                <h4 class="m-a-0 text-md _600"><a href>150</a></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-7 col-lg-8">
                <div class="row-col box bg">
                    <div class="col-sm-8">
                        <div class="box-header">
                            <h3>{{ trans('backLang.visitors') }}</h3>
                            <small>{{ trans('backLang.lastFor7Days') }}</small>
                        </div>
                        <div class="box-body">
                            <div ui-jp="plot" ui-refresh="app.setting.color" ui-options="
			              [
			                {
			                  data: [

                                    ],
      points: { show: true, radius: 0},
      splines: { show: true, tension: 0.45, lineWidth: 2, fill: 0 }
    }
  ],
  {
    colors: ['#0cc2aa','#fcc100'],
    series: { shadowSize: 3 },
    xaxis: { show: true, font: { color: '#ccc' }, position: 'bottom' },
    yaxis:{ show: true, font: { color: '#ccc' }},
    grid: { hoverable: true, clickable: true, borderWidth: 0, color: 'rgba(120,120,120,0.5)' },
    tooltip: true,
    tooltipOpts: { content: '%x.0 is %y.4',  defaultTheme: false, shifts: { x: 0, y: -40 } }
  }
" style="height:162px">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 dker">
                        <div class="box-header">
                            <h3>{{ trans('backLang.reports') }}</h3>
                        </div>
                        <div class="box-body">
                            <p class="text-muted">
                                {{ trans('backLang.reportsDetails') }} : <br>
                                <a href="">{{ trans('backLang.visitorsAnalyticsBydate') }}</a>,
                                <a href="">{{ trans('backLang.visitorsAnalyticsByCountry') }}</a>,
                                <a href="">{{ trans('backLang.visitorsAnalyticsByCity') }}</a>,
                                <a href="">{{ trans('backLang.visitorsAnalyticsByOperatingSystem') }}</a>,
                                <a href="">{{ trans('backLang.visitorsAnalyticsByBrowser') }}</a>,
                                <a href="">{{ trans('backLang.visitorsAnalyticsByReachWay') }}</a>,
                                <a href="">{{ trans('backLang.visitorsAnalyticsByHostName') }}</a>,
                                <a href="">{{ trans('backLang.visitorsAnalyticsByOrganization') }}</a>
                            </p>
                            <a href="" style="margin-bottom: 5px;"
                               class="btn btn-sm btn-outline rounded b-success">{{ trans('backLang.viewMore') }}</a><br>
                            <a href=""
                               class="btn btn-sm btn-outline rounded b-info">{{ trans('backLang.visitorsAnalyticsVisitorsHistory') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-xl-4">
                <div class="box">
                    <div class="box-header">
                        <h3>{{ trans('backLang.visitorsRate') }}</h3>
                        <small>{{ trans('backLang.visitorsRateToday')." [ ".date('Y-m-d')." ]" }}</small>
                    </div>
                    <div class="box-body">

                        <div ui-jp="plot" ui-options="
              [
                {
                  data: [],
                  points: { show: true, radius: 5},
                  splines: { show: true, tension: 0.45, lineWidth: 0, fill: 0.4}
                }
              ],
              {
                colors: ['#0cc2aa'],
                series: { shadowSize: 3 },
                xaxis: { show: true, font: { color: '#ccc' }, position: 'bottom' },
                yaxis:{ show: true, font: { color: '#ccc' }, min:1},
                grid: { hoverable: true, clickable: true, borderWidth: 0, color: 'rgba(120,120,120,0.5)' },
                tooltip: true,
                tooltipOpts: { content: '%x.0 is %y.4',  defaultTheme: false, shifts: { x: 0, y: -40 } }
              }
            " style="height:200px">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xl-4">
                <div class="box" style="min-height: 300px">
                    <div class="box-header">
                        <h3>{{ trans('backLang.browsers') }}</h3>
                        <small>{{ trans('backLang.browsersCalculated') }}</small>
                    </div>

                    @if($TodayByBrowser1_val >0)
                        <div class="text-center b-t">
                            <div class="row-col">
                                <div class="row-cell p-a">
                                    <div class="inline m-b">
                                        <div ui-jp="easyPieChart" class="easyPieChart" ui-refresh="app.setting.color"
                                             data-redraw='true' data-percent="55" ui-options="{
	                      lineWidth: 8,
	                      trackColor: 'rgba(0,0,0,0.05)',
	                      barColor: '#0cc2aa',
	                      scaleColor: 'transparent',
	                      size: 100,
	                      scaleLength: 0,
	                      animate:{
	                        duration: 3000,
	                        enabled:true
	                      }
	                    }">
                                            <div>
                                                <h5>
                                                    <?php
                                                    echo $perc1 = round(($TodayByBrowser1_val * 100) / ($TodayByBrowser1_val + $TodayByBrowser2_val)) . "%";
                                                    ?>
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        {{$TodayByBrowser1}}
                                        <small class="block m-b">{{$TodayByBrowser1_val}}</small>
                                        <a href=""
                                           class="btn btn-sm white text-u-c rounded">{{ trans('backLang.more') }}</a>
                                    </div>
                                </div>
                                <div class="row-cell p-a dker">
                                    <div class="inline m-b">
                                        <div ui-jp="easyPieChart" class="easyPieChart" ui-refresh="app.setting.color"
                                             data-redraw='true' data-percent="45" ui-options="{
	                      lineWidth: 8,
	                      trackColor: 'rgba(0,0,0,0.05)',
	                      barColor: '#fcc100',
	                      scaleColor: 'transparent',
	                      size: 100,
	                      scaleLength: 0,
	                      animate:{
	                        duration: 3000,
	                        enabled:true
	                      }
	                    }">
                                            <div>
                                                <h5>
                                                    <?php
                                                    echo $perc1 = round(($TodayByBrowser2_val * 100) / ($TodayByBrowser1_val + $TodayByBrowser2_val)) . "%";
                                                    ?>
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        {{$TodayByBrowser2}}
                                        <small class="block m-b">{{$TodayByBrowser2_val}}</small>
                                        <a href=""
                                           class="btn btn-sm white text-u-c rounded">{{ trans('backLang.more') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-12 col-xl-4">
                <div class="box light lt" style="min-height: 300px">
                    <div class="box-header">
                        <h3> {{ trans('backLang.todayByCountry') }}</h3>
                    </div>
                    <div class="box-tool">
                        <ul class="nav">
                            <li class="nav-item inline">
                                <a href=""
                                   class="btn btn-sm white text-u-c rounded">{{ trans('backLang.more') }}</a>
                            </li>
                        </ul>
                    </div>
                    @if(count($TodayByCountry) == 0)
                        <div class="text-center m-t-1" style="color:#bbb">
                            <h1><i class="material-icons">&#xe1b7;</i></h1>
                            {{ trans('backLang.noData') }}</div>
                    @else
                        <ul class="list no-border p-b">
                            <?php
                            $ii = 1;
                            ?>
                            @foreach($TodayByCountry as $id)
                                @if($ii<=4)
                                    <li class="list-item">
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

                                        <a herf class="list-left">
	                  <span class="w-40 rounded dker">
		                  <span>{{$v1}}</span>
		              </span>
                                        </a>
                                        <div class="list-body">
                                            <div>{!! $flag !!} {{$v0}}</div>
                                            <small class="text-muted text-ellipsis">
                                                {{ trans('backLang.visitors') }} : {{ $v2 }},
                                                {{ trans('backLang.pageViews') }} : {{ $v3 }}
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
        </div>
    </div>

@endsection
