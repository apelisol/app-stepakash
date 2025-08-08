<!doctype html>
<html lang="en" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Primary SEO Meta Tags -->
    <title>StepaKash - Fast & Secure Deriv M-PESA Money Transfers | Money on The Go...</title>
    <meta name="title" content="StepaKash - Fast & Secure Deriv M-PESA Money Transfers | Money on The Go...">
    <meta name="description" content="Send money instantly with StepaKash! Secure Deriv to M-PESA transfers in Kenya. Low fees, instant processing, 24/7 support. Join 100,000+ satisfied customers today.">
    <meta name="keywords" content="StepaKash, Deriv M-PESA, money transfer Kenya, Deriv to M-PESA, financial services Kenya, mobile money, instant transfers, secure payments, fintech Kenya, M-PESA deposit, Deriv withdrawal">
    <meta name="author" content="StepaKash">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ config('app.url') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ config('app.url') }}">
    <meta property="og:title" content="StepaKash - Fast & Secure Deriv M-PESA Money Transfers">
    <meta property="og:description" content="Send money instantly with StepaKash! Secure Deriv to M-PESA transfers in Kenya. Low fees, instant processing, 24/7 support.">
    <meta property="og:image" content="{{ asset('assets/img/stepakash-money-on-the-go.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="StepaKash">
    <meta property="og:locale" content="en_KE">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ config('app.url') }}">
    <meta property="twitter:title" content="StepaKash - Fast & Secure Deriv M-PESA Money Transfers">
    <meta property="twitter:description" content="Send money instantly with StepaKash! Secure Deriv to M-PESA transfers in Kenya. Low fees, instant processing, 24/7 support.">
    <meta property="twitter:image" content="{{ asset('assets/img/stepakash-money-on-the-go.png') }}">
    <meta name="twitter:creator" content="@stepakash">
    <meta name="twitter:site" content="@stepakash">

    <!-- Additional SEO Meta Tags -->
    <meta name="language" content="English">
    <meta name="geo.region" content="KE">
    <meta name="geo.country" content="Kenya">
    <meta name="geo.placename" content="Nairobi">
    <meta name="theme-color" content="#0f753a">
    <meta name="msapplication-TileColor" content="#0f753a">
    <meta name="application-name" content="StepaKash">

    <!-- PWA Meta Tags -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="StepaKash">
    <link rel="manifest" href="{{ asset('manifest.json') }}" />

    <!-- Favicons -->
    <link rel="apple-touch-icon" href="{{ asset('assets/img/stepakash-money-on-the-go.png') }}" sizes="180x180">
    <link rel="icon" href="{{ asset('assets/img/stepakash-money-on-the-go.png') }}" sizes="32x32" type="image/png">
    <link rel="icon" href="{{ asset('assets/img/stepakash-money-on-the-go.png') }}" sizes="16x16" type="image/png">
    <link rel="shortcut icon" href="{{ asset('assets/img/stepakash-money-on-the-go.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <!-- @vite(['resources/css/app.css', 'resources/js/app.js']) -->

    @stack('styles')


    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
        extend: {
            colors: {
            primary: {
                DEFAULT: '#0f753a',
                50: '#f0f9f4',
                100: '#dcf2e4',
                200: '#bbe5cd',
                300: '#8dd2ac',
                400: '#58b885',
                500: '#0f753a',
                600: '#0d6632',
                700: '#0b5529',
                800: '#094420',
                900: '#07381b',
            },
            secondary: {
                DEFAULT: '#2c8a53',
                50: '#f1f9f4',
                100: '#def2e6',
                200: '#c0e5d1',
                300: '#93d1b0',
                400: '#5fb588',
                500: '#2c8a53',
                600: '#237643',
                700: '#1d6137',
                800: '#194e2e',
                900: '#154025',
            },
            accent: {
                DEFAULT: '#efd050',
                50: '#fefce8',
                100: '#fef9c3',
                200: '#fef08a',
                300: '#fde047',
                400: '#efd050',
                500: '#eab308',
                600: '#ca8a04',
                700: '#a16207',
                800: '#854d0e',
                900: '#713f12',
                hover: '#f4dc74',
            },
            text: {
                DEFAULT: '#333333',
                light: '#666666',
            },
            background: '#e8e8e8',
            'card-bg': '#ffffff',
            success: '#0f753a',
            danger: '#e74c3c',
            warning: '#f39c12',
            }
        }
        }
    }
    </script>
</head>

<body class="h-full bg-background font-inter" data-page="@yield('page')">
    @yield('content')

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/js/pwa-services.js') }}"></script>

    @stack('scripts')
</body>

</html>