<?php

namespace App\Mail;

use App\Models\Transaction; // Import model Transaction
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransactionRejected extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The transaction instance.
     *
     * @var \App\Models\Transaction
     */
    public $transaction;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\Transaction $transaction
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Payment Was Rejected',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Pastikan view yang benar (emails.transactions.rejected)
        return new Content(
            markdown: 'emails.transactions.rejected',
            with: [ // Kirim data transaction ke view
                'transaction' => $this->transaction,
                'booking' => $this->transaction->booking, // Kirim juga data booking jika perlu
                'user' => $this->transaction->booking->user, // Kirim data user
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
