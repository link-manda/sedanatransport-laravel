<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Book Vehicle: {{ $vehicle->brand }} {{ $vehicle->model }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Vehicle Details -->
                    <div>
                        <img src="{{ $vehicle->photo ? asset('storage/' . $vehicle->photo) : 'https://placehold.co/600x400?text=Vehicle' }}" alt="{{ $vehicle->model }}" class="w-full h-auto object-cover rounded-lg">
                        <h3 class="font-bold text-2xl text-gray-900 mt-4">{{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->year }})</h3>
                        <p class="text-md text-gray-600">{{ $vehicle->category->name }}</p>
                        <p class="text-gray-700 mt-2">Plate Number: <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $vehicle->plate_number }}</span></p>
                        <p class="mt-4 text-3xl font-bold text-gray-800">
                            Rp {{ number_format($vehicle->daily_rate, 0, ',', '.') }} <span class="text-lg font-normal">/ day</span>
                        </p>
                    </div>

                    <!-- Booking Form -->
                    <div>
                        <h3 class="font-semibold text-lg border-b pb-2 mb-4">Rental Form</h3>

                        @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <strong class="font-bold">Oops!</strong>
                            <ul class="mt-1 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4">Book This Vehicle</h2>

                            <!-- Cek status otentikasi pengguna -->
                            @auth
                            <!-- Jika pengguna sudah login, tampilkan form -->
                            <form action="{{ route('bookings.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                                        <input type="date" id="start_date" name="start_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        @error('start_date')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                                        <input type="date" id="end_date" name="end_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        @error('end_date')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                @error('vehicle_unavailable')
                                <p class="text-red-500 text-sm mt-4 font-semibold">{{ $message }}</p>
                                @enderror

                                <div class="mt-6">
                                    <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-300 ease-in-out">
                                        Send Booking Request
                                    </button>
                                </div>
                            </form>
                            @else
                            <!-- Jika pengguna adalah tamu (belum login), tampilkan pesan ini -->
                            <div class="text-center border-2 border-dashed border-gray-300 p-8 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900">You need an account to book this vehicle.</h3>
                                <p class="mt-2 text-sm text-gray-500">Please log in to continue or register if you don't have an account yet.</p>
                                <div class="mt-6 flex justify-center gap-4">
                                    <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Log In
                                    </a>
                                    <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Register
                                    </a>
                                </div>
                            </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>