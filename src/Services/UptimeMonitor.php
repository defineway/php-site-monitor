<?php
namespace App\Services;

class UptimeMonitor {
    public function checkSite(string $url): array {
        $startTime = microtime(true);
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_USERAGENT => 'PHP Site Monitor/1.0',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        $responseTime = round((microtime(true) - $startTime) * 1000);
        
        $status = $this->determineStatus($httpCode, $error);
        
        return [
            'status' => $status,
            'status_code' => $httpCode,
            'response_time' => $responseTime,
            'error_message' => $error ?: null,
        ];
    }
    
    private function determineStatus(int $httpCode, string $error): string {
        if (!empty($error)) {
            return 'down';
        }
        
        if ($httpCode >= 200 && $httpCode < 400) {
            return 'up';
        }
        
        if ($httpCode >= 400 && $httpCode < 500) {
            return 'warning';
        }
        
        return 'down';
    }
}
