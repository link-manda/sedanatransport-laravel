<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Bookings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Menampilkan Pesan Sukses/Error --}}
                    @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-200 rounded-md">
                        {{ session('success') }}
                    </div>
                    @endif
                    @if (session('error'))
                    <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-200 rounded-md">
                        {{ session('error') }}
                    </div>
                    @endif

                    <h3 class="text-2xl font-semibold mb-6">Your Rental History</h3>
                    <p class="text-sm text-gray-600 mb-6">
                        {{ $bookings->total() }} {{ Str::plural('Booking', $bookings->total()) }} found.
                    </p>

                    @if ($bookings->isEmpty())
                    <div class="text-center py-10 border border-dashed rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17.25v2.25M15 17.25v2.25M6.375 12.375c-.352 0-.675.056-1 .15V8.25a.75.75 0 01.75-.75h1.5a.75.75 0 01.75.75v3.25m6.375 0c.352 0 .675.056 1 .15V8.25a.75.75 0 00-.75-.75h-1.5a.75.75 0 00-.75.75v3.25m6.375 0c.352 0 .675.056 1 .15V8.25a.75.75 0 00-.75-.75h-1.5a.75.75 0 00-.75.75v3.25" />
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 13.5c0-1.657 1.343-3 3-3h9c1.657 0 3 1.343 3 3v2.25c0 1.657-1.343 3-3 3h-9c-1.657 0-3-1.343-3-3V13.5z" />
                        </svg>
                        <p class="mt-4 text-gray-600">You haven't made any bookings yet.</p>
                    </div>
                    @else
                    <div class="space-y-6">
                        @foreach ($bookings as $booking)
                        {{-- 1. PERUBAHAN: Hapus flex class dari Card utama --}}
                        <div class="border rounded-lg p-4 md:p-6">
                            {{-- 2. PERUBAHAN: Buat inner flex container UNTUK item di atas (Gbr, Detail, Status) --}}
                            <div class="flex flex-col md:flex-row md:items-start space-y-4 md:space-y-0 md:space-x-6">
                                {{-- Kolom Kiri: Gambar --}}
                                <div class="flex-shrink-0 w-full md:w-32 h-32 md:h-24">
                                    @if ($booking->vehicle->photo)
                                    <img src="{{ Storage::url($booking->vehicle->photo) }}" alt="{{ $booking->vehicle->model }}" class="w-full h-full object-cover rounded-md">
                                    @else
                                    <img src="https://placehold.co/128x96/e2e8f0/e2e8f0?text=No+Image" alt="No Image Available" class="w-full h-full object-cover rounded-md bg-gray-100">
                                    @endif
                                </div>

                                {{-- Kolom Tengah: Detail Kendaraan & Tanggal --}}
                                <div class="flex-grow">
                                    <h4 class="text-lg font-semibold text-gray-800">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }} ({{ $booking->vehicle->year }})</h4>
                                    <p class="text-sm text-gray-500">Plate: {{ $booking->vehicle->plate_number }}</p>
                                    <p class="text-sm text-gray-600 mt-2">
                                        {{ $booking->start_date->format('d M Y') }} - {{ $booking->end_date->format('d M Y') }}
                                        <span class="text-gray-500">({{ $booking->start_date->diffInDays($booking->end_date) + 1 }} days)</span>
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Rate: Rp {{ number_format($booking->vehicle->daily_rate, 0, ',', '.') }} / day
                                    </p>
                                </div>

                                {{-- Kolom Kanan: Harga, Status, Aksi --}}
                                <div class="w-full md:w-60 flex-shrink-0 space-y-2 text-left md:text-right">
                                    {{-- Harga Total --}}
                                    <p class="text-lg font-bold text-gray-800">
                                        Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                    </p>

                                    {{-- Status Booking --}}
                                    <div class="flex items-center justify-start md:justify-end">
                                        <span class="mr-2 text-xs text-gray-500">Booking:</span>
                                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    @switch($booking->status)
                                                        @case('pending') bg-yellow-100 text-yellow-800 @break
                                                        @case('approved') bg-blue-100 text-blue-800 @break
                                                        @case('ongoing') bg-cyan-100 text-cyan-800 @break
                                                        @case('completed') bg-green-100 text-green-800 @break
                                                        @case('cancelled')
                                                        @case('expired') bg-red-100 text-red-800 @break
                                                        @case('waiting_confirmation') bg-purple-100 text-purple-800 @break
                                                        @default bg-gray-100 text-gray-800
                                                    @endswitch
                                                ">
                                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                        </span>
                                    </div>

                                    {{-- Status Pembayaran --}}
                                    @if ($booking->transaction)
                                    <div class="flex items-center justify-start md:justify-end">
                                        <span class="mr-2 text-xs text-gray-500">Payment:</span>
                                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full
                                                        @switch($booking->transaction->status)
                                                            @case('pending') bg-yellow-100 text-yellow-800 @break
                                                            @case('waiting_confirmation') bg-purple-100 text-purple-800 @break
                                                            @case('paid') bg-green-100 text-green-800 @break
                                                            @case('failed')
                                                            @case('expired') bg-red-100 text-red-800 @break
                                                            @default bg-gray-100 text-gray-800
                                                        @endswitch
                                                    ">
                                            {{ ucfirst(str_replace('_', ' ', $booking->transaction->status)) }}
                                        </span>
                                    </div>
                                    @endif

                                    {{-- Batas Waktu Pembayaran --}}
                                    @if ($booking->status == 'approved' && $booking->transaction && $booking->transaction->status == 'pending' && $booking->transaction->payment_due_at)
                                    <p class="text-xs text-gray-500 {{ $booking->transaction->payment_due_at->isPast() ? 'text-red-600 font-medium' : '' }}">
                                        Pay before: {{ $booking->transaction->payment_due_at->format('d M Y, H:i') }}
                                        @if ($booking->transaction->payment_due_at->isPast())
                                        (Overdue)
                                        @else
                                        ({{ $booking->transaction->payment_due_at->diffForHumans(null, true) }} left)
                                        @endif
                                    </p>
                                    @endif

                                    {{-- 3. PERUBAHAN: Alasan Penolakan DIPINDAH dari sini --}}

                                    {{-- Tombol Aksi --}}
                                    <div class="mt-3 pt-3 border-t border-gray-100 flex justify-start md:justify-end">
                                        @if ($booking->status == 'approved' && $booking->transaction && $booking->transaction->status == 'pending')
                                        <a href="{{ route('payment.show', $booking) }}">
                                            <x-primary-button> {{ __('Pay Now') }} </x-primary-button>
                                        </a>
                                        @elseif ($booking->transaction && $booking->transaction->status == 'waiting_confirmation')
                                        <a href="{{ route('payment.show', $booking) }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">
                                            {{ __('View Payment') }}
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- Akhir dari inner flex container --}}

                            {{-- 4. PERUBAHAN: Alasan Penolakan DITEMPATKAN DI SINI (di luar inner flex container) --}}
                            @if (($booking->status == 'cancelled' || $booking->status == 'expired') && $booking->transaction && $booking->transaction->rejection_reason)
                            {{-- Div ini akan membentang penuh di dalam card. --}}
                            {{-- Kita tambahkan pemisah (border-t) hanya di desktop (md:border-t) --}}
                            <div class="mt-4 pt-4 md:border-t border-gray-100">
                                <div class="p-3 bg-red-50 border border-red-200 rounded-md text-left text-sm"> {{-- Ubah ke text-sm agar lebih mudah dibaca --}}
                                    <p class="font-semibold text-red-800">Payment Rejected:</p>
                                    <p class="text-red-700 mt-1">{{ $booking->transaction->rejection_reason }}</p>
                                    <p class="text-red-700 mt-2">Contact support for refund.</p>
                                </div>
                            </div>
                            @endif

                        </div> {{-- Akhir dari Card Utama --}}
                        @endforeach
                    </div>

                    {{-- Pagination Links --}}
                    <div class="mt-8">
                        {{ $bookings->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>