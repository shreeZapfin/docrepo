<?php
// Current Full URL
$fullPagePath = Request::url();
// Char Count of Backend folder Plus 1
$envAdminCharCount = strlen(env('BACKEND_PATH')) + 1;
// URL after Root Path EX: admin/home
$urlAfterRoot = substr($fullPagePath, strpos($fullPagePath, env('BACKEND_PATH')) + $envAdminCharCount);
?>

<style>


</style>

<div id="aside" class="app-aside modal fade folded md nav-expand">
    <div class="left navside dark dk" layout="column">
        <div class="navbar navbar-md no-radius">
            <!-- brand -->
            <a class="navbar-brand" href="{{ url('customer/dashboard') }}">
                <img src="{{ URL::to('public/backEnd/assets/images/logo.png') }}" alt="Control">
                <span class="hidden-folded inline">DocBoyz</span>
            </a>
            <!-- / brand -->
        </div>
        <div flex class="hide-scroll">
            <nav class="scroll nav-active-primary">
                <ul class="nav" ui-nav>

                    <li {!! (Request::is('customer/pickups') ? 'class="active"' : '') !!}>
                        <a href="{{ route('customer.pickups') }}" onclick="location.href='{{ route('customer.pickups') }}'">
                            <span class="nav-icon"><i class="material-icons">&#xe1b8;</i></span>
                            <span class="nav-text">{{ trans('customer.pickups') }}</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>