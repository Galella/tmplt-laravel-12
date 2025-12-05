# Laravel Project Context

## Project Overview

This is a fresh Laravel 12 application skeleton, which is a web application framework with expressive, elegant syntax. The project is set up with the basic Laravel structure and uses PHP 8.2 or higher.

### Key Technologies:
- **Laravel 12** - PHP web application framework
- **PHP 8.2+** - Primary programming language
- **Tailwind CSS 4** - CSS framework for styling
- **Vite** - Build tool for frontend assets
- **SQLite** - Default database (configured in .env)
- **Laravel Pint** - Code formatter
- **PHPUnit** - Testing framework

### Architecture:
- MVC (Model-View-Controller) pattern
- PSR-4 autoloading structure
- Composer for dependency management
- Vite for frontend asset compilation
- Blade templating engine

## Directory Structure

```
├── app/                    # Application source code
│   ├── Http/               # HTTP controllers, middleware
│   ├── Models/             # Eloquent models
│   └── Providers/          # Service providers
├── bootstrap/              # Framework bootstrap files
├── config/                 # Configuration files
├── database/               # Database migrations, seeds, factories
├── public/                 # Public web assets and entry point
├── resources/              # Views, CSS, JS, and other assets
│   ├── css/                # CSS files
│   ├── js/                 # JavaScript files
│   └── views/              # Blade templates
├── routes/                 # Application routes
├── storage/                # Compiled templates, logs, cache
├── tests/                  # Test files
├── vendor/                 # Composer dependencies
├── artisan                 # Command-line interface script
├── composer.json           # PHP dependencies
├── package.json            # Node.js dependencies
├── .env                    # Environment variables
├── .env.example            # Example environment file
├── vite.config.js          # Vite build configuration
└── README.md               # Project documentation
```

## Building and Running

### Initial Setup:
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Copy environment file (if not exists)
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Build frontend assets
npm run build
```

### Development Commands:
```bash
# Start development server with hot-reload
npm run dev

# Or use the Laravel development command (includes concurrent processes)
composer run dev

# Alternative: Start PHP development server
php artisan serve
```

### Testing:
```bash
# Run unit tests
composer run test

# Or run tests directly
php artisan test
```

### Asset Compilation:
```bash
# Build assets for production
npm run build

# Compile assets for development with hot-reload
npm run dev
```

## Key Configuration Files

### composer.json
- Defines PHP dependencies and development scripts
- Includes custom setup and development scripts
- Contains Laravel framework and development tools

### package.json
- Frontend dependencies (Tailwind CSS, Vite, Axios)
- Build scripts for frontend assets
- Development dependencies

### .env
- Environment-specific configuration
- Database connection settings (SQLite by default)
- Mail settings, cache drivers, queue connections

### vite.config.js
- Vite configuration for Laravel integration
- Tailwind CSS integration
- Asset input/output configuration

## Database Configuration

By default, the application is configured to use SQLite with a database file stored in the project root. The database configuration can be changed in the `.env` file:

- `DB_CONNECTION=sqlite` (default)
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (for MySQL/PostgreSQL)

## Laravel Artisan Commands

Useful Artisan commands for development:

```bash
# Generate new components
php artisan make:controller ControllerName
php artisan make:model ModelName
php artisan make:migration MigrationName
php artisan make:seeder SeederName

# Database operations
php artisan migrate
php artisan migrate:rollback
php artisan db:seed

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Code formatting
php artisan pint

# Serve application
php artisan serve
```

## Development Conventions

### Coding Standards
- PSR-4 autoloading standards
- Laravel's coding style conventions
- Laravel Pint for code formatting
- Follow Laravel's directory structure conventions

### Testing Practices
- PHPUnit for unit and feature testing
- PestPHP can be integrated if desired (configured in composer.json)
- Tests organized in the `tests/` directory
- Follow Laravel testing documentation patterns

### Frontend Conventions
- Tailwind CSS for styling
- Vite for asset compilation
- Blade templating engine
- JavaScript/TypeScript in `resources/js/`
- CSS in `resources/css/`