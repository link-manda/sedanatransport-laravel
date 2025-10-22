<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Booking;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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

        $booked_dates = [];

        foreach ($bookings as $booking) {
            // Buat periode dari tanggal mulai hingga tanggal selesai untuk setiap booking
            $period = CarbonPeriod::create($booking->start_date, $booking->end_date);

            // Tambahkan setiap tanggal dalam periode tersebut ke dalam array
            foreach ($period as $date) {
                $booked_dates[] = $date->format('Y-m-d');
            }
        }

        $booked_dates = array_values(array_unique($booked_dates));
        
        return view('vehicles.show', compact('vehicle', 'booked_dates'));
    }
}
