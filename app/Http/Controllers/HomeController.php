<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
// Hapus use statement untuk Booking, CarbonPeriod, Carbon jika tidak digunakan lagi
use Illuminate\Http\Request;


class HomeController extends Controller
{
    /**
     * Menampilkan halaman utama dengan daftar kendaraan.
     */
    public function index()
    {
        // 1. Ambil kendaraan yang relevan (available atau rented)
        // Eager load relasi category untuk efisiensi
        $vehicles = Vehicle::with('category')
            ->whereIn('status', ['available', 'rented'])
            ->latest() // Urutkan berdasarkan terbaru
            ->paginate(9); // Gunakan pagination misal 9 item per halaman

        // 2. Kirim data ke view
        return view('welcome', [
            'vehicles' => $vehicles,
        ]);
    }
}
