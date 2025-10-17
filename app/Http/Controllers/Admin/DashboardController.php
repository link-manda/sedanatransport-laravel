<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Menghitung total statistik
        $totalVehicles = Vehicle::count();
        $availableVehicles = Vehicle::where('status', 'available')->count();
        $pendingBookings = Booking::where('status', 'pending')->count();

        // Menghitung pendapatan bulan ini dari transaksi yang sudah dibayar
        $monthlyRevenue = Transaction::where('status', 'paid')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('amount');

        // Mengirim data ke view
        return view('admin.dashboard.index', compact(
            'totalVehicles',
            'availableVehicles',
            'pendingBookings',
            'monthlyRevenue'
        ));
    }
}
