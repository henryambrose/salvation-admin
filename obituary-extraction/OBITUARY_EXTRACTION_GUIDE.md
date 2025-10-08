# Obituary Module Extraction Guide

## Overview
This document provides a comprehensive list of all files and dependencies needed to extract the Obituary module from the Salvation Admin application and create a standalone application.

---

## 📁 File Structure

### Backend Files

#### **Controllers** (`app/Modules/Graveyard/Http/Controllers/`)
- `ObituaryController.php` - Public obituary viewing, condolences submission
- `ObituaryManagementController.php` - Admin management (CRUD, payment, publishing)
- `ObituaryPlanController.php` - Obituary plans management
- `ObituaryBackgroundThemeController.php` - Background themes management
- `ObituaryManagerController.php` - External manager authentication & management

#### **Models** (`app/Modules/Graveyard/Models/`)
- `ObituaryPage.php` - Main obituary model
- `ObituaryPlan.php` - Subscription/pricing plans model
- `ObituaryPayment.php` - Payment tracking model
- `ObituaryCondolence.php` - Condolences model
- `ObituaryManager.php` - External managers model
- `ObituaryBackgroundTheme.php` - Background themes model

#### **Services** (`app/Modules/Graveyard/Services/`)
- `ObituaryService.php` - Core business logic
- `ObituaryBackgroundService.php` - Background styling service

#### **Policies** (`app/Modules/Graveyard/Policies/`)
- `ObituaryPolicy.php` - Authorization for obituaries
- `ObituaryPagePolicy.php` - Authorization for obituary pages
- `ObituaryPlanPolicy.php` - Authorization for plans

#### **Helpers** (`app/Modules/Graveyard/Helpers/`)
- `BackgroundHelper.php` - Background utility functions (may be used)
- `BackgroundThemeHelper.php` - Theme utility functions (may be used)

---

### Database Files

#### **Migrations** (`database/modules/graveyard/database/migrations/`)
- `2025_09_11_022950_create_obituary_plans_table.php`
- `2025_09_11_022953_create_obituary_pages_table.php`
- `2025_09_11_022958_create_obituary_condolences_table.php`
- `2025_09_11_023003_create_obituary_payments_table.php`
- `2025_09_15_120000_create_obituary_background_themes_table.php`
- `2025_09_18_081331_create_obituary_managers_table.php`
- `2025_10_05_154004_add_receipt_fields_to_obituary_payments_table.php`

#### **Seeders** (`database/modules/graveyard/database/seeders/`)
- `ObituaryPlanSeeder.php`
- `ObituaryBackgroundThemeSeeder.php`

---

### Console Commands (`app/Console/Commands/`)
- `GenerateObituaryQrCode.php` - QR code generation
- `ManageObituaryExpiration.php` - Expiration management
- `MigrateObituaryExpirations.php` - Migration for expiration logic

---

### Event Listeners (`app/Listeners/`)
- `AutoPublishObituaryOnPayment.php` - Auto-publish on payment completion

---

### Frontend Files

#### **Admin Pages** (`resources/js/PagesGraveyard/`)
- `Obituaries/Index.vue` - List view
- `Obituaries/Create.vue` - Create form
- `Obituaries/Edit.vue` - Edit form
- `Obituaries/Show.vue` - Detail view
- `Obituaries/Cleanup.vue` - File cleanup utility
- `Obituaries/Condolences.vue` - Condolence management
- `Obituaries/IndexSimple.vue` - Simplified list
- `obituary-plans/Index.vue` - Plans list
- `obituary-plans/Create.vue` - Create plan
- `obituary-plans/Edit.vue` - Edit plan
- `obituary-plans/Show.vue` - Plan details
- `ObituaryBackgroundThemes/Index.vue` - Themes list
- `ObituaryBackgroundThemes/Create.vue` - Create theme
- `ObituaryBackgroundThemes/Edit.vue` - Edit theme
- `ObituaryBackgroundThemes/Show.vue` - Theme preview
- `ObituaryManagers/Index.vue` - Managers list
- `ObituaryManagers/Create.vue` - Create manager
- `ObituaryManagers/Edit.vue` - Edit manager
- `ObituaryManagers/Show.vue` - Manager details

