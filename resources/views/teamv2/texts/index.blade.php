@extends('teamv2.layout.layout')

@section('content')

    @include('teamv2.partials._page_title')
    <section class="content">
    

            <div class="float-sm-right">
                <a class="btn btn-warning" href="{{ route('wvtexts.create') }}"> Ajouter du texte*</a>
            </div>
            <br>
    
   
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
   
    <table class="table table-bordered">
        <tr>
            <th>#</th>
            <th>Slug</th>
            <th>Titre</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($texts as $text)
        <tr>
            <td>{{ $text->id }}</td>
            <td>{{ $text->slug }}</td>
            <td>{{ $text->title }}</td>
            <td>
                <form action="{{ route('wvtexts.destroy',$text->id) }}" method="POST">
   
                    <a class="btn btn-info" href="{{ route('wvtexts.show',$text->id) }}">Montrer</a>
    
                    <a class="btn btn-primary" href="{{ route('wvtexts.edit',$text->id) }}">Modifier</a>
   
                    @csrf
                    @method('DELETE')
      
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
  
    {!! $texts->links() !!}
      

    </section>
    @endsection

@section('footer_scripts')
<script>
$(function () {
  
});
</script>

@endsection