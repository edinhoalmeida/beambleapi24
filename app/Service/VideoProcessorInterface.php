<?php 
namespace App\Service;

use App\Models\UserVideofeed;

interface VideoProcessorInterface
{
    public function converVideo(UserVideofeed $uservideofeed);

    public static function urlTest();
}

