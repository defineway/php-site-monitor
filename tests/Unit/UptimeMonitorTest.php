<?php
/**
 * PHP Site Monitor - Unit Tests
 *
 * @author Sushovan Mukherjee
 * @copyright 2025 Defineway Technologies Private Limited
 * @link https://defineway.com
 * @contact sushovan@defineway.com
 *
 * Licensed under the MIT License with Attribution Clause.
 * You must retain visible credit to the company ("Powered by Defineway Technologies Private Limited")
 * in the user interface and documentation of any derivative works or public deployments.
 */
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
