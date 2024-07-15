<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

use App\Http\Resources\History as HistoryResource;

use App\Libs\Beamer;
use Carbon\Carbon;

use DB;
   
class Call2Controller extends BaseController
{

    function __construct()
    {
        $this->middleware('user_type:isClient|isBeamer');
    }

/**
    * @OA\Get(
    *     path="/calls/history/all",
    *     description="Get a user history of calls.",
    *     tags={"videocall  🔒"},
   *     @OA\Response(
    *          response="200",
    *          description="Get beamer profile",
    *          @OA\JsonContent(
    *              @OA\Property(
    *                  property="success",
    *                  type="boolean",
    *                  description="Status of request"
    *              ),
    *              @OA\Property(
    *                  property="data",
    *                  type="object",
    *                  description="Request datas",
    *                  @OA\Property(
    *                      property="last_calls",
    *                      type="array",
    *                      @OA\Items(ref="#/components/schemas/history"),
    *                  ),
    *              ),
    *              @OA\Property(
    *                  property="message",
    *                  type="string",
    *                  description="Messages from api"
    *              ),
    *          ),
    *     ),
    * )
    */
    public function gethistory(Request $request, $filter = 'all')
    {
        $user = auth()->user();

        $calls = \DB::select('
            select 
                id
                , client_id
                , beamer_id
                , CASE 
                    WHEN beamer_id = ? THEN client_id
                    WHEN client_id = ? THEN beamer_id
                    ELSE 0 
                END AS user_id
                , DATE(updated_at) as updated_date
                , updated_at 
            from videocalls 
            where status = ?
            AND (
                client_id = ? OR beamer_id = ?
                ) 
            order by updated_at desc',
            [
                $user->id, $user->id,
                'accepted',
                $user->id, $user->id
            ]
            );

        $last_calls = [];

        // if empty
        if(count($calls)==0){
            $response = [
                'success' => true,
                'data'    => ['last_calls' => $last_calls]
            ];
            return response()->json($response, 200);
        };

        $today = Carbon::now();

        foreach ($calls as $call){
            if( isset($users_already[$call->user_id]) ){
                continue;
            } else {
                $users_already[$call->user_id] = 1;
            }
            $linha = Beamer::short_beamer_by_id($call->user_id);
            if(empty($linha)){
                continue;
            }
            $new_line = $linha->toArray();
            $new_line['updated_at'] = $call->updated_at;
            $new_line['updated_date'] = $call->updated_date;

            $call_date = Carbon::parse($call->updated_date);

            if($call_date->diffInDays($today)==0){
                $new_line['updated_group'] = 'Today';
            } else if($call_date->diffInDays($today)==1){
                $new_line['updated_group'] = 'Yesterday';
            } else if($call_date->diffInDays($today)<=7){
                $new_line['updated_group'] = 'Last week';
            } else if($call_date->diffInDays($today)<=30){
                $new_line['updated_group'] = 'Last month';
            } else {
                $new_line['updated_group'] = 'Everytime';
            }
            $last_calls[] = (object) $new_line;
        }
        $response = [
            'success' => true,
            'data'    => ['last_calls' => HistoryResource::collection($last_calls)]
        ];
        return response()->json($response, 200);
    }
}
