# Login Page Enhancements

## Overview
The login page has been enhanced with a beautiful split-layout design featuring Catholic calendar information and a saints carousel on the left side, while maintaining the login form on the right side. The Catholic calendar now integrates with the [calapi.inadiutorium.cz](http://calapi.inadiutorium.cz/api/v0/en/calendars/general-en/today) external API for accurate liturgical data.

## Features

### 1. Enhanced Layout Design
- **Split Layout**: Left side (50%) contains Catholic content, right side (50%) contains login form
- **Responsive Design**: On mobile devices, only the login form is shown with a mobile-optimized header
- **Gradient Background**: Beautiful blue-to-purple gradient on the left panel
- **Glass Morphism**: Semi-transparent panels with backdrop blur effects

### 2. Catholic Calendar Section
- **Current Date Display**: Prominent display of current date with weekday
- **Liturgical Season**: Shows current liturgical season (Advent, Christmas, Lent, Easter, Ordinary Time)
- **Season Week**: Displays the current week within the liturgical season
- **Feast Day**: Displays any special feast days for the current date
- **Saint of the Day**: Shows the saint whose feast day is celebrated today
- **Liturgical Color**: Indicates the liturgical color for the day
- **Multiple Celebrations**: Shows additional celebrations when multiple feasts occur on the same day
- **External API Integration**: Uses [calapi.inadiutorium.cz](http://calapi.inadiutorium.cz/api/v0/en/calendars/general-en/today) for accurate liturgical data

### 3. Saints Carousel
- **Auto-advancing**: Automatically cycles through saints every 5 seconds
- **Interactive Controls**: Click on carousel indicators to manually navigate
- **Saint Information**: Displays saint name, feast day, and brief description
- **Visual Appeal**: Uses prayer emoji as placeholder for saint images

### 4. Improved Login Form
- **Enhanced Styling**: Better visual hierarchy and spacing
- **Icon Integration**: Mail and lock icons in input fields
- **Better UX**: Improved placeholder text and form validation
- **Loading States**: Enhanced loading indicators during form submission
- **Forgot Password Link**: Added link to password reset functionality

## API Endpoints

### Catholic Calendar API
```
GET /api/catholic-calendar
```
Returns liturgical information for the current date or a specified date. Integrates with external [calapi.inadiutorium.cz](http://calapi.inadiutorium.cz/api/v0/en/calendars/general-en/today) API for accurate data.

**Query Parameters:**
- `date` (optional): Date in Y-m-d format (default: current date)

**Response:**
```json
{
  "date": "2025-07-26",
  "liturgicalSeason": "Ordinary Time",
  "feastDay": "Saints Joachim and Anne",
  "saintOfTheDay": "Saints Joachim and Anne",
  "color": "White",
  "reading": "Daily Mass Readings - July 26, 2025",
  "weekday": "Saturday",
  "seasonWeek": 16,
  "celebrations": [
    {
      "title": "Saints Joachim and Anne",
      "colour": "white",
      "rank": "memorial",
      "rank_num": 3.1
    }
  ],
  "source": "external_api"
}
```

### Saints API
```
GET /api/saints
```
Returns a list of saints for the carousel.

**Query Parameters:**
- `random` (optional): Return random selection (default: false)
- `count` (optional): Number of saints to return when random=true (default: 5)
- `id` (optional): Return specific saint by ID

**Response:**
```json
[
  {
    "id": 1,
    "name": "St. Francis of Assisi",
    "feastDay": "October 4",
    "image": "/images/saints/francis-assisi.jpg",
    "description": "Patron saint of animals and ecology",
    "bio": "Founder of the Franciscan Order...",
    "patronage": "Animals, Ecology, Merchants, Italy",
    "birthYear": 1181,
    "deathYear": 1226,
    "canonized": 1228
  }
]
```

### Saint of the Day API
```
GET /api/saints/saint-of-the-day
```
Returns the saint whose feast day is celebrated on the current date.

## External API Integration

### calapi.inadiutorium.cz
The Catholic calendar now integrates with the [calapi.inadiutorium.cz](http://calapi.inadiutorium.cz/api/v0/en/calendars/general-en/today) API, which provides:

- **Accurate Liturgical Seasons**: Real-time liturgical season calculations
- **Official Feast Days**: Church-approved feast day information
- **Multiple Celebrations**: Support for multiple feasts on the same day
- **Liturgical Colors**: Accurate liturgical color assignments
- **Season Weeks**: Current week within the liturgical season
- **Rank Information**: Celebration rank and importance

**Example External API Response:**
```json
{
  "date": "2025-07-26",
  "season": "ordinary",
  "season_week": 16,
  "celebrations": [
    {
      "title": "Saints Joachim and Anne",
      "colour": "white",
      "rank": "memorial",
      "rank_num": 3.1
    }
  ],
  "weekday": "saturday"
}
```

### Fallback System
If the external API is unavailable, the system falls back to internal calculations to ensure the calendar always displays information.

## Technical Implementation

### Frontend Components
- **AuthEnhancedLayout.vue**: New layout component with split design
- **Login.vue**: Updated to use the enhanced layout
- **API Integration**: Fetches data from Catholic calendar and saints APIs
- **TypeScript Support**: Properly typed interfaces for type safety
- **External API Integration**: Seamless integration with calapi.inadiutorium.cz

### Backend Controllers
- **CatholicCalendarController**: Handles liturgical calendar calculations and external API integration
- **SaintsController**: Manages saints data and carousel content
- **HTTP Client**: Uses Laravel's HTTP client for external API calls
- **Error Handling**: Graceful fallback when external API is unavailable

### Features
- **External API Integration**: Real-time liturgical data from calapi.inadiutorium.cz
- **Fallback System**: Internal calculations when external API fails
- **Multiple Celebrations**: Support for multiple feast days
- **Season Week Display**: Shows current week within liturgical season
- **Responsive Design**: Mobile-first approach with graceful degradation
- **Error Handling**: Comprehensive error handling and logging

## Customization

### Adding More Saints
Edit `app/Http/Controllers/SaintsController.php` to add more saints to the database.

### Modifying External API Integration
Update the `fetchExternalCalendarData` method in `CatholicCalendarController.php` to modify external API behavior.

### Styling Changes
Modify the CSS classes in `AuthEnhancedLayout.vue` to customize the visual appearance.

## Future Enhancements
- Add real saint images to `/public/images/saints/` directory
- Integrate with additional Catholic APIs for more comprehensive data
- Add daily mass readings from lectionary APIs
- Implement user preferences for carousel speed and content
- Add more interactive elements like saint biographies and prayers
- Cache external API responses for better performance
- Add support for different liturgical calendars (Roman, Ambrosian, etc.) 