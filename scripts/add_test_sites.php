<?php
require_once 'vendor/autoload.php';

use App\Models\Site;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$siteModel = new Site();

// Add some sample sites for testing
$testSites = [
    [
        'name' => 'Google',
        'url' => 'https://www.google.com',
        'check_interval' => 300,
        'ssl_check_enabled' => true,
        'ssl_check_interval' => 86400
    ],
    [
        'name' => 'GitHub',
        'url' => 'https://github.com',
        'check_interval' => 300,
        'ssl_check_enabled' => true,
        'ssl_check_interval' => 86400
    ],
    [
        'name' => 'HTTPBin (Test)',
        'url' => 'https://httpbin.org/status/200',
        'check_interval' => 300,
        'ssl_check_enabled' => true,
        'ssl_check_interval' => 86400
    ]
];

try {
    foreach ($testSites as $site) {
        $id = $siteModel->create($site);
        echo "Added site '{$site['name']}' with ID: {$id}\n";
    }
    echo "Test sites added successfully!\n";
} catch (Exception $e) {
    echo "Error adding test sites: " . $e->getMessage() . "\n";
}
