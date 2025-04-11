<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesertaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role->name === 'Peserta') {
            return $next($request);
        }
        return redirect('/')->with('error', 'Unauthorized Access');
    }
}

