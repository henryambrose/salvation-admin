# Chat API Troubleshooting Guide

If you're encountering the "Sorry, I encountered an error" message, follow these steps to debug the issue:

## 1. Check CatéGPT API Key

First, ensure your CatéGPT API key is properly configured:

1. **Add to .env file:**
   ```env
   CATEGPT_API_KEY=nOgpLY1egWPhBtxbwRXkgX0BM4tYeaLnSHENdbErYem8MBMXTDTa5ahhNrVV7U9ulFFU1sMfiT+DE/AFu6Zwia2pgLEUfD/v9RHl1m6jHLpcpBuNa+WkRQF+UbJ/agsVsGlojgT07N68A6bE7EghJg==
   ```

2. **Clear config cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

3. **Verify the key is loaded:**
   ```bash
   php artisan tinker
   echo config('services.categpt.key');
   ```

## 2. Test Chat Access

Test if the chat routes are accessible:

1. **Test chat interface:**
   ```
   GET /chat
   ```
   This should show the Catholic AI Chat interface

2. **Check if you're logged in as superadmin:**
   - Ensure you have the 'superadmin' role
   - Check your user roles in the database

## 3. Check Laravel Logs

Check the Laravel logs for detailed error information:

```bash
tail -f storage/logs/laravel.log
```

Look for entries related to:
- `Chat API Error`
- `CatéGPT API Error`
- `CatéGPT API key not configured`

## 4. Test CatéGPT API Directly

Test if your CatéGPT API key works:

```bash
curl -X POST https://categpt.chat/api/question \
  -H "Authorization: nOgpLY1egWPhBtxbwRXkgX0BM4tYeaLnSHENdbErYem8MBMXTDTa5ahhNrVV7U9ulFFU1sMfiT+DE/AFu6Zwia2pgLEUfD/v9RHl1m6jHLpcpBuNa+WkRQF+UbJ/agsVsGlojgT07N68A6bE7EghJg==" \
  -H "Content-Type: application/json" \
  -d '{
    "question": "What is prayer?",
    "lang": "en",
    "modechat": 1
  }'
```

## 5. Common Issues and Solutions

### Issue: "CatéGPT API key not configured"
**Solution:** Add `CATEGPT_API_KEY=your_key` to your `.env` file

### Issue: "Unauthorized. Superadmin access required"
**Solution:** Ensure your user has the 'superadmin' role

### Issue: "Failed to get response from CatéGPT service"
**Solution:** Check your CatéGPT API key validity and credits

### Issue: CSRF Token errors
**Solution:** The chat now uses web routes with proper session authentication

## 6. Debug Steps

1. **Check browser console** for JavaScript errors
2. **Check network tab** in browser dev tools for failed requests
3. **Check Laravel logs** for backend errors
4. **Verify user permissions** and roles
5. **Test chat endpoint** directly

## 7. Manual Testing

You can test the chat API manually using curl:

```bash
# First, get a session cookie by logging in through the web interface
# Then use that cookie for the API call

curl -X POST http://your-domain.com/chat/send \
  -H "Content-Type: application/json" \
  -H "X-Requested-With: XMLHttpRequest" \
  -b "laravel_session=your_session_cookie" \
  -d '{
    "message": "What is prayer?",
    "conversation_history": []
  }'
```

## 8. Environment Variables

Ensure these are set in your `.env`:

```env
CATEGPT_API_KEY=nOgpLY1egWPhBtxbwRXkgX0BM4tYeaLnSHENdbErYem8MBMXTDTa5ahhNrVV7U9ulFFU1sMfiT+DE/AFu6Zwia2pgLEUfD/v9RHl1m6jHLpcpBuNa+WkRQF+UbJ/agsVsGlojgT07N68A6bE7EghJg==
APP_DEBUG=true  # For development debugging
```

## 9. Permissions Check

Verify your user has the correct permissions:

```php
// In tinker or a test route
$user = auth()->user();
dd($user->getRoleNames()); // Should include 'superadmin'
```

## 10. Route Testing

Test if the routes are properly registered:

```bash
php artisan route:list | grep chat
```

This should show:
- `GET /chat`
- `POST /chat/send`
- `GET /chat/history`
- `GET /chat/answer/{uniqueID}`

## Still Having Issues?

If you're still experiencing problems:

1. Check the Laravel logs for specific error messages
2. Verify your CatéGPT API key has sufficient credits
3. Ensure your server can reach the CatéGPT API (no firewall issues)
4. Check if your Laravel application is properly configured for web authentication

The most common issue is either:
- Missing or invalid CatéGPT API key
- User doesn't have superadmin role
- Network connectivity issues to CatéGPT API 