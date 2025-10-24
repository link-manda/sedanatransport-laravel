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
    public function index(Request $request)
    {
        $query = Booking::with(['vehicle.category', 'transaction'])
            ->where('user_id', auth()->id());

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('vehicle', function ($q) use ($search) {
                $q->where('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('plate_number', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $query->where('start_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('end_date', '<=', $request->to_date);
        }

        $bookings = $query->latest()->paginate(10)->withQueryString();

        return view('bookings.index', compact('bookings'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

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

        try {
            DB::transaction(function () use ($request, $totalPrice, $vehicle, $startDate, $endDate) {
                $booking = Booking::create([
                    'user_id' => auth()->id(),
                    'vehicle_id' => $request->vehicle_id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'total_price' => $totalPrice,
                    'status' => 'pending',
                ]);

                Transaction::create([
                    'booking_id' => $booking->id,
                    'amount' => $totalPrice,
                    'status' => 'pending',
                ]);
            });
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred during the booking process. Please try again.');
        }

        return redirect()->route('bookings.index')->with('success', 'Booking created successfully! Please wait for admin approval.');
    }
}
