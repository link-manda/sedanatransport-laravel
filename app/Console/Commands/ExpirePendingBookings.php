<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ExpirePendingBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expire-pending-bookings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire bookings where payment is overdue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired bookings...');

        // Cari transaksi yang pending dan sudah melewati batas waktu
        $transactions = Transaction::where('status', 'pending')
            ->where('payment_due_at', '<', Carbon::now())
            ->get();

        if ($transactions->isEmpty()) {
            $this->info('No expired bookings found.');
            return;
        }

        foreach ($transactions as $transaction) {
            $booking = $transaction->booking;

            if ($booking && $booking->status === 'approved') {
                // Ubah status booking menjadi expired
                $booking->status = 'expired';
                $booking->save();

                // Ubah status transaksi menjadi failed
                $transaction->status = 'failed';
                $transaction->save();

                // Kembalikan status kendaraan menjadi available
                $vehicle = $booking->vehicle;
                // Pastikan tidak ada booking lain yang 'approved' untuk kendaraan ini
                $activeBookings = Booking::where('vehicle_id', $vehicle->id)
                    ->where('status', 'approved')
                    ->exists();

                if (!$activeBookings) {
                    $vehicle->status = 'available';
                    $vehicle->save();
                }

                $this->warn("Booking #{$booking->id} has expired.");
            }
        }

        $this->info('Expired bookings check complete.');
    }
}
