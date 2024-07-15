<?php

namespace App\Models\Robots;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/*
id  int(10) unsigned Auto Increment 
videofeed_id    int(10) unsigned NULL   
attempts    int(4) unsigned NULL [0]    
file    varchar(255)    
status  varchar(255)    
service_return  text NULL   
file_url    text NULL   
file_path   text NULL   
url_video   text NULL   
url_thumb   text NULL   
service_id  varchar(255) NULL   
created_at  datetime NULL

*/

class VideosJob extends Model
{

    protected $table = 'videos_job';

    public static function get_urls_bytescale($original){
        $exists = VideosJob::where('file', $original)->where('status', 'succeeded')->first();
        if($exists){
            return [
                'feed_url'=>$exists->url_video,
                'thumb_url'=>$exists->url_thumb
            ];
        }
        return false;
    }

}
