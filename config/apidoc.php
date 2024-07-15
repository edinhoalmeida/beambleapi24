<?php

//$cadastro = require __DIR__ . '/apidoc_setores/apidocregister.php';
$cadastro2 = require __DIR__ . '/apidoc_setores/apidocregister2.php';
$apimaps = require __DIR__ . '/apidoc_setores/apimaps.php';
$settings = require __DIR__ . '/apidoc_setores/apisettings.php';
$usuarios = require __DIR__ . '/apidoc_setores/apidocusers.php';

$inbox = require __DIR__ . '/apidoc_setores/inbox.php';
$language = require __DIR__ . '/apidoc_setores/language.php';
$webview = require __DIR__ . '/apidoc_setores/webview.php';

return [
// 'cadastro' => [
//     'titulo'=>'Cadastro',
//     'setores'=> $cadastro
//     ],
'settings' => [
    'titulo'=>'Settings',
    'setores'=> $settings
    ],
'cadastro2' => [
    'titulo'=>'Registro de usuários',
    'setores'=> $cadastro2,
    'new'=> false
    ],
'maps' => [
    'titulo'=>'Maps',
    'setores'=> $apimaps,
    'new'=> false
    ],
// 'inbox' => [
//     'titulo'=>'Inbox',
//     'setores'=> $inbox
//     ],
'users' => [
    'titulo'=>'Usuários',
    'setores'=> $usuarios,
    'new'=> false
    ],
'language' => [
    'titulo'=>'Idiomas',
    'setores'=> $language
    ],
'webview' => [
    'titulo'=>'Webview',
    'setores'=> $webview,
    'new'=> true
    ],
];
