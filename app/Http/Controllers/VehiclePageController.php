<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Booking; 
use Illuminate\Http\Request;
use Carbon\CarbonPeriod; 
use Carbon\Carbon; 

class VehiclePageController extends Controller
{
    /**
     * Menampilkan daftar semua kendaraan (mungkin tidak terpakai jika home sudah menampilkan).
     */
    public function index()
    {
        $vehicles = Vehicle::latest()->paginate(10); 
        return view('vehicles.index', compact('vehicles'));
    }

    /**
     * Menampilkan detail satu kendaraan beserta tanggal bookingnya.
     */
    public function show(Vehicle $vehicle)
    {
        $vehicle->load('category');
        $activeBookings = Booking::where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['approved', 'ongoing', 'paid']) 
            ->get(['start_date', 'end_date']);

        // Proses booking untuk mendapatkan SEMUA tanggal individual yang tidak tersedia
        $bookedDates = [];
        foreach ($activeBookings as $booking) {
            // Pastikan tanggal valid
            if ($booking->start_date && $booking->end_date) {
                $startDate = Carbon::parse($booking->start_date); 
                $endDate = Carbon::parse($booking->end_date); 
                $period = CarbonPeriod::create($startDate, $endDate); // Asumsi end_date INKLUSIF

                foreach ($period as $date) {
                    $bookedDates[] = $date->format('Y-m-d');
                }
            }
        }

        $uniqueBookedDates = array_unique($bookedDates);
        $formattedBookedDates = array_values($uniqueBookedDates);

        // Kirim data ke view
        return view('vehicles.show', [
            'vehicle' => $vehicle,
            'bookedDates' => $formattedBookedDates // <-- Pastikan variabel ini dikirim!
        ]);
    }
}
