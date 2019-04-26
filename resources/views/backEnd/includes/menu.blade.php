<?php
// Current Full URL
$fullPagePath = Request::url();
// Char Count of Backend folder Plus 1
$envAdminCharCount = strlen(env('BACKEND_PATH')) + 1;
// URL after Root Path EX: admin/home
$urlAfterRoot = substr($fullPagePath, strpos($fullPagePath, env('BACKEND_PATH')) + $envAdminCharCount);
?>

<div id="aside" class="app-aside modal fade folded md nav-expand">
    <div class="left navside dark dk" layout="column">
        <div class="navbar navbar-md no-radius">
            <!-- brand -->
            <a class="navbar-brand" href="{{ route('adminHome') }}">
                <img src="{{ URL::to('public/backEnd/assets/images/logo.png') }}" alt="Control">
                <span class="hidden-folded inline">{{ trans('backLang.control') }}</span>
            </a>
            <!-- / brand -->
        </div>
        <div flex class="hide-scroll">
            <nav class="scroll nav-active-primary">
                <ul class="nav" ui-nav>
                    <li {!! (Request::is('dashboard') ? 'class="active"' : '') !!}>
                        <a href="{{ route('adminHome') }}" onclick="location.href='{{ route('adminHome') }}'">
                            <span class="nav-icon">
                                <i class="material-icons">&#xe3fc;</i>
                            </span>
                            <span class="nav-text">{{ trans('backLang.dashboard') }}</span>
                        </a>
                    </li>
                    <li {!! (Request::is('customers') || Request::is('customers/import') ||  Request::is('customers/*')? 'class="active"' : '') !!}>
                        <a href="{{ route('customers') }}" onclick="location.href='{{ route('customers') }}'">
                            <span class="nav-icon">
                                <i class="material-icons"></i>
                            </span>
                            <span class="nav-text">{{ trans('backLang.company') }}</span>
                        </a>
                    </li>
                    <li {!! (Request::is('agents') ||  Request::is('agents/*')  ? 'class="active"' : '') !!}>
                        <a  href="{{ route('agents') }}" onclick="location.href='{{ route('agents') }}'">
                            <span class="nav-icon"><i class="material-icons">account_circle</i></span>
                            <span class="nav-text">{{ trans('backLang.fc') }}</span>
                        </a>
                    </li>
                    <li {!! (Request::is('redeems')   ? 'class="active"' : '') !!}>
                        <a  href="{{ route('redeems') }}" onclick="location.href='{{ route('redeems') }}'">
                            <span class="nav-icon"><i class="material-icons">&#xe156;</i></span>
                            <span class="nav-text">{{ trans('backLang.redeem') }}</span>
                        </a>
                    </li>
                    <li {!! (Request::is('products') || Request::is('products/*')  ? 'class="active"' : '') !!}>
                        <a  href="{{ url('products') }}" onclick="location.href='{{ url('products') }}'">
                            <span class="nav-icon"><i class="material-icons"></i></span>
                            <span class="nav-text">{{ trans('backLang.products') }}</span>
                        </a>
                    </li>

                    {{--<li {!! (Request::is('documents') || Request::is('documents/import_documents') ? 'class="active"' : '') !!}>
                        <a  href="{{ route('documents') }}" onclick="location.href='{{ route('documents') }}'">
                            <span class="nav-icon"><i class="material-icons">&#xe5c3;</i></span>
                            <span class="nav-text">{{ trans('backLang.documents') }}</span>
                        </a>
                    </li>--}}
                    <li {!! (Request::is('pickups') || Request::is('pickups/*')    ? 'class="active"' : '') !!}>
                        <a href="{{ route('pickups') }}" onclick="location.href='{{ route('pickups') }}'">
                            <span class="nav-icon"><i class="material-icons">&#xe1b8;</i></span>
                            <span class="nav-text">{{ trans('backLang.pickups') }}</span>
                        </a>
                    </li>
                    <li {!! (Request::is('questions') || Request::is('questions/*')    ? 'class="active"' : '') !!}>
                        <a href="{{ url('pickups') }}" onclick="location.href='{{ url('questions') }}'">
                            <span class="nav-icon"><i class="fa fa-question-circle-o"></i></span>
                            <span class="nav-text">{{ trans('backLang.questions') }}</span>
                        </a>
                    </li>
                    @if(@Auth::user()->permissionsGroup->settings_status)
                        <?php
                        $currentFolder = "settings"; // Put folder name here
                        $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));


                        $currentFolder2 = "menus"; // Put folder name here
                        $PathCurrentFolder2 = substr($urlAfterRoot, 0, strlen($currentFolder2));

                        $currentFolder3 = "users"; // Put folder name here
                        $PathCurrentFolder3 = substr($urlAfterRoot, 0, strlen($currentFolder2));
                        ?>
                        <?php
                        $currentFolder = "users"; // Put folder name here
                        $PathCurrentFolder = substr($urlAfterRoot, 0, strlen($currentFolder));
                        ?>
                        <li {!! (Request::is('users') || Request::is('users/*')    ? 'class="active"' : '') !!}>
                            <a  href="{{ route('users') }}">
                                <span class="nav-icon"> <i class="material-icons">&#xe8b8;</i></span>
                                <span class="nav-text">{{ trans('backLang.usersPermissions') }}</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</div>