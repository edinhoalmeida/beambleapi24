<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;


use App\Models\Videocall;
use App\Models\VideocallLog;
use App\Models\VideocallRating;
use App\Libs\Videosdk;
use App\Http\Resources\Videocall as VideocallResource;
use App\Http\Resources\VideocallShort as VideocallShortResource;

use App\Models\UserTrack;
use App\Models\UserTracking;

use App\Http\Requests\VideocallLogRequest;
use App\Http\Requests\RatingRequest;

use App\Events\ClientAsk;
use App\Events\BeamerAccept;
use App\Events\BeamerReject;
use App\Events\CallTimer;

use Carbon\Carbon;
use DB;

class CallController extends BaseController
{
    public function __construct()
    {
        // $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','show']]);
        // $this->middleware('permission:user-create', ['only' => ['create','store']]);
        // $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        // $this->middleware('permission:user-delete', ['only' => ['destroy']]);
        // $this->middleware('user_type:isClient|isBeamer');
        // Videocall::clearExpiredCalls();
    }

    public function test(Request $request)
    {
        $tosave = [
            'client_id' => 123,
            'beamer_id' => 123
        ];
        $videocall = Videocall::create($tosave);
        dd($videocall);
    }

    /**
    * @OA\Get(
    *    path="/calls/{user_id}",
    *    tags={"videocall  🔒"},
    *    description="Beamer verify if is there are waiting calls",
    *    @OA\Parameter(
    *      name="user_id",
    *      description="A beamer id",
    *       in="path",
    *       required=true,
    *       @OA\Schema(
    *           type="integer"
    *       )
    *    ),
    *    @OA\Response(
    *        response="200",
    *        description="if exists, return a Videocall",
    *         @OA\JsonContent(
    *           oneOf={
    *               @OA\Schema(
    *                 @OA\Property(
    *                     property="success",
    *                     type="boolean",
    *                     description="Status of request"
    *                  ),
    *                 @OA\Property(
    *                     property="data",
    *                     type="object",
    *                     @OA\Property(
    *                          property="videocall",
    *                          type="object",
    *                           @OA\Property(
    *                              property="status",
    *                              type="string",
    *                              example="none",
    *                          ),
    *                      ),
    *                 ),
    *                 @OA\Property(
    *                     property="message",
    *                     type="string",
    *                     description="Listing calls"
    *                 ),
    *              ),
    *               @OA\Schema(
    *                  @OA\Property(
    *                 property="success",
    *                 type="boolean",
    *                 description="Status of request"
    *              ),
    *                 @OA\Property(
    *                     property="data",
    *                     type="object",
    *                     @OA\Property(
    *                          property="videocall",
    *                          type="object",
    *                           ref="#/components/schemas/videocall",
    *                      ),
    *                 ),
    *                 @OA\Property(
    *                     property="message",
    *                     type="string",
    *                     description="Messages from api"
    *                 ),
    *               )
    *           }
    *    )
    *    ),
    *    @OA\Response(
    *        response="400",
    *        description="user_id or beamer id not recognized",
    *        @OA\JsonContent(
    *          ref="#/components/schemas/baseerror",
    *        ),
    *    ),
    * )
    */
    public function checkcall(Request $request, $user_id)
    {
        $userid = auth()->user()->id;
        $logs = [
            'request_user_id' => $user_id,
            'logged_user_id' => $userid
        ];
        dblog('checkcall', json_encode($logs));
        // only edit yourself
        if($userid == $user_id) {
            $videocall = Videocall::where('beamer_id', $user_id)->where('status', 'waiting')->first();
            $dados = [];
            $dados['videocall'] = empty($videocall) ? ['status' => 'none'] : new VideocallResource($videocall, $userid);

            dblog('checkcall videocall', json_encode($dados['videocall']));

            return $this->sendResponse($dados, __('beam.call_list_success'));
        }
        return $this->sendError(__('beam.user_not_allowed'));
    }

