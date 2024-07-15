<?php
namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use App\Http\Controllers\Controller as Controller;
use App\Http\Requests\ClientRequest;
use App\Libs\Beamer;
use App\Http\Resources\Pins as PinsResource;

class BaseController extends Controller
{
    public function login(Request $request)
    {
        $dados = [];
        return view('web.login', $dados);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        return redirect()->route('web.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
 
        if ( Auth::attempt($credentials) ){
            if (Gate::allows('isAdmin')) {
                $request->session()->regenerate();
                return redirect()->route('team.dashboard');
            } elseif (Gate::allows('isClient')) {
                $request->session()->regenerate();
                return redirect()->route('web.dashboard');
            } elseif (Gate::allows('isBeamer')) {
                $request->session()->regenerate();
                return redirect()->route('web.dashboard');
            } else {
                return back()->withErrors(
                    ['email' => 'usuário não autorizado']
                )->onlyInput('email');
            }
        }

        return back()->withErrors(
            ['email' => 'usuário não autorizado']
        )->onlyInput('email');

    }

    public function beamer_register(Request $request)
    {
        $dados  = ['languages_to_form' => config('language.languages_to_form')];
        return view('web.beamer_register', $dados);
    }

    public function client_register(Request $request)
    {
        $dados  = ['languages_to_form' => config('language.languages_to_form')];
        // dd($dados);
        return view('web.client_register', $dados);
    }

    public function search(Request $request)
    {
        return view('web.search');
    }

    public function beamer_view($id)
    {
        $address = Beamer::beamer_by_id($id);
        // dd($address);
        $pin = PinsResource::collection($address);
        $pin = $pin->first();

        $address = $address->first();
        $beamer_user = $address->user;
        return view('web.beamer_view', compact(['address', 'beamer_user','pin']));
    }

}