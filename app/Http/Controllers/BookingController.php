<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['vehicle.category', 'transaction'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(5);
        return view('bookings.index', compact('bookings'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // 3. Cek Ketersediaan Tanggal
        $isVehicleBooked = Booking::where('vehicle_id', $request->vehicle_id)
            ->whereIn('status', ['approved', 'ongoing', 'paid', 'waiting_confirmation'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<=', $endDate)
                        ->where('end_date', '>=', $startDate);
                });
            })->exists();

        if ($isVehicleBooked) {
            return back()->with('error', 'The selected dates are overlapping with an existing booking. Please choose different dates.');
        }

        $days = $startDate->diffInDays($endDate) + 1; 
        $dailyRate = abs((float) $vehicle->daily_rate);
        $totalPrice = $days * $dailyRate;

        // 5. Gunakan Database Transaction
        try {
            DB::transaction(function () use ($request, $totalPrice, $vehicle, $startDate, $endDate) {
                // Buat booking
                $booking = Booking::create([
                    'user_id' => auth()->id(),
                    'vehicle_id' => $request->vehicle_id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'total_price' => $totalPrice, // Simpan harga positif
                    'status' => 'pending',
                ]);

                // Buat transaksi
                Transaction::create([
                    'booking_id' => $booking->id,
                    'amount' => $totalPrice, // Simpan harga positif
                    'status' => 'pending',
                ]);
            });
        } catch (\Exception $e) {
            // Opsional: Log error $e->getMessage()
            return back()->with('error', 'An error occurred during the booking process. Please try again.');
        }

        return redirect()->route('bookings.index')->with('success', 'Booking created successfully! Please wait for admin approval.');
    }
}

