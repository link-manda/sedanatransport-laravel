<x-guest-layout>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gray-50 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">
            <!-- Header -->
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-800">Buat Akun <span class="text-blue-500">SedanaTransport</span></h1>
                <p class="mt-2 text-sm text-gray-600">Daftar untuk mulai menyewa kendaraan</p>
            </div>

            <!-- Registration Form -->
            <div class="bg-white p-8 rounded-xl shadow-md">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name -->
                    <div class="mb-5">
                        <x-input-label for="name" :value="__('Nama Lengkap')" class="text-gray-700 font-medium" />
                        <x-text-input
                            id="name"
                            class="block mt-2 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            type="text"
                            name="name"
                            :value="old('name')"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Nama lengkap Anda" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

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
                            autocomplete="username"
                            placeholder="contoh@email.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mb-5">
                        <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-medium" />
                        <x-text-input
                            id="password"
                            class="block mt-2 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="Minimal 8 karakter" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" class="text-gray-700 font-medium" />
                        <x-text-input
                            id="password_confirmation"
                            class="block mt-2 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Ulangi password Anda" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <!-- Submit Button & Login Link -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <a class="text-sm text-blue-500 hover:text-blue-700 font-medium" href="{{ route('login') }}">
                            {{ __('Sudah punya akun? Masuk di sini') }}
                        </a>
                        <x-primary-button
                            class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ __('Daftar') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="text-center text-xs text-gray-500 mt-6">
                &copy; {{ date('Y') }} RentCar. Semua hak dilindungi.
            </div>
        </div>
    </div>
</x-guest-layout>