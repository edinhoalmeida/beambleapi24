<!--
 API Documentation HTML Template  - 1.0.1
 Copyright © 2016 Florian Nicolas
 Licensed under the MIT license.
 https://github.com/ticlekiwi/API-Documentation-HTML-Template
 !-->
<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <title>API - Documentation</title>
    <meta name="description" content="">
    <meta name="author" content="ticlekiwi">

    <meta http-equiv="cleartype" content="on">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('apidoc-assets/css/hightlightjs-dark.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.8.0/highlight.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,500|Source+Code+Pro:300" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('apidoc-assets/css/style.css') }}" media="all">
    <script>hljs.initHighlightingOnLoad();</script>
</head>

<body >
<div class="left-menu">
    <div class="content-logo">
        <img alt="" title="" src="{{ asset('apidoc-assets/images/logo.png') }}" height="32" />
        <span>Beamble API</span>
    </div>
    <div class="content-menu">
        <ul>
            <li class="scroll-to-link active" data-target="get-intro">
                <a>Introdução</a>
            </li>
            <li class="scroll-to-link" data-target="get-login">
                <a>Autenticação</a>
            </li>

            <?php
            $i=0;
            foreach($setores as $chav => &$dados){
                $dados['htmlid'] = $chav . $i++;
                $mark = !empty($dados['new'])? '<mark>n</mark>' : '' ;
                echo '<li class="scroll-to-link" data-target="get-'.$dados['htmlid'].'">
                <a>'.$mark.$dados['titulo'].'</a>
                </li>';
            }

            ?>


            <li class="scroll-to-link" data-target="errors">
                <a>Erros</a>
            </li>
        </ul>
    </div>
</div>
<div class="content-page">
    <div class="content">
        <div class="overflow-hidden content-section" id="content-get-intro">
            <h1 id="get-intro">Introdução</h1>
            <p>
                Documentação das rotas implementadas na API.
            </p>
            <pre>
API Endpoint

{{ $api }}
            </pre>
            <p>
                Headers:<br>
                <strong>Authorization</strong> é obrigatórios para rotas protegidas.
            </p>
             <pre>
'Content-Type': 'application/json',
'Authorization': 'Bearer '+'______TOKEN______'
            </pre>

        </div>
        <div class="overflow-hidden content-section" id="content-get-login">
            <h2 id="get-login">Autenticação</h2>
            <pre><code class="bash">
curl \
-X POST {{ $api }}/api/login \
-d '{"email":"email@asda.com","password":"my_password","interface_as":"beamer|client"}'
-H "Content-Type: application/json" \
-H "Accept: application/json"
                </code></pre>
            <p>
                Para obter o token é preciso fazer uma requisição de login:<br>
                <code class="higlighted"><span class="method method-POST">POST</span> {{ $api }}/api/login</code>
            </p>
            <br>

            <h4>Parâmetros necessários no BODY</h4>
            <table>
                <thead>
                <tr>
                    <th>Campo</th>
                    <th>Tipo</th>
                    <th>Obs</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>email</td>
                    <td>String</td>
                    <td></td>
                </tr>
                <tr>
                    <td>password</td>
                    <td>String</td>
                    <td></td>
                </tr>
                <tr>
                    <td>interface_as</td>
                    <td>String</td>
                    <td>não obrigatório<br><i>beamer</i> ou<br><i>client</i> (default)</td>
                </tr>
                </tbody>
            </table>

            <pre><code class="json">
Resultado exemplo:

{
    "success": true,
    "data": {
        "token": "73|tNMBNu1w6xXX94cSZmEKWK9DHMYHJAiSaTP4jVLx",
        "name": "edinho",
        "permissions": [
            "utype-client"
        ],
        "interface_as": "beamer",
        "user": {
            "id": 19,
            "name": "edinho",
            "email": "edinhoclient@gmail.com",
            "interface_as": "beamer",
            "position": null,
            "birthday": null,
            "minibio": null,
            "pref_lang": "fr",
            "image": "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQABLAEsAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDA.........sdafasdfasdfa",
            "role_id": 5
        }
    },
    "message": "Login efetuado com sucesso!"
}

                </code></pre>
        </div>
        <div class="overflow-hidden content-section" id="content-get-login1">
            <h2 id="get-login1">Autenticação - retorno</h2>
            <pre><code class="json">
Em caso de erro na autenticação:
{
    "success": false,
    "message": "Usuário/senha não autorizado.",
    "data": {
        "error": "Unauthorised"
    }
}
Em caso de faltar algum campo na requisição:
{
    "success": false,
    "message": "Erro na validação",
    "data": {
        "email": [
            "O campo email é obrigatório."
        ]
    }
}
            </code></pre>

            <p>
            As chaves no json de retorno
            </p>

            <table>
                <tr>
                    <td>success</td>
                    <td>false ou true</td>
                </tr>
                <tr>
                    <td>message</td>
                    <td>mensagem informativa do processamento</td>
                </tr>
                <tr>
                    <td>interface_as</td>
                    <td>para ser usado na interface, recebe <i>beamer</i> ou <i>client</i></td>
                </tr>
                <tr>
                    <td>data</td>
                    <td>pode ser os dados requisitados, mas também pode ser os erros campo por campo</td>
                </tr>
                <tr>
                    <td>data -> token</td>
                    <td>importante para se incorporar nas requisições futuras</td>
                </tr>
                <tr>
                    <td>data -> permissions</td>
                    <td>tipo de usuário</td>
                </tr>
            </table>
        </div>


        <?php
        $i=0;
        foreach($setores as $chav => &$dados){
                ?><div class="overflow-hidden content-section" id="content-get-{{ $dados['htmlid'] }}">
                    <h1 id="get-{{ $dados['htmlid'] }}">{{$dados['titulo']}}</h1>

                @foreach($dados['setores'] as $setorr)



                <p>
                    <h3>{!! $setorr['title'] !!}</h3>
                    <?php

                    $arr = ['GET','HEAD','POST','PUT','PATCH','DELETE'];

                    foreach($arr as $method){
                        $setorr['url'] = str_replace($method,'<span class="method method-'.$method.'">'.$method.'</span>',$setorr['url']);
                    }

                    ?>
                    <code class="higlighted">{!! $setorr['url'] !!}</code>
                </p>
                @if(!empty($setorr['enviar']))
                    <p>Dados a enviar</p>
                    <table>
                        @foreach($setorr['enviar'] as $linha)
                            <?php
                            $cols = explode(":", $linha);
                            ?>
                        <tr>
                            <td>{{ $cols[0] }}</td>
                            @if(strpos($cols[1], "\n"))
                                <td><pre>{{ $cols[1] }}</pre></td>
                            @else
                                <td>{{ $cols[1] }}</td>
                            @endif
                        </tr>
                        @endforeach
                    </table>
                @endif

                 @if(!empty($setorr['enviar_txt']))
                    <p>Dados a enviar</p>
                    <pre>
                        {{ $setorr['enviar_txt'] }}
                    </pre>
                @endif

                @if(!empty($setorr['obs']))
                   <p><strong><?php echo nl2br($setorr['obs']) ?></strong></p>
                @endif


                @if(!empty($setorr['retorno']))
                   <pre><code class="json">
{{ $setorr['retorno'] }}
                    </code></pre>
                @endif

                @endforeach

                </div>
                <?php
        }
        ?>




        <div class="overflow-hidden content-section" id="content-errors">
            <h2 id="errors">Erros</h2>
            <p>
                Erros globais
            </p>
            <pre><code>
{
    "success": false,
    "message": "Ação não permitida para este perfil."
}
            </code></pre>
        </div>




    </div>

</div>
<script src="{{ asset('apidoc-assets/js/script.js') }}"></script>
</body>
</html>
