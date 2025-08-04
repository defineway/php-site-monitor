<?php
namespace App\Services;

class SSLMonitor {
    public function checkSSL(string $url): array {
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'] ?? '';
        $port = $parsedUrl['port'] ?? 443;
        
        if (empty($host)) {
            return [
                'status' => 'down',
                'ssl_expiry_date' => null,
                'error_message' => 'Invalid URL provided',
            ];
        }
        
        // Skip SSL check for HTTP URLs
        if (isset($parsedUrl['scheme']) && $parsedUrl['scheme'] === 'http') {
            return [
                'status' => 'N/A',
                'ssl_expiry_date' => null,
                'error_message' => 'HTTP URL - SSL not applicable',
            ];
        }
        
        $context = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);
        
        $socket = @stream_socket_client(
            "ssl://{$host}:{$port}",
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );
        
        if (!$socket) {
            return [
                'status' => 'down',
                'ssl_expiry_date' => null,
                'error_message' => "SSL connection failed: {$errstr}",
            ];
        }
        
        $cert = stream_context_get_params($socket)['options']['ssl']['peer_certificate'];
        fclose($socket);
        
        if (!$cert) {
            return [
                'status' => 'down',
                'ssl_expiry_date' => null,
                'error_message' => 'Unable to retrieve SSL certificate',
            ];
        }
        
        $certData = openssl_x509_parse($cert);
        $expiryDate = date('Y-m-d', $certData['validTo_time_t']);
        $daysUntilExpiry = ceil(($certData['validTo_time_t'] - time()) / 86400);
        
        $status = $this->determineSSLStatus($daysUntilExpiry);
        
        return [
            'status' => $status,
            'ssl_expiry_date' => $expiryDate,
            'error_message' => null,
        ];
    }
    
    private function determineSSLStatus(int $daysUntilExpiry): string {
        if ($daysUntilExpiry <= 0) {
            return 'down';
        }
        
        if ($daysUntilExpiry <= 30) {
            return 'warning';
        }
        
        return 'up';
    }
}
