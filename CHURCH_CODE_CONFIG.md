# Church Code Configuration

This document explains how to configure the church code used throughout the Salvation Admin application.

## Overview

The church code is used for member numbering and family identification throughout the application. By default, it's set to 'SAL' but can be configured via environment variables. The church code is **NOT stored in the database** - it's purely a configuration value that determines how member and family numbers are generated.

## Configuration

### Environment Variable

Add the following line to your `.env` file:

```env
CHURCH_CODE=SAL
```

You can change `SAL` to any 3-letter code that represents your church.

### Default Value

If `CHURCH_CODE` is not set in the `.env` file, the application will default to `SAL`.

## Usage

The church code is used in the following places:

1. **Member Numbering**: Member numbers are generated in the format `YYYY-{CHURCH_CODE}-MNNNNN`
2. **Family Numbering**: Family numbers are generated in the format `{CHURCH_CODE}-XXX`
3. **Frontend Display**: The church code is displayed in member forms and listings
4. **Validation**: Family number validation uses the configured church code

## Files Modified

The following files have been updated to support configurable church codes:

### Backend (PHP)
- `config/app.php` - Added church_code configuration
- `app/Http/Middleware/HandleInertiaRequests.php` - Passes church_code to frontend
- `app/Http/Controllers/MemberController.php` - Uses configurable church code
- `app/Models/Member.php` - Uses configurable church code
- `app/Services/FamilyNumberingService.php` - Uses configurable church code

### Frontend (Vue/TypeScript)
- `resources/js/types/index.d.ts` - Added church_code to SharedData interface
- `resources/js/pages/member/Member.vue` - Uses configurable church code
- `resources/js/pages/member/Index.vue` - Uses configurable church code
- `resources/js/components/AddMemberModal.vue` - Uses configurable church code
- `resources/js/components/ViewMemberModal.vue` - Uses configurable church code

## Example

If you set `CHURCH_CODE=ABC` in your `.env` file:

- Member numbers will be generated as: `2024-ABC-M000001`
- Family numbers will be generated as: `ABC-001`
- All forms and displays will show `ABC` instead of `SAL`

## Migration

If you're changing the church code for an existing installation:

1. Update the `CHURCH_CODE` in your `.env` file
2. Clear the application cache: `php artisan config:clear`
3. Restart your application

**Note**: Since the church code is not stored in the database, changing it will only affect new members and families. Existing data will retain their original church codes in their member/family numbers. 