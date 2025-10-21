<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function index()
    {
        // Ambil booking milik pengguna yang sedang login
        $bookings = auth()->user()->bookings()->with(['vehicle.category', 'transaction'])->latest()->paginate(5);
        
        return view('bookings.index', compact('bookings'));
    }

    public function store(Request $request)
    {
        // 1. Validasi yang lebih ketat dengan PESAN KUSTOM
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ], [
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh dipilih sebelum tanggal mulai.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak bisa dipilih dari hari yang telah lalu.',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Validasi ketersediaan kendaraan pada rentang tanggal yang dipilih
        $isBooked = Booking::where('vehicle_id', $vehicle->id)
            ->where('status', 'approved')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<=', $endDate)
                      ->where('end_date', '>=', $startDate);
                });
            })->exists();

        if ($isBooked) {
            return back()->withErrors(['vehicle_unavailable' => 'Sorry, this vehicle is not available for the selected dates.'])->withInput();
        }

        // 2. Perbaikan Logika Perhitungan Hari: tambahkan +1 untuk membuatnya inklusif
        // Contoh: 18 ke 19 adalah 2 hari, bukan 1. (19-18)+1 = 2.
        $numberOfDays = $startDate->diffInDays($endDate) + 1;

        // Pastikan jumlah hari tidak pernah 0 atau negatif (sebagai pengaman tambahan)
        if ($numberOfDays <= 0) {
             $numberOfDays = 1;
        }

        $totalPrice = $vehicle->daily_rate * $numberOfDays;

        // Buat booking baru
        Booking::create([
            'user_id' => auth()->id(),
            'vehicle_id' => $vehicle->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        return redirect()->route('bookings.index')->with('success', 'Booking request sent successfully!');
    }
}


