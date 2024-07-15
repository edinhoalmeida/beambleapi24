<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\UserVideofeed;

use App\Jobs\ProcessVideo;
use App\Service\VideoProcessor;


class Video extends Command
{
    protected $signature = 'video {id_video_convertido}';

    protected $description = 'Save video ok';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        $id_video = $this->argument('id_video_convertido');
        if($id_video=='url'){
            $this->info('Test url');
            $this->newLine(1);
            $VIDEO_SERVICE = config('videoconvert.service');
            $base_url = VideoProcessor::urlTest('scripts',$VIDEO_SERVICE);
            $this->info($base_url);
            return;
        }

        $this->newLine(2);

        $this->info('Video id:' . $id_video);
        $this->newLine(1);


        $video_feed = UserVideofeed::withTrashed()->find($id_video);
        if(empty($video_feed->original)){
            $this->error('Video not found');
        } else {
            $this->info('Video file:' . $video_feed->original);
            ProcessVideo::dispatch($video_feed)->onQueue('videos');
        }

        $this->newLine(2);
        return 0;
    }
}
