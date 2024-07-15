<?php
namespace App\Http\Controllers\Team;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\Validator;

use App\Models\Param;
use App\Models\User;


class TeamController extends BaseController
{

    function __construct()
    {
         $this->middleware('user_type:isAdmin');
    }
    public function dashboard2(){
        $dados = [
            'nome_da_variavel' => 'testando envio de dados para view',
            'numero_teste' => '27'
        ];
        return view('teamv2.layout.layout-ref', $dados);
    }

    public function dashboard(){
        $dados = [
            'nome_da_variavel' => 'testando envio de dados para view',
            'numero_teste' => '27'
        ]; 
        return view('teamv2.stats', $dados);
    }

    public function map(){
        $dados = [
            'nome_da_variavel' => 'testando envio de dados para view',
            'numero_teste' => '27'
        ];
        return view('teamv2.map', $dados);
    }

    public function users(){
        $dados = [
            'nome_da_variavel' => 'testando envio de dados para view',
            'numero_teste' => '27'
        ];
        return view('teamv2.users', $dados);
    }
    public function contacts(){
        $dados = [
            'nome_da_variavel' => 'testando envio de dados para view',
            'numero_teste' => '27'
        ];
        return view('teamv2.contacts', $dados);
    }

    public function params(){
        // session()->put('_old_input', []);
        // dd($_SESSION);
        $dados = Param::getArray();
        return view('teamv2.params', ['fromdb'=>$dados]);
    }

    public function save_params(Request $request){

        $validator = Validator::make($request->all(), [
            // 'call_interval_log' => 'bail|required|numeric|between:3,60',
            // 'call_interval_online' => 'bail|required|numeric|between:3,60',
            'commission_to_bb' => 'bail|required|numeric|between:0,100',
        ]);

        if ($validator->fails()) {
            return back()  
                ->withInput()
                ->withErrors($validator);
        } else {
            $validated = $validator->validated();
            Param::addOrUpdate($validated, auth()->user()->id);
            return back()->with('flash_success', 'Paramètres réécrits!');
        }
    }


    public function profile(){
        return view('teamv2.profile');
    }

    public function save_profile(Request $request){

        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return back()  
                ->withInput()
                ->withErrors($validator);
        } else {
            $validated = $validator->validated();
            $userid = auth()->user()->id;
            $user = User::find($userid);
            $user->password = bcrypt($validated['password']);
            $user->save();
            return back()->with('flash_success', 'Password was changed.');
        }
    }

}