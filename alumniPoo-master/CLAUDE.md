# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**alumniPoo** is a complete, production-ready PHP web application for alumni profile management for CNDA (a French alumni network). The application provides secure authentication, profile management, work experience tracking, and contact information management.

## Technology Stack

- **Language**: PHP >= 7.4
- **ORM**: RedBean 5.x
- **Database**: MySQL 5.7+
- **Autoloader**: PSR-4 (Formulair namespace to src/Formulair/)
- **Web Framework**: Custom MVC built on top of RedBean
- **Session Management**: PHP native sessions

## Application Architecture

### MVC Structure

The application follows a custom MVC pattern:

```
core/              → Application core (configuration, database, routing)
src/Formulair/
  ├── Controller/  → HTTP request handlers
  ├── Service/     → Business logic layer
  ├── Model/       → RedBean entity models
  ├── Middleware/  → Authentication & session management
  └── DataFixtures/→ Database seeding
web/               → Public entry point
src/views/         → View templates (HTML)
```

### Request Flow

```
HTTP Request
    ↓
web/index.php (Router)
    ↓
core/Router.php (Route matching)
    ↓
Controller (Request handling)
    ↓
Service (Business logic)
    ↓
Model + RedBean (Database operations)
    ↓
View (HTML rendering)
    ↓
HTTP Response
```

### Core Components

**core/Environment.php**
- Manages environment variables from `.env` file
- Supports local configuration overrides
- Usage: `Environment::get('DB_NAME', 'default')`

**core/Database.php**
- Singleton database connection manager
- Initializes RedBean ORM with environment config
- Usage: `Database::init()` in bootstrap

**core/Router.php**
- Simple URL routing system
- Supports RESTful-style routes with parameters
- Methods: `get()`, `post()`, `put()`, `delete()`

**core/Validator.php**
- Server-side data validation
- Rules: required, email, min, max, numeric, alpha, alphanumeric, url, date, regex

**core/Logger.php**
- Application event logging
- Methods: debug(), info(), warning(), error(), critical()
- Logs to `logs/app.log`

### Authentication & Sessions

**Middleware/AuthMiddleware.php**
- Session management helper
- `requireLogin()` - Redirects to login if not authenticated
- `isLoggedIn()` - Checks if user is logged in
- Session stored in `$_SESSION['user_id']`

**Service/AuthService.php**
- User registration and login logic
- Password hashing with bcrypt
- User existence checking

### Views

All views use a base layout template located in `src/views/layouts/base.php`. Each view:
1. Sets `$title` variable
2. Creates `$content` with HTML markup
3. Includes the base layout

Example:
```php
<?php
$title = 'Page Title';
$content = '<div class="card">Content here</div>';
include __DIR__ . '/../layouts/base.php';
?>
```

## Common Development Tasks

### Starting the Application

**Development server:**
```bash
composer run dev-server
```

**Or with Apache:**
- Ensure `mod_rewrite` is enabled
- Point VirtualHost to `web/` directory
- `.htaccess` handles routing

### Loading Test Data

```bash
php src/Formulair/DataFixtures/LoadFixtures.php
```

Creates:
- Database tables
- Reference data (countries, cities, job divisions, contact types)
- 3 test users (admin/admin123, john_doe/password123, jane_smith/password123)

### Adding a New Page

1. **Create controller method** in `src/Formulair/Controller/`:
```php
public function myPage(): string {
    $data = $this->alumniService->getData();
    return $this->view('my_page', ['data' => $data]);
}
```

2. **Add route** in `web/index.php`:
```php
$router->get('/my-page', fn($params) => $controller->myPage());
```

3. **Create view** in `src/views/my_page.php`:
```php
<?php
$title = 'My Page';
$content = '...html...';
include __DIR__ . '/layouts/base.php';
?>
```

### Adding Validation Rules

Edit `core/Validator.php` - add case in `applyRule()` method:
```php
'myRule' => $this->validateMyRule($field, $value),
```

Then implement the validation method.

### Database Queries

Use RedBean Facade in services:
```php
use RedBeanPHP\Facade as R;

// Create
$user = R::dispense('users');
$user->sLogin = 'john';
R::store($user);

// Read
$user = R::load('users', $id);
$users = R::find('users', 'sLogin = ?', ['john']);
$all = R::findAll('users');

// Update
$user->FirstName = 'Jane';
R::store($user);

// Delete
R::trash($user);
```

## Configuration Files

### `.env`
Application configuration - database connection, debug mode, timezone. **Never commit to version control.**

### `.env.example`
Template for `.env` - commit to version control.

### `composer.json`
- Dependencies: RedBean 5.x, PHPUnit (dev)
- Scripts: `dev-server`, `test`
- Autoloading: PSR-4 for `Formulair\\` namespace

### `web/.htaccess`
Apache rewrite rules to route all requests to `index.php`

## Security Considerations

- Passwords hashed with bcrypt (PASSWORD_BCRYPT)
- All user input escaped in views (htmlspecialchars)
- Session-based authentication with PHP native sessions
- Database queries use parameterized queries via RedBean
- Validation on both input (rules) and output (HTML escaping)

## Important Notes

- RedBean automatically creates database tables on first use
- No explicit migrations - schema is implicit
- Model prefix must be `\Formulair\Model\` (matches PSR-4 namespace)
- Environment variables loaded in `core/bootstrap.php`
- All controllers extend `BaseController` for common functionality
- Logs directory created automatically if needed
