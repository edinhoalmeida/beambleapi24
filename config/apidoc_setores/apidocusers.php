<?php

$api = include __DIR__ . '/apiurl.php';

return [
[
'title'=>'Regrava usuários',
'url'=>"PUT $api/api/users/{user_id}/update",
'enviar'=>['name:string', 'surname:string', 'email:email','image:base64_string', 'password:string (opcional)','email:email', 'password:string', 'interface_as:string client ou beamer (não obrigatório, default client)','languages:array
languages[0]=fr
languages[1]=en','keywords:array
keywords[0]=sports
keywords[1]=radical'],
'retorno'=>'{
"success": true,
    "data": {
        "user": {
            "id": 19,
            "name": "edinho",
            "surname": null,
            "email": "edinhoclient@gmail.com",
            "interface_as": "beamer",
            "position": null,
            "birthday": null,
            "minibio": null,
            "keywords": [
                "music",
                "guitar"
            ],
            "pref_lang": "fr",
            "client_enabled": false,
            "beamer_enabled": false,
            "shopper_enabled": false,
            "image": "data:image/jpeg;base64,/9j/4AAQSkZJ......",
            "role_id": 1,
            "uuid": "9999-9999-9999-9999-99999",
            "share_url": "https://apibb.beamble.com/api/share/9999-9999-9999-9999-99999"
        }
    },
    "message": "Usuário regravado com sucesso."
}'
],
[
'title'=>'Regrava interface ( troca de interface no dashboard )',
'url'=>"PUT $api/api/users/{user_id}/update",
'obs'=>"é a mesma rota de update, passsando somente o interface_as",
'enviar'=>['interface_as:string client ou beamer'],
'retorno'=>'{
"success": true,
    "data": {
        "user": {
            "id": 19,
            "name": "edinho",
            "surname": null,
            "email": "edinhoclient@gmail.com",
            "interface_as": "beamer",
            "position": null,
            "birthday": null,
            "minibio": null,
            "keywords": [
                "music",
                "guitar"
            ],
            "pref_lang": "fr",
            "client_enabled": false,
            "beamer_enabled": false,
            "shopper_enabled": false,
            "image": "data:image/jpeg;base64,/9j/4AAQSkZJ......",
            "role_id": 1,
            "uuid": "9999-9999-9999-9999-99999",
            "share_url": "https://apibb.beamble.com/api/share/9999-9999-9999-9999-99999"
        }
    },
    "message": "Usuário regravado com sucesso."
}'
],
[
'title'=>'Busca beamers por texto (em palavras-chave)',
'url'=>"POST $api/api/search_by_words",
'enviar'=>['beamer_type:string (Achats|classic|Evénements|Personallisé|Spirituel)','keyword:string (ex. volei)'],
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
],
[
'title'=>'Busca beamers para o Feed de videos',
'url'=>"POST $api/api/search_to_feed",
'enviar'=>['beamer_type:string (Achats|classic|Evénements|Personallisé|Spirituel) (opcional)'],
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
],
[
'title'=>'Compartilha experiência. Retorna um pin do beamer estando ou não online',
'url'=>"GET $api/api/user/getcard/{hash}",
'obs'=>"{hash} pode ser o uuid(ex: 999999-9999-9999-9999-99999) ou o id(ex: 19) do usuário
A Rota GET $api/api/share/{hash} se comporta de forma idêntica",
'enviar'=>[],
'retorno'=>'{
    "success": true,
    "data": {
        "pins": [
            {
                "id": 19,
                "name": "edinho",
                "surname": "",
                "online": 0,
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
            }
        ]
    }
}'
],
[
'title'=>'Excluir conta do usuário que está logado, IRREVERSÍVEL',
'url'=>"POST $api/api/users/{user_id}/eraseaccount",
'enviar'=>[],
'retorno'=>'{
    "success": true,
    "data": [],
    "message": ""
}'
]
];
