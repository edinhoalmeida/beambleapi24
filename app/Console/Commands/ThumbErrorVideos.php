<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\UserVideofeed;

use App\Jobs\ProcessVideo;
use App\Service\VideoProcessor;


class ThumbErrorVideos extends Command
{

    protected $signature = 'thumberrorvideos';

    protected $description = 'Regenerate all videos empty thumb';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->newLine(2);
        $videos = UserVideofeed::whereNull('thumb')->where('id','=', 11556)->get();
        foreach ($videos as $video_feed) {
            $this->info('Video id:' . $video_feed->id);
            if(empty($video_feed->original)){
                $this->error('Video not found');
            } else {
                $this->info('Video file:' . $video_feed->original);
                ProcessVideo::dispatch($video_feed)->onQueue('videos');
            }
            $this->newLine(1);    
        }
        $this->newLine(2);
        return 0;
    }
}
