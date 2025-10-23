<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Vehicle Details') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Notifikasi Sukses --}}
            @if (session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif
            {{-- Notifikasi Error --}}
            @if (session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                {{-- Grid Responsif Utama --}}
                <div class="p-6 md:p-8 grid grid-cols-1 lg:grid-cols-5 gap-8">
                    {{-- Kolom Gambar dan Detail (lg:col-span-3) --}}
                    <div class="lg:col-span-3">
                        {{-- Gambar Kendaraan --}}
                        <div class="mb-6 aspect-w-16 aspect-h-9 rounded-lg overflow-hidden shadow-md">
                            @if ($vehicle->photo)
                            <img src="{{ Storage::url($vehicle->photo) }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="w-full h-full object-cover">
                            @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            @endif
                        </div>
                        {{-- Judul dan Detail Kendaraan --}}
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $vehicle->brand }} {{ $vehicle->model }}</h1>
                        <p class="text-lg text-gray-600 mb-4">({{ $vehicle->year }}) - {{ $vehicle->category->name ?? 'Uncategorized' }}</p>
                        <div class="flex items-center mb-4">
                            <span class="text-sm font-medium text-gray-700 mr-2">Status:</span>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full capitalize
                                @if($vehicle->status == 'available') bg-green-100 text-green-800 border border-green-200
                                @elseif($vehicle->status == 'rented') bg-orange-100 text-orange-800 border border-orange-200
                                @elseif($vehicle->status == 'maintenance') bg-yellow-100 text-yellow-800 border border-yellow-200
                                @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                                {{ $vehicle->status }}
                            </span>
                        </div>
                        <p class="text-gray-700 mb-2"><span class="font-medium">License Plate:</span> {{ $vehicle->license_plate }}</p>
                        <p class="text-2xl font-bold text-indigo-700 mb-6">Rp {{ number_format($vehicle->daily_rate, 0, ',', '.') }} <span class="text-sm font-normal text-gray-500">/ day</span></p>
                    </div>
                    {{-- Kolom Booking (lg:col-span-2) --}}
                    <div class="lg:col-span-2 lg:sticky lg:top-8 self-start">
                        <div class="bg-gray-50 p-6 rounded-lg shadow-md border border-gray-200">
                            <h2 class="text-xl font-semibold mb-5 text-gray-800">Book This Vehicle</h2>
                            <form method="POST" action="{{ route('bookings.store') }}">
                                @csrf
                                <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                                {{-- Input Start Date --}}
                                <div class="mb-4">
                                    <x-input-label for="start_date" :value="__('Start Date')" />
                                    <x-text-input id="start_date" class="block mt-1 w-full" type="text" name="start_date" :value="old('start_date')" required autocomplete="off" placeholder="Select start date" />
                                    <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                                </div>
                                {{-- Input End Date --}}
                                <div class="mb-6">
                                    <x-input-label for="end_date" :value="__('End Date')" />
                                    <x-text-input id="end_date" class="block mt-1 w-full" type="text" name="end_date" :value="old('end_date')" required autocomplete="off" placeholder="Select end date" />
                                    <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                                    <p class="text-xs text-gray-500 mt-1">*Dates marked visually unavailable will be validated upon submission.</p>
                                </div>
                                {{-- Tombol Booking / Login --}}
                                <div class="mt-6">
                                    @auth
                                    <x-primary-button class="w-full justify-center py-3 text-base">
                                        {{ __('Book Now') }}
                                    </x-primary-button>
                                    @else
                                    <a href="{{ route('login') }}" class="w-full block text-center">
                                        <x-secondary-button type="button" class="w-full justify-center py-3 text-base bg-gray-200 hover:bg-gray-300 text-gray-700">
                                            {{ __('Login to Book') }}
                                        </x-secondary-button>
                                    </a>
                                    @endauth
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Flatpickr Styles & Script --}}
    @push('scripts')
    <style>
        /* Styling untuk tanggal yang sudah dibooking */
        .booked-date {
            background-color: #fee2e2 !important;
            /* tailwind red-100 */
            text-decoration: line-through !important;
            color: #d1d5db !important;
            /* tailwind gray-300 */
            cursor: not-allowed !important;
            pointer-events: none;
            /* opsional: benar-benar nonaktifkan interaksi */
        }
    </style>
    <script>
        // Helper: format Date ke YYYY-MM-DD sesuai zona lokal
        function formatDateToLocal(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const bookedDates = {!! Js::from($bookedDates) !!};

            flatpickr("#start_date", {
                mode: "range",
                dateFormat: "Y-m-d", // hanya untuk tampilan internal Flatpickr
                minDate: "today",
                disable: bookedDates,
                allowInput: false,
                onChange: function(selectedDates) {
                    if (selectedDates.length === 2) {
                        document.getElementById('start_date').value = formatDateToLocal(selectedDates[0]);
                        document.getElementById('end_date').value = formatDateToLocal(selectedDates[1]);
                    } else if (selectedDates.length === 1) {
                        document.getElementById('start_date').value = formatDateToLocal(selectedDates[0]);
                        document.getElementById('end_date').value = '';
                    } else {
                        document.getElementById('start_date').value = '';
                        document.getElementById('end_date').value = '';
                    }
                },
                onDayCreate: function(dObj, dStr, fp, dayElem) {
                    const dateStr = formatDateToLocal(dayElem.dateObj);
                    if (bookedDates.includes(dateStr)) {
                        dayElem.classList.add('booked-date');
                        dayElem.title = 'Already Booked';
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>