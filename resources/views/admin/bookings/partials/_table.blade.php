<!-- Modern responsive card/table hybrid design with action buttons -->
<div class="space-y-4">
    @forelse($bookings as $booking)
    <!-- Desktop Table View (hidden on mobile) -->
    <div class="hidden lg:block">
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow duration-200">
            <div class="grid grid-cols-12 gap-4 p-4 items-center">
                <!-- Customer Info -->
                @if($isAdminView)
                <div class="col-span-2">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $booking->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $booking->user->email }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Vehicle Info -->
                <div class="col-span-2">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $booking->vehicle->brand }}</p>
                            <p class="text-xs text-gray-500">{{ $booking->vehicle->model }} ({{ $booking->vehicle->plate_number }})</p>
                        </div>
                    </div>
                </div>

                <!-- Date Range -->
                <div class="col-span-2">
                    <div class="text-sm">
                        <p class="font-medium text-gray-900">{{ $booking->start_date->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500">to {{ $booking->end_date->format('d M Y') }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $booking->start_date->diffInDays($booking->end_date) }} days</p>
                    </div>
                </div>

                <!-- Price -->
                <div class="col-span-2">
                    <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500">Total Price</p>
                </div>

                <!-- Status -->
                <div class="col-span-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                        @if($booking->status == 'approved') bg-green-100 text-green-800 @endif
                        @if($booking->status == 'pending') bg-yellow-100 text-yellow-800 @endif
                        @if($booking->status == 'cancelled') bg-red-100 text-red-800 @endif
                        @if($booking->status == 'completed') bg-blue-100 text-blue-800 @endif
                    ">
                        <span class="w-2 h-2 mr-1 rounded-full
                            @if($booking->status == 'approved') bg-green-500 @endif
                            @if($booking->status == 'pending') bg-yellow-500 @endif
                            @if($booking->status == 'cancelled') bg-red-500 @endif
                            @if($booking->status == 'completed') bg-blue-500 @endif
                        "></span>
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>

                <!-- Actions -->
                @if($isAdminView)
                <div class="col-span-2 flex items-center justify-end space-x-2">
                    @if($booking->status == 'pending')
                    <form action="{{ route('admin.bookings.updateStatus', $booking) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="approved">
                        <button type="submit"
                            class="inline-flex items-center px-3 py-1.5 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            title="Approve Booking">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Approve
                        </button>
                    </form>
                    <form action="{{ route('admin.bookings.updateStatus', $booking) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit"
                            class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            title="Cancel Booking">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel
                        </button>
                    </form>
                    @elseif($booking->status == 'ongoing')
                    <form action="{{ route('admin.bookings.updateStatus', $booking) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit"
                            class="inline-flex items-center px-3 py-1.5 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            title="Complete Booking">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Complete
                        </button>
                    </form>
                    @else
                    <a href="#"
                        class="inline-flex items-center px-3 py-1.5 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        title="View Details">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Mobile Card View (hidden on desktop) -->
    <div class="lg:hidden">
        <!-- Optimized card layout with reduced padding and better space utilization -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
            <div class="p-3 space-y-2.5">
                <!-- Status Badge and Price -->
                <div class="flex justify-between items-start">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                        @if($booking->status == 'approved') bg-green-100 text-green-800 @endif
                        @if($booking->status == 'pending') bg-yellow-100 text-yellow-800 @endif
                        @if($booking->status == 'cancelled') bg-red-100 text-red-800 @endif
                        @if($booking->status == 'completed') bg-blue-100 text-blue-800 @endif
                    ">
                        <span class="w-2 h-2 mr-1 rounded-full
                            @if($booking->status == 'approved') bg-green-500 @endif
                            @if($booking->status == 'pending') bg-yellow-500 @endif
                            @if($booking->status == 'cancelled') bg-red-500 @endif
                            @if($booking->status == 'completed') bg-blue-500 @endif
                        "></span>
                        {{ ucfirst($booking->status) }}
                    </span>
                    <p class="text-sm font-bold text-gray-900">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                </div>

                <!-- Customer Info -->
                @if($isAdminView)
                <div class="flex items-center space-x-2.5">
                    <div class="flex-shrink-0 h-9 w-9 bg-indigo-100 rounded-full flex items-center justify-center">
                        <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $booking->user->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $booking->user->email }}</p>
                    </div>
                </div>
                @endif

                <!-- Vehicle Info -->
                <div class="flex items-center space-x-2.5">
                    <div class="flex-shrink-0 h-9 w-9 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</p>
                        <p class="text-xs text-gray-500">{{ $booking->vehicle->plate_number }}</p>
                    </div>
                </div>

                <!-- Rental Period - Compact Layout -->
                <div class="bg-gray-50 rounded-md p-2">
                    <p class="text-xs text-gray-500 mb-0.5">Rental Period</p>
                    <p class="text-sm font-medium text-gray-900">{{ $booking->start_date->format('d M Y') }} - {{ $booking->end_date->format('d M Y') }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $booking->start_date->diffInDays($booking->end_date) }} days</p>
                </div>

                <!-- Actions -->
                @if($isAdminView)
                <div class="flex flex-col space-y-2 pt-2">
                    @if($booking->status == 'pending')
                    <div class="grid grid-cols-2 gap-2">
                        <form action="{{ route('admin.bookings.updateStatus', $booking) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="approved">
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Approve
                            </button>
                        </form>
                        <form action="{{ route('admin.bookings.updateStatus', $booking) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel
                            </button>
                        </form>
                    </div>
                    @elseif($booking->status == 'ongoing')
                    <form action="{{ route('admin.bookings.updateStatus', $booking) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Complete Booking
                        </button>
                    </form>
                    @elseif($booking->status != 'approved')
                    <a href="{{ route('admin.bookings.show', $booking) }}"
                        class="w-full inline-flex items-center justify-center px-3 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View Details
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <!-- Empty State -->
    <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No bookings found</h3>
        <p class="mt-1 text-sm text-gray-500">No bookings match your current filters.</p>
    </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $bookings->links() }}
</div>