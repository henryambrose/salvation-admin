# Obituary Text Rephrasing Feature

This feature allows users to automatically rephrase and improve text in obituary forms using OpenAI's ChatGPT API.

## Setup Instructions

### 1. Choose Your AI Model

You have three options for text rephrasing:

#### Option A: OpenAI GPT (Paid - Highest Quality)
1. Go to [OpenAI Platform](https://platform.openai.com/api-keys)
2. Create an account or log in
3. Generate a new API key
4. Copy the API key (starts with `sk-`)

#### Option B: Hugging Face (Free - Good Quality)
1. No API key required
2. Uses free Hugging Face inference API
3. Automatic fallback to simple rules if API is unavailable

#### Option C: Simple Rules (Completely Free - Basic Quality)
1. No API key or internet connection required
2. Uses rule-based text improvements
3. Always available as fallback

### 2. Configure Environment
1. Open your `.env` file
2. Add one of the following lines:

**For OpenAI (Paid):**
```
OPENAI_API_KEY=your_actual_openai_key_here
```

**For Hugging Face (Free):**
```
OPENAI_API_KEY=hf-free
```

**For Simple Rules Only (Free):**
```
OPENAI_API_KEY=simple-free
```

**For Testing:**
```
OPENAI_API_KEY=test-key-for-debugging
```

### 3. Clear Configuration Cache
Run the following command to refresh the configuration:
```bash
php artisan config:cache
```

## Features

### Text Fields with Rephrase Functionality
The following obituary form fields now have "Rephrase" buttons:

1. **Biography** - Improves biographical text to be more eloquent and respectful
2. **Favorite Memory** - Enhances memory descriptions to be more touching and well-written
3. **Achievements** - Refines achievement listings to be more professional
4. **Hobbies & Interests** - Polishes hobby descriptions to be more eloquent
5. **Family Notes & Messages** - Improves family messages to be more respectful and appropriate

### How to Use

1. **Enter Text**: Type or paste your original text in any of the supported textarea fields
2. **Click Rephrase**: Click the "Rephrase" button next to the field label
   - The button will show a spinning icon and "Rephrasing..." while processing
   - Requires at least 10 characters of text to work
3. **Review Results**: A dialog will appear showing:
   - Your original text
   - The AI-rephrased version
4. **Choose**: Select either:
   - **"Use Rephrased Text"** - Replace your text with the improved version
   - **"Keep Original"** - Keep your original text unchanged

### Button States

- **Enabled**: Purple sparkle icon with "Rephrase" text
- **Processing**: Spinning refresh icon with "Rephrasing..." text
- **Disabled**: When field is empty or has less than 10 characters

## Technical Details

### API Endpoint
- **URL**: `POST /graveyard/obituaries/rephrase-text`
- **Authentication**: Required (uses Laravel auth middleware)
- **Rate Limiting**: Uses OpenAI's API rate limits

### Request Format
```json
{
  "text": "Text to be rephrased",
  "field_type": "biography|favorite_memory|achievements|hobbies_interests|notes"
}
```

### Response Format
```json
{
  "original_text": "Original input text",
  "rephrased_text": "AI-improved text",
  "field_type": "biography"
}
```

### Error Handling
- Missing API key: Shows configuration error message
- API failures: Shows user-friendly error message
- Network issues: Graceful timeout and error handling
- All errors are logged for administrator review

## Security & Privacy

- **API Key Security**: Stored securely in environment variables
- **Data Privacy**: Text is sent to OpenAI for processing - inform users as appropriate
- **Authentication**: Feature requires user login
- **Rate Limiting**: Inherits OpenAI's usage limits

## Troubleshooting

### Common Issues

1. **"OpenAI API key is not configured"**
   - Check that `OPENAI_API_KEY` is set in your `.env` file
   - Run `php artisan config:cache` after adding the key

2. **"Failed to rephrase text"**
   - Check your OpenAI account has available credits
   - Verify your API key is valid and active
   - Check server logs for detailed error information

3. **Button not appearing**
   - Ensure you've built the frontend assets: `npm run build`
   - Clear browser cache and refresh the page

4. **Request timeouts**
   - OpenAI API calls have a 30-second timeout
   - Very long text may take longer to process

### Logs
Check Laravel logs at `storage/logs/laravel.log` for detailed error information.

## Cost Considerations

- Uses OpenAI's GPT-3.5-turbo model (cost-effective option)
- Approximate cost: $0.001-0.002 per rephrase request
- Set up billing alerts in your OpenAI account
- Consider implementing usage limits if needed

## Future Enhancements

Potential improvements could include:
- Multiple language support
- Custom writing style options
- Bulk rephrasing for multiple fields
- Local AI model integration for privacy
- Usage analytics and reporting