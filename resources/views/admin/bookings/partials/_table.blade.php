<div class="overflow-x-auto">
    <table class="min-w-full bg-white">
        <thead class="bg-gray-200">
            <tr>
                @if($isAdminView)
                <th class="py-3 px-4 uppercase font-semibold text-sm">Customer</th>
                @endif
                <th class="py-3 px-4 uppercase font-semibold text-sm">Vehicle</th>
                <th class="py-3 px-4 uppercase font-semibold text-sm">Date Range</th>
                <th class="py-3 px-4 uppercase font-semibold text-sm">Total Price</th>
                <th class="py-3 px-4 uppercase font-semibold text-sm">Status</th>
                @if($isAdminView)
                <th class="py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                @endif
            </tr>
        </thead>
        <tbody class="text-gray-700">
            @forelse($bookings as $booking)
            <tr>
                @if($isAdminView)
                <td class="py-3 px-4">{{ $booking->user->name }}</td>
                @endif
                <td class="py-3 px-4">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</td>
                <td class="py-3 px-4">{{ $booking->start_date->format('d M Y') }} - {{ $booking->end_date->format('d M Y') }}</td>
                <td class="py-3 px-4">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                <td class="py-3 px-4">
                    <span class="px-2 py-1 font-semibold leading-tight rounded-full
                        @if($booking->status == 'approved') bg-green-100 text-green-800 @endif
                        @if($booking->status == 'pending') bg-yellow-100 text-yellow-800 @endif
                        @if($booking->status == 'cancelled') bg-red-100 text-red-800 @endif
                        @if($booking->status == 'completed') bg-blue-100 text-blue-800 @endif
                    ">
                        {{ ucfirst($booking->status) }}
                    </span>
                </td>
                @if($isAdminView && $booking->status == 'pending')
                <td class="py-3 px-4 flex items-center space-x-2">
                    <form action="{{ route('admin.bookings.updateStatus', $booking) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="text-green-500 hover:text-green-700 font-bold">Approve</button>
                    </form>
                    <form action="{{ route('admin.bookings.updateStatus', $booking) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="text-red-500 hover:text-red-700 font-bold">Cancel</button>
                    </form>
                </td>
                @elseif($isAdminView)
                <td class="py-3 px-4 text-center">-</td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ $isAdminView ? 6 : 4 }}" class="text-center py-4">No bookings found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">
    {{ $bookings->links() }}
</div>