# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Platform Notes

**This project runs on Windows (win32)**. When using command-line tools:
- Use Windows Command Prompt or PowerShell commands
- File paths use backslashes (`\`)
- Use `dir` instead of `ls`, `type` instead of `cat`, etc.
- Some Unix/Linux commands may not be available

## Project Overview

This is a **Salvation Admin** application - a comprehensive church management system built with Laravel 12 + Vue 3 + Inertia.js. The application manages three main domains through a modular monolith architecture:

- **Members** (root/default app) - Member management, roles, permissions, communities
- **Fund** - Financial contributions, mass intentions, payment tracking
- **Graveyard** - Cemetery management, obituaries, grave bookings, payment processing

## Common Commands

### Development (Windows Commands)
```cmd
# Start development environment (all services)
composer dev
# Equivalent to: php artisan serve + queue worker + logs + npm run dev

# Start with SSR support
composer dev:ssr

# Frontend only
npm run dev
npm run build
npm run build:ssr

# Code quality
npm run lint
npm run format
npm run format:check
```

### Laravel/PHP (Windows Commands)
```cmd
# Testing
composer test
# Equivalent to: php artisan config:clear && php artisan test

# Database
php artisan migrate
php artisan migrate:fresh --seed
php artisan db:seed

# Cache management
php artisan optimize        # Production cache optimization
php artisan optimize:clear  # Clear all caches (development)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Queue management
php artisan queue:work
php artisan queue:listen --tries=1

# Real-time logs
php artisan pail
php artisan pail --timeout=0

# Audit system (Members module only)
php artisan audit:generate-triggers [table_name]  # Enable audit for specific table
```

## Architecture Overview

### Modular Structure
The application uses a **modular monolith** pattern with three main modules:

```
app/Modules/
├── Members/     # Core member management (root app)
├── Fund/        # Financial management module
├── Graveyard/   # Cemetery & obituary management
```

Each module contains its own:
- **Controllers** (`Http/Controllers/`)
- **Models** (`Models/`)
- **Services** (`Services/`)
- **Requests** (`Http/Requests/`)
- **Resources** (`Http/Resources/`)

### Database Structure
```
database/modules/
├── fund/migrations/
├── fund/seeders/
├── graveyard/migrations/
└── graveyard/seeders/
```

### Frontend Architecture
```
resources/js/
├── app.ts                 # Single Inertia app entry point
├── layouts/AppShell.vue   # Shared layout (topbar + sidebar)
├── PagesMembers/          # Members module pages
├── PagesFund/             # Fund module pages
├── PagesGraveyard/        # Graveyard module pages
└── components/            # Shared UI components
```

### Route Organization
Routes are split by feature and module:
- `routes/web.php` - Main routes + Members module
- `routes/fund.php` - Fund module routes (prefix: `/fund`)
- `routes/graveyard.php` - Graveyard module routes (prefix: `/graveyard`)
- `routes/{feature}.php` - Feature-specific routes (member.php, auth.php, etc.)

## Key Patterns & Conventions

### Inertia Page Rendering
- **Members**: `Inertia::render('members/Page')` → `PagesMembers/Page.vue`
- **Fund**: `Inertia::render('fund/Page')` → `PagesFund/Page.vue`
- **Graveyard**: `Inertia::render('graveyard/Page')` → `PagesGraveyard/Page.vue`

### Permission System
Uses Spatie Laravel Permission with module-specific permissions:
- Super Admin role has full access
- Module access controlled via `can:access-fund`, `can:access-graveyard`
- Route groups protected with role/permission middleware

### Shared Services
Key service classes in `app/Services/`:
- `QueryOptimizationService` - Database query optimization
- `SecurityService` - File upload validation, rate limiting, XSS prevention

### Component Library
Uses **Reka UI** (Vue component library) with custom components in `resources/js/components/`

## AI/OpenAI Integration

The application includes an **Obituary Text Rephrasing** feature:
- Supports OpenAI GPT, Hugging Face, or simple rule-based rephrasing
- Configuration via `OPENAI_API_KEY` environment variable
- API endpoint: `POST /graveyard/obituaries/rephrase-text`
- Used in obituary form textareas with "Rephrase" buttons

## Module-Specific Notes

### Members Module
- Core authentication and user management
- Role/permission system with hierarchical access
- Community and parish management
- Family tree functionality

### Fund Module
- Financial contribution tracking by family
- Mass intention management with different types
- Annual contribution reports with export functionality
- Payment method tracking

### Graveyard Module
- Cemetery plot management (permanent/temporary graves, niches)
- Booking system with payment processing
- **Obituary pages** with public access, QR codes, and condolences
- File cleanup and management tools
- External manager authentication for obituary management

## Testing

Uses **Pest** testing framework:
```cmd
php artisan test                    # Run all tests
php artisan test --filter TestName # Run specific test
```

## Security Features

- CSRF protection on all forms
- Rate limiting on API endpoints
- File upload validation with malicious file detection
- XSS prevention with input sanitization
- SQL injection detection
- Content Security Policy headers
- Password strength validation

## Performance Optimizations

- **Database**: Composite indexes, eager loading, N+1 query prevention
- **Caching**: Route, config, view caching enabled in production
- **Frontend**: Vite with manual chunk splitting, vendor code separation
- **Security**: Built-in security service for common validations

## Audit Logging Status

### ✅ Members Module - PARTIAL IMPLEMENTATION
- Custom audit system with database triggers
- Requires manual setup: `php artisan audit:generate-triggers [table_name]`
- Tracks CREATE, UPDATE, DELETE operations
- Access via `/audit/logs` (superadmin only)

### ❌ Fund Module - NO AUDIT LOGGING
- No audit trails for financial transactions
- Missing coverage for contributions, mass intentions, payments

### ❌ Graveyard Module - NO AUDIT LOGGING
- No audit trails for cemetery operations
- Missing coverage for grave bookings, obituary changes, payments

## Deployment Notes

- **Platform**: Windows (win32) environment
- Supports Laravel Sail for Docker development
- Uses Vite for asset compilation
- Environment-based configuration (see `.env.example`)
- Queue workers recommended for background processing
- File storage in `public/storage` (ensure proper permissions)