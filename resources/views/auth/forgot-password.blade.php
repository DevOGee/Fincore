<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - {{ config('app.name', 'FinCore') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gold: '#ff7f50',
                        teal: '#037b90',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-4xl font-extrabold text-gray-900">
                    <span class="text-gold">Fin</span><span class="text-teal">Core</span>
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Reset your password
                </p>
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                <p class="text-sm text-gray-700">
                    Forgot your password? No problem. Just enter your email address and we'll email you a password reset
                    link.
                </p>
            </div>

            @if (session('status'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('status') }}</span>
                </div>
            @endif

            <form class="mt-8 space-y-6" action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="rounded-md shadow-sm">
                    <div>
                        <label for="email" class="sr-only">Email address</label>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            value="{{ old('email') }}"
                            class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-teal focus:border-teal focus:z-10 sm:text-sm"
                            placeholder="Email address">
                        @error('email')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-teal hover:bg-gold focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal transition duration-150">
                        Email Password Reset Link
                    </button>
                </div>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="font-medium text-teal hover:text-gold">
                        ← Back to login
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>