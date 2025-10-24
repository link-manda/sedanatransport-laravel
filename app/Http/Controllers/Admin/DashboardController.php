<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Vehicle Statistics
        $totalVehicles = Vehicle::count();
        $availableVehicles = Vehicle::where('status', 'available')->count();
        $rentedVehicles = Vehicle::where('status', 'rented')->count();
        $maintenanceVehicles = Vehicle::where('status', 'maintenance')->count();

        // Booking Statistics
        $pendingBookings = Booking::where('status', 'pending')->count();
        $approvedBookings = Booking::where('status', 'approved')->count();
        $ongoingBookings = Booking::where('status', 'ongoing')->count();
        $completedBookingsThisMonth = Booking::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $cancelledBookingsThisMonth = Booking::where('status', 'cancelled')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Payment Statistics
        $pendingPaymentConfirmation = Transaction::where('status', 'pending')->count();

        // Revenue Statistics
        $totalRevenueThisMonth = Transaction::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $totalRevenueOverall = Transaction::where('status', 'paid')->sum('amount');

        $monthlyRevenue = Transaction::where('status', 'paid')
            ->where('paid_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(paid_at, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Format data for Chart.js
        $revenueLabels = [];
        $revenueData = [];

        // Fill in missing months with 0
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $monthLabel = $date->locale('id')->isoFormat('MMM YYYY');

            $revenueLabels[] = $monthLabel;

            $found = $monthlyRevenue->firstWhere('month', $monthKey);
            $revenueData[] = $found ? (float) $found->total : 0;
        }

        $monthlyBookings = Booking::where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $bookingLabels = [];
        $bookingData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $monthLabel = $date->locale('id')->isoFormat('MMM YYYY');

            $bookingLabels[] = $monthLabel;

            $found = $monthlyBookings->firstWhere('month', $monthKey);
            $bookingData[] = $found ? (int) $found->total : 0;
        }

        // Recent Data
        $recentBookings = Booking::with(['user', 'vehicle'])
            ->latest()
            ->take(5)
            ->get();

        $recentTransactions = Transaction::with(['booking.user', 'booking.vehicle'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalVehicles',
            'availableVehicles',
            'rentedVehicles',
            'maintenanceVehicles',
            'pendingBookings',
            'approvedBookings',
            'ongoingBookings',
            'completedBookingsThisMonth',
            'cancelledBookingsThisMonth',
            'pendingPaymentConfirmation',
            'totalRevenueThisMonth',
            'totalRevenueOverall',
            'revenueLabels',
            'revenueData',
            'bookingLabels',
            'bookingData',
            'recentBookings',
            'recentTransactions'
        ));
    }
}
