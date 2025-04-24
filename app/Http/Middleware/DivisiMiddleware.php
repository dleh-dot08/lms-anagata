<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DivisiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$allowedDivisi)
    {
        $userDiv = auth()->user()->divisi;
        if (! in_array($userDiv, $allowedDivisi)) {
            abort(403, 'Akses dilarang untuk divisi Anda.');
        }
        return $next($request);
    }
    
}
