<!DOCTYPE html>
<html>
<head>
    {{--<meta charset="utf-8">--}}
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password | Docboyz</title>
    <!--global css starts-->
    <link rel="stylesheet" type="text/css" href="{{ asset('/public/backEnd/assets/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/x-icon">
    <!--end of global css-->
    <!--page level css starts-->
    <link rel="stylesheet" type="text/css" href="{{ asset('/public/css/forgot.css') }}">
    <!--end of page level css-->
</head>
<body>
<div class="container">
    <div class="row">
        @if(session()->get('success'))
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Success!</strong> Password reset successful.
            </div>
        @endif
        @if(session()->get('error'))
            <div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Warning!</strong> Password reset failed. Please try again.
            </div>
        @endif
        <div class="box animation flipInX">

            <img src="{{ asset('/public/logo.png') }}" alt="logo" class="img-responsive mar">
            <h4>DocBoyz</h4>

            <h3 class="text-primary">@lang('mails.reset_your_password', [])</h3>
            <p>@lang('mails.enter_password_details', [])</p>

            <form action="{{ route('forgot-password',compact(['userId'])) }}" class="omb_loginForm pwd_validation"  autocomplete="off" method="POST">
                <!-- CSRF Token -->
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <label class="sr-only">@lang('mails.new_password', [])</label>
                <input type="password" class="form-control" name="password" required placeholder="New Password">
                <span class="help-block">{{ $errors->first('password', ':message') }}</span>
                <label class="sr-only">@lang('mails.confirm_password', [])</label>
                <input type="password" class="form-control mt-15" name="password_confirm" required placeholder="Confirm New Password">
                <span class="help-block">{{ $errors->first('password_confirm', ':message') }}</span>
                <input type="submit" class="btn btn-block btn-primary" value="@lang('mails.submit_password', [])" style="margin-top:10px;">
            </form>
        </div>
    </div>
</div>
<!--global js starts-->
<script type="text/javascript" src="{{ asset('public/assets/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('public/assets/js/bootstrap.min.js') }}"></script>
<!--global js end-->
</body>
</html>
