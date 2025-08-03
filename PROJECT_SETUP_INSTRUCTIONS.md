# PHP Site Monitor - Complete Setup Instructions

> **Note:** This file is project documentation and should be included in version control (git).

## Project Overview
Build a comprehensive PHP-based application to monitor website uptime and SSL certificate expiration with a modern web interface, complete user authentication system, and advanced management features. The project has evolved into a full-featured web application with MVC architecture, role-based access control, and modern UI components.

## Current Status: ✅ PRODUCTION READY
The project is fully implemented with all major features completed:
- ✅ Complete user authentication and authorization system
- ✅ Modern MVC architecture with custom routing
- ✅ Advanced user management with safety protections
- ✅ Responsive UI with Bootstrap 5 and Font Awesome icons
- ✅ Security hardening and session management
- ✅ Visual statistics dashboard
- ✅ Site monitoring functionality (uptime + SSL)
- ✅ Docker containerization with phpMyAdmin

## Technology Stack
- **Backend**: PHP 8.2 with MVC architecture
- **Database**: MySQL 8.0 with comprehensive schema
- **Frontend**: Bootstrap 5, Font Awesome 6.4.0
- **Authentication**: Custom session-based system with role management
- **Containerization**: Docker & Docker Compose
- **Web Server**: Nginx (configured in Docker)
- **Database Management**: phpMyAdmin (http://localhost:8080)
- **Package Management**: Composer for PHP dependencies
- **Testing**: PHPUnit for unit and integration tests

## Quick Start Guide

### 1. Clone and Setup
```bash
git clone <repository-url>
cd php-site-monitor
```

### 2. Environment Configuration
```bash
# Create .env file with database credentials
cat > .env << 'EOF'
DB_HOST=mysql
DB_NAME=site_monitor
DB_USER=monitor_user
DB_PASS=secure_password
APP_ENV=development
APP_DEBUG=true
EOF
```

### 3. Build and Deploy
```bash
# Build and start all containers
docker-compose up -d --build

# Check container status
docker-compose ps

# View application logs
docker-compose logs -f app
```

### 4. Access the Application
- **Main Application**: http://localhost
- **phpMyAdmin**: http://localhost:8080
- **Default Admin**: Credentials are generated on first run—check the container logs (`docker-compose logs -f app`) for the initial admin username and password.

### 5. Initial Setup
1. Navigate to http://localhost
2. Register the first admin account
3. Log in to access the dashboard
4. Add sites for monitoring

## Enhanced Project Structure

The project now follows a clean MVC architecture:
```
├── src/                          # Core PHP classes with MVC architecture
│   ├── Controllers/             # Request handling and business logic
│   │   ├── BaseController.php   # Shared controller functionality
│   │   ├── AuthController.php   # Authentication handling
│   │   ├── UserController.php   # User management CRUD
│   │   ├── SiteController.php   # Site monitoring management
│   │   └── DashboardController.php # Dashboard and statistics
│   ├── Models/                  # Data layer and database operations
│   │   ├── User.php            # User data operations
│   │   ├── Site.php            # Site data operations
│   │   ├── Session.php         # Session management
│   │   └── MonitoringResult.php # Monitoring data operations
│   ├── Services/               # Business logic services
│   │   ├── UptimeMonitor.php   # Website uptime checking
│   │   ├── SSLMonitor.php      # SSL certificate monitoring
│   │   └── AuthService.php     # Authentication business logic
│   ├── Views/                  # HTML templates with Bootstrap UI
│   │   ├── partials/           # Reusable components
│   │   │   └── header.php      # Navigation and common elements
│   │   ├── auth/               # Authentication views
│   │   │   ├── login.php       # Login form
│   │   │   ├── register.php    # Registration form
│   │   │   └── change-password.php # Password change
│   │   ├── users/              # User management interface
│   │   │   ├── users.php       # User list with modern UI
│   │   │   └── profile.php     # User profile management
│   │   ├── sites/              # Site management views
│   │   └── dashboard.php       # Main dashboard with statistics
│   ├── Config/                 # Configuration classes
│   │   └── Database.php        # Database connection management
│   └── Router.php              # Custom routing system
├── public/                     # Web accessible files
│   └── index.php              # Application entry point and router
├── config/                     # External configuration files
│   ├── nginx/                 # Web server configuration
│   │   └── default.conf       # Nginx virtual host config
│   └── database/              # Database setup
│       └── schema.sql         # Enhanced database schema
├── tests/                     # Unit and integration tests
├── logs/                      # Application and monitoring logs
├── storage/                   # File storage and cache
├── vendor/                    # Composer dependencies (not in git)
├── .env                       # Environment variables (not in git)
├── .gitignore                 # Comprehensive git ignore rules
├── composer.json              # PHP dependency definitions
├── composer.lock              # Locked dependency versions
├── Dockerfile                 # PHP-FPM container configuration
├── docker-compose.yml         # Multi-container orchestration
├── monitor.php                # CLI monitoring script
├── PROJECT_SETUP_INSTRUCTIONS.md # This comprehensive guide
├── GEMINI.md                  # Project documentation and status
└── README.md                  # User-facing documentation
```

## Key Features Implemented

### 🔐 Authentication System
- Secure login/logout functionality
- User registration with validation
- Password hashing with bcrypt
- Session-based authentication
- Role-based access control (Admin/User)

### 👥 User Management
- Complete CRUD operations for users
- Modern Bootstrap UI with icons
- Safety protections:
  - Users cannot delete themselves
  - Users cannot modify their own status
  - System prevents deletion of last active admin
- Statistics dashboard with user counts
- Responsive design with visual feedback

### 🎨 Modern User Interface
- Bootstrap 5 with Font Awesome icons
- Responsive design for all screen sizes
- Visual statistics cards
- Dismissible alert notifications
- Clean navigation with active page highlighting
- Professional color schemes and typography

### 🛡️ Security Features
- Input validation and sanitization
- CSRF protection for forms
- Secure password storage
- Session management with database storage
- Role-based permissions
- Prevention of privilege escalation

### 📊 Monitoring Capabilities
- Website uptime monitoring
- SSL certificate expiration tracking
- Configurable check intervals
- Visual status indicators
- Historical data storage

## Database Schema (Enhanced)

The database schema has been significantly enhanced to support the full application:

### Enhanced Database Schema (`config/database/schema.sql`)
```sql
-- Users table for authentication and authorization
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- User sessions for secure authentication
CREATE TABLE user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_id VARCHAR(255) UNIQUE NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

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

-- Monitoring results table with enhanced tracking
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

-- Create indexes for better performance
CREATE INDEX idx_user_sessions_user_id ON user_sessions(user_id);
CREATE INDEX idx_user_sessions_session_id ON user_sessions(session_id);
CREATE INDEX idx_monitoring_results_site_id ON monitoring_results(site_id);
CREATE INDEX idx_monitoring_results_checked_at ON monitoring_results(checked_at);
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

## Development and Maintenance

### Architecture Overview
The application follows a clean MVC (Model-View-Controller) pattern:

- **Controllers**: Handle HTTP requests and coordinate between models and views
- **Models**: Manage database operations and business logic
- **Views**: Render HTML with modern Bootstrap UI components
- **Services**: Encapsulate complex business logic (monitoring, authentication)
- **Router**: Custom routing system for clean URLs

### Security Implementation
- **Password Security**: Bcrypt hashing with proper salt
- **Session Management**: Database-backed session storage
- **Access Control**: Role-based permissions (Admin/User)
- **Input Validation**: Comprehensive sanitization and validation
- **CSRF Protection**: Form tokens to prevent cross-site request forgery
- **Safe Operations**: Prevention of self-account deletion and last admin removal

### Testing and Quality Assurance

```bash
# Run unit tests
docker-compose exec app vendor/bin/phpunit

# View application logs
docker-compose logs -f app

# Access database via phpMyAdmin
# Navigate to http://localhost:8080

# Run monitoring manually
docker-compose exec app php monitor.php --debug

# Check application status
docker-compose ps
```

### Common Development Tasks

```bash
# Database operations
docker-compose exec mysql mysql -u monitor_user -p site_monitor

# View monitoring logs
docker-compose exec app tail -f logs/monitor.log

# Restart specific service
docker-compose restart app

# Clean rebuild
docker-compose down
docker-compose up -d --build

# Clean up everything (removes volumes)
docker-compose down -v --rmi all
```

## Production Deployment Considerations

### Environment Configuration
- Update `.env` file with production database credentials
- Set `APP_ENV=production` and `APP_DEBUG=false`
- Configure proper SSL certificates for HTTPS
- Set up database backups and monitoring

### Security Checklist
- [ ] Change default database passwords
- [ ] Enable HTTPS with valid SSL certificates
- [ ] Configure firewall rules
- [ ] Set up log rotation
- [ ] Enable database encryption at rest
- [ ] Configure session security settings
- [ ] Set up monitoring and alerting

### Performance Optimization
- Configure PHP-FPM pool settings
- Set up Redis for session storage (optional)
- Enable opcache for PHP
- Configure database query optimization
- Set up CDN for static assets

## Troubleshooting Guide

### Common Issues and Solutions

**Database Connection Errors:**
```bash
# Check if MySQL container is running
docker-compose ps mysql

# Verify database credentials in .env
cat .env

# Check MySQL logs
docker-compose logs mysql
```

**Permission Issues:**
```bash
# Fix file permissions
docker-compose exec app chown -R www-data:www-data /var/www
docker-compose exec app chmod -R 755 /var/www/storage /var/www/logs
```

**Authentication Problems:**
```bash
# Clear sessions table
docker-compose exec mysql mysql -u monitor_user -p -e "TRUNCATE site_monitor.user_sessions;"

# Reset user password (replace with actual user ID)
docker-compose exec app php -r "echo password_hash('newpassword', PASSWORD_DEFAULT);"

# Copy the generated hash above, then update the user's password in the database.
# Example (replace <USER_ID> and <HASH> with actual values):
docker-compose exec mysql mysql -u monitor_user -p -e "UPDATE site_monitor.users SET password='<HASH>' WHERE id=<USER_ID>;"

# Or, if you use username instead of user ID:
# docker-compose exec mysql mysql -u monitor_user -p -e \"UPDATE site_monitor.users SET password='<HASH>' WHERE username='actual_username';\"
```

### Monitoring and Logging

```bash
# View real-time application logs
docker-compose logs -f app

# Check monitoring execution
docker-compose exec app tail -f logs/monitor.log

# View Nginx access logs
docker-compose logs nginx

# Monitor system resources
docker stats
```

## Migration from Basic Setup

If you're upgrading from a basic monitoring setup:

1. **Backup existing data**
2. **Update database schema** with new user tables
3. **Migrate existing sites** to new structure
4. **Set up first admin user**
5. **Configure authentication** settings
6. **Test all functionality** before going live

## Next Steps and Future Enhancements

### Implemented Features ✅
- Complete user authentication and authorization
- Modern MVC architecture with custom routing
- Advanced user management with safety protections
- Responsive UI with Bootstrap 5 and Font Awesome
- Security hardening and session management
- Visual statistics dashboard

### Planned Enhancements 🚧
- Email/SMS notifications for downtime alerts
- API endpoints for external integrations
- Advanced SSL monitoring with certificate chain validation
- Performance metrics and detailed analytics
- Export/import configurations
- Webhook integrations
- Multi-tenant support

This comprehensive setup provides a solid foundation for a production-ready PHP site monitoring application with modern architecture, security best practices, and an intuitive user interface.
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