    /**
    * @OA\Post(
    *    path="/calls/{user_id}/{beamer_id}/ask",
    *    tags={"videocall  🔒"},
    *    @OA\Parameter(
    *       name="user_id",
    *       description="A client id",
    *       in="path",
    *       required=true,
    *       @OA\Schema(
    *           type="integer"
    *       )
    *    ),
    *    @OA\Parameter(
    *       name="beamer_id",
    *       description="A beamer id",
    *       in="path",
    *       required=true,
    *       @OA\Schema(
    *           type="integer"
    *       )
    *    ),
    *    @OA\RequestBody(
    *        @OA\MediaType(
    *            mediaType="multipart/form-data",
    *            @OA\Schema(
    *                @OA\Property(
    *                     property="lat",
    *                     type="number",
    *                ),
    *                @OA\Property(
    *                     property="lng",
    *                     type="number",
    *                ),
    *            )
    *        )
    *    ),
    *    @OA\Response(
    *        response="200",
    *        description="Video call asked with success, wainting that beamer confirms",
    *        @OA\JsonContent(
    *                @OA\Property(
    *                     property="success",
    *                     type="boolean",
    *                     description="Status of request"
    *                ),
    *                @OA\Property(
    *                     property="data",
    *                     type="object",
    *                     @OA\Property(
    *                          property="videocall",
    *                          type="object",
    *                          ref="#/components/schemas/videocall",
    *                      ),
    *                ),
    *                @OA\Property(
    *                     property="message",
    *                     type="string",
    *                     description="Messages from api"
    *                ),
    *        ),
    *    ),
    *    @OA\Response(
    *        response="400",
    *        description="user_id or beamer id not recognized",
    *        @OA\JsonContent(
    *          ref="#/components/schemas/baseerror",
    *        ),
    *    ),
    * )
    */
    public function askcall(Request $request, $user_id, $beamer_id)
    {
        $userid = auth()->user()->id;
        // only edit yourself
        $logs = [
            'request_user_id' => $user_id,
            'logged_user_id' => $userid,
            'beamer_id' => $beamer_id
        ];
        dblog('askcall', json_encode($logs));
        if($userid == $user_id) {
            // search if beamer is 'with_donation','is_freemium',
            $track_atual = UserTrack::where('user_id', $beamer_id)->first();
            if(empty($track_atual)) {
                return $this->sendError(__('beam.call_beamer_offline'));
            }
            $tosave = [
                'client_id' => $user_id,
                'beamer_id' => $beamer_id,
                'with_donation' => $track_atual->with_donation,
                'is_freemium' => $track_atual->is_freemium,
                'status' => 'waiting',
                // default madrid 40.3865059,-3.7186519
                'client_lat' => $request->get('lat', 40.3865059),
                'client_lng' => $request->get('lng', 3.7186519),
            ];
            // before create a new one erase olders calls
            Videocall::where('client_id', $user_id)
                ->where('beamer_id', $beamer_id)->delete();
            $videocall = Videocall::create($tosave);

            // Trigger the event
            event(new ClientAsk($videocall));

            dblog('videocall', json_encode($videocall->toArray()));

            $dados = [];
            $dados['videocall'] = new VideocallResource($videocall, $userid);
            dblog('askcall videocall', json_encode($dados['videocall']));

            // user 34 is accept automatically
            if($beamer_id >= 80 && $beamer_id <= 109) {
                $this->_acceptcall_call($videocall->id, $beamer_id);
            }

            return $this->sendResponse($dados, __('beam.call_list_success'));
        }
        return $this->sendError(__('beam.user_not_allowed'));
    }


    /**
    * @OA\Post(
    *    path="/calls/{call_id}/accept",
    *    tags={"videocall  🔒"},
    *    description="Beamer accept call",
    *    @OA\Parameter(
    *       name="call_id",
    *       description="A call id",
    *       in="path",
    *       required=true,
    *       @OA\Schema(
    *           type="integer"
    *       )
    *    ),
    *    @OA\Response(
    *        response="200",
    *        description="Videocall accepted with success, the status is 'accepted' now",
    *        @OA\JsonContent(
    *                @OA\Property(
    *                     property="success",
    *                     type="boolean",
    *                     description="Status of request"
    *                ),
    *                @OA\Property(
    *                     property="data",
    *                     type="object",
    *                     @OA\Property(
    *                          property="videocall",
    *                          type="object",
    *                          ref="#/components/schemas/videocall",
    *                      ),
    *                ),
    *                @OA\Property(
    *                     property="message",
    *                     type="string",
    *                     description="Messages from api"
    *                ),
    *        ),
    *    ),
    *    @OA\Response(
    *        response="400",
    *        description="user_id or beamer id not recognized",
    *        @OA\JsonContent(
    *          ref="#/components/schemas/baseerror",
    *        ),
    *    ),
    * )
    */
    public function acceptcall(Request $request, $call_id)
    {
        $userid = auth()->user()->id;
        $dados = $this->_acceptcall_call($call_id, $userid);
        return $this->sendResponse($dados, __('beam.call_list_success'));
    }

    public function _acceptcall_call($call_id, $userid)
    {
        $logs = [
            'call_id' => $call_id,
            'logged_user_id' => $userid
        ];
        dblog('acceptcall', json_encode($logs));
        $Videocall = Videocall::where('beamer_id', $userid)
            ->where('id', $call_id)
            ->where('status', 'waiting')->first();
        
        if(empty($Videocall)) {

            dblog('Videocall empty', '');

            $dados = [];
            $dados['videocall'] = ['status' => 'notfound'];
            return $dados;
        } else {

            dblog('Videocall', json_encode($Videocall->toArray()));

            $st_meeting = Videosdk::create_meeting();
            $meeting = json_decode($st_meeting);
            $tosave = [
                'beamer_agree_at' => date('Y-m-d H:i:s', time()),
                'status' => 'accepted',
                'meeting_id' => $meeting->roomId,
                'meeting_object' => $st_meeting
            ];

            $Videocall->update($tosave);

            // Trigger the event
            event(new BeamerAccept($Videocall));

            $ut = UserTrack::where('user_id', $userid)->first();
            if($ut) {
                $ut->status = 'on_call';
                $ut->save();
                UserTracking::create([
                    'user_id' => $userid,
                    'status' => 'on_call'
                ]);
            }
            $dados = [];
            $dados['videocall'] = new VideocallResource($Videocall, $userid);
            return $dados;
        }
    }

    /**
    * @OA\Post(
    *        path="/calls/{call_id}/reject",
    *        tags={"videocall  🔒"},
    *        description="Beamer reject call",
    *        @OA\Parameter(
    *           name="call_id",
    *           description="A call id",
    *           in="path",
    *           required=true,
    *           @OA\Schema(
    *               type="integer"
    *           )
    *        ),
    *        @OA\Response(
    *            response="200",
    *            description="Videocall rejected with success, the status is 'rejected' now",
    *            @OA\JsonContent(
    *                    @OA\Property(
    *                         property="success",
    *                         type="boolean",
    *                         description="Status of request"
    *                    ),
    *                    @OA\Property(
    *                         property="data",
    *                         type="object",
    *                         @OA\Property(
    *                              property="videocall",
    *                              type="object",
    *                               @OA\Property(
    *                                  property="status",
    *                                  type="string",
    *                                  example="rejected",
    *                              ),
    *                          ),
    *                    ),
    *                    @OA\Property(
    *                         property="message",
    *                         type="string",
    *                         description="Messages from api"
    *                    ),
    *            ),
    *        ),
    *        @OA\Response(
    *            response="400",
    *            description="user_id or beamer id not recognized",
    *            @OA\JsonContent(
    *              ref="#/components/schemas/baseerror",
    *            ),
    *        ),
        * )
        */
    public function rejectcall(Request $request, $call_id)
    {
        $userid = auth()->user()->id;

        $logs = [
            'call_id' => $call_id,
            'logged_user_id' => $userid
        ];
        dblog('rejectcall', json_encode($logs));
        $Videocall = Videocall::where('beamer_id', $userid)
            ->where('id', $call_id)
            ->where('status', 'waiting')->first();
        if(empty($Videocall)) {
            $dados = [];
            $dados['videocall'] = ['status' => 'notfound'];
            return $this->sendResponse($dados, __('beam.call_list_success'));
        } else {

            // Trigger the event
            if($Videocall){
                event(new BeamerReject($Videocall));
            }

            $tosave = [
                'beamer_agree_at' => date('Y-m-d H:i:s', time()),
                'status' => 'rejected'
            ];
            $Videocall->update($tosave);
            $VVideocall = Videocall::where('beamer_id', $call_id)->first();
            $dados = [];
            $dados['videocall'] = ['status' => 'rejected'];

            

            return $this->sendResponse($dados, __('beam.call_list_success'));
        }
    }


    /**
        * @OA\Get(
    *        path="/calls/{call_id}/accepted",
    *        tags={"videocall  🔒"},
    *        description="Client verify if call was accepted",
    *        @OA\Parameter(
    *           name="call_id",
    *           description="A call id",
    *           in="path",
    *           required=true,
    *           @OA\Schema(
    *               type="integer"
    *           )
    *        ),
    *        @OA\Response(
    *            response="200",
    *            description="if was accepted, return a Videocall",
    *            @OA\JsonContent(
    *               oneOf={
    *                   @OA\Schema(
    *                         description="Call was waiting for more than 40 seconds and was considered LOST",
    *                         @OA\Property(
    *                             property="success",
    *                             type="boolean",
    *                             example="true"
    *                          ),
    *                         @OA\Property(
    *                             property="data",
    *                             type="object",
    *                             @OA\Property(
    *                                  property="videocall",
    *                                  type="object",
    *                                   @OA\Property(
    *                                      property="status",
    *                                      type="string",
    *                                      example="lost",
    *                                  ),
    *                              ),
    *                         ),
    *                         @OA\Property(
    *                             property="message",
    *                             type="string",
    *                             description="Listing calls"
    *                         ),
    *                   ),
    *                   @OA\Schema(
    *                         description="Call was refused by beamer",
    *                         @OA\Property(
    *                             property="success",
    *                             type="boolean",
    *                             example="true"
    *                          ),
    *                         @OA\Property(
    *                             property="data",
    *                             type="object",
    *                             @OA\Property(
    *                                  property="videocall",
    *                                  type="object",
    *                                   @OA\Property(
    *                                      property="status",
    *                                      type="string",
    *                                      example="rejected",
    *                                  ),
    *                              ),
    *                         ),
    *                         @OA\Property(
    *                             property="message",
    *                             type="string",
    *                             description="Listing calls"
    *                         ),
    *                   ),
    *                   @OA\Schema(
    *                         description="Beamer hasn't accepted yet",
    *                         @OA\Property(
    *                             property="success",
    *                             type="boolean",
    *                             example="true"
    *                          ),
    *                         @OA\Property(
    *                             property="data",
    *                             type="object",
    *                             @OA\Property(
    *                                  property="videocall",
    *                                  type="object",
    *                                   @OA\Property(
    *                                      property="status",
    *                                      type="string",
    *                                      example="not_accepted_yet",
    *                                  ),
    *                              ),
    *                         ),
    *                         @OA\Property(
    *                             property="message",
    *                             type="string",
    *                             description="Listing calls"
    *                         ),
    *                   ),
    *                   @OA\Schema(
    *                         description="Beamer accepted the call",
    *                          @OA\Property(
    *                              property="success",
    *                              type="boolean",
    *                              example="true"
    *                          ),
    *                         @OA\Property(
    *                             property="data",
    *                             type="object",
    *                             @OA\Property(
    *                                  property="videocall",
    *                                  type="object",
    *                                   ref="#/components/schemas/videocall",
    *                              ),
    *                         ),
    *                         @OA\Property(
    *                             property="message",
    *                             type="string",
    *                             description="Messages from api"
    *                         ),
    *                   )
    *               }
    *            )
    *        ),
    *        @OA\Response(
    *            response="400",
    *            description="user_id or beamer id not recognized",
    *            @OA\JsonContent(
    *              ref="#/components/schemas/baseerror",
    *            ),
    *        ),
    * )
    */
    public function acceptedcall(Request $request, $call_id)
    {
        $userid = auth()->user()->id;
        $logs = [
            'call_id' => $call_id,
            'logged_user_id' => $userid
        ];
        dblog('acceptedcall', json_encode($logs));
        $Videocall = Videocall::where('client_id', $userid)
            ->where('id', $call_id)->withTrashed()->first();

        if(empty($Videocall->status)) {
            $dados = [];
            $dados['videocall'] = ['status' => 'call_expired_or_error'];
            return $this->sendResponse($dados, __('beam.call_list_success'));
        }
        if($Videocall->status == 'lost') {
            $dados = [];
            $Videocall->delete();
            $dados['videocall'] = ['status' => 'lost'];
            return $this->sendResponse($dados, __('beam.call_list_success'));
        }
        if($Videocall->status == 'rejected') {
            $dados = [];
            $dados['videocall'] = ['status' => 'rejected'];
            $Videocall->delete();
            return $this->sendResponse($dados, __('beam.call_list_success'));
        }

        $Videocall = Videocall::where('client_id', $userid)
            ->where('meeting_id', '<>', null)
            ->where('id', $call_id)
            ->where('status', 'accepted')->first();

        if(empty($Videocall)) {
            $dados = [];
            $dados['videocall'] = ['status' => 'not_accepted_yet'];
            return $this->sendResponse($dados, __('beam.call_list_success'));
        } else {
            $tosave = [
                'status' => 'client_aware'
            ];
            $videocall = $Videocall->update($tosave);
            $dados = [];
            $dados['videocall'] = new VideocallResource($Videocall, $userid);
            return $this->sendResponse($dados, __('beam.call_list_success'));
        }
    }

