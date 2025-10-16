<x-guest-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="font-semibold text-3xl text-gray-800 leading-tight mb-8 text-center">
                Our Available Vehicles
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($vehicles as $vehicle)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <a href="{{ route('vehicles.show', $vehicle) }}">
                        <img src="{{ $vehicle->photo ? asset('storage/' . $vehicle->photo) : 'https://placehold.co/600x400?text=Vehicle' }}" alt="{{ $vehicle->model }}" class="w-full h-48 object-cover">
                    </a>
                    <div class="p-6">
                        <h3 class="font-bold text-lg text-gray-900">{{ $vehicle->brand }} {{ $vehicle->model }}</h3>
                        <p class="text-sm text-gray-600">{{ $vehicle->category->name }}</p>
                        <p class="mt-4 text-xl font-semibold text-gray-800">
                            Rp {{ number_format($vehicle->daily_rate, 0, ',', '.') }} <span class="text-sm font-normal">/ day</span>
                        </p>
                        <a href="{{ route('vehicles.show', $vehicle) }}" class="mt-4 inline-block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Book Now
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-8">
                {{ $vehicles->links() }}
            </div>
        </div>
    </div>
</x-guest-layout>