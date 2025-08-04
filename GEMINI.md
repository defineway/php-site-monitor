# Project: PHP Site Monitor

> **Note:** This file is project documentation and should be included in version control (git).

## Overview
This project is a comprehensive PHP-based application to monitor the uptime of various websites and check SSL certificates expiration. Built with a modern MVC architecture, it features a complete user authentication system, role-based access control, and a beautiful responsive web interface. The application uses MySQL for data persistence and includes advanced user management capabilities with safety protections. Docker containerization ensures consistent deployment and includes phpMyAdmin for database management.

## Current Status
✅ **FULLY IMPLEMENTED** - The project has evolved significantly from a basic monitoring tool to a feature-rich web application with:
- Complete user authentication and authorization system
- Modern MVC architecture with custom routing
- Advanced user management with role-based access control
- Responsive UI with Bootstrap 5 and Font Awesome icons
- Security hardening and session management
- Visual statistics dashboard
- Safety protections (prevent self-deletion, last admin protection)
- **Background monitoring** via cron job

## Technology Stack
- **Backend**: PHP 8.2 with MVC architecture
- **Database**: MySQL 8.0 with comprehensive schema
- **Frontend**: Bootstrap 5, Font Awesome 6.4.0, responsive design
- **Authentication**: Custom session-based system with role management
- **Containerization**: Docker & Docker Compose
- **Web Server**: Nginx (configured in Docker)
- **Database Management**: phpMyAdmin (http://localhost:8080)
- **Package Management**: Composer for PHP dependencies
- **Version Control**: Git with comprehensive .gitignore
- **Testing**: PHPUnit for unit and integration tests

## Commands
- **Install dependencies**: `composer install`
- **Start application**: `docker-compose up -d --build`
- **Run monitor script (manual)**: `docker-compose exec app php monitor.php`
- **Run with debug**: `docker-compose exec app php monitor.php --debug`
- **Run tests**: `vendor/bin/phpunit` or `docker-compose exec app vendor/bin/phpunit`
- **Access phpMyAdmin**: Open http://localhost:8080 in browser
- **View logs**: `docker-compose logs -f app` or `docker-compose exec app tail -f logs/monitor.log`

## Cron Job Setup
To run the monitoring script automatically, you can set up a cron job.

1.  **Open your crontab:**
    ```bash
    crontab -e
    ```

2.  **Add the following line to run the monitor every minute:**
    ```bash
    * * * * * cd /path/to/your/project && /usr/bin/docker-compose exec -T app php monitor.php >> /dev/null 2>&1
    ```
    > **Note:** Replace `/path/to/your/project` with the absolute path to your project directory.

## Enhanced Directory Structure
- `src/`: Core PHP classes with MVC architecture
  - `Controllers/`: Request handling (AuthController, UserController, SiteController, DashboardController)
  - `Models/`: Data layer (User, Site, MonitoringResult, Session)
  - `Services/`: Business logic (UptimeMonitor, SSLMonitor, AuthService)
  - `Views/`: HTML templates with modern Bootstrap UI
    - `partials/`: Reusable components (header.php, navigation)
    - `auth/`: Authentication views (login.php, register.php, change-password.php)
    - `users/`: User management interface (users.php, profile.php)
    - `sites/`: Site management views
  - `Router.php`: Custom routing system with clean URLs
- `public/`: Web entry point
  - `index.php`: Main application bootstrap and router
- `config/`: Configuration files
  - `nginx/`: Web server configuration
  - `database/`: Enhanced database schema with user tables
- `tests/`: Comprehensive unit and integration tests
- `logs/`: Application and monitoring logs
- `storage/`: Persistent storage for uploads/cache
- `.env`: Environment variables (not committed to git)
- `.gitignore`: Comprehensive git ignore rules
- `composer.json`, `composer.lock`: PHP dependency management
- `Dockerfile`: Optimized PHP-FPM container setup
- `docker-compose.yml`: Multi-container orchestration with phpMyAdmin
- `monitor.php`: Enhanced monitoring script with debug support
- `PROJECT_SETUP_INSTRUCTIONS.md`: Detailed setup and development guide
- `GEMINI.md`: This project documentation and status file

## Key Features Implemented
- **🔐 Authentication System**: Secure login/logout with bcrypt password hashing
- **👥 User Management**: Complete CRUD with role-based access (Admin/User)
- **🛡️ Security Features**: Session management, CSRF protection, input validation
- **🎨 Modern UI**: Bootstrap 5 with Font Awesome icons, responsive design
- **📊 Dashboard**: Statistics cards, visual metrics, real-time status indicators
- **⚙️ Site Management**: Full CRUD operations for monitored websites
- **🔒 SSL Monitoring**: Certificate expiration tracking with warnings
- **📱 Responsive Design**: Mobile-friendly interface with modern components
- **🛡️ Safety Protections**: Prevent self-account deletion, last admin protection
- **📈 Visual Feedback**: Alert system with dismissible notifications
- **🎯 Role-Based Access**: Different permissions for Admin and User roles
- **🔄 Background Monitoring**: Automated site checks via cron job

## Security Implementation
- Password hashing with bcrypt and proper salting
- Session-based authentication with database storage
- Role-based access control (Admin/User permissions)
- Input sanitization and validation
- CSRF protection for forms
- Prevention of privilege escalation
- Safe account management (no self-deletion, last admin protection)
- Secure database connections with prepared statements

## Development Progress & Status

### ✅ Completed Major Milestones
1. **Foundation Setup** (Phase 1): Docker environment, basic structure
2. **Database Layer** (Phase 2): Enhanced schema with user authentication tables
3. **MVC Architecture** (Phase 3): Complete refactoring to proper MVC pattern
4. **Authentication System** (Phase 4): Secure login/logout with session management
5. **User Management** (Phase 5): Full CRUD operations with role-based access
6. **UI/UX Enhancement** (Phase 6): Modern Bootstrap 5 interface with icons
7. **Security Hardening** (Phase 7): Safety protections and input validation
8. **Bug Fixes & Polish** (Phase 8): Redirect loops, double headers, PHP warnings
9. **Background Monitoring** (Phase 9): Added cron job support for automated checks

### 🎯 Current State
The project is **PRODUCTION READY** with a comprehensive feature set:
- Fully functional authentication and authorization system
- Complete user and site management capabilities
- Modern, responsive web interface
- Robust security implementation
- Docker-based deployment ready
- Comprehensive documentation
- Automated background monitoring

### 📋 Technical Achievements
- **Architecture**: Clean MVC pattern with custom routing
- **Security**: Industry-standard authentication and authorization
- **UI/UX**: Modern, accessible interface with visual feedback
- **Database**: Normalized schema with proper relationships
- **Deployment**: Containerized with Docker Compose
- **Documentation**: Comprehensive setup and usage guides
- **Quality**: Input validation, error handling, logging

## Notes
- The application has evolved from a basic monitoring script to a full-featured web application
- Security best practices are implemented throughout (password hashing, session management, input validation)
- The UI provides immediate visual feedback with dismissible alerts and status indicators
- User management includes safety protections to prevent system lockout scenarios
- The monitoring functionality includes both uptime and SSL certificate checking
- Docker setup includes phpMyAdmin for easy database management (http://localhost:8080)
- The codebase follows PSR standards and includes comprehensive error handling
- All user inputs are properly sanitized and validated
- The system supports role-based access control with Admin and User roles
- Modern responsive design ensures compatibility across devices
- The project structure supports easy maintenance and future feature additions
- README provides detailed project and usage info.
- Automated background monitoring is now supported via cron jobs.
 