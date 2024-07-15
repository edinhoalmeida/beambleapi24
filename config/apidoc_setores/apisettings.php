<?php

$api = include __DIR__ . '/apiurl.php';

return [
[
'title'=>'Retorna os tokens e api keys',
'url'=>"GET $api/api/settings",
'enviar'=>[],
'retorno'=>'{
    "success": true,
    "data": {
        "maps_api_key": "===============KEY================",
        "video_sdk_token": "===============TOKEN===========",
        "languages_list": [
            {
                "name": "Arabic",
                "code": "ar"
            },
            {
                "name": "Chinese",
                "code": "zh"
            },
            ...
            etc...
            ...
            {
                "name": "Ukrainian",
                "code": "uk"
            },
            {
                "name": "Vietnamese",
                "code": "vi"
            }
        ]
    },
    "messages": []
}'
]
];