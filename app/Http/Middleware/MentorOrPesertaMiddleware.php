<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MentorOrPesertaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && in_array($user->role_id, [2, 3])) {
            return $next($request);
        }

        return redirect('/')->with('error', 'Unauthorized access.');
    }
}
