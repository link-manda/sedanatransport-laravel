<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaction Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Payment Proof
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($transactions as $transaction)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->booking->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->booking->vehicle->brand }} {{ $transaction->booking->vehicle->model }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if ($transaction->status == 'paid') bg-green-100 text-green-800 @endif
                                                @if ($transaction->status == 'unpaid') bg-yellow-100 text-yellow-800 @endif
                                                @if ($transaction->status == 'cancelled') bg-red-100 text-red-800 @endif                                                
                                                @if ($transaction->status == 'waiting_confirmation') bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300 @endif">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->booking->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4">
                                        @if ($transaction->payment_proof)
                                        <a href="{{ Storage::url($transaction->payment_proof) }}" target="_blank" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                            View Proof
                                        </a>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        {{-- Tampilkan tombol hanya jika statusnya waiting_confirmation --}}
                                        @if ($transaction->status == 'waiting_confirmation')
                                        <form action="{{ route('admin.transactions.updateStatus', $transaction->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="paid">
                                            <button type="submit" class="font-medium text-green-600 dark:text-green-500 hover:underline">Confirm</button>
                                        </form>
                                        <span class="mx-1">|</span>
                                        <form action="{{ route('admin.transactions.updateStatus', $transaction->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="failed">
                                            <button type="submit" class="font-medium text-red-600 dark:text-red-500 hover:underline">Reject</button>
                                        </form>
                                        @else
                                        -
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No transactions found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>