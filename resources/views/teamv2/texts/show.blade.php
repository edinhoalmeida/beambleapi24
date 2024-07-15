@extends('teamv2.layout.layout')

@section('content')

@include('teamv2.partials._page_title')

<section class="content">


        <div class="float-sm-right">
            <a class="btn btn-warning" href="{{ route('wvtexts.index') }}"> retourner</a>
        </div>
        <br>
   
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Slug:</strong>
                {{ $text->slug }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Titre:</strong>
                {{ $text->title }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Language code:</strong>
                {{ $text->lang_code }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Texte:</strong>
                <div style="padding:2rem;background:#e1e1e1">
                {!! $text_html !!}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection