<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Inbox\Inbox;
use Validator;
use App\Http\Resources\InboxMsg as InboxMsgResource;
   
class InboxController extends BaseController
{
    function __construct()
    {
        $this->middleware('permission:utype-client');
    }
   
    public function messages($client_id, $beamer_id)
    {
        $logged_user_id = auth()->user()->id;
        // verificando se é um dos usuários da conversa
        if( $logged_user_id==$client_id OR $logged_user_id==$beamer_id ){

        } else {
            return $this->sendError("user not allowed");
        }

        $inbox = Inbox::where('user_id_beamer', $beamer_id)
                ->where('user_id_client', $client_id)
                ->orWhere(function($query) use ($client_id, $beamer_id) {
                    $query->where('user_id_beamer', $client_id)
                          ->where('user_id_client', $beamer_id);
                })
                ->first();
        if(empty($inbox)){
            $messages = [];
        } else {
            $messages = $inbox->messages()
                    // ->select(['id','status','','message', 'created_at'])
                    ->orderBy('created_at', 'asc')
                    ->get();

            $messages = InboxMsgResource::collection($messages);
        }
        $response = [
            'messages' => $messages
        ];
        return $this->sendResponse($response, "inbox return ok");
    }
}
