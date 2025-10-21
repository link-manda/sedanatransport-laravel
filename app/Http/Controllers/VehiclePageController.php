<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Booking;
use Illuminate\Http\Request;

class VehiclePageController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::where('status', 'available')->latest()->paginate(9);
        return view('vehicles.index', compact('vehicles'));
    }

    public function show(Vehicle $vehicle)
    {
        $bookings = Booking::where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['approved', 'ongoing', 'paid'])
            ->get();

        $booked_dates = $bookings->map(function ($booking) {
            return [
                'from' => $booking->start_date->format('Y-m-d'),
                'to'   => $booking->end_date->format('Y-m-d'),
            ];
        });

        return view('vehicles.show', compact('vehicle', 'booked_dates'));
    }
}
