<?php

declare(strict_types=1);

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Image extends Facade
{
    /**
     * @method \Intervention\Image\ImageManager read(mixed $input, string|array|DecoderInterface $decoders = [])
     * @method \Intervention\Image\ImageManager create(int $width, int $height)
     * @method \Intervention\Image\ImageManager animate(callable $callback)
     */
    protected static function getFacadeAccessor()
    {
        return 'image';
    }
}