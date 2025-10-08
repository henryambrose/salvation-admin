# Obituary Module - Standalone Extraction

## Overview
This package contains all files needed to create a standalone Obituary Management System extracted from the Salvation Admin application.

## What's Included

### Backend Components
- **Controllers**: Complete CRUD operations, payment processing, QR generation
- **Models**: All obituary-related Eloquent models with relationships
- **Services**: Business logic layer (ObituaryService, ObituaryBackgroundService)
- **Policies**: Authorization logic for all operations
- **Commands**: CLI commands for QR generation and expiration management
- **Listeners**: Event listeners for auto-publishing on payment

### Frontend Components
- **Admin Pages**: Full admin interface for managing obituaries, plans, themes, managers
- **Public Pages**: Public-facing obituary viewing pages with condolences
- **External Manager Pages**: Login and management interface for external obituary managers

### Database
- **Migrations**: 7 migration files for complete database schema
- **Seeders**: Pre-populated plans and background themes

### Configuration
- Obituary settings (durations, expiration, grace periods)
- Background theme definitions
- Authentication guard configuration notes

### Routes
- Admin routes (CRUD, management)
- Public routes (viewing, condolences)
- API routes (integrations)
- External manager routes (authentication, editing)

## Quick Start

### 1. Review Documentation
Start by reading **OBITUARY_EXTRACTION_GUIDE.md** for complete setup instructions.

### 2. Key Features
- ✅ Full obituary CRUD operations
- ✅ Multiple subscription plans (Basic, Premium)
- ✅ Payment processing and tracking
- ✅ QR code generation for obituary pages
- ✅ Public condolence submission
- ✅ External manager authentication
- ✅ Background theme customization
- ✅ File upload management (photos, audio)
- ✅ Expiration and renewal handling
- ✅ Text rephrasing (OpenAI integration)
- ✅ Receipt generation
- ✅ File cleanup utilities

### 3. Dependencies Required
- Laravel 12+
- Vue 3
- Inertia.js
- Spatie Laravel Permission
- SimpleSoftwareIO QR Code
- Laravel Sanctum

### 4. Important Notes

#### Decoupling from Graveyard Module
The obituary system currently has dependencies on:
- `PermanentGraveBooking` and `TemporaryGraveBooking` models
- Member search functionality

**Options for standalone app:**
1. Keep these models if you need grave management
2. Replace with standalone deceased person records
3. Make booking relationships optional (nullable)

#### Authentication Guards
You'll need to add an `external` guard to `config/auth.php` for external manager authentication. See the guide for details.

### 5. Migration Path
1. Create new Laravel application
2. Copy all files maintaining directory structure
3. Update namespaces from `Modules\Graveyard\` to your app namespace
4. Install dependencies (see guide)
5. Run migrations and seeders
6. Configure authentication guards
7. Update route names/prefixes
8. Test thoroughly

## File Structure

```
obituary-extraction/
├── app/
│   ├── Console/Commands/          # CLI commands
│   ├── Listeners/                 # Event listeners
│   └── Modules/Graveyard/
│       ├── Helpers/               # Utility helpers
│       ├── Http/Controllers/      # Controllers
│       ├── Models/                # Eloquent models
│       ├── Policies/              # Authorization
│       ├── Providers/             # Service providers
│       └── Services/              # Business logic
├── config/                        # Configuration files
├── database/
│   └── modules/graveyard/
│       └── database/
│           ├── migrations/        # Database migrations
│           └── seeders/           # Database seeders
├── resources/
│   ├── js/
│   │   ├── PagesGraveyard/       # Admin Vue pages
│   │   └── pages/Public/Obituary/ # Public Vue pages
│   └── views/graveyard/receipts/  # Blade templates
├── routes/
│   └── obituary-routes.php        # All routes
├── OBITUARY_EXTRACTION_GUIDE.md   # Complete setup guide
└── README.md                       # This file
```

## Support

For detailed instructions on:
- Installation and setup
- Decoupling from parent application
- Database schema modifications
- Environment configuration
- Security considerations
- Testing checklist
- Deployment recommendations

Please refer to **OBITUARY_EXTRACTION_GUIDE.md**

## License

This code is extracted from the Salvation Admin project. Please ensure you have appropriate rights to use this code in your new application.

---

**Created:** 2025-10-06
**Source:** Salvation Admin (Laravel 12 + Vue 3 + Inertia.js)
**Module:** Obituary Management System
