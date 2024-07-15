<?php

$api = include __DIR__ . '/apiurl.php';

return [
[
'title'=>'Registro de usuários do tipo Client e Beamer:',
'url'=>"POST $api/api/register_all",
'enviar'=>['name:string','surname:string','image:base64_string','minibio:string (não obrigatório)','interface_as:string client ou beamer (não obrigatório, default client)','tos_accepted:int (0 ou 1 para registrarmos no banco o aceite dos termos de serviço)','email:email','password:string','confirm_password:string','languages:array
languages[0]=fr
languages[1]=en','keywords:array
keywords[0]=sports
keywords[1]=radical'],
'retorno'=>'{
    "success": true,
    "data": {
        "token": "69|INz5RacpeXR59beXGNQkBM5ejuddSuW7r35VfR9Y",
        "name": "edinho11",
        "user_id": 27
    },
    "message": "Usuário criado com sucesso!"
}

CASO HAJA ERRO o success retorna como false.

{
    "success": false,
    "message": "Validation errors",
    "data": {
        "email": [
            "Email already registered"
        ]
    }
}'
],
[
'title'=>'Registro de usuários Generic, Efetua o login automático',
'url'=>"POST $api/api/register_generic",
'enviar'=>['interface_as:string client ou beamer (não obrigatório, default client)'],
'retorno'=>'{
    "success": true,
    "data": {
        "name": "User Generic",
        "user_id": 46,
        "token": "145|ChzvGAFMuvbYblqKGIBajRBzbAiX2p7DEpwGGk17",
        "permissions": [
            "utype-client"
        ],
        "user": {
            "id": 46,
            "name": "User Generic",
            "surname": "Freemium",
            "email": "e64fb3ee759bcd@beamblefreemium.com",
            "interface_as": "client",
            "position": null,
            "birthday": null,
            "minibio": null,
            "keywords": [
                "freemium"
            ],
            "pref_lang": "en",
            "client_enabled": false,
            "beamer_enabled": false,
            "shopper_enabled": false,
            "freemium_enabled": true,
            "is_generic": true,
            "image": "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQABLAEsAAD/2wBDA.........d/5iX//Z",
            "role_id": 5,
            "uuid": "9999-9999-9999-9999-99999",
            "share_url": "https://apibb.beamble.com/api/share/9999-9999-9999-9999-99999"
        }
    },
    "message": "Usuário criado com sucesso!"
}'
],
// [
// 'title'=>'Registro de usuários do tipo Beamers:',
// 'url'=>"POST $api/api/register_beamer",
// 'enviar'=>['name:string','surname:string','gender:string (f ou m)','beaming_address:string (endereço de prestação do serviço)','contact_address:string','contact_city:string','contact_country:string','phone:string','doc_id:string','type:string (\'beamer\')','email:email','password:string','my_language:string'],
// 'retorno'=>'{
//     "success": true,
//     "data": {
//         "token": "69|INz5RacpeXR59beXGNQkBM5ejuddSuW7r35VfR9Y",
//         "name": "edinho11",
//         "user_id": 27
//     },
//     "message": "Usuário criado com sucesso!"
// }'
// ]
];