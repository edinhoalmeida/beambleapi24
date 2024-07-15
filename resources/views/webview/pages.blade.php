@extends('webview.layout.layout')

@section('content')
<div class="container">
    <img src="{{ asset('team/imgs/app_icon.png') }}" class="wv-logo"> <br>
    <h2>{{$title}}</h2>
    <div>
        {!! $body !!}
    </div>
</div>
@endsection