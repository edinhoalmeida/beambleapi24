<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\ImageB64;
use App\Service\ImageProcessorInterface;

class ProcessImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;

    protected $imagedb;

    public function __construct(ImageB64 $imagedb)
    {
        $this->imagedb = $imagedb;
    }

    public function handle(ImageProcessorInterface $service)
    {
        dblog('image process image:', $this->imagedb->id);
        $service->convertImage($this->imagedb);
    }
}
