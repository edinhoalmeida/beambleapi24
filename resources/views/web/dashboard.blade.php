@extends('web.layout')

@section('content')
 
      <div class="starter-template">
        <h1>Beamble website</h1>
        <p class="lead">Dashboard web.</p>
        <p>{{ $User_type }}.</p>
        <hr>
        <a class="btn btn-info" href="{{ route('web.charge') }}">charger</a>
        <a class="btn btn-info" href="{{ route('web.connect') }}">connect with your stripe account</a>
      </div>

@endsection

  