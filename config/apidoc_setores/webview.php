<?php

$api = include __DIR__ . '/apiurl.php';

return [
[
'title'=>'Mensagens de contato',
'url'=>"POST $api/api/contact-save",
'enviar'=>['name:string','email:string','message:string'],
'retorno'=>'
em caso de sucesso:

{
    "success": true,
    "errors": [],
    "message": "OK"
}

em caso de erro na validação:

{
    "success": false,
    "errors": {
        "email": [
            "The email must be a valid email address."
        ]
    },
    "message": "ERROR"
}

'
]
];