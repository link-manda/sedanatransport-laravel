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

                        <form action="{{ route('bookings.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

                            <div>
                                <x-input-label for="start_date" :value="__('Start Date')" />
                                <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date')" required />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="end_date" :value="__('End Date')" />
                                <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="old('end_date')" required />
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <x-primary-button>
                                    {{ __('Send Booking Request') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>