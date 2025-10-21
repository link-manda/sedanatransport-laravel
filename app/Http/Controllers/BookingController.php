<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
        // 1. Validasi Input Dasar
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // 2. Cek Status Kendaraan Secara Langsung
        if ($vehicle->status !== 'available') {
            return back()->with('error', 'Sorry, this vehicle is not available for booking right now.');
        }

        // 3. Cek Ketersediaan Tanggal (Overlap)
        $isVehicleBooked = Booking::where('vehicle_id', $request->vehicle_id)
            ->whereIn('status', ['approved', 'ongoing', 'paid']) // Hanya cek booking yang aktif
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<=', $endDate)
                        ->where('end_date', '>=', $startDate);
                });
            })->exists();

        if ($isVehicleBooked) {
            return back()->with('error', 'The selected dates are overlapping with an existing booking. Please choose different dates.');
        }

        // 4. Hitung Total Hari dan Harga
        $days = $endDate->diffInDays($startDate) + 1;
        $totalPrice = $days * $vehicle->price_per_day;

        // 5. Gunakan Database Transaction untuk Keamanan Data
        try {
            DB::transaction(function () use ($request, $totalPrice, $vehicle, $startDate, $endDate) {
                // Buat booking
                $booking = Booking::create([
                    'user_id' => auth()->id(),
                    'vehicle_id' => $request->vehicle_id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'total_price' => $totalPrice,
                    'status' => 'pending',
                ]);

                // Buat transaksi
                Transaction::create([
                    'booking_id' => $booking->id,
                    'amount' => $totalPrice,
                    'status' => 'pending',
                ]);

                // PENTING: Jangan ubah status kendaraan di sini.
                // Status kendaraan baru berubah menjadi 'rented' setelah admin APPROVE booking.
                // Jika diubah di sini, kendaraan akan langsung tidak tersedia bahkan sebelum booking disetujui.
            });
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred during the booking process. Please try again.');
        }

        return redirect()->route('bookings.index')->with('success', 'Booking created successfully! Please wait for admin approval.');
    }
}


