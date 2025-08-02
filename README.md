# PHP Site Monitor

A PHP-based application to monitor website uptime and SSL certificate expiration with a web interface for visualization and configuration.

## Features

- **Uptime Monitoring**: Check website availability and response times
- **SSL Certificate Monitoring**: Monitor SSL certificate expiration dates
- **Web Interface**: Beautiful dashboard to view monitoring results and manage sites
- **Configurable Intervals**: Custom monitoring intervals for each site
- **Docker Support**: Easy deployment with Docker containers (includes phpMyAdmin for DB management)
- **Automated Monitoring**: Cron job integration for scheduled checks

## Technology Stack

- PHP 8.2
- MySQL 8.0
- Nginx
- Docker & Docker Compose
- phpMyAdmin (http://localhost:8080)
- Bootstrap 5 (Frontend)
- PHPUnit (Testing)

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

4. **Add your first site**
   Click "Add New Site" on the dashboard and enter your website details

## Project Structure

```
├── src/                    # Core PHP classes
│   ├── Models/            # Data models (Site, MonitoringResult, User, Session)
│   ├── Services/          # Monitoring and Auth services
├── public/                # Web interface
│   ├── index.php         # Main entry point
│   └── views/            # HTML templates
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

### Adding Sites

1. Navigate to the dashboard
2. Click "Add New Site"
3. Fill in the site details:
   - **Site Name**: A friendly name for identification
   - **URL**: The full URL to monitor (http:// or https://)
   - **Check Interval**: How often to check uptime (in seconds)
   - **SSL Monitoring**: Enable/disable SSL certificate checking
   - **SSL Check Interval**: How often to check SSL certificates

### Viewing Results

- The dashboard shows all monitored sites with their current status
- Click "View Details" on any site to see the monitoring history
- Status indicators:
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

- [ ] Email/SMS notifications for downtime
- [ ] API endpoints for external integrations
- [ ] Advanced SSL monitoring (certificate chain validation)
- [ ] Performance metrics and analytics
- [ ] Multi-user support with authentication
- [ ] Mobile-responsive improvements
- [ ] Export/import site configurations
- [ ] Webhook integrations
- [ ] Custom alerting rules