    /**
    * @OA\Post(
    *        path="/calls/{call_id}/savelog",
    *        tags={"videocall  🔒"},
    *        description="Beamer and Client send logs of call status. ",
    *        @OA\Parameter(
    *           name="call_id",
    *           description="A call id",
    *           in="path",
    *           required=true,
    *           @OA\Schema(
    *               type="integer"
    *           )
    *        ),
    *        @OA\Response(
    *            response="200",
    *            description="if exists, return a Videocall",
    *             @OA\JsonContent(
    *                  @OA\Property(
    *                     property="success",
    *                     type="boolean",
    *                     description="Status of request"
    *                  ),
    *                  @OA\Property(
    *                         property="data",
    *                         type="object",
    *                         @OA\Property(
    *                              property="videocall",
    *                              type="object",
    *                               ref="#/components/schemas/videocallshort",
    *                          ),
    *                  ),
    *                  @OA\Property(
    *                         property="message",
    *                         type="string",
    *                         description="Messages from api"
    *                  ),
    *          )
    *        ),
    *        @OA\Response(
    *            response="400",
    *            description="user_id or beamer id not recognized",
    *            @OA\JsonContent(
    *              ref="#/components/schemas/baseerror",
    *            ),
    *        ),
    * )
    */
    public function savelog(VideocallLogRequest $request, $call_id)
    {
        $userid = auth()->user()->id;

        $VideocallClient = Videocall::where('client_id', $userid)
            ->where('id', $call_id)->withTrashed()->first();
        $VideocallBeamer = Videocall::where('beamer_id', $userid)
            ->where('id', $call_id)->withTrashed()->first();


        if(empty($VideocallClient->status) && empty($VideocallBeamer->status)) {
            $dados = [];
            $dados['videocall'] =
                [
                    'status' => 'call_expired_or_error',
                    'environmental_care_msg' => ''
                ];
            dblog('savelog - error (call, user) - ' . $call_id . ',' . $userid, json_encode($dados['videocall']));
            return $this->sendError([], 'error');
        }

        $log = $request->get('log');

        // MeetingJoined
        // MeetingLeft
        // ConectionInstable
        // ConectionInstable
        $dados = [
            'call_id' => $call_id,
            'status' => trim($log, " \n\r")
        ];

        $Videocall = Videocall::where('id', $call_id)->withTrashed()->first();

        if($Videocall->client_id == $userid) {
            $dados['side'] = 'client';
        } else { //if(!empty($VideocallBeamer->status)){
            $dados['side'] = 'beamer';
        }
        VideocallLog::create($dados);
        dblog('savelog - videolog - ' . $call_id . ',' . $userid, json_encode($dados));

        $ret_array = [];

        if($log == 'MeetingLeft') {
            // calcula o tempo da chamada
            $duration = -1;
            $tries = 5;
            do {
                $duration = VideocallLog::call_duration_seconds($call_id);
                if($duration == -1) {
                    $tries--;
                    usleep(300);
                }
            } while($duration == -1 && $tries > 0);
            $Videocall = Videocall::where('id', $call_id)->withTrashed()->first();
            $imutable = Carbon::now();
            $Videocall->duration = $duration;
            $Videocall->timer_end_at = $imutable;
            $Videocall->status = 'success_with_duration';
            $Videocall->save();

            Videocall::call_CO2_emissions($call_id);

            UserTrack::force_end_track($Videocall->beamer_id);

            // recall a line from db
            $Videocall2 = Videocall::where('id', $call_id)->withTrashed()->first();
            $ret_array['videocall'] = new VideocallShortResource($Videocall2);
        } else {
            $Videocall2 = Videocall::where('id', $call_id)->withTrashed()->first();
            $ret_array['videocall'] = new VideocallShortResource($Videocall2);
        }

        dblog('savelog - ret_array - ' . $call_id . ',' . $userid, json_encode($ret_array));

        return $this->sendResponse($ret_array, __('beam.call_log'));
    }

/**
    * @OA\Post(
    *        path="/calls/{call_id}/rating",
    *        tags={"videocall  🔒"},
    *        description="Beamer and Client send evalution of negotiation. ",
    *        @OA\Parameter(
    *           name="call_id",
    *           description="A call id",
    *           in="path",
    *           required=true,
    *           @OA\Schema(
    *               type="integer"
    *           )
    *        ),
    *    @OA\RequestBody(
    *        @OA\MediaType(
    *            mediaType="multipart/form-data",
    *            @OA\Schema(
    *                @OA\Property(
    *                     property="rating",
    *                     type="number",
    *                ),
    *            )
    *        )
    *    ),
    *        @OA\Response(
    *            response="200",
    *            description="if exists, return a Videocall",
    *             @OA\Schema(
    *                 ref="#/components/schemas/base",
    *             ),
    *        ),
    *        @OA\Response(
    *            response="400",
    *            description="user_id or beamer id not recognized",
    *            @OA\JsonContent(
    *              ref="#/components/schemas/baseerror",
    *            ),
    *        ),
    * )
    */
    public function rating(RatingRequest $request, $call_id)
    {
        $userid = auth()->user()->id;

        $VideocallClient = Videocall::where('client_id', $userid)
            ->where('id', $call_id)->withTrashed()->first();
        $VideocallBeamer = Videocall::where('beamer_id', $userid)
            ->where('id', $call_id)->withTrashed()->first();

        if(empty($VideocallClient->status) && empty($VideocallBeamer->status)) {
            $dados = [];
            $dados['videocall'] = ['status' => 'call_expired_or_error'];
            return $this->sendResponse($dados, __('beam.call_list_success'));
        }
        $rating = $request->get('rating');
        $evaluation = $request->get('evaluation');
        // rating
        $dados = [
            'call_id' => $call_id,
            'rating' => $rating,
            'evaluation' => $evaluation
        ];
        if(!empty($VideocallClient->status)) {
            $dados['side'] = 'client';
        } else { //if(!empty($VideocallBeamer->status)){
            $dados['side'] = 'beamer';
        }
        VideocallRating::create($dados);
        return $this->sendResponse([], __('beam.call_rating'));
    }

