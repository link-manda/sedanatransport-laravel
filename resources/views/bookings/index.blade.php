<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Bookings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">

                @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
                @endif
                @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
                @endif

                @forelse ($bookings as $booking)
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg flex flex-col md:flex-row">
                    <!-- Vehicle Image -->
                    <div class="md:w-1/3">
                        <img src="{{ $booking->vehicle->photo ? Storage::url($booking->vehicle->photo) : 'https://placehold.co/600x400/e2e8f0/e2e8f0' }}" alt="{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}" class="object-cover h-48 w-full md:h-full">
                    </div>

                    <!-- Booking Details -->
                    <div class="p-6 flex-grow md:w-2/3">
                        <div class="flex flex-col sm:flex-row justify-between items-start">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</h3>
                                <p class="text-sm text-gray-500">{{ $booking->vehicle->plate_number }}</p>
                            </div>
                            <span class="mt-2 sm:mt-0 px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if ($booking->status == 'approved') bg-green-100 text-green-800 @endif
                                    @if ($booking->status == 'pending') bg-yellow-100 text-yellow-800 @endif
                                    @if ($booking->status == 'cancelled') bg-red-100 text-red-800 @endif
                                    @if ($booking->status == 'expired') bg-gray-100 text-gray-800 @endif
                                    @if ($booking->status == 'completed') bg-blue-100 text-blue-800 @endif">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>

                        <div class="mt-4 border-t border-gray-200 pt-4">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500">Start Date</p>
                                    <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($booking->start_date)->format('d M Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">End Date</p>
                                    <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($booking->end_date)->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <p class="text-gray-500">Total Price</p>
                                <p class="text-2xl font-bold text-indigo-600">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="mt-4 border-t border-gray-200 pt-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Payment Status:</p>
                                    @if ($booking->transaction)
                                    @if ($booking->transaction->status == 'paid')
                                    <p class="font-semibold text-green-600">Paid on {{ $booking->transaction->updated_at->format('d M Y') }}</p>
                                    @elseif ($booking->transaction->status == 'pending')
                                    <p class="font-semibold text-yellow-600">Waiting for Payment</p>
                                    @elseif ($booking->transaction->status == 'waiting_confirmation')
                                    <p class="font-semibold text-blue-600">Waiting for Confirmation</p>
                                    @if ($booking->transaction->payment_due_at)
                                    <p class="text-xs text-gray-500">Due: {{ $booking->transaction->payment_due_at->format('d M Y, H:i') }}</p>
                                    @endif
                                    @else
                                    <p class="font-semibold text-red-600">{{ ucfirst($booking->transaction->status) }}</p>
                                    @endif
                                    @else
                                    <p class="text-gray-500">-</p>
                                    @endif
                                </div>
                                {{-- Tombol "Pay Now" tidak memerlukan perubahan --}}
                                @if ($booking->status == 'approved' && $booking->transaction && $booking->transaction->status == 'pending')
                                <a href="{{ route('payment.show', $booking) }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    Pay Now
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 text-center">
                        <p>You have no bookings yet.</p>
                        <a href="{{ route('home') }}" class="mt-4 inline-block bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-indigo-700">
                            Find a Vehicle to Rent
                        </a>
                    </div>
                </div>
                @endforelse

                <div class="mt-6">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>