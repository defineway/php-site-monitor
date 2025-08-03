# PHP Site Monitor

A comprehensive PHP-based application to monitor website uptime and SSL certificate expiration with a modern web interface, user authentication, and advanced management features.

## Features

- **🔐 User Authentication System**: Secure login/logout with role-based access control (Admin/User roles)
- **👥 User Management**: Complete CRUD operations for user accounts with safety protections
- **📊 Uptime Monitoring**: Check website availability and response times
- **🔒 SSL Certificate Monitoring**: Monitor SSL certificate expiration dates with warnings
- **🎨 Modern Web Interface**: Beautiful, responsive dashboard with Bootstrap 5 and Font Awesome icons
- **📈 Statistics Dashboard**: Visual cards showing user counts and system metrics
- **⚙️ Site Management**: Full CRUD operations for monitored websites
- **🔧 Configurable Intervals**: Custom monitoring intervals for each site
- **🐳 Docker Support**: Complete containerization with phpMyAdmin for database management
- **⏰ Automated Monitoring**: Cron job integration for scheduled checks
- **🛡️ Security Features**: Password hashing, session management, CSRF protection
- **📱 Responsive Design**: Mobile-friendly interface with modern UI components

## Technology Stack

- **Backend**: PHP 8.2 with MVC Architecture
- **Database**: MySQL 8.0
- **Web Server**: Nginx
- **Frontend**: Bootstrap 5, Font Awesome 6.4.0
- **Containerization**: Docker & Docker Compose
- **Database Management**: phpMyAdmin (http://localhost:8080)
- **Testing**: PHPUnit
- **Authentication**: Custom session-based auth system

## Quick Start

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd php-site-monitor
   ```

2. **Build and start the containers**
   ```bash
   docker-compose up -d --build
   ```

3. **Access the web interface**
   - Main app: Open your browser and navigate to `http://localhost`
   - phpMyAdmin: Open your browser and navigate to `http://localhost:8080`
   - **Default Login**: Use the credentials created during setup (check container logs for initial admin user)

4. **First-time Setup**
   - Create an admin account through the registration process
   - Log in to access the dashboard
   - Add your first site for monitoring

## Project Structure

```
├── src/                     # Core PHP classes with MVC architecture
│   ├── Models/             # Data models (Site, MonitoringResult, User, Session)
│   ├── Controllers/        # MVC Controllers (Auth, Dashboard, Site, User)
│   ├── Services/           # Business logic (UptimeMonitor, SSLMonitor, AuthService)
│   ├── Views/              # HTML templates with modern Bootstrap UI
│   │   ├── partials/       # Reusable components (header, navigation)
│   │   ├── auth/           # Authentication views (login, register)
│   │   ├── sites/          # Site management views
│   │   └── users/          # User management views
│   └── Router.php          # Custom routing system
├── public/                 # Web interface entry point
│   └── index.php          # Main application router
├── config/               # Configuration files
│   ├── database/         # Database schema
│   └── nginx/            # Nginx configuration
├── tests/                # Unit and integration tests
├── logs/                 # Application logs
├── monitor.php           # Command-line monitoring script
├── docker-compose.yml    # Docker services configuration (includes phpMyAdmin)
├── Dockerfile            # PHP container configuration
├── .env                  # Environment variables (not committed)
├── .gitignore            # Git ignore rules
├── PROJECT_SETUP_INSTRUCTIONS.md  # Setup documentation
├── GEMINI.md             # Project requirements/notes
```

## Usage

### Authentication & User Management

1. **Initial Setup**: Register the first admin account
2. **User Management**: Admins can create, edit, activate/deactivate, and delete users
3. **Role-Based Access**: Admin and User roles with different permissions
4. **Security Features**: 
   - Users cannot delete or modify their own accounts
   - System prevents deletion of the last active admin
   - Secure password hashing and session management

### Adding Sites

1. Log in to the dashboard
2. Navigate to "Sites" section
3. Click "Add New Site"
4. Fill in the site details:
   - **Site Name**: A friendly name for identification
   - **URL**: The full URL to monitor (http:// or https://)
   - **Check Interval**: How often to check uptime (in seconds)
   - **SSL Monitoring**: Enable/disable SSL certificate checking
   - **SSL Check Interval**: How often to check SSL certificates

### Dashboard Features

- **Statistics Cards**: Overview of total users, active users, and administrators
- **Site Status Overview**: Real-time monitoring status for all sites
- **User Management**: Complete user administration interface
- **Responsive Navigation**: Modern sidebar navigation with active page highlighting
- **Status Indicators**: 
  - 🟢 **Up**: Site is accessible and responding normally
  - 🟡 **Warning**: Site has issues (4xx errors, SSL expiring soon)
  - 🔴 **Down**: Site is inaccessible or has critical errors

### Manual Monitoring

You can run the monitoring script manually:

```bash
# Run monitoring for all sites
docker-compose exec app php monitor.php

# Run with debug output
docker-compose exec app php monitor.php --debug
```

### Viewing Logs

```bash
# View monitoring logs
docker-compose exec app tail -f logs/monitor.log

# View container logs
docker-compose logs -f app
```

## Development

### Architecture

The application follows a clean MVC (Model-View-Controller) architecture:

- **Models**: Handle database operations and business logic
- **Controllers**: Process requests and coordinate between models and views
- **Views**: Render HTML with modern Bootstrap UI components
- **Services**: Encapsulate complex business logic (monitoring, authentication)
- **Router**: Custom routing system for clean URLs

### Security Features

- **Password Security**: Bcrypt hashing with proper salt
- **Session Management**: Secure session handling with database storage
- **Access Control**: Role-based permissions (Admin/User)
- **CSRF Protection**: Form tokens to prevent cross-site request forgery
- **Input Validation**: Proper sanitization and validation of user inputs
- **Safe Operations**: Prevention of self-account deletion and last admin removal

### Running Tests

```bash
docker-compose exec app vendor/bin/phpunit
```

### Database Access

```bash
# Connect to MySQL CLI
docker-compose exec mysql mysql -u monitor_user -p site_monitor

# Use phpMyAdmin (browser)
# http://localhost:8080
```

### Customization

#### Adding New Monitoring Types

1. Create a new service in `src/Services/`
2. Implement the monitoring logic
3. Update the monitor script to use the new service
4. Add database fields if needed

#### Modifying Check Intervals

You can customize monitoring intervals for each site through the web interface or directly in the database.

## Configuration

### Environment Variables

- `DB_HOST`: MySQL host (default: mysql)
- `DB_NAME`: Database name (default: site_monitor)
- `DB_USER`: Database user (default: monitor_user)
- `DB_PASS`: Database password (default: secure_password)

### .gitignore Best Practices

Your project includes a comprehensive `.gitignore` that excludes:
- OS-specific files (macOS, Windows)
- Logs and environment files
- Composer dependencies (`/vendor`)
- Compressed/archive files (e.g., .zip, .tar.gz)
- Node modules (if any in future)

This helps keep your repository clean and focused on source code and documentation only.

### Cron Schedule

The monitoring script runs every 5 minutes by default. To change this, modify the cron job in the Dockerfile:

```dockerfile
RUN echo "*/5 * * * * cd /var/www && php monitor.php >> /var/www/logs/monitor.log 2>&1" | crontab -
```

## Troubleshooting

### Database Connection Issues

1. Ensure MySQL container is running: `docker-compose ps`
2. Check database logs: `docker-compose logs mysql`
3. Verify environment variables in `.env` file

### Permission Issues

```bash
# Fix file permissions
docker-compose exec app chown -R www-data:www-data /var/www
docker-compose exec app chmod -R 755 /var/www/storage /var/www/logs
```

### SSL Certificate Errors

- Ensure the site uses HTTPS
- Check if the certificate is valid and not expired
- Some sites may block automated requests

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## License

This project is open source and available under the [MIT License](LICENSE).

## Support

For issues and questions:
1. Check the troubleshooting section
2. Review the logs for error messages
3. Create an issue in the repository

## Roadmap

### ✅ Completed Features
- [x] User Authentication System with role-based access
- [x] Complete User Management (CRUD with safety protections)
- [x] Modern UI with Bootstrap 5 and Font Awesome icons
- [x] MVC Architecture refactoring
- [x] Security hardening (password hashing, session management)
- [x] Statistics dashboard with visual metrics
- [x] Responsive design improvements

### 🚧 Planned Features
- [ ] Email/SMS notifications for downtime alerts
- [ ] API endpoints for external integrations
- [ ] Advanced SSL monitoring (certificate chain validation)
- [ ] Performance metrics and detailed analytics
- [ ] Export/import site configurations
- [ ] Webhook integrations for third-party services
- [ ] Custom alerting rules and thresholds
- [ ] Multi-tenant support for organizations
- [ ] Advanced reporting and historical data analysis
- [ ] Mobile app companion
