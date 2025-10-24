<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\VehiclePageController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\BookingManagementController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\CustomerDashboardController;


// Rute Publik
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole('admin') || $user->hasRole('petugas')) {
        return redirect()->route('admin.dashboard.index');
    }
    // Redirect pelanggan ke customer dashboard
    return redirect()->route('customer.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/customer/dashboard', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');
});

Route::get('/vehicles', [VehiclePageController::class, 'index'])->name('vehicles.index');
Route::get('/vehicles/{vehicle}', [VehiclePageController::class, 'show'])->name('vehicles.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute untuk Pelanggan
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/my-bookings', [BookingController::class, 'index'])->name('bookings.index');

    // Rute Pembayaran
    Route::get('/bookings/{booking}/pay', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/bookings/{booking}/pay', [PaymentController::class, 'uploadProof'])->name('payment.upload');

    // Grup untuk Rute Admin & Petugas
    Route::middleware(['auth', 'role:admin|petugas'])->prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.index');

        // Resourceful route for vehicles
        Route::resource('vehicles', VehicleController::class);

        // Rute Manajemen Booking
        Route::get('bookings', [BookingManagementController::class, 'index'])->name('bookings.index');
        Route::patch('bookings/{booking}/status', [BookingManagementController::class, 'updateStatus'])->name('bookings.updateStatus');

        // Rute Transaksi
        Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::patch('transactions/{transaction}/status', [TransactionController::class, 'updateStatus'])->name('transactions.updateStatus');

        // Rute Laporan
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    });
});

require __DIR__ . '/auth.php';
