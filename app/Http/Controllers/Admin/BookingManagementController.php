<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Jobs\SendBookingApprovedEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; // Import Log Facade

class BookingManagementController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'vehicle'])->latest()->paginate(10);
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

            $transaction = $booking->transaction; // Dapatkan transaksi via relasi

            if ($transaction) {
                $dueDate = Carbon::now()->addHours(24);
                $updateResult = $transaction->update([
                    'payment_due_at' => $dueDate
                ]);

                // --- Tambahkan Debugging Di Sini ---
                $transaction->refresh(); // Ambil data terbaru dari DB
                Log::info("Attempting to approve Booking ID: {$booking->id}. Transaction ID: {$transaction->id}. Update result: " . ($updateResult ? 'Success' : 'Failed') . ". Due Date set to: " . $dueDate->toDateTimeString() . ". Value after refresh: " . ($transaction->payment_due_at ? $transaction->payment_due_at->toDateTimeString() : 'NULL'));
                // --- Akhir Debugging ---


                // Periksa apakah update benar-benar menyimpan nilai
                if (!$transaction->payment_due_at) {
                    Log::error("Failed to save payment_due_at for Transaction ID: {$transaction->id}. Value remains NULL after update and refresh.");
                    // Mungkin lempar exception atau kembalikan error spesifik jika perlu
                    // throw new \Exception("Failed to save payment_due_at.");
                    return redirect()->route('admin.bookings.index')->with('error', 'Failed to set payment deadline. Please check logs.');
                }
            } else {
                Log::error("Transaction not found for booking ID: {$booking->id} during approval.");
                return redirect()->route('admin.bookings.index')->with('error', 'Associated transaction not found.');
            }

            // SendBookingApprovedEmail::dispatch($booking); // Nonaktifkan sementara untuk debugging jika perlu

        } catch (\Exception $e) {
            Log::error("Error approving booking ID {$booking->id}: " . $e->getMessage());
            return redirect()->route('admin.bookings.index')->with('error', 'Failed to approve booking. An error occurred.');
        }

        return redirect()->route('admin.bookings.index')->with('success', 'Booking approved successfully.'); // Hapus 'and notification sent' jika email dinonaktifkan
    }

    public function cancel(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return redirect()->route('admin.bookings.index')->with('error', 'Booking cannot be rejected as it is not in pending status.');
        }

        try {
            $booking->update(['status' => 'rejected']);
            if ($booking->transaction) {
                $booking->transaction->update(['status' => 'failed']);
            }
        } catch (\Exception $e) {
            Log::error("Error rejecting booking ID {$booking->id}: " . $e->getMessage());
            return redirect()->route('admin.bookings.index')->with('error', 'Failed to reject booking. An error occurred.');
        }

        return redirect()->route('admin.bookings.index')->with('success', 'Booking rejected successfully.');
    }
}
