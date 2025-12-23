<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes gradient-xy {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient-xy 6s ease infinite;
        }
    </style>
</head>

<body
    class="font-sans text-gray-900 antialiased bg-gradient-to-br from-[#037B90] to-[#FF7F50] animate-gradient min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
    <div class="w-full sm:max-w-md px-6 py-4 bg-white shadow-xl overflow-hidden sm:rounded-lg">
        <div class="flex flex-col items-center mb-6">
            <x-application-logo class="w-20 h-20 fill-current text-[#037B90]" />
        </div>

        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold bg-gray-900 rounded-lg px-4 py-2"><span class="text-[#FF7F50]">Fin</span><span
                    class="text-white">Core</span></h2>
            <p class="text-sm font-semibold text-gray-600 mt-2">Secure Financial Core System</p>
            <p class="text-sm text-gray-500 mt-1">Sign in to continue</p>
        </div>

        {{ $slot }}
    </div>

    <div class="mt-8 text-white/80 text-sm">
        &copy; {{ date('Y') }} Wab. Wire Systems. All rights reserved. | Powered by DevGee
    </div>
</body>

</html>