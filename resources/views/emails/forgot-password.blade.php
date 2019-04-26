@component('mail::layout')
    {{-- Header --}}
    @slot('header')
    @component('mail::header', ['url' => config('app.url')])
    <div class="clearfix float-my-children">

        <img style="width: 70px; " src="{{asset('/logo.png') }}">
        <div>Docboyz</div>
    </div>
    @endcomponent
    @endslot

    {{-- Body --}}
# @lang('mails.hello', [])  {!! $agent->name !!},<br>

@lang('mails.link_update_your_password', [])
<a href="{!! $agent->forgotPasswordUrl !!}"><button type="button" style="background-color: #2e8c37; color: white; border: 2px solid #2e8c37; font-size: 16px; padding: 10px 24px; border-radius: 4px;">@lang('mails.reset_password', [])</button></a>
@lang('mails.thanks', []),

    {{-- Footer --}}
    @slot('footer')
    @component('mail::footer')
    &copy; 2018 @lang('mails.all_copyrights_reserved', [])
@endcomponent
@endslot
@endcomponent