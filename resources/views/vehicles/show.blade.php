<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Vehicle Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            @if (session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 grid grid-cols-1 lg:grid-cols-5 gap-8">

                    <!-- Kolom Gambar dan Detail -->
                    <div class="lg:col-span-3">
                        <img src="{{ Storage::url($vehicle->photo) }}" alt="{{ $vehicle->name }}" class="w-full h-auto object-cover rounded-lg shadow-md mb-6">

                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900">{{ $vehicle->brand }} {{ $vehicle->model }}</h1>
                        <p class="text-lg text-gray-600 mt-1">{{ $vehicle->category->name }} &bull; {{ $vehicle->year }}</p>

                        <div class="mt-6 border-t pt-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Specifications</h3>
                            <ul class="space-y-2 text-gray-700">
                                <li><span class="font-semibold w-24 inline-block">Brand:</span> {{ $vehicle->brand }}</li>
                                <li><span class="font-semibold w-24 inline-block">Model:</span> {{ $vehicle->model }}</li>
                                <li><span class="font-semibold w-24 inline-block">Year:</span> {{ $vehicle->year }}</li>
                                <li><span class="font-semibold w-24 inline-block">Plate:</span> {{ $vehicle->plate_number }}</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Kolom Booking -->
                    <div class="lg:col-span-2">
                        <div class="bg-gray-50 border rounded-lg p-6 sticky top-8">
                            <p class="text-2xl font-bold text-indigo-600">
                                Rp {{ number_format($vehicle->daily_rate, 0, ',', '.') }}
                                <span class="text-base font-normal text-gray-500">/ day</span>
                            </p>

                            <div class="mt-4 text-sm text-gray-600">
                                Status:
                                @if ($vehicle->status == 'available')
                                <span class="font-semibold text-green-600">Available</span>
                                @else
                                <span class="font-semibold text-red-600">Not Available</span>
                                @endif
                            </div>

                            @auth
                            {{-- Tampilkan form hanya jika pengguna sudah login --}}
                            <form action="{{ route('bookings.store') }}" method="POST" class="mt-6">
                                @csrf
                                <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

                                <div class="space-y-4">
                                    <div>
                                        <x-input-label for="start_date" :value="__('Start Date')" />
                                        <x-text-input id="start_date" class="block mt-1 w-full" type="text" name="start_date" :value="old('start_date')" required autocomplete="off" placeholder="Select start date" />
                                        <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-input-label for="end_date" :value="__('End Date')" />
                                        <x-text-input id="end_date" class="block mt-1 w-full" type="text" name="end_date" :value="old('end_date')" required autocomplete="off" placeholder="Select end date" />
                                        <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                                    </div>
                                </div>

                                @if ($vehicle->status == 'available')
                                <x-primary-button class="w-full justify-center mt-6 text-lg py-3">
                                    {{ __('Book Now') }}
                                </x-primary-button>
                                @else
                                <button type="button" class="w-full text-center mt-6 text-lg py-3 bg-gray-400 text-white font-bold rounded-md cursor-not-allowed">
                                    Not Available
                                </button>
                                @endif
                            </form>
                            @endauth

                            @guest
                            {{-- Tampilkan pesan dan tombol login jika pengguna adalah tamu --}}
                            <div class="mt-6 border-t pt-6">
                                <p class="text-center text-gray-700">You must be logged in to book this vehicle.</p>
                                <a href="{{ route('login') }}" class="w-full inline-block text-center mt-4 text-lg py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-md transition duration-300">
                                    Login to Book
                                </a>
                            </div>
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bookedDates = {!! Illuminate\Support\Js::from($booked_dates) !!};

            const picker = new Litepicker({
                element: document.getElementById('start_date'),
                elementEnd: document.getElementById('end_date'),
                singleMode: false,
                allowRepick: true,
                minDate: new Date(),
                format: 'YYYY-MM-DD',

                lockDaysFilter: (day) => {
                    const d = day.dateInstance;
                    const dateString = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;

                    if (bookedDates.includes(dateString)) {
                        // Tambahkan label "Booked"
                        day.html += '<div style="font-size: 9px; color: red; text-align: center; position: absolute; bottom: 0; width: 100%;">Booked</div>';
                        return true; // Kunci tanggal ini
                    }
                    return false;
                },

                // Opsional: cegah pemilihan rentang yang mengandung tanggal terkunci
                onSelect: (start, end) => {
                    if (!start || !end) return;

                    let current = new Date(start.dateInstance);
                    const endDate = new Date(end.dateInstance);
                    const invalidInRange = [];

                    while (current <= endDate) {
                        const y = current.getFullYear();
                        const m = String(current.getMonth() + 1).padStart(2, '0');
                        const d = String(current.getDate()).padStart(2, '0');
                        const dateStr = `${y}-${m}-${d}`;

                        if (bookedDates.includes(dateStr)) {
                            invalidInRange.push(dateStr);
                        }

                        current.setDate(current.getDate() + 1);
                    }

                    if (invalidInRange.length > 0) {
                        alert('Rentang tanggal mengandung hari yang sudah dipesan: ' + invalidInRange.join(', '));
                        // Kosongkan input
                        document.getElementById('start_date').value = '';
                        document.getElementById('end_date').value = '';
                        picker.setDate(null);
                    }
                },

                buttonText: {
                    previousMonth: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>`,
                    nextMonth: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>`,
                },
            });
        });
    </script>
    @endpush
</x-app-layout>