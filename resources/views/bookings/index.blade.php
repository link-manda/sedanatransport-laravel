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

                    {{-- Menampilkan Pesan Sukses --}}
                    @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-200 rounded-md">
                        {{ session('success') }}
                    </div>
                    @endif

                    {{-- Menampilkan Pesan Error --}}
                    @if (session('error'))
                    <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-200 rounded-md">
                        {{ session('error') }}
                    </div>
                    @endif

                    <h3 class="text-lg font-medium mb-4">Your Rental History</h3>

                    @if ($bookings->isEmpty())
                    <p>You haven't made any bookings yet.</p>
                    @else
                    <div class="space-y-4">
                        @foreach ($bookings as $booking)
                        <div class="border rounded-lg p-4 flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                            <div class="flex-1">
                                <div class="flex items-center space-x-4">
                                    @if ($booking->vehicle->photo)
                                    <img src="{{ Storage::url($booking->vehicle->photo) }}" alt="{{ $booking->vehicle->model }}" class="w-20 h-16 object-cover rounded">
                                    @else
                                    <img src="https://placehold.co/80x64/e2e8f0/e2e8f0?text=No+Image" alt="No Image Available" class="w-20 h-16 object-cover rounded">
                                    @endif
                                    <div>
                                        <h4 class="font-semibold">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }} ({{ $booking->vehicle->year }})</h4>
                                        <p class="text-sm text-gray-600">Plate: {{ $booking->vehicle->plate_number }}</p>
                                        <p class="text-sm text-gray-600">
                                            {{ $booking->start_date->format('d M Y') }} - {{ $booking->end_date->format('d M Y') }}
                                            ({{ $booking->start_date->diffInDays($booking->end_date) + 1 }} days)
                                        </p>
                                        <p class="text-sm font-semibold">Total: Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right space-y-2 w-full md:w-auto">
                                {{-- Booking Status --}}
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                                            @switch($booking->status)
                                                @case('pending') bg-yellow-100 text-yellow-800 @break
                                                @case('approved') bg-blue-100 text-blue-800 @break
                                                @case('completed') bg-green-100 text-green-800 @break
                                                @case('cancelled') bg-gray-100 text-gray-800 @break
                                                @case('expired') bg-orange-100 text-orange-800 @break
                                                @case('completed') bg-blue-100 text-blue-800 @break                                                
                                                @default bg-gray-100 text-gray-800
                                            @endswitch
                                        ">
                                    {{ ucfirst($booking->status) }}
                                </span>

                                {{-- Payment Status & Deadline Info --}}
                                {{-- Kita hanya tampilkan jika ada transaksi terkait --}}
                                @if ($booking->transaction)
                                <div class="mt-1">
                                    <span class="text-xs text-gray-500">
                                        Payment:
                                        <span class="font-medium
                                                        @switch($booking->transaction->status)
                                                            @case('pending') text-yellow-600 @break
                                                            @case('waiting_confirmation') text-blue-600 @break
                                                            @case('paid') text-green-600 @break
                                                            @case('failed') text-red-600 @break
                                                            @case('expired') text-orange-600 @break
                                                            @default text-gray-600
                                                        @endswitch
                                                    ">
                                            {{ ucfirst(str_replace('_', ' ', $booking->transaction->status)) }}
                                        </span>
                                    </span>

                                    {{-- Tampilkan Batas Waktu HANYA jika booking 'approved' DAN transaksi 'pending' DAN payment_due_at ADA --}}
                                    @if ($booking->status == 'approved' && $booking->transaction->status == 'pending' && $booking->transaction->payment_due_at)
                                    @if ($booking->transaction->payment_due_at->isPast())
                                    <p class="text-xs text-red-600 font-medium">Payment overdue</p>
                                    @else
                                    {{-- Perbaiki di sini: Pastikan $booking->transaction->payment_due_at tidak null sebelum diffForHumans --}}
                                    <p class="text-xs text-gray-500">
                                        Pay before: {{ $booking->transaction->payment_due_at->format('d M Y, H:i') }}
                                        ({{ $booking->transaction->payment_due_at->diffForHumans() }})
                                    </p>
                                    @endif
                                    @endif
                                </div>
                                @endif

                                {{-- Tombol Aksi: Pay Now --}}
                                {{-- Tampilkan HANYA jika booking 'approved' DAN transaksi 'pending' --}}
                                @if ($booking->status == 'approved' && $booking->transaction && $booking->transaction->status == 'pending')
                                <a href="{{ route('payment.show', $booking) }}" class="inline-block">
                                    <x-primary-button>
                                        {{ __('Pay Now') }}
                                    </x-primary-button>
                                </a>
                                @endif

                                {{-- Tambahkan tombol aksi lain jika perlu (misal: Cancel jika masih pending) --}}
                                {{-- @if ($booking->status == 'pending')
                                            <form action="{{ route('bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                @csrf
                                @method('PATCH')
                                <x-danger-button type="submit">Cancel</x-danger-button>
                                </form>
                                @endif --}}
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Pagination Links --}}
                    <div class="mt-6">
                        {{ $bookings->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-app-layout>