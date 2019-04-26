<div class="modal-header">
    <h5 class="modal-title">{{ trans('backLang.confirmation') }}</h5>
</div>
<div class="modal-body text-center p-lg">
    <p>
        {{ trans('backLang.confirmationDeleteMsg') }}
        <br>
        <strong></strong>
    </p>
</div>
<div class="modal-footer">
    <button type="button" class="btn dark-white p-x-md"
            data-dismiss="modal">{{ trans('backLang.no') }}</button>
    <a href="{{ route("usersDestroy",["id"=>1]) }}"
       class="btn danger p-x-md">{{ trans('backLang.yes') }}</a>
</div>