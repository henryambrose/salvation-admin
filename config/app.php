<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Church Name
    |--------------------------------------------------------------------------
    |
    | This value is the full name of the church/parish. This will be used in
    | receipts, certificates, and other official documents.
    |
    */

    'church_name' => env('CHURCH_NAME', 'Our Lady of Salvation Church'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => env('APP_TIMEZONE', 'Asia/Kolkata'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Church Code Configuration
    |--------------------------------------------------------------------------
    |
    | This value determines the church code used for member numbering and
    | family identification throughout the application.
    |
    */

    'church_code' => env('CHURCH_CODE', 'SAL'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Parish Information
    |--------------------------------------------------------------------------
    |
    | Parish details used in certificates and documents.
    |
    */

    'parish_name' => env('CHURCH_NAME', 'Church of Our Lady of Salvation'),
    'parish_address' => env('PARISH_ADDRESS', 'Dadar (W), Mumbai - 400 028'),
    'parish_priest_name' => env('PARISH_PRIEST_NAME', 'Parish Priest'),

    /*
    |--------------------------------------------------------------------------
    | Certificate Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for certificate generation including logo settings.
    |
    */

    'certificate_show_logo' => env('CERTIFICATE_SHOW_LOGO', true),
    'certificate_logo_path' => env('CERTIFICATE_LOGO_PATH', 'church-logo.png'),

    /*
    |--------------------------------------------------------------------------
    | Graveyard Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the graveyard module.
    |
    */

    'graveyard_min_months_before_remains_transfer' => env('GRAVEYARD_MIN_MONTHS_BEFORE_REMAINS_TRANSFER', 6),

    /*
    |--------------------------------------------------------------------------
    | Chrome/Puppeteer Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Browsershot/Puppeteer PDF generation.
    |
    */

    'chrome_path' => env('PUPPETEER_EXECUTABLE_PATH', '/usr/bin/google-chrome'),
    'chrome_no_sandbox' => env('CHROME_NO_SANDBOX', true),

];
