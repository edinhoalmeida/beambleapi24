<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OptionalAuthSanctum
{
    public function handle(Request $request, Closure $next)
    {
        // if ( Auth('sanctum')->user() ) {
        //     auth('sanctum')->user();
        // }
        if (request()->bearerToken() && $user = Auth::guard('sanctum')->user()) {
            Auth::setUser($user);
        }

        return $next($request);
    }
}

