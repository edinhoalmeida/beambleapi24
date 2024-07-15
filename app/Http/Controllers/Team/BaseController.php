<?php
namespace App\Http\Controllers\Team;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller as Controller;

use Illuminate\Support\Facades\Gate;

class BaseController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->session()->all();
        $dados = [];
        return view('teamv2.login', $dados);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        return redirect()->route('team.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
 
        if ( Auth::attempt($credentials) ){           
            if ( Gate::allows('isAdmin') ) {
                $request->session()->regenerate();
                return redirect()->route('team.dashboard');
            } else {
                return back()->withErrors(
                    ['email' => __('team.user_not_auth')]
                )->onlyInput('email');
            }
        }

        return back()->withErrors(
            ['email' => __('team.user_not_found')]
        )->onlyInput('email');

    }
}