@extends('teamv2.layout.layout')

@section('content')

    @include('teamv2.partials._page_title')
    <section class="content">
    
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
   
    <table class="table table-bordered">
        <tr>
            <th>Name</th>
            <th>Last name</th>
            <th>Email</th>
            <th>Profile</th>
            <th>Date et l'heure</th>
        </tr>
        @foreach ($contacts as $contact)
        <tr>
            <td>{{ $contact->name }}</td>
            <td>{{ $contact->surname }}</td>
            <td>{{ $contact->email }}</td>
            <td>{{ $contact->profile_type }}</td>
            <td>{{ $contact->created_at }}</td>
        </tr>
        @endforeach
    </table>


    </section>
    @endsection
