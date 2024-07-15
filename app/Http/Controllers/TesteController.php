<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Helpers\MediaInfo;

use Illuminate\Support\Facades\Artisan;

use Babel;

class TesteController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function index(){
        $exitCode = Artisan::call('teste:api', [
            'urlport' =>  env('APP_URL','127.0.0.1') . ':80'
        ]);
    }

    public function babel($text="bom dia, amigos!", $text_lang="auto", $target_lang="fr"){

        // $a = [1, 2, 3, 4, 5];
        // print_r(array_map(fn($value): int => $value * 2, range(1, 5)));
        return Babel::text_to_text($text,$target_lang,$text_lang);
    }

    public function babel_t2s($text="Good morning, friends!", $text_lang="en"){

        // $a = [1, 2, 3, 4, 5];
        // print_r(array_map(fn($value): int => $value * 2, range(1, 5)));
        return Babel::text_to_speech($text, $text_lang);
    }

    public function mediatest(){
               
        $general = null;
        $video = null;
        $settings = null;
        $audio = null;
        $general_crumbs = null;
        $text_crumbs = null;
        $subtitle = null;
        $view_crumbs = null;
        $video_crumbs = null;
        $settings = null;
        $audio_crumbs = null;
        $subtitle = null;
        $subtitle_crumbs = null;

        $movie = new \stdClass();
        $movie->audio = storage_path('storage_tmp/leo.mp3');

        if ($movie->mediainfo != null) {
            $parser = new MediaInfo();
            $parsed = $parser->parse($movie->mediainfo);
            $view_crumbs = $parser->prepareViewCrumbs($parsed);
            $general = $parsed['general'];
            $general_crumbs = $view_crumbs['general'];
            $video = $parsed['video'];
            $video_crumbs = $view_crumbs['video'];
            $settings = ($parsed['video'] !== null && isset($parsed['video'][0]) && isset($parsed['video'][0]['encoding_settings'])) ? $parsed['video'][0]['encoding_settings'] : null;
            $audio = $parsed['audio'];
            $audio_crumbs = $view_crumbs['audio'];
            $subtitle = $parsed['text'];
            $text_crumbs = $view_crumbs['text'];
        }

        return view('web.mediainfo', compact('general','video','settings','audio','general_crumbs','text_crumbs','subtitle','view_crumbs','video_crumbs','settings','audio_crumbs','subtitle','subtitle_crumbs','movie'));
    }
}
