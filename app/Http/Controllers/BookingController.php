<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Auth::user()->bookings()->with('vehicle')->latest()->paginate(10);
        return view('bookings.index', compact('bookings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Cek ketersediaan kendaraan pada rentang tanggal yang dipilih
        $isUnavailable = Booking::where('vehicle_id', $vehicle->id)
            ->where('status', 'approved')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $endDate)
                    ->where('end_date', '>=', $startDate);
            })
            ->exists();

        if ($isUnavailable) {
            throw ValidationException::withMessages([
                'start_date' => 'The selected vehicle is not available for the chosen dates. Please select another date range.',
            ]);
        }

        $rentalDays = $endDate->diffInDays($startDate);
        $totalPrice = $rentalDays * $vehicle->daily_rate;

        Booking::create([
            'user_id' => Auth::id(),
            'vehicle_id' => $vehicle->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        return redirect()->route('bookings.index')->with('success', 'Booking request has been sent successfully! Please wait for admin approval.');
    }
}
