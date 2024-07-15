<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserShopper;
use Illuminate\Http\Request;

use App\Http\Resources\UserBackOffice as UserResource;

class WvusersController extends TeamController
{

    public $view_data = [
        'icon'=>'fa-users',
        'page_title'=>'Utilisateurs'
    ];

    public function index(Request $request)
    {
        $users = User::all();

        if( $request->ajax() ){
            $dados = [];
            $dados['data'] = UserResource::collection($users);
            return response()->json($dados, 200);
        }

        $this->view_data['users'] = $users;
        return view('teamv2.users.index',$this->view_data);
    }

    public function users_shopper(Request $request)
    {
        $user_id = $request->get('userid');
        $shopstate = $request->get('shopstate');
        
        UserShopper::where('user_id', $user_id)->delete();

        if($shopstate==1){
            UserShopper::create(['user_id'=>$user_id, 'shopper_enabled'=>1]);
        }
        $user = User::where('id', $user_id)->first();
        $json = new UserResource($user);
        return response()->json($json, 200);
    }

}
