<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserVideofeed;

use App\Jobs\ProcessVideo;

class AllVideos extends Command
{

    protected $signature = 'allvideos';

    protected $description = 'Regenerate all videos and thumbs';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->newLine(2);
        $videos = UserVideofeed::all();
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
