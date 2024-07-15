@extends('emails.layout')

@section('content')

	<font face="Arial" size="2"><strong>Vérification de l'E-mail</strong></font><br /><br />
	<font face="Arial" size="2">Entrez le code ci-dessous dans l'application <strong>Beamble</strong> pour finaliser votre inscription:<br /><br />

	@component('emails.box')
	    @slot('text'){{ $otp_code }}@endslot
	@endcomponent
	<br /><br />
	</font>

@stop
