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
        logMessage("Evaluating site: {$site->getName()} ({$site->getUrl()})", true);
        
        $currentTime = new DateTime();
        $siteId = $site->getId();
        
        // Get last check times for this site
        $lastUptimeCheck = $monitoringResultService->getLastCheckTime($siteId, 'uptime');
        $lastSSLCheck = $monitoringResultService->getLastCheckTime($siteId, 'ssl');
        
        $uptimeCheckDue = false;
        $sslCheckDue = false;
        
        // Check if uptime monitoring is due
        if ($lastUptimeCheck) {
            $lastUptimeTime = new DateTime($lastUptimeCheck);
            $minutesSinceLastUptime = $currentTime->getTimestamp() - $lastUptimeTime->getTimestamp();
            $minutesSinceLastUptime = floor($minutesSinceLastUptime / 60); // Convert to minutes
            
            if ($minutesSinceLastUptime >= $site->getCheckInterval()) {
                $uptimeCheckDue = true;
                logMessage("Uptime check due: {$minutesSinceLastUptime} minutes since last check (interval: {$site->getCheckInterval()} min)", true);
            } else {
                logMessage("Uptime check not due: {$minutesSinceLastUptime} minutes since last check (interval: {$site->getCheckInterval()} min)", true);
            }
        } else {
            // No previous check, so it's due
            $uptimeCheckDue = true;
            logMessage("Uptime check due: No previous check found", true);
        }
        
        // Check if SSL monitoring is due (only if SSL monitoring is enabled)
        if ($site->isSslCheckEnabled()) {
            if ($lastSSLCheck) {
                $lastSSLTime = new DateTime($lastSSLCheck);
                $secondsSinceLastSSL = $currentTime->getTimestamp() - $lastSSLTime->getTimestamp();
                
                if ($secondsSinceLastSSL >= $site->getSslCheckInterval()) {
                    $sslCheckDue = true;
                    $hoursSinceSSL = floor($secondsSinceLastSSL / 3600);
                    $intervalHours = floor($site->getSslCheckInterval() / 3600);
                    logMessage("SSL check due: {$hoursSinceSSL} hours since last check (interval: {$intervalHours} hours)", true);
                } else {
                    $hoursSinceSSL = floor($secondsSinceLastSSL / 3600);
                    $intervalHours = floor($site->getSslCheckInterval() / 3600);
                    logMessage("SSL check not due: {$hoursSinceSSL} hours since last check (interval: {$intervalHours} hours)", true);
                }
            } else {
                // No previous SSL check, so it's due
                $sslCheckDue = true;
                logMessage("SSL check due: No previous SSL check found", true);
            }
        }
        
        // Perform uptime check if due
        if ($uptimeCheckDue) {
            logMessage("Performing uptime check for: {$site->getName()}", true);
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
            
            logMessage("Uptime check result: {$uptimeResult['status']} ({$uptimeResult['response_time']}ms)", true);
        }
        
        // Perform SSL check if due and enabled
        if ($sslCheckDue && $site->isSslCheckEnabled()) {
            logMessage("Performing SSL check for: {$site->getName()}", true);
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
            
            logMessage("SSL check result: {$sslResult['status']} (expires: {$sslResult['ssl_expiry_date']})", true);
        }
        
        // Log if no checks were performed
        if (!$uptimeCheckDue && !$sslCheckDue) {
            logMessage("No checks performed for {$site->getName()} - all checks up to date", true);
        }
    }
    
    logMessage('Site monitoring completed.', true);
    
} catch (Exception $e) {
    logMessage('Error: ' . $e->getMessage());
    exit(1);
}
