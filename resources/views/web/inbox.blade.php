@extends('web.layout-simple')


@push('after-styles')
    <link href="{{ asset('assets/web/css/getcss.php?main.css') }}" rel="stylesheet">
@endpush


@section('content')

    <div class="row">
        <div class="col-2"><a href="#" class="back"><i class="bi bi-arrow-left-square icon_m"></i></a></div>
        <div class="col-8 text-center"><img src="https://via.placeholder.com/50"><br>
            {{$beamer->surname}}, {{$beamer->name}}</div>
        <div class="col-2 text-right"><i class="bi bi-three-dots icon_m"></i></div>
    </div>

    <div id="inbox_msg" data-beamer_id="{{$beamer->id}}" data-client_id="{{$client->id}}">

        

    </div>


    <script id="template-mensage" type="text/template">
        <div class="box {class} {status}" data-message_id="{msg_id}">{content}</div>
    </script>

    <div class="row">
        <div class="col-12 text-center">
            Reppel 1 asd adsa 12/2 as 16h
        </div>
    </div>
    <div class="row" id="inbox_form">
        <div class="col-2">
            <i class="bi bi-camera"></i>
        </div>
        <div class="col-8 text-center">
            <input type="text" name="asadsd">
        </div>
        <div class="col-2 text-right">
            <a href="#" class="send_inbox"><i class="bi bi-send"></i></a>
        </div>
    </div>

@endsection


@push('after-scripts')

<script src="{{ asset('assets/web/js/inbox.js') }}"></script>

@endpush

