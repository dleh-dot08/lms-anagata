<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SekolahMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role_id === 6 && auth()->user()->sekolah_id) {
            return $next($request);
        }

        abort(403, 'Unauthorized access. Only School PIC can access this page.');
    }
} 