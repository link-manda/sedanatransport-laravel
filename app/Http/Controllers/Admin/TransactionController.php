<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; 
use App\Jobs\SendTransactionRejectedEmail; 
use Illuminate\Support\Facades\Log;      

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('booking.user', 'booking.vehicle')->latest()->paginate(10);
        return view('admin.transactions.index', compact('transactions'));
    }

    public function updateStatus(Request $request, Transaction $transaction)
    {
        // Validasi input - tambahkan validasi untuk rejection_reason
        $validated = $request->validate([
            'status' => ['required', Rule::in(['paid', 'failed'])],
            'rejection_reason' => [Rule::requiredIf($request->input('status') === 'failed'), 'nullable', 'string', 'max:1000'],
        ]);

        $newStatus = $validated['status'];
        $rejectionReason = $validated['rejection_reason'] ?? null; // Ambil alasan penolakan
        $booking = $transaction->booking; // Eager load relasi jika belum

        // Jika status tidak berubah, langsung kembalikan
        if ($transaction->status === $newStatus) {
            return redirect()->route('admin.transactions.index');
        }

        // --- Logika Penolakan (failed) ---
        if ($newStatus === 'failed') {
            // Periksa apakah alasan penolakan diberikan
            if (empty($rejectionReason)) {
                return back()->withInput()->withErrors(['rejection_reason' => 'Rejection reason is required.']);
            }

            try {
                $transaction->update([
                    'status' => 'failed',
                    'rejection_reason' => $rejectionReason // Simpan alasan
                ]);

                // Batalkan booking terkait
                if ($booking) {
                    $booking->update(['status' => 'cancelled']);
                    // Kembalikan status kendaraan menjadi 'available' hanya jika tidak ada booking aktif lain
                    $vehicle = $booking->vehicle;
                    if ($vehicle) {
                        $isActiveBooking = Booking::where('vehicle_id', $vehicle->id)
                            ->whereIn('status', ['approved', 'ongoing', 'paid', 'waiting_confirmation'])
                            ->where('id', '!=', $booking->id) // Jangan hitung booking saat ini
                            ->exists();

                        if (!$isActiveBooking) {
                            $vehicle->update(['status' => 'available']);
                        }
                    }
                }

                // Kirim notifikasi email penolakan (menggunakan Job)
                SendTransactionRejectedEmail::dispatch($transaction);

                return redirect()->route('admin.transactions.index')->with('success', 'Transaction has been rejected, booking cancelled, and notification sent.');
            } catch (\Exception $e) {
                Log::error("Error rejecting transaction ID {$transaction->id}: " . $e->getMessage());
                return redirect()->route('admin.transactions.index')->with('error', 'Failed to reject transaction. An error occurred.');
            }
        }

        // --- Logika Konfirmasi (paid) ---
        if ($newStatus === 'paid') {
            // Pastikan rejection_reason di-null-kan jika sebelumnya ditolak lalu diterima
            $transaction->update([
                'status' => 'paid',
                'rejection_reason' => null
            ]);
            // Status booking tetap 'approved'
            // Status kendaraan tetap 'rented'

            // Opsional: Kirim email konfirmasi pembayaran (jika belum ada)

            return redirect()->route('admin.transactions.index')->with('success', 'Transaction has been confirmed successfully.');
        }

        // Seharusnya tidak sampai sini, tapi sebagai fallback
        return redirect()->route('admin.transactions.index')->with('error', 'Invalid status update.');
    }
}
