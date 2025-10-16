<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Vehicles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('admin.vehicles.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Add Vehicle
                        </a>
                    </div>

                    @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="w-1/6 py-3 px-4 uppercase font-semibold text-sm">Photo</th>
                                    <th class="w-1/6 py-3 px-4 uppercase font-semibold text-sm">Plate No.</th>
                                    <th class="w-1/4 py-3 px-4 uppercase font-semibold text-sm">Brand & Model</th>
                                    <th class="w-1/6 py-3 px-4 uppercase font-semibold text-sm">Rate</th>
                                    <th class="w-1/6 py-3 px-4 uppercase font-semibold text-sm">Status</th>
                                    <th class="py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @forelse($vehicles as $vehicle)
                                <tr>
                                    <td class="py-3 px-4">
                                        <img src="{{ $vehicle->photo ? asset('storage/' . $vehicle->photo) : 'https://placehold.co/100x60?text=No+Image' }}" alt="{{ $vehicle->model }}" class="h-16 w-auto object-cover rounded">
                                    </td>
                                    <td class="py-3 px-4 font-mono">{{ $vehicle->plate_number }}</td>
                                    <td class="py-3 px-4">{{ $vehicle->brand }} {{ $vehicle->model }}</td>
                                    <td class="py-3 px-4">Rp {{ number_format($vehicle->daily_rate, 0, ',', '.') }}/day</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 font-semibold leading-tight rounded-full
                                            @if($vehicle->status == 'available') bg-green-100 text-green-800 @endif
                                            @if($vehicle->status == 'rented') bg-red-100 text-red-800 @endif
                                            @if($vehicle->status == 'maintenance') bg-yellow-100 text-yellow-800 @endif
                                        ">
                                            {{ ucfirst($vehicle->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 flex items-center space-x-2">
                                        <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="text-yellow-500 hover:text-yellow-700">Edit</a>
                                        <form action="{{ route('admin.vehicles.destroy', $vehicle) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">No vehicles found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $vehicles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>