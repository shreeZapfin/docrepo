

<div class="app-header white box-shadow navbar-md">
    <div class="navbar">
        <!-- Open side - Naviation on mobile -->
        <a data-toggle="modal" data-target="#aside" class="navbar-item pull-left hidden-lg-up">
            <i class="material-icons">&#xe5d2;</i>
        </a>
        <!-- / -->

        <!-- Page title - Bind to $state's title -->
        <div class="navbar-item pull-left h5" ng-bind="$state.current.data.title" id="pageTitle"></div>

        <!-- navbar right -->
        <ul class="nav navbar-nav pull-right">

            <?php
            $alerts = count(Helper::webmailsAlerts());
            ?>
            @if($alerts >0)
                <li class="nav-item dropdown pos-stc-xs">
                    {{--<a class="nav-link" href data-toggle="dropdown">--}}
                        {{--<i class="material-icons">&#xe7f5;</i>--}}
                        {{--@if($alerts >0)--}}
                            {{--<span class="label label-sm up warn">{{ $alerts }}</span>--}}
                        {{--@endif--}}
                    {{--</a>--}}
                    <div class="dropdown-menu pull-right w-xl animated fadeInUp no-bg no-border no-shadow">
                        <div class="box dark">
                            <div class="box p-a scrollable maxHeight320">
                                <ul class="list-group list-group-gap m-a-0">
                                    @foreach(Helper::webmailsAlerts() as $webmailsAlert)
                                        <li class="list-group-item lt box-shadow-z0 b">
                                    <span class="clear block">
                                        <small>{{ $webmailsAlert->from_name }}</small><br>
                                        <a href="{{ route("webmailsEdit",["id"=>$webmailsAlert->id]) }}"
                                           class="text-primary">{{ $webmailsAlert->title }}</a>
                                        <br>
                                        <small class="text-muted">
                                            {{ date('d M Y  h:i A', strtotime($webmailsAlert->date)) }}
                                        </small>
                                    </span></li>
                                    @endforeach


                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
            @endif
            <li class="nav-item dropdown">
                <a class="nav-link clear" href data-toggle="dropdown">
                  <span class="avatar w-32">
                        @if(session()->get('customer')->photo !="")
                          <img src="{{ URL::to('public/uploads/users/'.session()->get('customer')->photo) }}" alt="{{ session()->get('customer')->name }}"
                               title="{{ session()->get('customer')->name}}">
                      @else
                          <img src="{{ URL::to('public/backEnd/assets/images/profile.jpg') }}" alt="{{session()->get('customer')->name }}"
                               title="{{ session()->get('customer')->name }}">
                      @endif

                      <i class="on b-white bottom"></i>
                  </span>
                </a>
                <div class="dropdown-menu pull-right dropdown-menu-scale">
                    @if(session()->get('customer')->name)
                        <a class="dropdown-item"
                           href=""><span>{{session()->get('customer')->name}}</span></a>
                    @endif
                    <div class="dropdown-divider"></div>
                    <a onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                       class="dropdown-item" href="{{ url('customer/logout') }}">{{ trans('customer.logout') }}</a>

                    <form id="logout-form" action="{{ url('customer/logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
            </li>

            <li class="nav-item hidden-md-up">
                <a class="nav-link" data-toggle="collapse" data-target="#collapse">
                    <i class="material-icons">&#xe5d4;</i>
                </a>
            </li>
        </ul>
        <!-- / navbar right -->

        <!-- navbar collapse -->
        {{--<div class="collapse navbar-toggleable-sm" id="collapse">--}}
            {{--{{Form::open(['route'=>['adminFind'],'method'=>'POST', 'role'=>'search', 'class' => "navbar-form form-inline pull-right pull-none-sm navbar-item v-m" ])}}--}}

            {{--<div class="form-group l-h m-a-0">--}}
                {{--<div class="input-group input-group-sm"><input type="text" name="q" class="form-control p-x b-a rounded"--}}
                                                               {{--placeholder="{{ trans('customer.search') }}..." required>--}}
                    {{--<span--}}
                            {{--class="input-group-btn"><button type="submit" class="btn white b-a rounded no-shadow"><i--}}
                                    {{--class="fa fa-search"></i></button></span></div>--}}
            {{--</div>--}}
        {{--{{Form::close()}}--}}
        <!-- link and dropdown -->
            {{--<ul class="nav navbar-nav">--}}
                {{--<li class="nav-item dropdown">--}}
                    {{--<a class="nav-link" href data-toggle="dropdown">--}}
                        {{--<i class="fa fa-fw fa-plus text-muted"></i>--}}
                        {{--<span>{{ trans('customer.new') }} </span>--}}
                    {{--</a>--}}
                {{--</li>--}}
            {{--</ul>--}}
            <!-- / -->
        {{--</div>--}}
        <!-- / navbar collapse -->
    </div>
</div>