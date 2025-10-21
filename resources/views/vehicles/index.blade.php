<x-app-layout>
    <div class="bg-gray-100">
        <!-- Hero Section -->
        <div class="bg-white">
            <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                    Find Your Perfect Ride
                </h1>
                <p class="mt-4 max-w-md mx-auto text-lg text-gray-500 sm:text-xl md:mt-5 md:max-w-3xl">
                    Easy, reliable, and ready for your next adventure. Explore our collection of high-quality vehicles.
                </p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <h2 class="text-3xl font-bold text-gray-800 mb-8 px-4 sm:px-0">Available Vehicles</h2>

                @if($vehicles->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($vehicles as $vehicle)
                    <a href="{{ route('vehicles.show', $vehicle) }}" class="group block bg-white rounded-lg shadow-md overflow-hidden transform hover:-translate-y-2 transition-transform duration-300">
                        <!-- Vehicle Image -->
                        <div class="relative h-56">
                            <img class="w-full h-full object-cover" src="{{ $vehicle->photo ? Storage::url($vehicle->photo) : 'https://placehold.co/600x400/e2e8f0/e2e8f0' }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}">
                            <div class="absolute inset-0 bg-black bg-opacity-20 group-hover:bg-opacity-10 transition-colors duration-300"></div>
                        </div>

                        <div class="p-6">
                            <!-- Category Badge -->
                            <span class="inline-block bg-indigo-100 text-indigo-800 text-xs font-semibold px-2.5 py-0.5 rounded-full mb-2">{{ $vehicle->category->name }}</span>

                            <!-- Vehicle Name -->
                            <h3 class="text-xl font-bold text-gray-900 truncate">
                                {{ $vehicle->brand }} {{ $vehicle->model }}
                            </h3>
                            <p class="text-sm text-gray-500">{{ $vehicle->year }}</p>

                            <div class="mt-4 border-t border-gray-100 pt-4">
                                <p class="text-lg font-bold text-indigo-600">
                                    Rp {{ number_format($vehicle->daily_rate, 0, ',', '.') }}
                                    <span class="text-sm font-normal text-gray-500">/ day</span>
                                </p>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>

                <!-- Pagination Links -->
                <div class="mt-8">
                    {{ $vehicles->links() }}
                </div>

                @else
                <div class="text-center py-16 px-4 bg-white rounded-lg shadow-md">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17.25v2.25M15 17.25v2.25M6.375 12.375c-.352 0-.675.056-1 .15V8.25a.75.75 0 01.75-.75h1.5a.75.75 0 01.75.75v3.25m6.375 0c.352 0 .675.056 1 .15V8.25a.75.75 0 00-.75-.75h-1.5a.75.75 0 00-.75.75v3.25m6.375 0c.352 0 .675.056 1 .15V8.25a.75.75 0 00-.75-.75h-1.5a.75.75 0 00-.75.75v3.25" />
                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 13.5c0-1.657 1.343-3 3-3h9c1.657 0 3 1.343 3 3v2.25c0 1.657-1.343 3-3 3h-9c-1.657 0-3-1.343-3-3V13.5z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No vehicles available</h3>
                    <p class="mt-1 text-sm text-gray-500">Please check back later or contact us for more information.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>