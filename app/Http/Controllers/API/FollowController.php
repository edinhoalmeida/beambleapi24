<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\API\BaseController as BaseController;

use App\Models\UserFollowers;
use App\Models\User;

use App\Http\Resources\Follow as FollowResource;
   
class FollowController extends BaseController
{

    function __construct()
    {
        // $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','show']]);
        // $this->middleware('permission:user-create', ['only' => ['create','store']]);
        // $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        // $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

/**
    * @OA\Get(
    *     path="/follow/{beamer_id}",
    *     tags={"beamer"},
    *     @OA\Parameter(
    *         name="beamer_id",
    *         description="A beamer id",
    *         in="path",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),
    *     @OA\Response(
    *          response="200",
    *          description="Get follow status of a user",
    *          @OA\JsonContent(
    *             oneOf={
    *                  @OA\Schema(type="follow"),
    *                  @OA\Schema(type="autherror"),
    *             },
    *               @OA\Examples(example="following", 
    *                   value={"total"=21, "this_user_follow"=1}, 
    *                   summary="An following return."),
    *               @OA\Examples(example="unfollowing", 
    *                   value={"total"=20, "this_user_follow"=0},
    *                   summary="An unfollowing return."),
    *               @OA\Examples(example="auth error", 
    *                   value={"success": false,"message": "Auth error. Header Baerer is required."},
    *                   summary="An auth error"),
    *          ),
    *     )
    * )
    */
    public function beamer($beamer_id)
    {
        $follow_obj  = new \stdClass();
        $follower_id = auth()->user()->id;
        if( ! empty($follower_id) ){
            $has = UserFollowers::where('user_id', $beamer_id)->where('follower_id', $follower_id)->first();
            if($has){
                $has->delete();
            } else {
                $input = ['user_id'=>$beamer_id, 'follower_id'=>$follower_id];
                UserFollowers::create($input);
                $follow_obj->this_user_follow = 1;
            }
        }
        $user = User::find($beamer_id);
        $follow_obj->total = count($user->followers);

        $ar_return = new FollowResource($follow_obj);
        return response()->json($ar_return, 200);
    }
}
