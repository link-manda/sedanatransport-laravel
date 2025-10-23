{{-- Menggunakan Guest Layout (pastikan @vite directive ada di layout) --}}
<x-guest-layout>

    {{-- Container Utama dengan Padding Responsif --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">

        {{-- Header Section --}}
        <div class="text-center mb-10 md:mb-12">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-800 mb-4">
                Welcome to Sedana Transport
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Explore our premium fleet of vehicles for your transportation needs</p>
        </div>

        {{-- Daftar Kendaraan - Grid Responsif --}}
        <div class="mb-8">
            <h2 class="text-2xl md:text-3xl font-semibold text-gray-800 mb-2 text-center sm:text-left">Our Fleet</h2>
            <p class="text-gray-500 text-center sm:text-left mb-6">Choose from our selection of premium vehicles</p>

            @if($vehicles->isEmpty())
            <div class="text-center py-12 bg-gray-50 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-lg text-gray-600">No vehicles available at the moment.</p>
                <p class="text-gray-500 mt-2">Please check back later for updates.</p>
            </div>
            @else
            {{-- Grid dengan jumlah kolom berbeda berdasarkan ukuran layar --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">
                @foreach ($vehicles as $vehicle)
                {{-- Kartu Kendaraan --}}
                <div class="bg-white rounded-xl shadow-sm overflow-hidden flex flex-col transform transition duration-300 hover:scale-[1.02] hover:shadow-lg border border-gray-100">
                    {{-- Gambar Kendaraan --}}
                    <a href="{{ route('vehicles.show', $vehicle) }}" class="block h-48 sm:h-52 bg-gray-100 flex-shrink-0 relative group overflow-hidden">
                        @if ($vehicle->photo)
                        <img src="{{ Storage::url($vehicle->photo) }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                        {{-- Placeholder yang lebih menarik --}}
                        <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm font-medium">No Image Available</span>
                        </div>
                        @endif
                        {{-- Overlay on Hover --}}
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-opacity duration-300"></div>
                        {{-- Status Badge di atas gambar --}}
                        <div class="absolute top-3 right-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold capitalize shadow-sm
                                        @if($vehicle->status == 'available') bg-green-100 text-green-800 border border-green-200
                                        @elseif($vehicle->status == 'rented') bg-orange-100 text-orange-800 border border-orange-200
                                        @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                                {{ $vehicle->status }}
                            </span>
                        </div>
                    </a>

                    {{-- Detail Kendaraan --}}
                    <div class="p-5 flex flex-col flex-grow">
                        {{-- Nama dan Tahun --}}
                        <h3 class="text-xl font-bold text-gray-900 mb-2 leading-tight">
                            <a href="{{ route('vehicles.show', $vehicle) }}" class="hover:text-indigo-600 transition duration-150 ease-in-out">
                                {{ $vehicle->brand }} {{ $vehicle->model }}
                            </a>
                        </h3>
                        <p class="text-gray-500 text-sm mb-1">{{ $vehicle->year }}</p>

                        {{-- Informasi Kendaraan --}}
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span>{{ $vehicle->category->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <span>{{ $vehicle->plate_number }}</span>
                            </div>
                        </div>

                        {{-- Harga dan Tombol Detail --}}
                        <div class="flex justify-between items-center mt-auto pt-4 border-t border-gray-100">
                            <div>
                                <p class="text-xl font-bold text-indigo-700">Rp {{ number_format($vehicle->daily_rate, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">per day</p>
                            </div>
                            <a href="{{ route('vehicles.show', $vehicle) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm hover:shadow-md">
                                Details
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Tautan Pagination --}}
            <div class="mt-10 flex justify-center">
                {{ $vehicles->links() }}
            </div>
            @endif
        </div>

    </div>

</x-guest-layout>