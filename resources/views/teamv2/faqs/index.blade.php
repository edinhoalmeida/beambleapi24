@extends('teamv2.layout.layout')

@section('content')

    @include('teamv2.partials._page_title')
    <section class="content">
    

            <div class="float-sm-right">
                <a class="btn btn-warning" href="{{ route('wvfaqs.create') }}"> Ajouter du FAQ*</a>
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
            <th>Question</th>
            <th>Répondre</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($faqs as $faq)
        <tr>
            <td>{{ $faq->id }}</td>
            <td>{{ $faq->title }}</td>
            <td>{{ $faq->body_txt }}</td>
            <td>
                <form action="{{ route('wvfaqs.destroy',$faq->id) }}" method="POST">
   
                    <a class="btn btn-info" href="{{ route('wvfaqs.show',$faq->id) }}">Montrer</a>
    
                    <a class="btn btn-primary" href="{{ route('wvfaqs.edit',$faq->id) }}">Modifier</a>
   
                    @csrf
                    @method('DELETE')
      
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
  
    {!! $faqs->links() !!}
      

    </section>
    @endsection

@section('footer_scripts')
<script>
$(function () {
  
});
</script>

@endsection