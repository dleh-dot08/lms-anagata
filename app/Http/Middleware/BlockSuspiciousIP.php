<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;

class BlockSuspiciousIP
{
    // Batas request per menit
    private $maxRequests = 5; // Test value
    // Lama blokir (menit)
    private $banDuration = 1; // Test value

    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        if (RateLimiter::tooManyAttempts($ip, $this->maxRequests)) {
            $seconds = RateLimiter::availableIn($ip);
            Storage::append('banned_ips.log', now() . " - {$ip}");
            
            return response('Too Many Requests - Your IP is temporarily blocked', 429)
                ->header('Retry-After', $seconds)
                ->header('X-RateLimit-Reset', now()->addSeconds($seconds)->getTimestamp());
        }

        RateLimiter::hit($ip, $this->banDuration * 60);

        return $next($request);
    }
}