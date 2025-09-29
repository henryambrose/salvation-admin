<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $filename }} - {{ config('app.name', 'Salvation Admin') }}</title>

    <!-- Church favicon instead of Laravel default -->
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Open Graph meta tags for better sharing -->
    <meta property="og:title" content="Gallery Image - {{ config('app.name') }}">
    <meta property="og:type" content="image">
    <meta property="og:image" content="{{ $imageUrl }}">
    <meta property="og:description" content="Gallery image from {{ config('app.name') }}">

    <!-- Twitter Card meta tags -->
    <meta name="twitter:card" content="photo">
    <meta name="twitter:image" content="{{ $imageUrl }}">
    <meta name="twitter:title" content="Gallery Image - {{ config('app.name') }}">

    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #000;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .image-container {
            max-width: 95vw;
            max-height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        }

        .image-info {
            margin-top: 20px;
            text-align: center;
            opacity: 0.8;
            font-size: 14px;
        }

        .actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            transition: background 0.2s;
        }

        .btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .header {
            position: absolute;
            top: 20px;
            left: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo {
            width: 32px;
            height: 32px;
            background: url('/favicon.svg') no-repeat center;
            background-size: contain;
        }

        .site-name {
            font-weight: 600;
            font-size: 18px;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            .header {
                position: relative;
                top: auto;
                left: auto;
                margin-bottom: 20px;
            }
            .actions {
                flex-direction: column;
                align-items: center;
            }
            .btn {
                width: 200px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo"></div>
        <div class="site-name">{{ config('app.name') }}</div>
    </div>

    <div class="image-container">
        <img src="{{ $imageUrl }}" alt="{{ $filename }}" class="image" />
    </div>

    <div class="image-info">
        <div><strong>{{ $filename }}</strong></div>
        <div>{{ number_format($fileSize / 1024, 1) }} KB</div>
    </div>

    <div class="actions">
        <a href="{{ $imageUrl }}" class="btn" download="{{ $filename }}">Download Image</a>
        <a href="javascript:history.back()" class="btn">Go Back</a>
    </div>
</body>
</html>