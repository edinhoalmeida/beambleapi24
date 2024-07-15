<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\API\BaseController as BaseController;

   
class AudioController extends BaseController
{

    function __construct()
    {
        // $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','show']]);
        // $this->middleware('permission:user-create', ['only' => ['create','store']]);
        // $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        // $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }
    public function audio(Request $request, $audio_name)
    {
        // $userid = auth()->user()->id;

        // TODO: testar privacidade do audio
        $audioResource = storage_path('storage_tmp/' . $audio_name);

        return response()->download($audioResource, $audio_name);

        // sbrm5XpRZfP0Jc.mp3

    }
}
