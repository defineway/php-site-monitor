<?php
require_once 'vendor/autoload.php';

use App\Services\UptimeMonitor;
use App\Services\SSLMonitor;
use App\Services\SiteService;
use App\Services\MonitoringResultService;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$debugMode = isset($argv[1]) && $argv[1] === '--debug';

function logMessage(string $message, bool $debug = false): void {
    global $debugMode;
    if ($debug && !$debugMode) return;
    
    echo '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
}

try {
    logMessage('Starting site monitoring...', true);

    $siteService = new SiteService();
    $monitoringResultService = new MonitoringResultService();
    $uptimeMonitor = new UptimeMonitor();
    $sslMonitor = new SSLMonitor();

    $sites = $siteService->findAll();
    
    if (empty($sites)) {
        logMessage('No sites configured for monitoring.', true);
        exit(0);
    }
    
    foreach ($sites as $site) {
        logMessage("Checking site: {$site->getName()} ({$site->getUrl()})", true);

        // Check uptime
        $uptimeResult = $uptimeMonitor->checkSite($site->getUrl());
        $monitoringResultService->create([
            'site_id' => $site->getId(),
            'check_type' => 'uptime',
            'status' => $uptimeResult['status'],
            'response_time' => $uptimeResult['response_time'],
            'status_code' => $uptimeResult['status_code'],
            'ssl_expiry_date' => null,
            'error_message' => $uptimeResult['error_message'],
        ]);
        
        logMessage("Uptime check: {$uptimeResult['status']} ({$uptimeResult['response_time']}ms)", true);
        
        // Check SSL if enabled
        if ($site->isSslCheckEnabled()) {
            $sslResult = $sslMonitor->checkSSL($site->getUrl());
            $monitoringResultService->create([
                'site_id' => $site->getId(),
                'check_type' => 'ssl',
                'status' => $sslResult['status'],
                'response_time' => null,
                'status_code' => null,
                'ssl_expiry_date' => $sslResult['ssl_expiry_date'],
                'error_message' => $sslResult['error_message'],
            ]);
            
            logMessage("SSL check: {$sslResult['status']} (expires: {$sslResult['ssl_expiry_date']})", true);
        }
    }
    
    logMessage('Site monitoring completed.', true);
    
} catch (Exception $e) {
    logMessage('Error: ' . $e->getMessage());
    exit(1);
}
