@extends('emails.layout')

@section('content')

	<font face="Arial" size="2"><strong>Alteração de senha</strong></font><br /><br />
	<font face="Arial" size="2">Para alterar sua senha no <strong>Beamble</strong> clique no link abaixo:<br /><br />

	@component('emails.button')
	   	@slot('url')
	    	{{ route('forget', [$reset_password_code]) }}
	    @endslot
	    @slot('link')
	      	alterar senha
	    @endslot
	@endcomponent
	<br /><br />
	Ou copie e cole a linha abaixo em seu navegador:<br />
	{{ route('forget', [$reset_password_code]) }}<br />

	</font>

@stop
