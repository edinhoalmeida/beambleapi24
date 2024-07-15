@extends('web.layout')

@section('content')
@if($movie->mediainfo != null)
<div class="table-responsive">
<table class="table table-condensed table-bordered table-striped">
<tbody>
      <tr>
        <td>
          <div class="panel-body">
              <center><span class="text-bold text-blue">Media Info Output</span></center>
              <br>
              @if($general !== null && isset($general['file_name']))
                <span class="text-bold text-blue">FILE:</span>
                <span class="text-bold"><em>{{ $general['file_name'] }}</em></span>
                <br>
                <br>
              @endif
              @if($general_crumbs !== null)
                <span class="text-bold text-blue">GENERAL:</span>
                <span class="text-bold"><em>
                    @foreach($general_crumbs as $crumb)
                      {{ $crumb }}
                      @if(!$loop->last)
                        /
                      @endif
                    @endforeach
                  </em></span>
                <br>
                <br>
              @endif
              @if($video_crumbs !== null)
                @foreach($video_crumbs as $key => $v)
                  <span class="text-bold text-blue">VIDEO:</span>
                  <span class="text-bold"><em>
                      @foreach($v as $crumb)
                        {{ $crumb }}
                        @if(!$loop->last)
                          /
                        @endif
                      @endforeach
                    </em></span>
                  <br>
                  <br>
                @endforeach
              @endif
              @if($audio_crumbs !== null)
                @foreach($audio_crumbs as $key => $a)
                <span class="text-bold text-blue">AUDIO {{ ++$key }}:</span>
                <span class="text-bold"><em>
                    @foreach($a as $crumb)
                      {{ $crumb }}
                      @if(!$loop->last)
                        /
                      @endif
                    @endforeach
                  </em></span>
                <br>
                @endforeach
              @endif
              <br>
              @if($text_crumbs !== null)
                @foreach($text_crumbs as $key => $s)
                <span class="text-bold text-blue">SUBTITLE {{ ++$key }}:</span>
                <span class="text-bold"><em>
                    @foreach($s as $crumb)
                        {{ $crumb }}
                        @if(!$loop->last)
                          /
                      @endif
                    @endforeach
                  </em></span>
                <br>
                @endforeach
              @endif
              @if($settings)
              <br>
              <span class="text-bold text-blue">ENCODE SETTINGS:</span>
              <br>
              <div class="decoda-code text-black">{{ $settings }}</div>
              @endif
              <br>
              <br>
              <center>
              <button class="show_hide btn btn-primary" href="#">
                Show/Hide Original Dump</button>
              </center>
              <div class="slidingDiv">
                <pre class="decoda-code"><code>{{ $movie->mediainfo }}</code></pre>
            </div>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</div>
@else
  <h4>Não lido</h4>
@endif
@endsection

@section('javascripts')
<script>
$(document).ready(function(){

$(".slidingDiv").hide();
$(".show_hide").show();

$('.show_hide').click(function(){
$(".slidingDiv").slideToggle();
});

});
</script>
@endsection