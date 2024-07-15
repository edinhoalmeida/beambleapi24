<?php
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\API\BaseController as BaseController;

use App\Libs\FirebasePN;

class MessagingController extends BaseController
{

    private $firebase = null;

    function __construct()
    {
        // $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','show']]);
        // $this->middleware('permission:user-create', ['only' => ['create','store']]);
        // $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        // $this->middleware('permission:user-delete', ['only' => ['destroy']]);
        $this->firebase = new FirebasePN();
    }
    public function all(Request $request)
    {
        $dados = $request->all();
        $this->firebase->all($dados);
        return $this->sendResponse($dados, "Firebase route all");
    }

    public function one(Request $request)
    {
        $dados = $request->all();
        $sended = $this->firebase->one($dados);
        if($sended){
            return $this->sendResponse($dados, "Firebase route one");
        }
        return $this->sendError("Firebase token invalid!");
    }
}
