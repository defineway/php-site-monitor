# Changelog

All notable changes to the PHP Site Monitor project will be documented in this file.

## [2.0.0] - 2025-08-04

### Added
- **Intelligent Monitoring System**: Monitor script now respects individual site check intervals
  - Uptime monitoring honors `check_interval` (in minutes)
  - SSL monitoring honors `ssl_check_interval` (in seconds) 
  - Debug mode shows interval calculations and decisions
- **Enhanced Dashboard UI**: Completely redesigned dashboard with statistics cards
  - Total sites, uptime percentage, average response time, SSL monitoring count
  - Color-coded metrics and visual indicators
  - Modern card-based layout with icons
- **Improved Site Details**: Separated uptime and SSL monitoring views
  - Dedicated sections for uptime history and SSL certificate history
  - Enhanced status badges and visual indicators
  - Latest status information in top card
- **Xdebug Development Support**: Full debugging environment setup
  - Xdebug configured and enabled in Docker container
  - VS Code launch configuration for debugging
  - Development-ready debugging environment
- **Enhanced User Models**: Improved data handling
  - User model methods now return User objects instead of arrays
  - Better type safety and consistency throughout application

### Changed
- **Monitoring Efficiency**: Monitor script only checks sites when intervals have elapsed
- **Cron Schedule**: Changed from every 5 minutes to every minute with intelligent interval checking
- **UI/UX Improvements**: Modern design with better visual hierarchy and responsive elements
- **Database Queries**: Added `getLastCheckTime()` method to MonitoringResultService

### Fixed
- **Type Safety**: Fixed nullable property issues in Site model
- **Monitoring Logic**: Eliminated unnecessary duplicate checks
- **User Interface**: Improved responsiveness and mobile compatibility

## [1.0.0] - 2025-07-XX

### Added
- Initial release with core functionality
- User authentication and authorization system
- Site monitoring for uptime and SSL certificates
- Modern MVC architecture
- Bootstrap 5 UI with responsive design
- Docker containerization
- Database management with phpMyAdmin
- Security features and session management

### Features
- Role-based access control (Admin/User)
- Complete user management CRUD operations
- Website uptime monitoring
- SSL certificate expiration monitoring
- Modern web interface
- Automated cron job monitoring
- Security hardening

---

## Version Numbering

This project follows [Semantic Versioning](https://semver.org/):
- **MAJOR** version for incompatible API changes
- **MINOR** version for backwards-compatible functionality additions  
- **PATCH** version for backwards-compatible bug fixes
