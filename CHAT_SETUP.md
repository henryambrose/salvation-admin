# Catholic AI Chat Setup Guide

This guide will help you set up the Catholic AI Chat feature using the CatéGPT API, which provides specialized Catholic teachings and guidance.

## Prerequisites

- Laravel application with authentication
- Superadmin user role configured
- CatéGPT API key

## Setup Instructions

### 1. Add CatéGPT API Key to Environment

Add the following line to your `.env` file:

```env
CATEGPT_API_KEY=nOgpLY1egWPhBtxbwRXkgX0BM4tYeaLnSHENdbErYem8MBMXTDTa5ahhNrVV7U9ulFFU1sMfiT+DE/AFu6Zwia2pgLEUfD/v9RHl1m6jHLpcpBuNa+WkRQF+UbJ/agsVsGlojgT07N68A6bE7EghJg==
```

### 2. Clear Configuration Cache

After adding the API key, clear the configuration cache:

```bash
php artisan config:clear
php artisan cache:clear
```

### 3. Verify Superadmin Access

Ensure you have a user with the 'superadmin' role. You can check this in your database or create the role if it doesn't exist:

```bash
php artisan tinker
```

Then run:
```php
$user = \App\Models\User::find(1); // Replace with your user ID
$user->assignRole('superadmin');
```

### 4. Test the Chat Feature

1. Log in as a superadmin user
2. Navigate to `/chat` in your application
3. Try asking a Catholic-related question like:
   - "What are the different types of prayer?"
   - "Explain the significance of the Eucharist"
   - "What does the Church teach about forgiveness?"

## Features

The Catholic AI Chat includes:

- **Catholic-Specific Responses**: Powered by CatéGPT, specialized in Catholic teachings
- **References**: Links to official Church documents and sources
- **Keywords**: Relevant theological terms and concepts
- **Related Questions**: Suggestions for further exploration
- **Conversational Mode**: Natural, flowing responses
- **Multi-language Support**: Can be configured for different languages

## API Endpoints

- `POST /api/chat/send` - Send a message to CatéGPT
- `GET /api/chat/history` - Get chat history (placeholder)
- `GET /api/chat/answer/{uniqueID}` - Retrieve a specific answer by ID

## Troubleshooting

### Common Issues

1. **"CatéGPT API key not configured"**
   - Ensure `CATEGPT_API_KEY` is set in your `.env` file
   - Clear config cache: `php artisan config:clear`

2. **"Unauthorized. Superadmin access required"**
   - Verify your user has the 'superadmin' role
   - Check role assignments in the database

3. **API Connection Issues**
   - Check your internet connection
   - Verify the CatéGPT API key is valid
   - Check Laravel logs for detailed error messages

### Debug Steps

1. Check Laravel logs: `tail -f storage/logs/laravel.log`
2. Test API access: Visit `/api/chat/test`
3. Verify user permissions in the database
4. Check browser console for JavaScript errors

## CatéGPT API Documentation

For more information about the CatéGPT API, visit:
https://categpt.chat/documentation/

## Support

If you encounter issues, check the Laravel logs for detailed error messages and ensure all prerequisites are met. 