<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Transaction; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Menampilkan halaman detail pembayaran.
     */
    public function show(Booking $booking)
    {
        // 1. Otorisasi: Pastikan booking milik user yang sedang login
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // --- Pengecekan Kritis ---
        // 2. Pastikan transaksi TERKAIT ADA sebelum mengakses propertinya
        if (!$booking->transaction) {
            \Log::warning("Attempted to access payment page for booking ID {$booking->id} which has no associated transaction.");
            return redirect()->route('bookings.index')->with('error', 'Payment details are not available for this booking.');
        }
        // --- Akhir Pengecekan Kritis ---

        // 3. Setelah memastikan $booking->transaction ada, baru cek propertinya
        if (!$booking->transaction->payment_due_at) {
            return redirect()->route('bookings.index')->with('error', 'Payment deadline information is missing.');
        }

        // 4. Pengecekan status booking (Boleh lihat jika pending/approved tapi belum bayar)
        // Kita perlu pastikan user bisa lihat halaman ini jika statusnya approved dan transaksinya pending
        if (!in_array($booking->status, ['approved'])) {
            return redirect()->route('bookings.index')->with('error', 'Payment cannot be made for this booking status yet.');
        }

        // 5. Pengecekan status transaksi
        if ($booking->transaction->status !== 'pending') {
            return redirect()->route('bookings.index')->with('error', 'This transaction is no longer pending payment.');
        }

        // Jika semua pengecekan lolos, tampilkan view
        return view('payments.show', compact('booking'));
    }

    /**
     * Memproses upload bukti pembayaran.
     */
    public function uploadProof(Request $request, Booking $booking)
    {
        // 1. Otorisasi
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // 2. Validasi file
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        // 3. Pastikan transaksi ada dan statusnya sesuai
        if (!$booking->transaction || $booking->transaction->status !== 'pending') {
            return back()->with('error', 'Cannot upload proof for this transaction.');
        }

        // 4. Proses upload
        if ($request->hasFile('payment_proof')) {
            // Hapus bukti lama jika ada
            if ($booking->transaction->payment_proof && Storage::disk('public')->exists($booking->transaction->payment_proof)) {
                Storage::disk('public')->delete($booking->transaction->payment_proof);
            }

            // Simpan file baru
            $path = $request->file('payment_proof')->store('payment_proofs', 'public');

            // Update record transaksi
            $booking->transaction->update([
                'payment_proof' => $path,
                'status' => 'waiting_confirmation', // Ubah status
            ]);

            return redirect()->route('bookings.index')->with('success', 'Payment proof uploaded successfully. Please wait for admin confirmation.');
        }

        return back()->with('error', 'Failed to upload payment proof. Please try again.');
    }
}
