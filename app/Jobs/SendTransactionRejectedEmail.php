<?php

namespace App\Jobs;

use App\Mail\TransactionRejected; // Import Mailable yang benar
use App\Models\Transaction;       // Import model Transaction
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log; // Tambahkan Log

class SendTransactionRejectedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The transaction instance.
     *
     * @var \App\Models\Transaction
     */
    protected $transaction;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Transaction $transaction
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction->withoutRelations(); // Hindari serialisasi relasi yang tidak perlu
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Ambil relasi yang dibutuhkan di dalam job handler
            $this->transaction->load(['booking.user', 'booking.vehicle']);

            // Pastikan user ada sebelum mengirim email
            if ($this->transaction->booking && $this->transaction->booking->user) {
                Mail::to($this->transaction->booking->user->email)
                    ->send(new TransactionRejected($this->transaction));
                Log::info("Transaction rejection email sent for Transaction ID: {$this->transaction->id}");
            } else {
                Log::warning("Cannot send rejection email. User or booking not found for Transaction ID: {$this->transaction->id}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send transaction rejection email for Transaction ID {$this->transaction->id}: " . $e->getMessage());
            // Anda bisa melempar ulang exception jika ingin job di-retry
            // throw $e;
        }
    }
}
