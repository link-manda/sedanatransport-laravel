<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Menampilkan halaman pembayaran untuk booking tertentu.
     */
    public function show(Booking $booking)
    {
        // Pastikan hanya pemilik booking yang bisa mengakses halaman ini
        if (auth()->id() !== $booking->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Pastikan booking memiliki transaksi yang menunggu pembayaran
        if (
            !$booking->transaction ||
            $booking->transaction->status !== 'pending' ||
            !$booking->transaction->payment_due_at || 
            $booking->status !== 'approved'
        ) {
            return redirect()->route('bookings.index')->with('error', 'This booking cannot be paid for at the moment.');
        }

        return view('payments.show', compact('booking'));
    }


    /**
     * Mengunggah dan memproses bukti pembayaran.
     */
    public function uploadProof(Request $request, Booking $booking)
    {
        // Pastikan hanya pemilik booking yang bisa mengupload
        if (auth()->id() !== $booking->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Validasi request
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048', // maks 2MB
        ]);

        // Pastikan transaksi ada dan statusnya 'pending'
        $transaction = $booking->transaction;
        if (!$transaction || $transaction->status !== 'pending') {
            return redirect()->route('bookings.index')->with('error', 'This payment cannot be processed.');
        }

        // Hapus bukti lama jika ada
        if ($transaction->payment_proof) {
            Storage::disk('public')->delete($transaction->payment_proof);
        }

        // Simpan file baru
        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        // Update record transaksi
        $transaction->update([
            'payment_proof' => $path,
            'status' => 'waiting_confirmation',
        ]);

        return redirect()->route('bookings.index')->with('success', 'Payment proof uploaded successfully. Please wait for admin confirmation.');
    }
}
