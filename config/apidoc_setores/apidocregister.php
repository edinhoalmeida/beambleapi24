<?php

$api = include __DIR__ . '/apiurl.php';

return [
[
'title'=>'Cadastro de usuários (criando um usuário rápido para teste)',
'url'=>"POST $api/api/register",
'enviar'=>['name:string','email:email','password:string','password:string','c_password:string (confirmação de senha)','type:string (client ou beamer)'],
'retorno'=>'{
    "success": true,
    "data": {
        "token": "42|NTNE3nKP8PLzIhM2Swt5VLuMu0iZLRv7GTI6GYHu",
        "name": "edinho",
        "user_id": 18
    },
    "message": "Usuário criado com sucesso!"
}'
]
];
