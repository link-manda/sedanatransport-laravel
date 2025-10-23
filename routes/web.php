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


// Rute Publik
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    // Arahkan ke dashboard yang sesuai berdasarkan peran
    if (auth()->user()->hasRole('admin')) {
        return redirect()->route('admin.bookings.index');
    }
    return redirect()->route('bookings.index');
})->middleware(['auth', 'verified'])->name('dashboard');


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
    // Rute Baru untuk Upload Bukti Bayar
    Route::post('/bookings/{booking}/pay', [PaymentController::class, 'uploadProof'])->name('payment.upload');

    // Grup untuk Rute Admin
    Route::middleware('role:admin|petugas')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('vehicles', VehicleController::class);
        // Rute Manajemen Booking
        Route::get('bookings', [BookingManagementController::class, 'index'])->name('bookings.index');
        Route::patch('bookings/{booking}/status', [BookingManagementController::class, 'updateStatus'])->name('bookings.updateStatus');
        Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::patch('transactions/{transaction}/mark-as-paid', [TransactionController::class, 'markAsPaid'])->name('transactions.markAsPaid');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
        Route::post('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
        // Rute Transaksi
        Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
        // RUTE BARU: Tambahkan baris ini untuk menangani konfirmasi/penolakan
        Route::patch('transactions/{transaction}/status', [TransactionController::class, 'updateStatus'])->name('transactions.updateStatus');
    });
});

require __DIR__ . '/auth.php';
