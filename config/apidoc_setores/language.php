<?php

$api = include __DIR__ . '/apiurl.php';

return [
[
'title'=>'Rotorna a tradução de um texto<BR>api associada a uma chamada em andamento',
'url'=>"POST $api/api/chat/text2text",
'enviar'=>['from_text:string','from_lang:string (padrão é auto)','target_lang:string (fr,es,it,en,br...)', 'call_id:int (id beamble) or string (padrão do videosdk)'],
'retorno'=>'

Se from from_lang e target_lang forem iguais não será feita tradução, somente salvamento do texto.
{
    "success": true,
    "data": {
        "videocall_id": "1",
        "user_id": 19,
        "from_lang": "pt",
        "target_lang": "pt",
        "from_text": "vem sentando gostozinho pro pai.",
        "target_text": "vem sentando gostozinho pro pai.",
        "status": "new",
        "service_version": "None"
    },
    "message": "Texto traduzido"
},

Idiomas diferentes

{
    "success": true,
    "data": {
        "videocall_id": "1",
        "user_id": 19,
        "from_lang": "pt",
        "target_lang": "fr",
        "from_text": "vem sentando gostozinho pro pai.",
        "target_text": "vient s&#39;asseoir comme pour papa.",
        "status": "new",
        "service_version": "GoogleV3"
    },
    "message": "Texto traduzido"
}

OU
 
{
    "success": true,
    "data": {
        "videocall_id": "1",
        "user_id": 19,
        "from_lang": "pt",
        "target_lang": "fr",
        "from_text": "vem sentando gostozinho pro pai.",
        "target_text": "Viens t\'asseoir gentiment pour papa.",
        "status": "new",
        "service_version": "OpenAIV1"
    },
    "message": "Texto traduzido"
}'
],
[
'title'=>'Rotorna um arquivo de audio a partir de um texto<BR>api associada a uma chamada em andamento',
'url'=>"POST $api/api/chat/text2speech",
'enviar'=>['from_text:string','from_lang:string', 'call_id:int (id beamble) or string (padrão do videosdk)'],
'retorno'=>'
{
    "success": true,
    "data": {
        "videocall_id": 10,
        "user_id": 19,
        "from_text": "api qui génère l\'audio en fonction du texte",
        "from_lang": "fr",
        "target_lang": null,
        "target_text": null,
        "status": "new",
        "file_name": "wVGp7jRR0gDFSP.mp3",
        "service_version": "GoogleTextToSpeechV1",
        "file_url_to_download": "https://api.beamble.com/api/chat/getaudio/wVGp7jRR0gDFSP.mp3"
    },
    "message": "Texto para audio gerado"
}'
],
[
'title'=>'Rotorna o texto transcrito a partir de audio<BR>api associada a uma chamada em andamento',
'url'=>"POST $api/api/chat/speech2text",
'enviar'=>['from_lang:string', 'call_id:int (id beamble) or string (padrão do videosdk)', 'audio_file:binary (mp3,mp4,mpeg,mpga,m4a,wav,webm) até 10MBytes'],
'retorno'=>'
{
    "success": true,
    "data": {
        "videocall_id": "1",
        "user_id": 19,
        "from_text": "Não é por estar na sua presença, meu presa do rapaz, Mas você vai mal, vai mal demais. São três horas, o samba tá quente, Deixa a morena contente, deixa a menina sambar em paz. Eu não queria jogar confetes, mas tenho que dizer, Cê tá de lascar, cê tá de doer. E se vai continuar angustido com essa cara de marido, É... tá bom.",
        "from_lang": "pt-BR",
        "target_lang": null,
        "target_text": null,
        "status": "new",
        "file_name": "oH6hYnNuHlI94I.mp3",
        "service_version": "OpenAIV1:Whisper-1",
        "file_url_to_download": "https://apibb.beamble.com/api/chat/getaudio/oH6hYnNuHlI94I.mp3"
    },
    "message": "Audio para Texto gerado"
}

OBSERVAÇÕES:

COMO não há tradução o texto transcrito fica como from_text.

Open ai - Free trial users permite 3 requisições por minuto para conversão de audio

Google Speech - tem limite de 10MBytes e 1 minuto. Para tempos maiores é preciso subir o arquivo num serviço de arquivos na novem do google.
Google Speech -  não funcionou sem passar o samplerate, númerio de canais. Só está funcionando para FLAC

'
]
];