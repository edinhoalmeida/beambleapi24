@extends('webview.layout.layout')

@section('content')
<div class="container">
    <img src="{{ asset('team/imgs/app_icon.png') }}" class="wv-logo"> <br>
    <h2>{!!__('beam.stripe_pending')!!}</h2>
    <form class="form-style-6" name="FormName" method="POST" action="{{ route('webview.onboarding') }}">
        <input type="hidden" name="user_id" value="{{ $user_id }}">
        <input type="submit" name="onboarding" value="{!!__('beam.stripe_continue_register')!!}">
    </form>
</div>
@endsection
