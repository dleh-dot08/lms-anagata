<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlockSuspiciousIP
{
    // Batas request per menit
    private $maxRequests = 100; // Restored to original value
    // Lama blokir (menit)
    private $banDuration = 60; // Restored to original value

    public function handle(Request $request, Closure $next)
    {
        // Skip rate limiting for CSRF token route
        if ($request->is('csrf-token')) {
            return $next($request);
        }

        $ip = $request->ip();
        $countFile = storage_path("framework/blocking/{$ip}_count.txt");
        $banFile = storage_path("framework/blocking/{$ip}_banned.txt");
        
        // Create directory if it doesn't exist
        if (!file_exists(storage_path("framework/blocking"))) {
            mkdir(storage_path("framework/blocking"), 0777, true);
        }

        // Check if IP is banned
        if (file_exists($banFile)) {
            $banTime = (int) file_get_contents($banFile);
            if (time() < $banTime) {
                return response()->json(['message' => 'Your IP is temporarily blocked'], 429);
            } else {
                unlink($banFile); // Remove ban if expired
            }
        }

        // Get current count and timestamp
        $data = file_exists($countFile) ? explode('|', file_get_contents($countFile)) : [0, 0];
        $count = (int) $data[0];
        $timestamp = (int) ($data[1] ?? 0);

        // Reset count if more than a minute has passed
        if (time() - $timestamp > 60) {
            $count = 0;
            $timestamp = time();
        }

        // Increment count
        $count++;
        file_put_contents($countFile, "{$count}|{$timestamp}");

        // If count exceeds limit, ban the IP
        if ($count > $this->maxRequests) {
            $banUntil = time() + ($this->banDuration * 60);
            file_put_contents($banFile, $banUntil);
            Storage::append('banned_ips.log', now() . " - {$ip}");
            return response()->json(['message' => 'Too Many Requests - Your IP is temporarily blocked'], 429);
        }

        return $next($request);
    }
}