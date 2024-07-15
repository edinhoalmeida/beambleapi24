<?php

namespace App\Http\Resources;

class ImagePh
{
    public static function user()
    {
        $dados = [
            'id' => 'null',
            'title' => 'placeholder',
            'url_full' => asset('assets/placeholders/users-placeholder.png'),
            'url_thumbnail' => asset('assets/placeholders/users-placeholder.png')
        ];
        return $dados;
    }
    public static function company()
    {
        $dados = [
            'id' => 'null',
            'title' => 'placeholder',
            'url_full' => asset('assets/placeholders/empresas-placeholder.png'),
            'url_thumbnail' => asset('assets/placeholders/empresas-placeholder.png')
        ];
        return $dados;
    }
}
