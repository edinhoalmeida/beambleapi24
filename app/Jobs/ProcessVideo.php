<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\UserVideofeed;
use App\Service\VideoProcessorInterface;

class ProcessVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;

    protected $videofeed;

    public function __construct(UserVideofeed $uservideofeed)
    {
        $this->videofeed = $uservideofeed;
    }

    public function handle(VideoProcessorInterface $videoservice)
    {
        dblog('video process video', $this->videofeed->id);
        $videoservice->converVideo($this->videofeed);
    }
}
