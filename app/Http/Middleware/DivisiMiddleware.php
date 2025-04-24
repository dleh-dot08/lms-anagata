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
    public function handle(Request $request, Closure $next, ...$allowedDivisi): Response
    {
        $user = auth()->user();

        // Prevent access if not logged in or user has no divisi field
        if (! $user || ! in_array($user->divisi, $allowedDivisi)) {
            abort(403, 'Akses dilarang untuk divisi Anda.');
        }

        return $next($request);
    }
    
}