    public function status(Request $request, $call_id)
    {
        $userid = auth()->user()->id;

        if(is_numeric($call_id)) {
            $VideocallClient = Videocall::where('client_id', $userid)
                ->where('id', $call_id)->withTrashed()->first();
            $VideocallBeamer = Videocall::where('beamer_id', $userid)
                ->where('id', $call_id)->withTrashed()->first();
        } else {
            $VideocallClient = Videocall::where('client_id', $userid)
                ->where('meeting_id', $call_id)->withTrashed()->first();
            $VideocallBeamer = Videocall::where('beamer_id', $userid)
                ->where('meeting_id', $call_id)->withTrashed()->first();
        }

        if(empty($VideocallClient->status) && empty($VideocallBeamer->status)) {
            $dados = [];
            $dados['videocall'] =
                [
                    'status' => 'none',
                    'environmental_care_msg' => ''
                ];
            dblog('status - error (call, user) - ' . $call_id . ',' . $userid, json_encode($dados['videocall']));
            return $this->sendError($dados, __('beam.call_list_success'), 404);
        }

        $ret_array = [];
        $ret_array['videocall'] = ['status' => 'none'];

        if(!empty($VideocallClient->status)) {
            $ret_array['videocall'] = new VideocallResource($VideocallClient, $userid);
        } elseif (!empty($VideocallBeamer->status)) {
            $ret_array['videocall'] = new VideocallResource($VideocallBeamer, $userid);
        } else {
            return $this->sendError($ret_array, __('beam.call_list_success'), 404);
        }

        return $this->sendResponse($ret_array, __('beam.call_log'));
    }