#### **Public Pages** (`resources/js/pages/Public/Obituary/`)
- `Show.vue` - Public obituary page
- `NotFound.vue` - 404 page
- `PaymentPending.vue` - Payment/status messages
- `ExternalLogin.vue` - External manager login
- `ExternalDashboard.vue` - External manager dashboard
- `ExternalEdit.vue` - External manager edit page
- `ExternalCondolences.vue` - External manager condolences view

---

### Configuration Files
- `config/obituary.php` - Duration, expiration, grace period settings
- `config/obituary_backgrounds.php` - Background theme definitions
- `config/auth.php` - Add external manager guard configuration

---

### Routes
Extract from `routes/graveyard.php`:
- Lines 180-270: Obituary management routes (admin)
- Lines 282-318: API routes
- Lines 321-338: Public routes (obituary viewing, condolences)
- Lines 339-435: Alternative public obituary route with inline logic
- Lines 438-468: Condolence submission route
- Lines 471-486: External manager authentication routes

---

### Views (Blade Templates)
- `resources/views/graveyard/receipts/obituary-payment.blade.php` - Payment receipt template

---

### Storage/Assets
- `storage/app/public/qr-codes/` - QR code images (auto-generated)
- `public/storage/backgrounds/` - Background images referenced in config

---

## 🔗 Dependencies

### Laravel Packages
- **Spatie Laravel Permission** - Role/permission system (for admin access control)
- **Inertia.js** - Frontend-backend bridge
- **SimpleSoftwareIO/simple-qrcode** - QR code generation
- **Laravel Sanctum** - API authentication (for external managers)

### Vue/Frontend Dependencies
- **Vue 3** - Frontend framework
- **Inertia.js Vue adapter** - For Vue-Laravel integration
- **Reka UI** - Component library (used in forms/UI)
- **Vite** - Build tool

### External Services (Optional)
- **OpenAI API** - Text rephrasing feature (see `OPENAI_API_KEY` in `.env`)
- Alternative: Hugging Face API or rule-based rephrasing

---

## 🔄 Relationships with Other Modules

### **Direct Dependencies**
1. **Members Module** (for family/member association)
   - `ObituaryPage` may reference member records
   - Used for searching members during obituary creation

2. **Graveyard Booking System**
   - `PermanentGraveBooking` model - Links obituaries to grave bookings
   - `TemporaryGraveBooking` model - Links obituaries to temporary graves
   - `ValidMember` model - Associates obituaries with deceased members

### **Optional Dependencies** (Can be decoupled)
- Payment integration with graveyard payment system
- Service types from graveyard module

---

## 📝 Key Configuration Changes Needed

### Environment Variables (`.env`)
```env
# Obituary Settings
OBITUARY_BASIC_DURATION_DAYS=90
OBITUARY_PREMIUM_DURATION_DAYS=365
OBITUARY_WARNING_DAYS=7
OBITUARY_AUTO_PROCESS_EXPIRED=true
OBITUARY_GRACE_PERIOD_DAYS=30

# OpenAI (Optional - for text rephrasing)
OPENAI_API_KEY=your-api-key-here
```

### Authentication Guard (`config/auth.php`)
Add external manager guard:
```php
'guards' => [
    // ... existing guards
    'external' => [
        'driver' => 'session',
        'provider' => 'external_managers',
    ],
],

'providers' => [
    // ... existing providers
    'external_managers' => [
        'driver' => 'eloquent',
        'model' => Modules\Graveyard\Models\ObituaryManager::class,
    ],
],
```

### Service Provider Registration
Register in `config/app.php`:
```php
'providers' => [
    // ...
    Modules\Graveyard\Providers\ModuleServiceProvider::class,
],
```

---

## 🚀 Migration Steps for Standalone App

### 1. **Create New Laravel App**
```bash
composer create-project laravel/laravel obituary-app
```

### 2. **Copy Files**
- Copy all backend files maintaining directory structure
- Copy all frontend files
- Copy migrations and seeders
- Copy configuration files
- Copy views/templates

