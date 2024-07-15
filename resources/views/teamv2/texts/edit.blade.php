@extends('teamv2.layout.layout')

@section('content')

@include('teamv2.partials._page_title')

<section class="content">


        <div class="float-sm-right">
            <a class="btn btn-warning" href="{{ route('wvtexts.index') }}"> retourner</a>
        </div>
        <br>
   
    @if ($errors->any())
        <div class="alert alert-danger">
        Il y a eu des problèmes avec votre entrée.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
  
    <form action="{{ route('wvtexts.update',$text->id) }}" method="POST">
        @csrf
        @method('PUT')
   
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Slug:</strong>
                    <input type="text" name="slug" value="{{ $text->slug }}" class="form-control" placeholder="Slug">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Titre:</strong>
                    <input type="text" name="title" value="{{ $text->title }}" class="form-control" placeholder="Titre">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Language code:</strong>
                    <input type="text" name="lang_code" value="{{ $text->lang_code }}" class="form-control" placeholder="Language code">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Texte:</strong>
                    <textarea class="form-control" style="height:150px" name="body_txt" placeholder="Texte">{{ $text->body_txt }}</textarea>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
              <button type="submit" class="btn btn-primary">{{ __('Sauvegarder') }}</button>
            </div>
        </div>
   
    </form>

</section>
@endsection