    /**
    * @OA\Get(
    *        path="/calls/{call_id}/timer/start",
    *        tags={"videocall  🔒"},
    *        description="Beamer asks the customer for the start time that will be charged",
    *        @OA\Parameter(
    *          name="call_id",
    *          description="A call id",
    *          in="path",
    *          required=true,
    *          @OA\Schema(
    *              type="integer"
    *          )
    *        ),
    *        @OA\Response(
    *           response="200",
    *           description="Return true to successfully request",
    *            @OA\JsonContent(
    *              ref="#/components/schemas/base",
    *            )
    *        ),
    *        @OA\Response(
    *           response="400",
    *           description="user_id or beamer id not recognized",
    *           @OA\JsonContent(
    *             ref="#/components/schemas/baseerror",
    *           ),
    *        ),
    * )
    */
    public function timer_start(Request $request, $call_id)
    {
        $userid = auth()->user()->id;
        $VideocallBeamer = Videocall::where('beamer_id', $userid)
            ->where('id', $call_id)->withTrashed()->first();
        if(empty($VideocallBeamer)) {
            return $this->sendError([], 'error');
        }
        $dados = [
            'call_id' => $call_id,
            'status' => 'TimerStartRequest',
            'side' => 'beamer'
        ];
        VideocallLog::create($dados);
        event(new CallTimer($VideocallBeamer, 'timer_start'));
        return $this->sendResponse([], __('beam.call_log'));
    }

    /**
    * @OA\Get(
    *        path="/calls/{call_id}/timer/accept",
    *        tags={"videocall  🔒"},
    *        description="Customer accepts the start of the billing period",
    *        @OA\Parameter(
    *          name="call_id",
    *          description="A call id",
    *          in="path",
    *          required=true,
    *          @OA\Schema(
    *              type="integer"
    *          )
    *        ),
    *        @OA\Response(
    *           response="200",
    *           description="Return true to successfully request",
    *             @OA\JsonContent(
    *               ref="#/components/schemas/base",
    *             )
    *        ),
    *        @OA\Response(
    *            response="400",
    *            description="user_id or beamer id not recognized",
    *            @OA\JsonContent(
    *              ref="#/components/schemas/baseerror",
    *            ),
    *        ),
    * )
    */
    public function timer_accept(Request $request, $call_id)
    {
        $userid = auth()->user()->id;
        $VideocallClient = Videocall::where('client_id', $userid)
            ->where('id', $call_id)->withTrashed()->first();

        $imutable = Carbon::now();
        if(empty($VideocallClient)) {
            return $this->sendError([], 'error');
        } else {
            $VideocallClient->timer_start_at = $imutable;
            $VideocallClient->save();
        }
        $dados = [
            'call_id' => $call_id,
            'status' => 'TimerStartAccept',
            'side' => 'client'
        ];
        VideocallLog::create($dados);
        event(new CallTimer($VideocallClient, 'timer_accept'));
        return $this->sendResponse([], __('beam.call_log'));
    }

    /**
    * @OA\Get(
    *        path="/calls/{call_id}/timer/reject",
    *        tags={"videocall  🔒"},
    *        description="Customer reject the start of the billing period",
    *        @OA\Parameter(
    *           name="call_id",
    *           description="A call id",
    *           in="path",
    *           required=true,
    *           @OA\Schema(
    *               type="integer"
    *           )
    *        ),
    *        @OA\Response(
    *            response="200",
    *            description="Return true to successfully reject",
    *             @OA\JsonContent(
    *               ref="#/components/schemas/base",
    *             )
    *        ),
    *        @OA\Response(
    *            response="400",
    *            description="user_id or beamer id not recognized",
    *            @OA\JsonContent(
    *              ref="#/components/schemas/baseerror",
    *            ),
    *        ),
    * )
    */
    public function timer_reject(Request $request, $call_id)
    {
        $userid = auth()->user()->id;
        $VideocallClient = Videocall::where('client_id', $userid)
            ->where('id', $call_id)->withTrashed()->first();
        if(empty($VideocallClient)) {
            return $this->sendError([], 'error');
        } else {
            $VideocallClient->timer_start_at = null;
            $VideocallClient->save();
        }
        $dados = [
            'call_id' => $call_id,
            'status' => 'TimerStartReject',
            'side' => 'client'
        ];
        VideocallLog::create($dados);
        event(new CallTimer($VideocallClient, 'timer_reject'));
        return $this->sendResponse([], __('beam.call_log'));
    }

}
