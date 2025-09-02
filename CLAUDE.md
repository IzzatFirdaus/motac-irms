
# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

MOTAC Integrated Resource Management System (IRMS) is a Laravel 11 application designed for the Ministry of Tourism, Arts and Culture, Malaysia. It manages ICT equipment loans and includes a comprehensive helpdesk/ticketing system.

## Development Commands

### Backend (PHP/Laravel)

```bash
# Install dependencies
composer install

# Database setup
php artisan migrate --seed

# Run tests
php artisan test                    # All tests
php artisan test --filter=Feature  # Feature tests only
php artisan test --filter=Unit     # Unit tests only

# Code quality
vendor/bin/pint --dirty            # Format code (required before commits)
vendor/bin/larastan analyse        # Static analysis
vendor/bin/rector process          # Code refactoring

# Development server
php artisan serve                   # Start development server
```

### Frontend (Node.js/Vite)

```bash
# Install dependencies
npm install

# Development
npm run dev        # Start Vite development server
npm run watch      # Watch for changes
npm run build      # Build for production
npm run preview    # Preview production build

# Linting
npm run lint:css       # Lint CSS files
npm run lint:css:fix   # Fix CSS linting issues
```

## Architecture Overview

### Framework Stack

- **Laravel 11** with PHP 8.2+ (maintains Laravel 10 structure)
- **Livewire 3** for dynamic UI components
- **Jetstream** with Sanctum for authentication
- **Vuexy** Bootstrap theme integration
- **Spatie Packages** for permissions and activity logging

### Key Architectural Patterns

#### Multi-Language Support

- Custom `SuffixedTranslator` replaces Laravel's default translator
- Translation files use suffix pattern: `app_en.php`, `app_ms.php`, `forms_en.php`, `forms_ms.php`
- `LocaleMiddleware` handles automatic locale detection
- Default locale: Bahasa Melayu (`ms`), fallback: English (`en`)

#### Role-Based Access Control (RBAC)

- Spatie Permission package with custom middleware
- Key roles: Admin, IT Admin, BPM Staff, Approver, User, Helpdesk Agent
- Grade-based authorization with `CheckUserGrade` and `CheckGradeLevel` middleware
- Permission-based route protection throughout

#### Livewire Component Architecture

- Components use `App\Livewire` namespace (Livewire 3)
- Organized by domain: `ResourceManagement`, `Settings`, `Helpdesk`, `Dashboard`
- Heavy use of Livewire for forms, tables, and interactive elements
- Components follow single responsibility principle

#### Database & Models

- Eloquent relationships extensively used
- `CreatedUpdatedDeletedBy` trait for audit trails
- Activity logging via Spatie package
- Factory and Seeder support for testing

### Directory Structure

#### Backend (Laravel)

```text
app/
├── Http/
│   ├── Controllers/          # Traditional controllers for complex operations
│   ├── Middleware/           # Custom middleware (RBAC, locale, grade checks)
│   └── Requests/             # Form request validation classes
├── Livewire/                 # Livewire components organized by domain
│   ├── Dashboard/
│   ├── ResourceManagement/
│   ├── Settings/
│   └── Helpdesk/
├── Models/                   # Eloquent models with relationships
├── Services/                 # Business logic services
├── Policies/                 # Authorization policies
├── Notifications/            # Email/in-app notifications
└── Helpers/                  # Global helper functions
```

#### Frontend Assets

```text
resources/
├── views/
│   ├── components/         # Blade components
│   ├── livewire/           # Livewire component views
│   └── dashboard/          # Role-specific dashboard views
├── lang/
│   ├── en/                 # English translations (suffixed files)
│   └── ms/                 # Bahasa Melayu translations (suffixed files)
└── assets/                 # Frontend assets (CSS, JS, images)
```

## Key Features & Modules

### Equipment Loan Management

- Loan applications with approval workflow
- Equipment inventory management
- Issuance and return tracking
- Grade-based approval hierarchy

### Helpdesk System

- Ticket creation and management
- Category and priority system
- Assignment and resolution workflow
- File attachments support

### Multi-Tenant Dashboard

- Role-specific dashboards (Admin, User, BPM, IT Admin, Approver)
- Real-time statistics and charts
- Activity monitoring

## Testing Guidelines

### Test Environment

- Uses in-memory SQLite database (`:memory:`)
- Test environment configured in `phpunit.xml`
- Factories and seeders for test data

### Testing Patterns

- Feature tests for HTTP/Livewire interactions
- Unit tests for services and utilities
- Use model factories rather than manual setup
- Follow Laravel 11 testing conventions

## Code Standards

### PHP Standards

- Strict typing: `declare(strict_types=1);`
- PSR-12 code style enforced by Pint
- Constructor property promotion
- Explicit return types
- PHPDoc blocks for complex arrays

### Livewire Standards

- Single root element per component
- Use `wire:key` in loops
- Lifecycle hooks for initialization
- `wire:model.live` for real-time updates
- Proper loading states with `wire:loading`

### Translation Standards

- Use suffixed translation files
- Keys should be descriptive: `dashboard.loan_applications_title`
- Fallback to English when Bahasa Melayu not available
- Test translation keys exist before use

## Environment Setup

### Required Environment Variables

```bash
APP_NAME="Sistem Pengurusan Sumber MOTAC"
APP_TIMEZONE="Asia/Kuala_Lumpur"
APP_LOCALE="ms"
APP_FALLBACK_LOCALE="en"
DB_CONNECTION="mysql"  # or sqlite for testing
```

### MOTAC-Specific Settings

- Organization: Kementerian Pelancongan, Seni dan Budaya Malaysia
- Division: Bahagian Pengurusan Maklumat (BPM)
- Default theme: `theme-motac`
- Primary colors: MOTAC blue (#0047AB) and gold (#FFD700)

## Important Notes

- Always run `vendor/bin/pint --dirty` before committing
- Use `composer install` and `npm install` for dependency management
- Database migrations include seeders for MOTAC-specific data
- Custom translation system requires cache clearing when adding new keys
- Grade-based permissions are hierarchical and enforced at route level
- Livewire components handle most UI interactions, controllers for business logic
