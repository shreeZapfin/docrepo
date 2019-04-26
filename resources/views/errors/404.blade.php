<!DOCTYPE html>
<html  lang="{{ trans('backLang.code') }}" dir="{{ trans('backLang.direction') }}">
<head>
    <link rel="shortcut icon" sizes="196x196" href="{{ URL::to('/backEnd/assets/images/logo.png') }}">

    <!-- style -->
    <link rel="stylesheet" href="{{ URL::to('/backEnd/assets/animate.css/animate.min.css') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ URL::to('/backEnd/assets/glyphicons/glyphicons.css') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ URL::to('/backEnd/assets/font-awesome/css/font-awesome.min.css') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ URL::to('/backEnd/assets/material-design-icons/material-design-icons.css') }}"
          type="text/css"/>

    <link rel="stylesheet" href="{{ URL::to('/backEnd/assets/bootstrap/dist/css/bootstrap.min.css') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ URL::to('/backEnd/assets/styles/app.min.css') }}">



    @if( trans('backLang.direction')=="rtl")
        <link rel="stylesheet" href="{{ URL::to('/backEnd/assets/styles/rtl.css') }}">
    @endif

    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
</head>
<body>

<div class="app" id="app">

    <!-- ############ LAYOUT START-->

    <!-- content -->
    <div class="app-body indigo bg-auto w-full">
        <div class="text-center pos-rlt p-y-md">
            <h1 class="text-shadow text-white text-4x">
                <span class="text-2x font-bold block m-t-lg">404</span>
            </h1>
            <p class="h5 m-y-lg text-u-c font-bold">{{ trans('backLang.notFound') }}.</p>
            <a href="{{ URL::previous() }}" class="md-btn amber-700 md-raised p-x-md">
                <span class="text-white">{{ trans('backLang.returnTo') }} <i class="material-icons">&#xe5c4;</i></span>
            </a>
        </div>
    </div>
    <!-- / -->


    <!-- ############ LAYOUT END-->

</div>


</body>
</html>
<script type="text/javascript">
    var public_lang = "{{ trans('backLang.calendarLanguage') }}"; // this is a public var used in app.html.js to define path to js files
    var public_folder_path = "{{ URL::to('') }}"; // this is a public var used in app.html.js to define path to js files

    window.onload=function(){

    };
</script>
<script src="{{ URL::to('/backEnd/scripts/app.html.js') }}"></script>

