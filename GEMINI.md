# Project: PHP Site Monitor

> **Note:** This file is project documentation and should be included in version control (git).

## Overview
This project is a PHP-based application to monitor the uptime of various websites also check SSL certificates expiration. A Database is used to store the monitoring results as well as the configuration. There will be a beautiful web interface for visualizing the monitoring data as well as adding new websites to monitor. Every websites can be configured with custom monitoring intervals for uptime and SSL checks. A cron job will be set up to run the monitoring script at specified intervals. It's designed to run within a Docker container.

## Technology Stack
- PHP 8.2
- Composer for dependency management
- cURL for making HTTP requests
- MySQL for database storage
- PHPUnit for unit testing
- Docker for containerization
- Nginx as the web server (in Docker)
- phpMyAdmin for database management (http://localhost:8080)
- Git for version control

## Commands
- Install dependencies: `composer install`
- Run the monitor script: `php monitor.php`
- Run tests: `vendor/bin/phpunit` (if applicable)

## Directory Structure
- `src/`: Core PHP classes (Models, Services)
- `public/`: Web entry point and views
- `config/`: Nginx and database configs
- `tests/`: Unit and integration tests
- `logs/`: Monitoring logs
- `storage/`: Persistent storage (if needed)
- `.env`: Environment variables (not committed)
- `.gitignore`: Git ignore rules
- `composer.json`, `composer.lock`: PHP dependencies
- `Dockerfile`: PHP-FPM container setup
- `docker-compose.yml`: Multi-container orchestration (includes phpMyAdmin)
- `monitor.php`: Main monitoring script
- `PROJECT_SETUP_INSTRUCTIONS.md`, `GEMINI.md`: Documentation

## Notes
- The script outputs its status to the console when run with debug mode enabled.
- Monitoring results are stored in a MySQL database for persistence.
- The web interface (PHP) allows users to view monitoring results, configure monitored sites, and manage users.
- User authentication secures access to the web interface.
- The project runs in Docker containers for consistent environments and easy deployment.
- phpMyAdmin is included for easy database management (http://localhost:8080).
- The monitoring script is scheduled via cron in the container.
- Unit tests ensure reliability of monitoring functionality.
- Security best practices are followed (input sanitization, secure DB connections).
- Documentation is provided for setup, usage, and contribution.
- `.gitignore` excludes OS, log, environment, vendor, and archive files for a clean repo.
- README provides detailed project and usage info.
- 