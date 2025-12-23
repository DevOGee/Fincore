<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <!-- Email Address -->
        <div>
            <label for="email" class="block font-medium text-sm text-gray-700">Email Address</label>
            <div class="relative mt-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <span class="text-gray-500 sm:text-sm">📧</span>
                </div>
                <input id="email"
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#037B90] focus:ring-[#037B90] pl-10 px-4 py-3"
                    type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                    placeholder="Email address" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <!-- Password -->
        <div class="mt-4" x-data="{ show: false }">
            <label for="password" class="block font-medium text-sm text-gray-700">Password</label>

            <div class="relative mt-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <span class="text-gray-500 sm:text-sm">🔒</span>
                </div>
                <input id="password"
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#037B90] focus:ring-[#037B90] pl-10 px-4 py-3 pr-10"
                    :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                    placeholder="Password" />

                <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                    <svg x-show="!show" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="show" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-4">
            <div class="flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 text-[#037B90] shadow-sm focus:ring-[#037B90]" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </div>

            @if (Route::has('password.request'))
                <a class="underline text-sm text-[#037B90] hover:text-[#025F70] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#037B90]"
                    href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <div class="mt-6">
            <button
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-[#037B90] hover:bg-[#025F70] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#037B90] transition ease-in-out duration-150">
                {{ __('Log in') }}
            </button>
        </div>

        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">
                Don't have an account?
                <a href="{{ route('register') }}"
                    class="font-medium text-[#037B90] hover:text-[#025F70] transition ease-in-out duration-150">
                    Sign up here
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>