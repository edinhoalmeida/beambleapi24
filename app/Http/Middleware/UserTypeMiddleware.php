<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UserTypeMiddleware
{
    public function handle($request, Closure $next, $permission)
    {
        $verified = false;
        if (Gate::allows( $permission ) ) {
            $verified = true;
        } elseif ($permission=='isClient|isBeamer' && (Gate::allows('isClient')||Gate::allows('isBeamer'))) {
            $verified = true;
        } elseif ($permission=='isPublic') {
            $verified = true;
        } 
        if( ! $verified ){
            switch($permission){
                case "isAdmin":
                    return redirect()->route('team.login');
                    break;
                case "isClient":
                    return redirect()->route('login');
                    break;
                case "isBeamble":
                    return redirect()->route('login');
                    break;
            }
        }
        return $next($request);
    }
}
