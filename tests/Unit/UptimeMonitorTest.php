<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\UptimeMonitor;

class UptimeMonitorTest extends TestCase
{
    public function testCheckSiteReturnsArray()
    {
        $monitor = new UptimeMonitor();
        $result = $monitor->checkSite('https://httpbin.org/status/200');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('status_code', $result);
        $this->assertArrayHasKey('response_time', $result);
        $this->assertArrayHasKey('error_message', $result);
    }
}
