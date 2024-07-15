<?php
namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\Validator;

use App\Models\Param;

class WebController extends BaseController
{

    function __construct()
    {
        $this->middleware('user_type:isClient|isBeamer');
    }

    public function dashboard(){
        $dados = [
            'User_type' => 'Client or Beamer'
        ];
        return view('web.dashboard', $dados);
    }

    public function settings(){
        // session()->put('_old_input', []);
        // dd($_SESSION);
        $dados = Param::getArray();
        return view('web.settings', $dados);
    }

    public function save_settings(Request $request){

        $validator = Validator::make($request->all(), [
            'call_interval_log' => 'bail|required|numeric|between:3,20',
            'commission_to_bb' => 'bail|required|numeric|between:0,100',
        ]);

        if ($validator->fails()) {
            return back()  
                ->withInput()
                ->withErrors($validator);
        } else {
            // $validated = $validator->validated();
            // Param::addOrUpdate($validated, auth()->user()->id);
            // return back()->with('flash_success', 'Params updated!');
            // $validated = $validator->validated();
            // Param::addOrUpdate($validated, auth()->user()->id);
            return back()->with('flash_success', 'Settings updated!');
        }
    }

}