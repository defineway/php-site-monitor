## Big Picture & Data Flow
 - **Goal:** Monitor website uptime and SSL certificate status, with results shown in a modern PHP web UI.
 - **Architecture:** Clean MVC in `src/` — Controllers handle HTTP requests, call Services for business logic, which use Models for DB access. Views render Bootstrap-based HTML.
 - **Monitoring:** `monitor.php` (CLI/cron) runs intelligent interval-based checks using Services, stores results in DB. Results are displayed in enhanced dashboard and site details views with visual indicators.
 - **Routing:** Custom router (`src/Controllers/Router.php`) maps `?action=` URLs to controller methods. Entry point is `public/index.php`.

## Data & Component Interactions
 - **Controllers** receive HTTP requests, validate input, and call **Services** for business logic.
 - **Services** (e.g., `UptimeMonitor`, `SSLMonitor`, `MonitoringResultService`) encapsulate monitoring/auth logic and interact with **Models** for DB operations.
 - **Models** handle all DB access (see `src/Models/`). Use singleton Database class (`src/Config/Database.php`) with PDO. Models now return proper object instances instead of arrays.
 - **Views** (in `src/Views/`) are PHP templates with enhanced Bootstrap 5 UI, rendered with data from controllers. Always escape output with `htmlspecialchars()`.
 - **Monitoring results** are written to the DB with intelligent interval checking and surfaced in the enhanced dashboard and site details views with visual status indicators.
 - **Security:** All views include `security.php` to prevent direct access via `APP_RUNNING` constant.

## Key Workflows
- **Local Development:**
  - Use Docker Compose: `docker-compose up -d --build`
  - Access app at `http://localhost`, phpMyAdmin at `http://localhost:8080`
  - **Debugging:** Xdebug configured for VS Code remote debugging on port 9003
- **Testing:**
  - Run tests: `docker-compose exec app vendor/bin/phpunit`
- **Manual Monitoring:**
  - Run: `docker-compose exec app php monitor.php [--debug]`
  - **Intelligent Monitoring:** Respects per-site interval settings to avoid unnecessary checks
- **Logs:**
  - App logs: `logs/monitor.log`
  - View: `docker-compose exec app tail -f logs/monitor.log`

## Monitoring Workflow
 - `monitor.php` is run by cron (see Dockerfile) or manually. It uses Services to check all sites, logs results to DB and `logs/monitor.log`.
 - **Intelligent Monitoring:** Only checks sites when their configured interval has elapsed since last check.
 - Monitoring results are displayed in the enhanced dashboard and per-site views with visual status indicators.

## Project Conventions
- **Authentication:** Custom session-based, with Admin/User roles. Admins manage users/sites; users have limited access.
- **Security:**
  - Passwords hashed with bcrypt
  - CSRF tokens in forms
  - Prevent self-deletion and last-admin removal
- **Site Monitoring:**
  - Sites have configurable check intervals and SSL monitoring
  - **Intelligent Interval Checking:** Monitor respects per-site intervals to optimize performance
  - Monitoring results stored in DB, shown in enhanced dashboard and site details with visual indicators
- **UI:**
  - Enhanced Bootstrap 5 and Font Awesome 6.4.0 for modern styling
  - Statistics cards, visual status indicators, and improved responsive design
  - Use `Views/partials/` for shared UI (header, navigation)
- **Database:**
  - Schema in `config/database/schema.sql`
  - Access via MySQL CLI or phpMyAdmin
- **Environment:**
  - Config via `.env` (not committed)
  - See README for required variables

## Non-Obvious Conventions
 - Always escape output in views with `htmlspecialchars()`.
 - Session/auth logic is custom (see `src/Services/AuthService.php` and `src/Models/Session.php`).
 - Never allow a user to delete their own account or the last admin (see controllers).
 - All development/testing is Dockerized; use provided Docker Compose commands.
 - Update DB schema in `config/database/schema.sql` if adding new features needing DB changes.
 - **URL Routing:** Uses `?action=route_name` pattern (e.g., `?action=site_details&id=1`), mapped in `Router.php`.
 - **View Security:** All views must include `require_once __DIR__ . '/security.php';` at the top.
 - **Database Pattern:** Models use singleton Database class with PDO; all queries use prepared statements.
 - **CLI Monitoring:** `monitor.php` accepts `--debug` flag for verbose output during development.

## Patterns & Examples
- **Adding a Monitoring Type:**
  1. Add service in `src/Services/`
  2. Update `monitor.php` to use new service
  3. Update DB/schema if needed
- **Views:**
  - Use PHP templates in `Views/`, pass data from controllers
  - Escape output with `htmlspecialchars()`
- **Testing:**
  - Place unit/integration tests in `tests/`
  - Example: `tests/Unit/UptimeMonitorTest.php`

## Integration Points
- **Docker:**
  - All services run in containers (see `docker-compose.yml`)
  - Nginx config in `config/nginx/`
- **External:**
  - phpMyAdmin for DB management
  - Planned: Email/SMS, API, webhooks (see README Roadmap)

## File/Directory Reference
- `src/` — Main app code (MVC)
- `public/` — Web entrypoint
- `monitor.php` — CLI monitoring script
- `config/` — DB and Nginx config
- `logs/` — App logs
- `tests/` — PHPUnit tests

---

**If you are unsure about a workflow or convention, check `README.md` or `PROJECT_SETUP_INSTRUCTIONS.md` for details.**

