<style>

    p{

        background-color: white;

    }

    body{

        background-color: white;

    }

</style>



<p style="">Dear Sir/Madam,</p>

<p>Please click below links of Document For {!! $data->subject_name!!}</p>
@foreach($data->links as $doc_link)
    <a href="{!! $doc_link->link !!}"><button type="button" class="btn btn-md white btn-addon default" style="background-color: #0cc2aa;margin-top: 20px; color: white; border: 2px solid #0cc2aa; font-size: 16px; padding: 10px 24px; border-radius: 4px;">{!! $doc_link->name !!}</button></a>
@endforeach



<p>Regards</p>

<p>DocBoyz Support.</p>































