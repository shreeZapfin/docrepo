<!DOCTYPE html>
<html lang="{{ trans('customer.code') }}" dir="{{ trans('customer.direction') }}">
<head>
    @include('customer.includes.head')
    @yield('headerInclude')
</head>
<body>

<div class="app" id="app">

    <!-- ############ LAYOUT START-->

    <!-- aside -->
@include('customer.includes.menu')
<!-- / aside -->

    <!-- content -->
    <div id="content" class="app-content box-shadow-z0" role="main">
        @include('customer.includes.header')
        @include('customer.includes.footer')
        <div ui-view class="app-body" id="view">

            <!-- ############ PAGE START-->
        @include('customer.includes.errors')
        @yield('content')
        <!-- ############ PAGE END-->

        </div>
    </div>
    <!-- / -->

    <!-- theme switcher -->
@include('customer.includes.settings')
<!-- / -->

    <!-- ############ LAYOUT END-->

</div>


@include('customer.includes.foot')
@yield('footerInclude')

</body>
</html>
