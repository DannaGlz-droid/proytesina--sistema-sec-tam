<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" href="{{ asset('images/tam_pag_logoF.png') }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        @php($scaleFor100 = $attributes->has('scale-for-100'))
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            @if ($scaleFor100)
                <div class="guest-auth-scale-wrapper">
            @endif
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
            @if ($scaleFor100)
                </div>
            @endif
        </div>
        @if ($scaleFor100)
            <style>
                .guest-auth-scale-wrapper {
                    display: flex;
                    width: 100%;
                    flex-direction: column;
                    align-items: center;
                }

                @media (min-width: 641px) {
                    .guest-auth-scale-wrapper {
                        zoom: 0.65;
                    }
                }
            </style>
        @endif
    </body>
</html>
