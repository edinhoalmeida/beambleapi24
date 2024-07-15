@extends('emails.newlayout')

@section('content')
	<strong>Email Verification</strong><br /><br />
	
	Enter the code below into the <strong>Beamble</strong> app to finalize your registration:<br /><br />
	
	@component('emails.newbox')
	    @slot('text'){{ $otp_code }}@endslot
	@endcomponent
@stop
