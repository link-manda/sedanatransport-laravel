<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendBookingApprovedEmail;
use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingManagementController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'vehicle'])->latest()->paginate(10);
        return view('admin.bookings.index', compact('bookings'));
    }

    public function cancel(Booking $booking)
    {
        if ($booking->status === 'approved') {
            $vehicle = $booking->vehicle;
            $vehicle->status = 'available';
            $vehicle->save();
        }

        $booking->status = 'cancelled';
        $booking->save();

        if ($booking->transaction) {
            $booking->transaction->update(['status' => 'cancelled']);
        }

        return redirect()->route('admin.bookings.index')->with('success', 'Booking cancelled successfully.');
    }

    public function approve(Booking $booking)
    {
        // Pastikan kita tidak meng-approve booking yang sudah di-approve
        if ($booking->status !== 'pending') {
            return redirect()->route('admin.bookings.index')->with('error', 'This booking cannot be approved.');
        }

        $booking->status = 'approved';
        $booking->save();

        $vehicle = $booking->vehicle;
        $vehicle->status = 'rented';
        $vehicle->save();

        // 2. Tambahkan blok kode ini untuk membuat transaksi
        Transaction::create([
            'booking_id' => $booking->id,
            'amount' => $booking->total_price,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.bookings.index')->with('success', 'Booking approved successfully and a new transaction has been created.');
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:approved,cancelled,completed',
        ]);

        if ($booking->status === $request->status) {
            return redirect()->route('admin.bookings.index')->with('error', 'Booking status is already ' . $request->status);
        }

        $booking->status = $request->status;
        $booking->save();

        $vehicle = $booking->vehicle;

        if ($request->status == 'approved') {
            $vehicle->status = 'rented';
            $vehicle->save();

            // Buat transaksi baru
            Transaction::create([
                'booking_id' => $booking->id,
                'amount' => $booking->total_price,
                'status' => 'pending',
            ]);

            SendBookingApprovedEmail::dispatch($booking);

            return redirect()->route('admin.bookings.index')->with('success', 'Booking approved and transaction created.');
        }

        if (in_array($request->status, ['cancelled', 'completed'])) {
            $activeBookings = Booking::where('vehicle_id', $vehicle->id)
                ->where('status', 'approved')
                ->exists();

            if (!$activeBookings) {
                $vehicle->status = 'available';
                $vehicle->save();
            }

            if ($booking->transaction) {
                $booking->transaction->update(['status' => 'cancelled']);
            }
        }

        return redirect()->route('admin.bookings.index')->with('success', 'Booking status has been updated.');
    }
}