### 3. **Update Namespaces**
- Change `Modules\Graveyard\` namespace to your new app namespace
- Update all references in controllers, models, services

### 4. **Install Dependencies**
```bash
composer require spatie/laravel-permission
composer require simplesoftwareio/simple-qrcode
composer require laravel/sanctum
npm install @inertiajs/vue3
```

### 5. **Run Migrations**
```bash
php artisan migrate
php artisan db:seed --class=ObituaryPlanSeeder
php artisan db:seed --class=ObituaryBackgroundThemeSeeder
```

### 6. **Configure Routes**
- Move routes from `routes/graveyard.php` to `routes/web.php`
- Remove `/graveyard` prefix if desired
- Update route names

### 7. **Setup Storage**
```bash
php artisan storage:link
mkdir -p storage/app/public/qr-codes
mkdir -p storage/app/public/obituaries
mkdir -p storage/app/public/backgrounds
```

### 8. **Configure Permissions**
```bash
php artisan permission:create-role "Super Admin"
php artisan permission:create-permission "access-obituary"
php artisan permission:create-permission "manage-obituary"
```

---

## ⚠️ Breaking Changes to Address

### 1. **Decouple from Graveyard Bookings**
Current obituaries are linked to:
- `permanent_grave_booking_id`
- `temporary_grave_booking_id`

**Options:**
- **A)** Keep booking system (recommended if you need grave management)
- **B)** Replace with standalone deceased person records
- **C)** Make booking relationships optional

### 2. **Member Search Integration**
Obituary creation uses member search from Members module.

**Solution:** Create standalone person/deceased management or import member data.

### 3. **Payment System**
Current system integrates with graveyard payment processing.

**Options:**
- Extract payment system as well
- Integrate with external payment gateway (Stripe, PayPal)
- Build standalone payment module

---

## 🧪 Testing Checklist

After extraction, test:
- [ ] Obituary CRUD operations
- [ ] Public obituary page viewing
- [ ] QR code generation and download
- [ ] Condolence submission
- [ ] External manager authentication
- [ ] Payment processing
- [ ] Background theme selection
- [ ] Plan management
- [ ] Expiration handling
- [ ] File cleanup utilities
- [ ] Receipt generation
- [ ] Text rephrasing (if using OpenAI)

---

## 📚 Additional Documentation Needed

1. **API Documentation** - Document all public/external APIs
2. **User Guide** - For admin and external managers
3. **Deployment Guide** - Server requirements, optimization
4. **Security Review** - File upload validation, XSS prevention, rate limiting

---

## 🔒 Security Considerations

- File upload validation (profile images, gallery, audio)
- Rate limiting on public routes (condolences, downloads)
- CSRF protection on all forms
- XSS prevention in user-submitted content
- SQL injection prevention (using Eloquent ORM)
- Authentication for external managers
- Permission checks for admin operations

---

## 💡 Recommended Enhancements for Standalone App

1. **Multi-tenancy Support** - Allow multiple organizations
2. **Email Notifications** - Notify on condolences, expiration
3. **Social Media Sharing** - Share obituary pages
4. **Analytics Dashboard** - View counts, condolence stats
5. **Mobile App** - Companion mobile application
6. **Payment Gateway Integration** - Stripe/PayPal integration
7. **Theme Customization** - More background options
8. **Multi-language Support** - Internationalization
9. **SEO Optimization** - Meta tags, structured data
10. **Print-friendly Layout** - Print obituary pages

---

## 📦 Files to Include in ZIP

All files listed in the sections above, organized in the following structure:

```
obituary-module/
├── app/
│   ├── Console/Commands/
│   ├── Listeners/
│   └── Modules/Graveyard/
│       ├── Helpers/
│       ├── Http/Controllers/
│       ├── Models/
│       ├── Policies/
│       └── Services/
├── config/
│   ├── obituary.php
│   └── obituary_backgrounds.php
├── database/
│   └── modules/graveyard/
│       ├── database/migrations/
│       └── database/seeders/
├── resources/
│   ├── js/
│   │   ├── PagesGraveyard/
│   │   │   ├── Obituaries/
│   │   │   ├── obituary-plans/
│   │   │   ├── ObituaryBackgroundThemes/
│   │   │   └── ObituaryManagers/
│   │   └── pages/Public/Obituary/
│   └── views/graveyard/receipts/
├── routes/
│   └── obituary-routes.php (extracted from graveyard.php)
└── OBITUARY_EXTRACTION_GUIDE.md (this file)
```

---

## 📞 Support & Next Steps

After extracting the files:
1. Review all dependencies and relationships
2. Plan database schema changes if decoupling from bookings
3. Set up development environment
4. Run migrations and seeders
5. Test all features thoroughly
6. Deploy to staging environment
7. Conduct security audit
8. Launch production application

---

**Created:** 2025-10-06
**Version:** 1.0
**Module:** Obituary Management System
**Original App:** Salvation Admin (Laravel 12 + Vue 3 + Inertia.js)
