<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Book Vehicle: {{ $vehicle->brand }} {{ $vehicle->model }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Vehicle Details -->
                    <div>
                        <img src="{{ $vehicle->photo ? Storage::url($vehicle->photo) : 'https://placehold.co/600x400/e2e8f0/e2e8f0' }}" alt="{{ $vehicle->model }}" class="w-full h-auto object-cover rounded-lg shadow">
                        <h3 class="font-bold text-2xl text-gray-900 mt-4">{{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->year }})</h3>
                        <p class="text-md text-gray-600">{{ $vehicle->category->name }}</p>
                        <p class="text-gray-700 mt-2">Plate Number: <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $vehicle->plate_number }}</span></p>
                        <p class="mt-4 text-3xl font-bold text-gray-800">
                            Rp {{ number_format($vehicle->daily_rate, 0, ',', '.') }} <span class="text-lg font-normal">/ day</span>
                        </p>
                    </div>

                    <!-- Booking Form -->
                    <div>
                        <h3 class="font-semibold text-xl border-b pb-2 mb-4">Rental Form</h3>
                        
                        @auth
                            <form action="{{ route('bookings.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

                                <!-- General Error for vehicle availability -->
                                @error('vehicle_unavailable')
                                    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
                                        <p class="font-bold">Not Available</p>
                                        <p>{{ $message }}</p>
                                    </div>
                                @enderror

                                <div class="space-y-4">
                                    <!-- Start Date -->
                                    <div>
                                        <x-input-label for="start_date" :value="__('Start Date')" />
                                        <x-text-input id="start_date" class="block mt-1 w-full @error('start_date') border-red-500 @enderror" type="date" name="start_date" :value="old('start_date')" required />
                                        @error('start_date')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- End Date -->
                                    <div>
                                        <x-input-label for="end_date" :value="__('End Date')" />
                                        <x-text-input id="end_date" class="block mt-1 w-full @error('end_date') border-red-500 @enderror" type="date" name="end_date" :value="old('end_date')" required />
                                        @error('end_date')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="flex items-center justify-end mt-6">
                                    <x-primary-button>
                                        {{ __('Send Booking Request') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        @else
                             <div class="text-center border-2 border-dashed border-gray-300 p-8 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900">You need an account to book.</h3>
                                <p class="mt-2 text-sm text-gray-500">Please log in to continue or register if you don't have an account yet.</p>
                                <div class="mt-6 flex justify-center gap-4">
                                    <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                        Log In
                                    </a>
                                    <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
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
</x-app-layout>

