<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        // Mengambil data transaksi beserta relasi yang dibutuhkan
        return Transaction::query()
            ->with(['booking.user', 'booking.vehicle'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate]);
    }

    public function headings(): array
    {
        // Mendefinisikan judul kolom pada file Excel/CSV
        return [
            'Transaction ID',
            'Customer Name',
            'Vehicle',
            'Booking Date',
            'Amount',
            'Status',
            'Payment Date',
        ];
    }

    public function map($transaction): array
    {
        // Memetakan data dari setiap transaksi ke kolom yang sesuai
        return [
            $transaction->id,
            $transaction->booking->user->name,
            $transaction->booking->vehicle->brand . ' ' . $transaction->booking->vehicle->model,
            $transaction->booking->created_at->format('Y-m-d'),
            $transaction->amount,
            ucfirst($transaction->status),
            $transaction->updated_at->format('Y-m-d H:i'),
        ];
    }
}
