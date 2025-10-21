<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('booking.user', 'booking.vehicle')->latest()->paginate(10);
        return view('admin.transactions.index', compact('transactions'));
    }

    public function markAsPaid(Transaction $transaction)
    {
        $transaction->status = 'paid';
        $transaction->save();

        return redirect()->route('admin.transactions.index')->with('success', 'Transaction has been marked as paid.');
    }

    public function updateStatus(Request $request, Transaction $transaction)
    {
        // Validasi input
        $request->validate([
            'status' => 'required|in:paid,failed',
        ]);

        $newStatus = $request->input('status');
        $booking = $transaction->booking;

        // Jika status tidak berubah, langsung kembalikan
        if ($transaction->status === $newStatus) {
            return redirect()->route('admin.transactions.index');
        }

        // Logika jika pembayaran DITOLAK (failed)
        if ($newStatus === 'failed') {
            $transaction->update(['status' => 'failed']);

            // Batalkan booking terkait
            if ($booking) {
                $booking->update(['status' => 'cancelled']);
                // Kembalikan status kendaraan menjadi 'available'
                $booking->vehicle->update(['status' => 'available']);
            }

            return redirect()->route('admin.transactions.index')->with('success', 'Transaction has been rejected and the booking is cancelled.');
        }

        // Logika jika pembayaran DITERIMA (paid)
        if ($newStatus === 'paid') {
            $transaction->update(['status' => 'paid']);
            // Booking status tetap 'approved' karena rental belum selesai
            // Kendaraan status tetap 'rented' karena sudah dialokasikan

            return redirect()->route('admin.transactions.index')->with('success', 'Transaction has been confirmed successfully.');
        }

        return redirect()->route('admin.transactions.index')->with('error', 'Invalid status update.');
    }
}
