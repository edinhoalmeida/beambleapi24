<!-- address -->
<div class="complete_address_gmaps_fields" data-address_type='{{$address_type}}'><!-- complete_address -->
<div class="form-group">
    <x-input-label for="{{$address_type}}_address" :value="__('Address')" />
    <x-text-input id="{{$address_type}}_address" class="form-control gmaps_autocomplete"
                    type="text"
                    name="address"
                    required />
    <x-input-error :messages="$errors->get('{{$address_type}}_address')" class="mt-2" />
</div>
<div class="form-group">
    <x-input-label for="{{$address_type}}_street2" :value="__('Apartment, unit, suite, or floor')" />
    <x-text-input id="{{$address_type}}_street2" class="form-control"
                    type="text"
                    name="{{$address_type}}_street2" />
    <x-input-error :messages="$errors->get('{{$address_type}}_street2')" class="mt-2" />
</div>


<div class="form-group">
    Lat:<input id="{{$address_type}}_lat" type="text" name="{{$address_type}}_lat" /><br>
    Lng:<input id="{{$address_type}}_lng" type="text" name="{{$address_type}}_lng" /><br>
    Country:<input id="{{$address_type}}_country" type="text" name="{{$address_type}}_country" /><br>
    Country_code:<input id="{{$address_type}}_country_code" type="text" name="{{$address_type}}_country_code" /><br>
    City:<input id="{{$address_type}}_city" type="text" name="{{$address_type}}_city" /><br>
    Street:<input id="{{$address_type}}_street" type="text" name="{{$address_type}}_street" /><br>
    street_number :<input id="{{$address_type}}_street_number" type="text" name="{{$address_type}}_street_number" /><br>
    Postal code:<input id="{{$address_type}}_postal_code" type="text" name="{{$address_type}}_postal_code" /><br>
    Others:<input id="{{$address_type}}_others" type="text" name="{{$address_type}}_others" /><br>
    Others key:<input id="{{$address_type}}_others_key" type="text" name="{{$address_type}}_others_key" /><br>
</div>

</div><!-- complete_address -->


@if($show_after_scripts)

@push('after-scripts')
<?php $conf_maps = config('maps'); ?>
<script type="text/javascript">

function initMap() {
    
    var rec_html = $(".complete_address_gmaps_fields");

    console.log(rec_html);
    // campo autocomplete
    const center = { lat: 48.86, lng: 2.30 };
    const defaultBounds = {
      north: center.lat + 30,
      south: center.lat - 20,
      east: center.lng + 30,
      west: center.lng - 30,
    };

    var total = rec_html.length;

    const options = {
        bounds: defaultBounds,
        // componentRestrictions: { country: "us" },
        fields: ["address_components", "geometry", "icon", "name"],
        origin: center,
        strictBounds: false
        // types: ["establishment"],
    };
    

    var input = [], rec = [], chave = [], autocomplete = [];

    input[0] = rec_html.find('.gmaps_autocomplete').get(0);
    rec[0] = rec_html.eq(0);
    chave[0] = rec_html.eq(0).attr('data-address_type');
    autocomplete[0] = new google.maps.places.Autocomplete(input[0], options);
    autocomplete[0].addListener("place_changed", () => {
 
        const place = autocomplete[0].getPlace();
        console.log(place);

        $('#' + chave[0] + '_lat').val(place.geometry['location'].lat());
        $('#' + chave[0] + '_lng').val(place.geometry['location'].lng());

        const send_address_components = {
            address_components:JSON.stringify(place.address_components)
        };

        $.ajax({
          type: "POST",
          url: '/api/worldaddress',
          data: send_address_components,
          success: function(data){
            if(data.success){
                Object.entries(data.data.fmt_address).forEach(entry => {
                    const [key, value] = entry;
                    console.log(key, value);
                    $('#' + chave[0] + '_' + key).val(value);
                });
            };
          },
          dataType: 'json'
        });

        // place.address_components.forEach(function(linha, i) {
        //     if(linha.types.includes('country')){
        //         $('#beaming_country').val(linha.long_name);
        //     }
        //     if(linha.types.includes('locality')){
        //         $('#beaming_locality').val(linha.long_name);
        //     }
        //     if(linha.types.includes('postal_code')){
        //         $('#beaming_postal_code').val(linha.long_name);
        //     }
        //     if(linha.types.includes('street_number')){
        //         $('#beaming_street_number').val(linha.long_name);
        //     } 
        // });

        return;
        if (!place.geometry || !place.geometry.location) {
            // User entered the name of a Place that was not suggested and
            // pressed the Enter key, or the Place Details request failed.
            // window.alert("Endereço não encontrado: '" + place.name + "'");
            return;
        }
    });

}
</script>
<script id="gm-ap" data-apik="<?php echo $conf_maps['key'];?>" async defer src="https://maps.googleapis.com/maps/api/js?<?php echo http_build_query($conf_maps); ?>"></script>
@endpush

@endif