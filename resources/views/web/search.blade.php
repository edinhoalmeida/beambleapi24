@extends('web.layout')


@section('content')

    <div class="starter-template starter-template-small text-to-left" style="padding: 0;">
    	<h1>Search</h1>
        <form method="POST" action="">
            <div class="form-group">
                <x-text-input id="search_id" class="form-control " type="text" name="search" :value="old('search')" autofocus />
                <x-input-error :messages="$errors->get('search')" class="mt-2" />
            </div>
        </form>
    </div>
    <div id="mapGig">
        
    </div>


    <hr>

    <script id="template-tendence" type="text/template">
            <div class="alert alert-info container" role="alert"    id="tendence-user_id{user_id}" data-user_id="{user_id}">
                <a href="/beamer/{user_id}" class="link2beam">
              <div class="row">
                <div class="col-12">{surname}, {name}</div>
                <div class="col-12">{beamer_type}</div>
                <div class="col-12">&nbsp;</div>
                <div class="col-9">{city} - {country}</div>
                <div class="col-3 text-right">{beamer_cost}</div>
              </div>
              </a>
            </div>
    </script>
    <div id="tendence_rec">
        
        
    </div>
        {{-- <div class="alert alert-info hide" role="alert">
          <div class="row">
            <div class="col-12">place holder</div>
            <div class="col-12">classic</div>
            <div class="col-12">&nbsp;</div>
            <div class="col-9">paris - france</div>
            <div class="col-3 text-right">10 €</div>
          </div>
        </div> --}}

@endsection


@push('after-scripts')
<!-- <script src="{{ asset('assets/web/js/tendence.js') }}"></script> -->


<?php $conf_maps = config('maps'); ?>
<script type="text/javascript">
var map;
var mapa_carregado = false;
let marker2 = null;
var pin_makers = [];
var pin_contents = [];
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
    // clean actuals
    pin_makers.forEach(function(markpin, i) {
        markpin.setMap(null);
    })
    pin_makers = [];
    pin_contents = [];
    for(var i in dados){
        var infowindow = new google.maps.InfoWindow();
        var local = dados[i];
        var zindex = 5;
        var myLatLng = {lat: parseFloat(local.lat), lng:parseFloat(local.lng)};
        var marker = new google.maps.Marker({
          map: map,
          position: myLatLng,
          title: local.city + ' - ' + local.country + ' (' + i + ')',
          zIndex: zindex
        });
        console.log(local.city + ' - ' + local.country + ' (' + i + ')');
        content = '<div><img src="'+local.image+'" width="100"><br>' + 
                '' + local.followers.total + ' followers</strong><br>' +
                '<strong>' + local.name + '</strong><br>' +
                '<strong>type: ' + local.company_type + '</strong><br>' +
                '<strong>rating: ' + local.rating + '</strong><br>' +
                '<strong>lang: ' + local.pref_lang + '</strong><br>' +
                '</div>';
                pin_contents[i] = content;   
        marker.setPosition(myLatLng);
        pin_makers.push(marker);
        google.maps.event.addListener(marker, 'click', function() {
            var regexp = /.*\(([0-9]+)\)/g;
            const array = [...this.title.matchAll(regexp)];
            var i = array.map(m => m[1]);
            var i = parseInt(i);
            infowindow.setContent( pin_contents[i] );
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
        // data: {cords: cords},
        success: function( data ) {
            draw_pins2(data.data.pins);
            // tendence.display(data.data.tendences);
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

    var zoom_padrao = 5;

    var centro = {lat: 48.8699836, lng: 2.3036852}; 

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
        // drawMark(place.geometry.location);

        draw_pins();

    });

}
</script>
<script id="gm-ap" data-apik="<?php echo $conf_maps['key'];?>" async defer src="https://maps.googleapis.com/maps/api/js?<?php echo http_build_query($conf_maps); ?>"></script>
@endpush

