<?php

namespace Tests\Feature\Middleware;

use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Http\Middleware\BlockSuspiciousIP;
use Illuminate\Http\Request;

class BlockSuspiciousIPTest extends TestCase
{
    private $middleware;
    private $request;
    
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        Storage::delete('banned_ips.log');
        
        $this->middleware = new BlockSuspiciousIP();
        $this->request = new Request();
        $this->request->server->set('REMOTE_ADDR', '127.0.0.1');
    }

    protected function makeMiddlewareRequest()
    {
        return $this->middleware->handle($this->request, function ($request) {
            return response('OK', 200);
        });
    }

    public function test_allows_requests_under_limit()
    {
        // Make 49 requests (under the 50 limit)
        for ($i = 0; $i < 49; $i++) {
            $response = $this->makeMiddlewareRequest();
            $this->assertEquals(200, $response->status(), "Request $i should not be blocked");
        }
    }

    public function test_blocks_requests_over_limit()
    {
        // Make 51 requests (over the 50 limit)
        for ($i = 0; $i < 51; $i++) {
            $response = $this->makeMiddlewareRequest();
            
            if ($i < 50) {
                $this->assertEquals(200, $response->status(), "Request $i should not be blocked");
            } else {
                $this->assertEquals(429, $response->status(), "Request $i should be blocked");
                $this->assertEquals(
                    'Too Many Requests - Your IP is temporarily blocked',
                    json_decode($response->getContent())->message
                );
            }
        }

        // Verify IP is logged
        $this->assertTrue(Storage::exists('banned_ips.log'));
    }

    public function test_ip_remains_blocked()
    {
        // First trigger the block
        for ($i = 0; $i < 51; $i++) {
            $this->makeMiddlewareRequest();
        }

        // Try another request after being blocked
        $response = $this->makeMiddlewareRequest();
        $this->assertEquals(429, $response->status());
        $this->assertEquals(
            'Your IP is temporarily blocked',
            json_decode($response->getContent())->message
        );
    }

    public function test_block_expires_after_duration()
    {
        // First trigger the block
        for ($i = 0; $i < 51; $i++) {
            $this->makeMiddlewareRequest();
        }

        // Move time forward by 61 minutes (past the 60 minute block)
        $this->travel(61)->minutes();

        // Request should now work again
        $response = $this->makeMiddlewareRequest();
        $this->assertEquals(200, $response->status(), "Request should not be blocked after ban duration");
    }
} 