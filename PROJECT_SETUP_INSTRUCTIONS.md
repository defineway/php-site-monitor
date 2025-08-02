# PHP Site Monitor - Complete Setup Instructions

> **Note:** This file is project documentation and should be included in version control (git).

## Project Overview
Build a PHP-based application to monitor website uptime and SSL certificate expiration with a web interface for visualization and configuration.

## Technology Stack
- PHP 8.2
- Composer for dependency management
- MySQL for database storage
- Nginx web server
- Docker for containerization
- PHPUnit for testing
- phpMyAdmin for database management (http://localhost:8080)

## Phase 1: Project Foundation

### 1.1 Initialize Composer Project
```bash
composer init --name="yourname/php-site-monitor" --description="PHP Site Monitor Application" --type="project"
```

### 1.2 Install Required Dependencies
```bash
# Core dependencies
composer require php:^8.2
composer require ext-curl:*
composer require ext-pdo:*
composer require ext-pdo_mysql:*

# Development dependencies
composer require --dev phpunit/phpunit:^10.0
composer require --dev symfony/var-dumper  # For debugging
```

### 1.3 Create Directory Structure
```
├── src/
│   ├── Models/
│   ├── Services/
├── public/
│   ├── index.php
│   └── views/
├── config/
│   ├── nginx/
│   │   └── default.conf
│   └── database/
│       └── schema.sql
├── tests/
├── logs/
├── storage/
├── .env
├── .gitignore
├── composer.json
├── composer.lock
├── Dockerfile
├── docker-compose.yml
├── monitor.php
├── PROJECT_SETUP_INSTRUCTIONS.md
├── GEMINI.md
```

## Phase 2: Database Layer

### 2.1 Create Database Schema (`config/database/schema.sql`)
```sql
-- Sites table for storing monitored websites
CREATE TABLE sites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    url VARCHAR(500) NOT NULL,
    check_interval INT DEFAULT 300, -- seconds
    ssl_check_enabled BOOLEAN DEFAULT TRUE,
    ssl_check_interval INT DEFAULT 86400, -- daily
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Monitoring results table
CREATE TABLE monitoring_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    site_id INT NOT NULL,
    check_type ENUM('uptime', 'ssl') NOT NULL,
    status ENUM('up', 'down', 'warning') NOT NULL,
    response_time INT, -- milliseconds
    status_code INT,
    ssl_expiry_date DATE,
    error_message TEXT,
    checked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (site_id) REFERENCES sites(id) ON DELETE CASCADE
);

-- Users table for authentication
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 2.2 Create Database Configuration (`src/Config/Database.php`)
```php
<?php
namespace App\Config;

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $host = $_ENV['DB_HOST'] ?? 'mysql';
        $dbname = $_ENV['DB_NAME'] ?? 'site_monitor';
        $username = $_ENV['DB_USER'] ?? 'root';
        $password = $_ENV['DB_PASS'] ?? 'password';
        
        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
        
        try {
            $this->connection = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (\PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection(): \PDO {
        return $this->connection;
    }
}
```

## Phase 3: Core Models

### 3.1 Create Site Model (`src/Models/Site.php`)
```php
<?php
namespace App\Models;

use App\Config\Database;

class Site {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create(array $data): int {
        $sql = "INSERT INTO sites (name, url, check_interval, ssl_check_enabled, ssl_check_interval) 
                VALUES (:name, :url, :check_interval, :ssl_check_enabled, :ssl_check_interval)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        
        return $this->db->lastInsertId();
    }
    
    public function findAll(): array {
        $sql = "SELECT * FROM sites ORDER BY name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function findById(int $id): ?array {
        $sql = "SELECT * FROM sites WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    public function update(int $id, array $data): bool {
        $sql = "UPDATE sites SET name = :name, url = :url, check_interval = :check_interval, 
                ssl_check_enabled = :ssl_check_enabled, ssl_check_interval = :ssl_check_interval 
                WHERE id = :id";
        
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
    
    public function delete(int $id): bool {
        $sql = "DELETE FROM sites WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
```

### 3.2 Create MonitoringResult Model (`src/Models/MonitoringResult.php`)
```php
<?php
namespace App\Models;

use App\Config\Database;

class MonitoringResult {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create(array $data): int {
        $sql = "INSERT INTO monitoring_results (site_id, check_type, status, response_time, status_code, ssl_expiry_date, error_message) 
                VALUES (:site_id, :check_type, :status, :response_time, :status_code, :ssl_expiry_date, :error_message)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        
        return $this->db->lastInsertId();
    }
    
    public function findBySiteId(int $siteId, int $limit = 50): array {
        $sql = "SELECT * FROM monitoring_results WHERE site_id = :site_id 
                ORDER BY checked_at DESC LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':site_id', $siteId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function getLatestStatus(int $siteId): ?array {
        $sql = "SELECT * FROM monitoring_results WHERE site_id = :site_id 
                ORDER BY checked_at DESC LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['site_id' => $siteId]);
        
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
```

## Phase 4: Monitoring Services

### 4.1 Create UptimeMonitor Service (`src/Services/UptimeMonitor.php`)
```php
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
```

### 4.2 Create SSLMonitor Service (`src/Services/SSLMonitor.php`)
```php
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
```

## Phase 5: Main Monitor Script

### 5.1 Create Monitor Script (`monitor.php`)
```php
<?php
require_once 'vendor/autoload.php';

use App\Models\Site;
use App\Models\MonitoringResult;
use App\Services\UptimeMonitor;
use App\Services\SSLMonitor;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$debugMode = isset($argv[1]) && $argv[1] === '--debug';

function logMessage(string $message, bool $debug = false): void {
    global $debugMode;
    if ($debug && !$debugMode) return;
    
    echo '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
}

try {
    logMessage('Starting site monitoring...', true);
    
    $siteModel = new Site();
    $resultModel = new MonitoringResult();
    $uptimeMonitor = new UptimeMonitor();
    $sslMonitor = new SSLMonitor();
    
    $sites = $siteModel->findAll();
    
    if (empty($sites)) {
        logMessage('No sites configured for monitoring.', true);
        exit(0);
    }
    
    foreach ($sites as $site) {
        logMessage("Checking site: {$site['name']} ({$site['url']})", true);
        
        // Check uptime
        $uptimeResult = $uptimeMonitor->checkSite($site['url']);
        $resultModel->create([
            'site_id' => $site['id'],
            'check_type' => 'uptime',
            'status' => $uptimeResult['status'],
            'response_time' => $uptimeResult['response_time'],
            'status_code' => $uptimeResult['status_code'],
            'ssl_expiry_date' => null,
            'error_message' => $uptimeResult['error_message'],
        ]);
        
        logMessage("Uptime check: {$uptimeResult['status']} ({$uptimeResult['response_time']}ms)", true);
        
        // Check SSL if enabled
        if ($site['ssl_check_enabled']) {
            $sslResult = $sslMonitor->checkSSL($site['url']);
            $resultModel->create([
                'site_id' => $site['id'],
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
```

## Phase 6: Docker Configuration

### 6.1 Update Dockerfile
```dockerfile
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    cron \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/logs

# Add cron job for monitoring
RUN echo "*/5 * * * * cd /var/www && php monitor.php >> /var/www/logs/monitor.log 2>&1" | crontab -

# Start cron and PHP-FPM
CMD ["sh", "-c", "cron && php-fpm"]

EXPOSE 9000
```

### 6.2 Update docker-compose.yml
```yaml
version: '3.8'

services:
  app:
    build: .
    container_name: php-site-monitor
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
      - ./logs:/var/www/logs
    depends_on:
      - mysql
    environment:
      - DB_HOST=mysql
      - DB_NAME=site_monitor
      - DB_USER=monitor_user
      - DB_PASS=secure_password
    networks:
      - monitor-network

  nginx:
    image: nginx:alpine
    container_name: nginx-site-monitor
    restart: unless-stopped
    ports:
      - "80:80"
    volumes:
      - ./:/var/www
      - ./config/nginx:/etc/nginx/conf.d
    depends_on:
      - app
    networks:
      - monitor-network

  mysql:
    image: mysql:8.0
    container_name: mysql-site-monitor
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: site_monitor
      MYSQL_USER: monitor_user
      MYSQL_PASSWORD: secure_password
      MYSQL_ROOT_PASSWORD: root_password
    volumes:
      - mysql_data:/var/lib/mysql
      - ./config/database/schema.sql:/docker-entrypoint-initdb.d/schema.sql
    ports:
      - "3306:3306"
    networks:
      - monitor-network

  phpmyadmin:
    image: phpmyadmin/phpmyadmin:latest
    container_name: phpmyadmin-site-monitor
    restart: unless-stopped
    environment:
      PMA_HOST: mysql
      PMA_PORT: 3306
      PMA_USER: root
      PMA_PASSWORD: root_password
      MYSQL_ROOT_PASSWORD: root_password
    ports:
      - "8080:80"
    depends_on:
      - mysql
    networks:
      - monitor-network

volumes:
  mysql_data:

networks:
  monitor-network:
    driver: bridge
```

### 6.3 Update Nginx Configuration (`config/nginx/default.conf`)
```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
}
```

## Phase 7: Web Interface (Basic)

### 7.1 Create Basic Web Interface (`public/index.php`)
```php
<?php
require_once '../vendor/autoload.php';

use App\Models\Site;
use App\Models\MonitoringResult;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$siteModel = new Site();
$resultModel = new MonitoringResult();

$action = $_GET['action'] ?? 'dashboard';

switch ($action) {
    case 'dashboard':
        $sites = $siteModel->findAll();
        include 'views/dashboard.php';
        break;
    
    case 'add_site':
        if ($_POST) {
            $siteModel->create($_POST);
            header('Location: index.php');
            exit;
        }
        include 'views/add_site.php';
        break;
    
    case 'site_details':
        $siteId = (int)$_GET['id'];
        $site = $siteModel->findById($siteId);
        $results = $resultModel->findBySiteId($siteId);
        include 'views/site_details.php';
        break;
    
    default:
        http_response_code(404);
        echo '404 Not Found';
}
```

## Phase 8: Setup and Deployment Commands

### 8.1 Environment Setup
```bash
# Create .env file
cat > .env << 'EOF'
DB_HOST=mysql
DB_NAME=site_monitor
DB_USER=monitor_user
DB_PASS=secure_password
APP_ENV=development
APP_DEBUG=true
EOF
```

### 8.2 Build and Run
```bash
# Build and start containers
docker-compose up -d --build

# Check logs
docker-compose logs -f app

# Run monitoring manually (for testing)
docker-compose exec app php monitor.php --debug

# Access web interface
open http://localhost:8080
```

### 8.3 Testing
```bash
# Run unit tests
docker-compose exec app vendor/bin/phpunit

# Check container status
docker-compose ps

# View monitoring logs
docker-compose exec app tail -f logs/monitor.log
```

## Phase 9: Next Steps (Additional Features)

1. **Authentication System**: Implement user login/logout
2. **Dashboard Enhancements**: Add charts and graphs
3. **Alert System**: Email/SMS notifications for downtime
4. **API Endpoints**: REST API for external integrations
5. **Advanced SSL Monitoring**: Certificate chain validation
6. **Performance Metrics**: Detailed response time analytics
7. **Configuration Management**: Environment-based configs
8. **Backup System**: Database backup automation
9. **Multi-tenant Support**: Support multiple organizations
10. **Mobile Responsive**: Improve mobile interface

## Common Commands Reference

```bash
# Development
composer install
docker-compose up -d
docker-compose logs -f

# Database operations (MySQL CLI)
docker-compose exec mysql mysql -u monitor_user -p site_monitor

# Database management (phpMyAdmin)
# Access http://localhost:8080 in your browser

# Monitoring
docker-compose exec app php monitor.php --debug
docker-compose exec app tail -f logs/monitor.log

# Testing
docker-compose exec app vendor/bin/phpunit

# Clean up containers, volumes, and images
docker-compose down -v --rmi all
```

This instruction set provides a complete foundation for your PHP Site Monitor project. You can now implement it step by step without having to ask for each component individually.

## .gitignore Best Practices

Your project includes a comprehensive `.gitignore` that excludes:
- OS-specific files (macOS, Windows)
- Logs and environment files
- Composer dependencies (`/vendor`)
- Compressed/archive files (e.g., .zip, .tar.gz)
- Node modules (if any in future)

This helps keep your repository clean and focused on source code and documentation only.
