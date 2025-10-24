<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Jobs\SendBookingApprovedEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BookingManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'vehicle']);

        // Search by customer name, email, or vehicle
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                    ->orWhereHas('vehicle', function ($vehicleQuery) use ($search) {
                        $vehicleQuery->where('brand', 'like', "%{$search}%")
                            ->orWhere('model', 'like', "%{$search}%")
                            ->orWhere('plate_number', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('start_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('end_date', '<=', $request->to_date);
        }

        $bookings = $query->latest()->paginate(10)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function updateStatus(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return redirect()->route('admin.bookings.index')->with('error', 'Booking cannot be approved as it is not in pending status.');
        }

        try {
            $booking->update(['status' => 'approved']);

            if ($booking->vehicle) {
                $booking->vehicle->update(['status' => 'rented']);
            }

            $transaction = $booking->transaction;

            if ($transaction) {
                $dueDate = Carbon::now()->addHours(24);
                $transaction->update([
                    'payment_due_at' => $dueDate
                ]);

                $transaction->refresh();

                if (!$transaction->payment_due_at) {
                    Log::error("Failed to save payment_due_at for Transaction ID: {$transaction->id}");
                    return redirect()->route('admin.bookings.index')->with('error', 'Failed to set payment deadline. Please check logs.');
                }
            } else {
                Log::error("Transaction not found for booking ID: {$booking->id} during approval.");
                return redirect()->route('admin.bookings.index')->with('error', 'Associated transaction not found.');
            }

            // SendBookingApprovedEmail::dispatch($booking);

        } catch (\Exception $e) {
            Log::error("Error approving booking ID {$booking->id}: " . $e->getMessage());
            return redirect()->route('admin.bookings.index')->with('error', 'Failed to approve booking. An error occurred.');
        }

        return redirect()->route('admin.bookings.index')->with('success', 'Booking approved successfully.');
    }

    public function cancel(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return redirect()->route('admin.bookings.index')->with('error', 'Booking cannot be rejected as it is not in pending status.');
        }

        try {
            $booking->update(['status' => 'cancelled']);

            if ($booking->transaction) {
                $booking->transaction->update(['status' => 'failed']);
            }

            if ($booking->vehicle && $booking->vehicle->status === 'rented') {
                $booking->vehicle->update(['status' => 'available']);
            }
        } catch (\Exception $e) {
            Log::error("Error rejecting booking ID {$booking->id}: " . $e->getMessage());
            return redirect()->route('admin.bookings.index')->with('error', 'Failed to reject booking. An error occurred.');
        }

        return redirect()->route('admin.bookings.index')->with('success', 'Booking cancelled successfully.');
    }

    public function complete(Booking $booking)
    {
        if ($booking->status !== 'ongoing') {
            return redirect()->route('admin.bookings.index')->with('error', 'Only ongoing bookings can be marked as completed.');
        }

        try {
            $booking->update(['status' => 'completed']);

            // Set vehicle back to available
            if ($booking->vehicle) {
                $booking->vehicle->update(['status' => 'available']);
            }

            Log::info("Booking ID {$booking->id} marked as completed by admin.");
        } catch (\Exception $e) {
            Log::error("Error completing booking ID {$booking->id}: " . $e->getMessage());
            return redirect()->route('admin.bookings.index')->with('error', 'Failed to complete booking. An error occurred.');
        }

        return redirect()->route('admin.bookings.index')->with('success', 'Booking completed successfully. Vehicle is now available.');
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'vehicle', 'transaction']);
        return view('admin.bookings.show', compact('booking'));
    }
}
