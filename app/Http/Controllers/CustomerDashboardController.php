<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Statistik Personal
        $totalBookings = Booking::where('user_id', $userId)->count();

        $activeBookings = Booking::where('user_id', $userId)
            ->whereIn('status', ['approved', 'completed'])
            ->whereDate('end_date', '>=', Carbon::today())
            ->count();

        $totalSpent = Transaction::whereHas('booking', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->where('status', 'paid')
            ->sum('amount');

        $pendingPayments = Booking::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereDoesntHave('transaction', function ($query) {
                $query->where('status', 'paid');
            })
            ->count();

        // Booking Terbaru (5 terakhir)
        $recentBookings = Booking::where('user_id', $userId)
            ->with(['vehicle.category', 'transaction'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Transaksi Terbaru (5 terakhir)
        $recentTransactions = Transaction::whereHas('booking', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->with(['booking.vehicle'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Alert: Booking yang perlu dibayar (approved tapi belum bayar)
        $needPayment = Booking::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereDoesntHave('transaction', function ($query) {
                $query->where('status', 'paid');
            })
            ->with('vehicle')
            ->get();

        // Alert: Booking yang akan dimulai dalam 3 hari
        $upcomingBookings = Booking::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereDate('start_date', '>=', Carbon::today())
            ->whereDate('start_date', '<=', Carbon::today()->addDays(3))
            ->with('vehicle')
            ->get();

        return view('dashboard', compact(
            'totalBookings',
            'activeBookings',
            'totalSpent',
            'pendingPayments',
            'recentBookings',
            'recentTransactions',
            'needPayment',
            'upcomingBookings'
        ));
    }
}
