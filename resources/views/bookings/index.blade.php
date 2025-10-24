<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Bookings') }}
            </h2>
            <span class="text-sm text-gray-600">
                Total: {{ $bookings->total() }} {{ Str::plural('booking', $bookings->total()) }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Added statistics summary cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-600">Total</p>
                            <p class="text-xl font-semibold text-gray-900">{{ $bookings->total() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-600">Pending</p>
                            <p class="text-xl font-semibold text-gray-900">{{ $bookings->where('status', 'pending')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-cyan-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-600">Active</p>
                            <p class="text-xl font-semibold text-gray-900">{{ $bookings->whereIn('status', ['approved', 'ongoing'])->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-600">Completed</p>
                            <p class="text-xl font-semibold text-gray-900">{{ $bookings->where('status', 'completed')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Added filter section --}}
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6 border border-gray-200">
                <form method="GET" action="{{ route('bookings.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Vehicle or plate..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                        <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="md:col-span-4 flex gap-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filter
                        </button>
                        <a href="{{ route('bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Messages --}}
            @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-md">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
            @endif

            @if (session('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-md">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
            @endif

            {{-- Improved empty state --}}
            @if ($bookings->isEmpty())
            <div class="bg-white rounded-lg shadow-sm p-12 text-center border border-gray-200">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No bookings found</h3>
                <p class="text-gray-600 mb-6">You haven't made any bookings yet. Start exploring our vehicles!</p>
                <a href="{{ route('vehicles.index') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    Browse Vehicles
                </a>
            </div>
            @else
            {{-- Modern card design with better layout --}}
            <div class="space-y-4">
                @foreach ($bookings as $booking)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                    <div class="p-4 md:p-6">
                        <div class="flex flex-col lg:flex-row gap-4">
                            {{-- Vehicle image - larger and more prominent --}}
                            <div class="flex-shrink-0">
                                <div class="w-full lg:w-48 h-32 rounded-lg overflow-hidden bg-gray-100">
                                    @if ($booking->vehicle->photo)
                                    <img src="{{ Storage::url($booking->vehicle->photo) }}" alt="{{ $booking->vehicle->model }}" class="w-full h-full object-cover">
                                    @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Main content area with better organization --}}
                            <div class="flex-grow min-w-0">
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                    {{-- Vehicle details --}}
                                    <div class="flex-grow">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                            {{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}
                                        </h3>
                                        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600 mb-3">
                                            <span class="inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                </svg>
                                                {{ $booking->vehicle->plate_number }}
                                            </span>
                                            <span class="inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ $booking->vehicle->year }}
                                            </span>
                                        </div>

                                        {{-- Rental period with better visual design --}}
                                        <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                            <div class="flex items-center text-sm">
                                                <div class="flex-1">
                                                    <p class="text-gray-500 text-xs mb-1">Start Date</p>
                                                    <p class="font-medium text-gray-900">{{ $booking->start_date->format('d M Y') }}</p>
                                                </div>
                                                <div class="px-3">
                                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-gray-500 text-xs mb-1">End Date</p>
                                                    <p class="font-medium text-gray-900">{{ $booking->end_date->format('d M Y') }}</p>
                                                </div>
                                                <div class="ml-4 text-right">
                                                    <p class="text-gray-500 text-xs mb-1">Duration</p>
                                                    <p class="font-semibold text-indigo-600">{{ $booking->start_date->diffInDays($booking->end_date) + 1 }} days</p>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Status timeline/progress indicator --}}
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="text-xs font-medium text-gray-600">Status:</span>
                                            <div class="flex items-center gap-1">
                                                @php
                                                $statuses = ['pending', 'approved', 'cancelled', 'completed'];
                                                $currentIndex = array_search($booking->status, $statuses);
                                                if ($currentIndex === false) $currentIndex = -1;
                                                @endphp

                                                @foreach($statuses as $index => $status)
                                                <div class="flex items-center">
                                                    <div class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-medium
                                                            {{ $index <= $currentIndex ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                                                        @if($index < $currentIndex)
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                            </svg>
                                                            @else
                                                            {{ $index + 1 }}
                                                            @endif
                                                    </div>
                                                    @if($index < count($statuses) - 1)
                                                        <div class="w-8 h-0.5 {{ $index < $currentIndex ? 'bg-indigo-600' : 'bg-gray-200' }}">
                                                </div>
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Status badges --}}
                                    <div class="flex flex-wrap gap-2">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                                @switch($booking->status)
                                                    @case('pending') bg-yellow-100 text-yellow-800 @break
                                                    @case('approved') bg-blue-100 text-blue-800 @break
                                                    @case('ongoing') bg-cyan-100 text-cyan-800 @break
                                                    @case('completed') bg-green-100 text-green-800 @break
                                                    @case('cancelled')
                                                    @case('expired') bg-red-100 text-red-800 @break
                                                    @default bg-gray-100 text-gray-800
                                                @endswitch">
                                            <span class="w-1.5 h-1.5 rounded-full mr-1.5
                                                    @switch($booking->status)
                                                        @case('pending') bg-yellow-600 @break
                                                        @case('approved') bg-blue-600 @break
                                                        @case('ongoing') bg-cyan-600 @break
                                                        @case('completed') bg-green-600 @break
                                                        @case('cancelled')
                                                        @case('expired') bg-red-600 @break
                                                        @default bg-gray-600
                                                    @endswitch"></span>
                                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                        </span>

                                        @if ($booking->transaction)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                                @switch($booking->transaction->status)
                                                    @case('pending') bg-yellow-100 text-yellow-800 @break
                                                    @case('waiting_confirmation') bg-purple-100 text-purple-800 @break
                                                    @case('paid') bg-green-100 text-green-800 @break
                                                    @case('failed')
                                                    @case('expired') bg-red-100 text-red-800 @break
                                                    @default bg-gray-100 text-gray-800
                                                @endswitch">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                            Payment: {{ ucfirst(str_replace('_', ' ', $booking->transaction->status)) }}
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Price and actions sidebar --}}
                                <div class="flex-shrink-0 md:w-48 md:text-right">
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600 mb-1">Total Price</p>
                                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                                        <p class="text-xs text-gray-500 mt-1">Rp {{ number_format($booking->vehicle->daily_rate, 0, ',', '.') }} / day</p>
                                    </div>

                                    {{-- Payment deadline alert --}}
                                    @if ($booking->status == 'approved' && $booking->transaction && $booking->transaction->status == 'pending' && $booking->transaction->payment_due_at)
                                    <div class="mb-4 p-3 {{ $booking->transaction->payment_due_at->isPast() ? 'bg-red-50 border border-red-200' : 'bg-yellow-50 border border-yellow-200' }} rounded-lg">
                                        <p class="text-xs font-medium {{ $booking->transaction->payment_due_at->isPast() ? 'text-red-800' : 'text-yellow-800' }} mb-1">
                                            {{ $booking->transaction->payment_due_at->isPast() ? 'Payment Overdue!' : 'Pay Before' }}
                                        </p>
                                        <p class="text-xs {{ $booking->transaction->payment_due_at->isPast() ? 'text-red-600' : 'text-yellow-600' }}">
                                            {{ $booking->transaction->payment_due_at->format('d M Y, H:i') }}
                                        </p>
                                        @if (!$booking->transaction->payment_due_at->isPast())
                                        <p class="text-xs {{ $booking->transaction->payment_due_at->isPast() ? 'text-red-600' : 'text-yellow-600' }} mt-1">
                                            ({{ $booking->transaction->payment_due_at->diffForHumans(null, true) }} left)
                                        </p>
                                        @endif
                                    </div>
                                    @endif

                                    {{-- Action buttons with better styling --}}
                                    @if ($booking->status == 'approved' && $booking->transaction && $booking->transaction->status == 'pending')
                                    <a href="{{ route('payment.show', $booking) }}" class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        Pay Now
                                    </a>
                                    @elseif ($booking->transaction && $booking->transaction->status == 'waiting_confirmation')
                                    <a href="{{ route('payment.show', $booking) }}" class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-purple-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View Payment
                                    </a>
                                    @elseif ($booking->status == 'ongoing')
                                    <div class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-cyan-100 border border-cyan-200 rounded-lg font-semibold text-sm text-cyan-800">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        Rental Active
                                    </div>
                                    @elseif ($booking->status == 'completed')
                                    <div class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-green-100 border border-green-200 rounded-lg font-semibold text-sm text-green-800">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Completed
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Rejection reason - full width at bottom --}}
                    @if (($booking->status == 'cancelled' || $booking->status == 'expired') && $booking->transaction && $booking->transaction->rejection_reason)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-red-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <p class="font-semibold text-red-800 text-sm">Payment Rejected</p>
                                    <p class="text-red-700 text-sm mt-1">{{ $booking->transaction->rejection_reason }}</p>
                                    <p class="text-red-600 text-xs mt-2">Please contact support for assistance with refund.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>
    </div>
</x-app-layout>