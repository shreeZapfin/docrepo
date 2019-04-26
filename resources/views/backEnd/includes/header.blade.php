<?php
$connectEmailAddress = "";
$connectEmailPassword = "";
$connectDomainURL = "";
$nMsgCount = "";
if (Auth::user()->connect_email != "" && Auth::user()->connect_password) {
    try {
        $connectEmailAddress = Auth::user()->connect_email; // Full email address
        $connectEmailPassword = Auth::user()->connect_password;        // Email password
        $connectDomainURL = substr($connectEmailAddress, strpos($connectEmailAddress, "@") + 1);
        $useHTTPS = true;
    } catch (Exception $e) {

    }
}
?>

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
            $alerts = \App\User::find(\Illuminate\Support\Facades\Auth::getUser()->id)->unreadNotifications;

            ?>

                <li class="nav-item dropdown pos-stc-xs">
                    <a class="nav-link" href data-toggle="dropdown">
                        <i class="material-icons">&#xe7f5;</i>
                        <span class="label label-sm up warn">{{ $alerts->count() }}</span>

                    </a>
                    @if($alerts->count() >0)
                    <div class="dropdown-menu pull-right w-xl animated fadeInUp no-bg no-border no-shadow">
                        <div class="box dark">
                            <div class="box p-a scrollable maxHeight320">
                                <ul class="list-group list-group-gap m-a-0">
                                    <li class="list-group-item lt box-shadow-z0 b">
                                        @foreach($alerts as $notification)

                                            <span class="clear block">
                                                {{--<small>{{str_replace('"','',json_encode($notification->data['pickup_person']))}}</small><br>--}}
                                        <a href="{!! route('notification.view',json_encode($notification->data['notification_id'])) !!}" class="text-primary">{{str_replace('"','',json_encode($notification->data['notification']))}}</a>
                                       <br> <small class="text-muted">
                                                {{\Carbon\Carbon::parse($notification['created_at'])->format('d-M-Y')}}
                                                    </small>
                                    </span><br>
                                        @endforeach
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif
                </li>

            <li class="nav-item dropdown">
                <a class="nav-link clear" href data-toggle="dropdown">
                  <span class="avatar w-32">
                      @if(Auth::user()->photo !="")
                          <img src="{{ URL::to('public/uploads/users/'.Auth::user()->photo) }}" alt="{{ Auth::user()->name }}"
                               title="{{ Auth::user()->name }}">
                      @else
                          <img src="{{ URL::to('public/backEnd/assets/images/profile.jpg') }}" alt="{{ Auth::user()->name }}"
                               title="{{ Auth::user()->name }}">
                      @endif
                      <i class="on b-white bottom"></i>
                  </span>
                </a>
                <div class="dropdown-menu pull-right dropdown-menu-scale">
                    @if(Auth::user()->permissions ==0 || Auth::user()->permissions ==1)
                        <a class="dropdown-item"
                           href="{{ route('usersEdit',Auth::user()->id) }}"><span>{{ trans('backLang.profile') }}</span></a>
                    @endif
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ url('/logout') }}">{{ trans('backLang.logout') }}</a>

                    {{--<form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">--}}
                        {{--{{ csrf_field() }}--}}
                    {{--</form>--}}
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
        <div class="collapse navbar-toggleable-sm" id="collapse">
            {{Form::open(['route'=>['adminFind'],'method'=>'POST', 'role'=>'search', 'class' => "navbar-form form-inline pull-right pull-none-sm navbar-item v-m" ])}}

            <div class="form-group l-h m-a-0">
                {{--<div class="input-group input-group-sm"><input type="text" name="q" class="form-control p-x b-a rounded"--}}
                                                               {{--placeholder="{{ trans('backLang.search') }}..." required>--}}
                    {{--<span--}}
                            {{--class="input-group-btn"><button type="submit" class="btn white b-a rounded no-shadow"><i--}}
                                    {{--class="fa fa-search"></i></button></span></div>--}}
            </div>
        {{Form::close()}}
        <!-- link and dropdown -->
            <ul class="nav navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link" href data-toggle="dropdown">
                        <i class="fa fa-fw fa-plus text-muted"></i>
                        <span>{{ trans('backLang.new') }} </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-scale">
                        <a class="dropdown-item" href="{{ route('pickups.import') }}" onclick="location.href='{{ route('pickups') }}'">
                            <span class="nav-icon"><i class="material-icons">&#xe1b8;</i></span>
                            <span class="nav-text">{{ trans('backLang.pickups') }}</span>
                        </a>

                        <a class="dropdown-item" href="{{ route('customers.import') }}" onclick="location.href='{{ route('customers') }}'">
                            <span class="nav-icon">
                                <i class="material-icons"></i>
                            </span>
                            <span class="nav-text">{{ trans('backLang.company') }}</span>
                        </a>

                    </div>
                </li>
            </ul>
            <!-- / -->
        </div>
        <!-- / navbar collapse -->
    </div>
</div>