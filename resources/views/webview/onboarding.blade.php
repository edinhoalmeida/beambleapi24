@extends('webview.layout.layout')

@section('content')
<div class="container">
    <img src="{{ asset('team/imgs/app_icon.png') }}" class="wv-logo"> <br>
    <h2>Webview - onboarding</h2>
    <?php 
    pr($status);
    ?> 
</div>
@endsection


@section('footer_scripts')

<script>
var a = 'teste';
</script>

@endsection
