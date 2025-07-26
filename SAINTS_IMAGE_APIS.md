# Saints Image APIs Integration Guide

## Overview
This guide explains how to integrate external image APIs to fetch high-quality saints images for your Salvation application. The system includes multiple fallback options to ensure reliable image delivery.

## Available Image APIs

### 1. Wikimedia Commons API (Primary - No API Key Required)
**URL**: `https://commons.wikimedia.org/w/api.php`
**Features**:
- ✅ **Free and open-source**
- ✅ **No API key required**
- ✅ **High-quality historical images**
- ✅ **Rich metadata**
- ✅ **Reliable and stable**

**Usage**: Automatically used as the primary source for saint images.

### 2. Pixabay API (Secondary)
**URL**: `https://pixabay.com/api/`
**Features**:
- ✅ **Free tier available**
- ✅ **High-quality photography**
- ✅ **Good saint-related content**
- ✅ **Simple API**

**Setup**:
1. Visit [Pixabay API](https://pixabay.com/api/docs/)
2. Create a free account
3. Get your API key
4. Add to `.env` file: `PIXABAY_API_KEY=your_key_here`

**Free Tier**: 5,000 requests per hour

### 3. Unsplash API (Tertiary)
**URL**: `https://api.unsplash.com/`
**Features**:
- ✅ **High-quality photography**
- ✅ **Free for most use cases**
- ✅ **Good for artistic representations**

**Setup**:
1. Visit [Unsplash Developers](https://unsplash.com/developers)
2. Create a developer account
3. Create a new application
4. Get your Access Key
5. Add to `.env` file: `UNSPLASH_ACCESS_KEY=your_key_here`

**Free Tier**: 5,000 requests per hour

### 4. Google Custom Search API (Optional)
**URL**: `https://www.googleapis.com/customsearch/v1`
**Features**:
- ✅ **Highly customizable**
- ✅ **Excellent search results**
- ✅ **Good for specific saint images**

**Setup**:
1. Create Google Cloud project
2. Enable Custom Search API
3. Create Custom Search Engine
4. Get API key and Search Engine ID
5. Add to `.env` file:
   ```
   GOOGLE_CUSTOM_SEARCH_API_KEY=your_key_here
   GOOGLE_CUSTOM_SEARCH_ENGINE_ID=your_engine_id_here
   ```

**Free Tier**: 100 queries per day

## Environment Configuration

Add these variables to your `.env` file:

```env
# External Image APIs
PIXABAY_API_KEY=your_pixabay_api_key_here
UNSPLASH_ACCESS_KEY=your_unsplash_access_key_here
GOOGLE_CUSTOM_SEARCH_API_KEY=your_google_api_key_here
GOOGLE_CUSTOM_SEARCH_ENGINE_ID=your_search_engine_id_here
```

## API Endpoints

### Get Saints with External Images
```
GET /api/saints?random=true&count=5
```

**Response**:
```json
[
  {
    "id": 1,
    "name": "St. Francis of Assisi",
    "feastDay": "October 4",
    "image": "https://commons.wikimedia.org/wiki/Special:FilePath/St_Francis_Assisi.jpg",
    "description": "Patron saint of animals and ecology",
    "bio": "Founder of the Franciscan Order...",
    "patronage": "Animals, Ecology, Merchants, Italy",
    "birthYear": 1181,
    "deathYear": 1226,
    "canonized": 1228
  }
]
```

### Get Saint Image by Name
```
GET /api/saints/image?name=St. Francis of Assisi
```

**Response**:
```json
{
  "saint_name": "St. Francis of Assisi",
  "image_url": "https://commons.wikimedia.org/wiki/Special:FilePath/St_Francis_Assisi.jpg",
  "source": "external_api"
}
```

## Implementation Features

### 1. Intelligent Fallback System
The system tries multiple APIs in order of preference:
1. **Wikimedia Commons** (no API key required)
2. **Pixabay** (if API key configured)
3. **Unsplash** (if API key configured)
4. **Default placeholder** (if all APIs fail)

### 2. Caching System
- Images are cached for 24 hours
- Reduces API calls and improves performance
- Cache key: `saint_image_{md5(saint_name)}`

### 3. Error Handling
- Graceful degradation when APIs fail
- Comprehensive logging for debugging
- Timeout protection (5 seconds per API)

### 4. Search Optimization
- Uses optimized search terms (e.g., "St. Francis of Assisi saint catholic")
- Filters for relevant categories
- Limits results to top matches

## Frontend Integration

### Update Saints Carousel
The saints carousel will automatically use external images when available:

```vue
<template>
  <div class="saint-carousel">
    <div v-for="saint in saints" :key="saint.id" class="saint-card">
      <img 
        :src="saint.image" 
        :alt="saint.name"
        @error="handleImageError"
        class="saint-image"
      />
      <h3>{{ saint.name }}</h3>
      <p>{{ saint.description }}</p>
    </div>
  </div>
</template>

<script setup>
const handleImageError = (event) => {
  // Fallback to default image if external image fails
  event.target.src = '/images/saints/default-saint.jpg';
};
</script>
```

## Performance Optimization

### 1. Image Caching
- Server-side caching reduces API calls
- Browser caching for frequently accessed images
- CDN integration for faster delivery

### 2. Lazy Loading
```vue
<img 
  :src="saint.image" 
  loading="lazy"
  :alt="saint.name"
/>
```

### 3. Image Optimization
- Use appropriate image sizes
- Implement responsive images
- Consider WebP format for better compression

## Monitoring and Analytics

### 1. API Usage Tracking
Monitor API usage to stay within free tiers:
- Pixabay: 5,000 requests/hour
- Unsplash: 5,000 requests/hour
- Google: 100 queries/day

### 2. Error Monitoring
Check logs for API failures:
```bash
tail -f storage/logs/laravel.log | grep "API failed"
```

### 3. Cache Hit Rate
Monitor cache effectiveness:
```php
// Check cache statistics
Cache::get('saint_image_cache_stats');
```

## Troubleshooting

### Common Issues

1. **No Images Loading**
   - Check API keys in `.env`
   - Verify internet connectivity
   - Check API rate limits

2. **Slow Image Loading**
   - Enable caching
   - Check image sizes
   - Consider CDN

3. **API Errors**
   - Check API key validity
   - Verify API quotas
   - Review error logs

### Debug Mode
Enable debug logging by adding to `.env`:
```env
LOG_LEVEL=debug
```

## Best Practices

### 1. API Key Security
- Never commit API keys to version control
- Use environment variables
- Rotate keys regularly

### 2. Rate Limiting
- Implement request throttling
- Monitor API usage
- Stay within free tier limits

### 3. Image Quality
- Prefer high-resolution images
- Maintain aspect ratios
- Use appropriate formats (JPEG for photos, PNG for graphics)

### 4. Fallback Strategy
- Always have local fallback images
- Test with API failures
- Provide meaningful alt text

## Future Enhancements

### 1. Additional APIs
- Catholic Art Database APIs
- Museum APIs (Metropolitan Museum, Vatican Museums)
- Religious Art Collections

### 2. Advanced Features
- Image tagging and categorization
- Saint-specific image collections
- User-uploaded saint images

### 3. Performance Improvements
- Image compression and optimization
- Progressive image loading
- Advanced caching strategies

## Support and Resources

### API Documentation
- [Wikimedia Commons API](https://commons.wikimedia.org/w/api.php)
- [Pixabay API](https://pixabay.com/api/docs/)
- [Unsplash API](https://unsplash.com/developers)
- [Google Custom Search API](https://developers.google.com/custom-search)

### Community Resources
- Catholic art databases
- Religious image collections
- Open-source saint image repositories

This integration provides a robust, scalable solution for displaying high-quality saints images in your Salvation application while maintaining excellent performance and reliability. 