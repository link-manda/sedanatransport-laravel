<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Http\Request;

class BookingManagementController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'vehicle'])->latest()->paginate(10);
        return view('admin.bookings.index', compact('bookings'));
    }

    public function approve(Booking $booking)
    {
        $booking->status = 'approved';
        $booking->save();

        $vehicle = $booking->vehicle;
        $vehicle->status = 'rented';
        $vehicle->save();

        // Buat transaksi baru saat booking di-approve
        Transaction::create([
            'booking_id' => $booking->id,
            'amount' => $booking->total_price,
            'status' => 'unpaid',
        ]);

        return redirect()->route('admin.bookings.index')->with('success', 'Booking approved successfully and transaction created.');
    }

    public function cancel(Booking $booking)
    {
        // Logika untuk mengembalikan status kendaraan jika booking sudah di-approve sebelumnya
        if ($booking->status === 'approved') {
            $vehicle = $booking->vehicle;
            $vehicle->status = 'available';
            $vehicle->save();
        }

        $booking->status = 'cancelled';
        $booking->save();

        // Jika sudah ada transaksi, tandai sebagai failed atau batalkan
        if ($booking->transaction) {
            $booking->transaction->update(['status' => 'cancelled']);
        }


        return redirect()->route('admin.bookings.index')->with('success', 'Booking cancelled successfully.');
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:approved,cancelled,completed',
        ]);

        $booking->status = $request->status;
        $booking->save();

        $vehicle = $booking->vehicle;

        if ($request->status == 'approved') {
            $vehicle->status = 'rented';
            $vehicle->save();
        } elseif (in_array($request->status, ['cancelled', 'completed'])) {
            // Cek apakah ada booking lain yang 'approved' untuk mobil ini. Jika tidak, set available.
            $activeBookings = Booking::where('vehicle_id', $vehicle->id)
                ->where('status', 'approved')
                ->exists();
            if (!$activeBookings) {
                $vehicle->status = 'available';
                $vehicle->save();
            }
        }

        return redirect()->route('admin.bookings.index')->with('success', 'Booking status has been updated.');
    }
}
