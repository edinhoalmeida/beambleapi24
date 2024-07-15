<?php
namespace App\Service;

use App\Models\ImageB64;

interface ImageProcessorInterface
{
    public function convertImage(ImageB64 $imagedb);

    public static function urlTest();
}

