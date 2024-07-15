<?php

$api = include __DIR__ . '/apiurl.php';

return [
[
'title'=>'Rotorna as mensagens entre dois usuários',
'url'=>"GET $api/api/inbox/{id_usuario_atual}/{id_usuario_beamer}",
'enviar_txt'=>'
ROTA REQUER BAERER TOKEN PARA RETORNAR
SÓ RETORNA ALGO SE A COVERSA PERTENCER AO USUÀRIO LOGADO
',
'retorno'=>'{
    "success": true,
    "data": {
        "messages": [
            {
                "id": 1,
                "status": "new",
                "user_id": 19,
                "is_me": "yes",
                "message": "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip",
                "created_at": "2023-01-26T20:43:55.000000Z"
            },
            {
                "id": 2,
                "status": "new",
                "user_id": 32,
                "is_me": "no",
                "message": "Tree bian",
                "created_at": "2023-01-26T20:45:15.000000Z"
            }
        ]
    },
    "message": "inbox return ok"
}'
]
];