<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#0369a1">
        <link rel="icon" type="image/png" href="{{ asset('images/yapim.png?v=' . time()) }}">
        <link rel="shortcut icon" href="{{ asset('favicon.png?v=' . time()) }}" type="image/png">

        <title>{{ config('app.name', 'SMK YAPIM BIRU-BIRU') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-linear-to-br from-blue-600 via-cyan-500 to-teal-600">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-white" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white/10 backdrop-blur-md shadow-md overflow-hidden sm:rounded-lg border border-white/10">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
