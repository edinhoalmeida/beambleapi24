<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\API\BaseController as BaseController;

use App\Models\UserVideofeed;

   
class VideoController extends BaseController
{

    function __construct()
    {
        // $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','show']]);
        // $this->middleware('permission:user-create', ['only' => ['create','store']]);
        // $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        // $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }
    public function video(Request $request, $video_name)
    {
        // $userid = auth()->user()->id;

        if(is_numeric($video_name)){
            $video = UserVideofeed::find($video_name);
            if(empty($video->original)){
                return "error";
            }
            $video_name = empty($video->converted) ? $video->original : $video->converted;
        }
        // TODO: testar privacidade do audio
        $videoResource = storage_path('storage_tmp/' . $video_name);

        return response()->download($videoResource, $video_name);

        // sbrm5XpRZfP0Jc.mp3

    }
    public function thumb(Request $request, $thumb_name)
    {
        // $userid = auth()->user()->id;
        if(is_numeric($thumb_name)){
            $video = UserVideofeed::find($thumb_name);
            if(empty($video->original)){
                return "error";
            }
            $thumb_name = empty($video->thumb) ? null : $video->thumb;
        }

        if(empty($thumb_name)){
            return 'error';
        }
        $thumbResource = storage_path('storage_tmp/' . $thumb_name);
        return response()->download($thumbResource, $thumb_name);
        // sbrm5XpRZfP0Jc.mp3

    }
}
