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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonText: 'Oke',
            timer: 3000,
            timerProgressBar: true
        });
    });
</script>
@endif

    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-linear-to-br from-blue-50 via-cyan-50 to-teal-50 dark:bg-linear-to-br dark:from-blue-900 dark:via-cyan-900 dark:to-teal-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-linear-to-r from-blue-600 via-cyan-500 to-teal-600 shadow-lg">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-white">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
