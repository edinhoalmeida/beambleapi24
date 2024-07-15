<?php
namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\Validator;

use App\Models\Param;

class BeamerController extends BaseController
{

    function __construct()
    {
         $this->middleware('user_type:isBeamer');
    }

    public function dashboard(){
        $dados = [
            'User type' => 'Beamer'
        ];
        return view('web.dashboard', $dados);
    }

}