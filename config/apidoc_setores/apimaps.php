<?php

$api = include __DIR__ . '/apiurl.php';

return [
[
'title'=>'Rotorna os diferentes tipos de address_components para um padrão beamble no DB',
'url'=>"POST $api/api/worldaddress",
'enviar_txt'=>'
enviando o obj (place.address_components) que retorna no google maps

const send_address_components = {
    address_components:JSON.stringify(place.address_components)
};

$.ajax({
    type: "POST",
    url: \'/api/worldaddress\',
    data: send_address_components,
    success: function(data){

    },
    dataType: \'json\'
});',
'retorno'=>'{
    "success": true,
    "data": {
        "fmt_address": {
            "street": "Avenue des Champs-Élysées",
            "street2": "",
            "street_number": "25",
            "address": "",
            "city": "Paris",
            "postal_code": "75008",
            "others": "Île-de-France, Département de Paris",
            "others_key": "administrative_area_level_1, administrative_area_level_2",
            "country": "France",
            "country_code": "FR"
        }
    }
}'
],
[
'title'=>'Rotorna beamers dentro de um retangulo de coordenadas',
'url'=>"POST $api/api/search_beamer",
'enviar_txt'=>'
{ beamer_type:string (Achats|classic|Evénements|Personallisé|Spirituel) },
{ keyword:string (optional) },
{ cords: {lat0:x, lng0:y, lat1:z, lng1:n } }
================================lat0, lng0
|                                        |
|                                        |
|                                        |
|                                        |
|                                        |
|                                        |
|                                        |
|                                        |
lat1, lng1 ===============================

{ cords: {
        lat0: map.getBounds().getNorthEast().lat(),
        lng0: map.getBounds().getNorthEast().lng(),
        lat1: map.getBounds().getSouthWest().lat(),
        lng1: map.getBounds().getSouthWest().lng()
        }
};
',
'retorno'=>'{
    "success": true,
    "data": {
        "pins": [
            {
                "id": 19,
                "name": "edinho",
                "surname": "",
                "online": 1,
                "minibio": "",
                "pref_lang": "fr",
                "beamer_type": "classic",
                "with_donation": 0,
                "is_freemium": 0,
                "cost_per_minute": 1.88,
                "event_title": "Cercle de samba à voile",
                "keywords": [
                    "volei",
                    "beach",
                    "music",
                    "guitar"
                ],
                "image": "data:image/jpeg;base64,/9j/4AAQS...vcre9lY9yqZvsH80RaHVd/5iX//Z",
                "feed_url": "https://beamble.com/api/user/getvideo/3",
                "thumb_url": "https://beamble.com/api/user/getthumb/3",
                "lat": -23.57727623,
                "lng": -46.64764786,
                "uuid": "999999-9999-9999-9999-99999",
                "share_url": "https://api.beamble.com/api/share/999999-9999-9999-9999-99999",
                "followers": {
                    "total": 1,
                    "this_user_follow": 1
                }
            },
            {
                ...
            },
            {
                ...
            }
        ]
    }
}'
]

];
