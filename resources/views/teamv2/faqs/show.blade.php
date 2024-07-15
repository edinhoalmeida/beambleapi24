@extends('teamv2.layout.layout')

@section('content')

@include('teamv2.partials._page_title')

<section class="content">


        <div class="float-sm-right">
            <a class="btn btn-warning" href="{{ route('wvfaqs.index') }}"> retourner</a>
        </div>
        <br>
   
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Question:</strong>
                {{ $faq->title }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Language code:</strong>
                {{ $faq->lang_code }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Répondre:</strong>
                <div style="padding:2rem;background:#e1e1e1">
                {!! $faq_html !!}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection