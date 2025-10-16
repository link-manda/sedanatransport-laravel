<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\VehiclePageController; 
use App\Http\Controllers\BookingController; 
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Admin\BookingManagementController; 


// Rute Publik
Route::get('/', [VehiclePageController::class, 'index'])->name('home');
Route::get('/vehicles/{vehicle}', [VehiclePageController::class, 'show'])->name('vehicles.show');


Route::get('/dashboard', function () {
    // Arahkan ke dashboard yang sesuai berdasarkan peran
    if (auth()->user()->hasRole('admin')) {
        return redirect()->route('admin.bookings.index');
    }
    return redirect()->route('bookings.index');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute untuk Pelanggan
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/my-bookings', [BookingController::class, 'index'])->name('bookings.index');

    // Grup untuk Rute Admin
    Route::middleware('role:admin|petugas')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('vehicles', VehicleController::class);
        // Rute Manajemen Booking
        Route::get('bookings', [BookingManagementController::class, 'index'])->name('bookings.index');
        Route::patch('bookings/{booking}/status', [BookingManagementController::class, 'updateStatus'])->name('bookings.updateStatus');
        Route::get('transactions', [\App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('transactions.index');
        Route::patch('transactions/{transaction}/mark-as-paid', [\App\Http\Controllers\Admin\TransactionController::class, 'markAsPaid'])->name('transactions.markAsPaid');
    });
});

require __DIR__ . '/auth.php';
