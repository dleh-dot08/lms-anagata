<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class BlockSuspiciousIP
{
    // Batas request per menit
    private $maxRequests = 50;
    // Lama blokir (menit)
    private $banDuration = 60;

    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();
        $key = 'req_count_' . $ip;
        $banKey = 'banned_' . $ip;

        // Kalau IP sedang diblokir → hentikan akses
        if (Cache::has($banKey)) {
            return response()->json(['message' => 'Your IP is temporarily blocked'], 429);
        }

        // Hitung jumlah request dalam 1 menit
        $count = Cache::get($key, 0) + 1;
        Cache::put($key, $count, now()->addMinutes(1));

        // Kalau melebihi batas → blokir & simpan di file
        if ($count > $this->maxRequests) {
            Cache::put($banKey, true, now()->addMinutes($this->banDuration));
            Storage::append('banned_ips.log', now() . " - $ip");
            return response()->json(['message' => 'Too Many Requests - Your IP is temporarily blocked'], 429);
        }

        return $next($request);
    }
}