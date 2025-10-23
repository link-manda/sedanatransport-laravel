<x-guest-layout>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gray-50 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">
            <!-- Logo / Header -->
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-800">Sedana<span class="text-blue-500">Transport</span></h1>
                <p class="mt-2 text-sm text-gray-600">Masuk untuk mengelola kendaraan Anda</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Login Form -->
            <div class="bg-white p-8 rounded-xl shadow-md">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-5">
                        <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-medium" />
                        <x-text-input
                            id="email"
                            class="block mt-2 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="yourname@mail.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mb-5">
                        <div class="flex items-center justify-between">
                            <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-medium" />
                            @if (Route::has('password.request'))
                            <a class="text-sm text-blue-500 hover:text-blue-700 font-medium" href="{{ route('password.request') }}">
                                {{ __('Lupa password?') }}
                            </a>
                            @endif
                        </div>
                        <x-text-input
                            id="password"
                            class="block mt-2 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center mb-6">
                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500 border-gray-300" />
                        <label for="remember_me" class="ms-2 block text-sm text-gray-700">
                            {{ __('Ingat saya') }}
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <x-primary-button
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex items-center justify-center">
                        {{ __('MASUK') }}
                    </x-primary-button>
                </form>
            </div>

            <!-- Footer (Opsional) -->
            <div class="text-center text-xs text-gray-500 mt-6">
                &copy; {{ date('Y') }} RentCar. Semua hak dilindungi.
            </div>
        </div>
    </div>
</x-guest-layout>