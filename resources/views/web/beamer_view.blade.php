@extends('web.layout-simple')


@section('content')

    <div class="row">
        <div class="col-2"><a href="#" class="back"> <i class="bi bi-x-square-fill icon_m"></i></a></div>
        <div class="col-8 text-center">Beamer Classic</div>
        <div class="col-2 text-right"><i class="bi bi-bookmark icon_m"></i></div>
    </div>

    <div class="row">
        <div class="col-2"><img src="https://via.placeholder.com/50"></div>
        <div class="col-10">
            {{$beamer_user->surname}}, {{$beamer_user->name}}<br>
            {{$beamer_user->my_language}} <br>
            Disponible <br>
            superbeamer
        </div>

    </div>
    <div class="row">
        <div class="col-12 text-center">
            <a href="/beamerinbox/{{$beamer_user->id}}" class="btn btn-secondary">send message</a>
        </div>
    </div>
    <div class="row">
        <div class="col-8">
            <span >Prix</span> <i class="bi bi-info-circle-fill" data-toggle="tooltip" data-placement="top" title="Tooltip on top"></i>
        </div>
        <div class="col-4 text-right">
            10 €/min
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <strong>{{$address->street}} - {{$address->city}} - {{$address->country}}</strong>
        </div>
    </div>


    <a class="toggle_map links_cancel_ul" href="#"><strong>Voir sur la carte</strong></a>
    <div id="mapRec">
        <div id="mapGig">
        
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-12 text-center">
            <a class="links_cancel_ul" href="#">Signater cette annonce</a>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-12 text-center">
            <button type="submit" class="btn btn-secondary" disabled="disabled">schredule a video call</button>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-12 text-center">
            <button type="submit" class="btn btn-secondary" disabled="disabled">start a videocall now</button>
        </div>
    </div>
    <br>
    <br>
    <br>
    <br>


@endsection


@push('after-scripts')

<script src="{{ asset('assets/web/js/tendence.js') }}"></script>


<?php $conf_maps = config('maps'); ?>
<script type="text/javascript">

$('[data-toggle="tooltip"]').tooltip()
$('.back').on('click', function(e){
    backurl();
    e.preventDefault();
});

var map;
var mapa_carregado = false;
let marker2 = null;
var resultados;
function drawMark(location) {
    var location = location || false;
    if(marker2==null){
        marker2 = new google.maps.Marker({
        map,
            anchorPoint: new google.maps.Point(0, -29),
        });
    } 
    if(location !== false){
        marker2.setPosition(location)
        map.setCenter(location);
    }
}
var get_view_coords = function(){
    return {
        lat0: map.getBounds().getNorthEast().lat(),
        lng0: map.getBounds().getNorthEast().lng(),
        lat1: map.getBounds().getSouthWest().lat(),
        lng1: map.getBounds().getSouthWest().lng()
    };
};
var draw_pins2 = function(dados, draw_lines){
    for(var i in dados){
        var infowindow = new google.maps.InfoWindow();
        var local = dados[i];
        var zindex = 5;
        var myLatLng = {lat: parseFloat(local.lat), lng:parseFloat(local.lng)};
        var marker = new google.maps.Marker({
          map: map,
          position: myLatLng,
          title: local.name + ' ' + local.surname,
          cidade: local.city + ' - ' + local.country,
          zIndex: zindex
        });
        marker.setPosition(myLatLng);
        google.maps.event.addListener(marker, 'click', function() {
          infowindow.setContent('<div><strong>' + this.title + '</strong><br>' +
            this.cidade + '<br></div>');
          infowindow.open(map, this);
        });
    }
}
var buscando_timer = false;
var draw_pins = function(){
    if(buscando_timer) return;
    var cords = get_view_coords();
    $.ajax({
        method: 'POST',
        dataType: "json",
        url: '/api/search_beamer',
        data: {cords: cords},
        success: function( data ) {
            draw_pins2(data.data.pins);
            tendence.display(data.data.tendences);
        }
    });
    buscando_timer = true;
    setTimeout(function(){
        buscando_timer = false;
    },1000);
};

function initMap() {

    bounds = new google.maps.LatLngBounds();

    if (!google.maps.Polygon.prototype.getBounds) {
        google.maps.Polygon.prototype.getBounds = function () {
            var bounds = new google.maps.LatLngBounds();
            this.getPath().forEach(function (element, index) { bounds.extend(element); });
            return bounds;
        }
    }

    let map_ele = document.getElementById('mapGig');

    var zoom_padrao = {{$address->zoom_default}};

    var centro = {lat: {{$address->lat}}, lng: {{$address->lng}}}; 

    map = new google.maps.Map(map_ele, {
      zoom: zoom_padrao,
      center: centro,
      streetViewControl: false,
      mapTypeControl: false
    });
    var centg = new google.maps.LatLng(-23.57019728104872,-46.65720820426941);
    map.addListener("center_changed", () => {
        draw_pins();
    });
    map.addListener("zoom_changed", () => {
        draw_pins();
    });
    

    var input = [], autocomplete = [];

    // campo autocomplete
    const center = { lat: 48.86, lng: 2.30 };
    const defaultBounds = {
      north: center.lat + 30,
      south: center.lat - 20,
      east: center.lng + 30,
      west: center.lng - 30,
    };

    const options = {
        bounds: defaultBounds,
        // componentRestrictions: { country: "us" },
        fields: ["address_components", "geometry", "icon", "name"],
        origin: center,
        strictBounds: false
        // types: ["establishment"],
    };

    input[0] = $("#search_id").get(0);
    autocomplete[0] = new google.maps.places.Autocomplete(input[0], options);
    autocomplete[0].addListener("place_changed", () => {
        const place = autocomplete[0].getPlace();
        map.setZoom(zoom_padrao);
        map.setCenter(place.geometry.location);
        drawMark(place.geometry.location);

        draw_pins();

    });

    draw_pins2([JSON.parse('@php echo json_encode($pin) @endphp')]);

    $("#mapRec").slideToggle();
}
$('.toggle_map').on('click', function(e){
    $("#mapRec").slideToggle();
    e.preventDefault();
});

</script>
<script id="gm-ap" data-apik="<?php echo $conf_maps['key'];?>" async defer src="https://maps.googleapis.com/maps/api/js?<?php echo http_build_query($conf_maps); ?>"></script>
@endpush

