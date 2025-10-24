<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Carbon\Carbon;

class TransactionsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $startDate;
    protected $endDate;
    protected $status; // Add status property

    /**
     * Constructor to accept start date, end date, and status.
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @param string|null $status
     */
    public function __construct($startDate = null, $endDate = null, $status = null) // Add status parameter
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status; // Store status
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $query = Transaction::query()->with(['booking.user', 'booking.vehicle']);

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            // Add one day to end date to include the whole day
            $endDateCarbon = Carbon::parse($this->endDate)->addDay();
            $query->where('created_at', '<', $endDateCarbon); // Use '<' with the next day start
            // Alternatively, ensure time part is end of day:
            // $query->whereDate('created_at', '<=', $this->endDate); // Simpler if only date matters
        }

        if ($this->status) { // Apply status filter
            $query->where('status', $this->status);
        }


        // Order by creation date
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Transaction ID',
            'Booking ID',
            'User Name',
            'User Email',
            'Vehicle',
            'Plate Number',
            'Amount (Rp)',
            'Payment Method',
            'Status',
            'Rejection Reason',
            'Payment Due At',
            'Paid At',
            'Created At',
        ];
    }

    /**
     * @param mixed $transaction // Type hint Transaction model
     * @return array
     */
    public function map($transaction): array
    {
        return [
            $transaction->id,
            $transaction->booking_id,
            $transaction->booking->user->name ?? 'N/A',
            $transaction->booking->user->email ?? 'N/A',
            ($transaction->booking->vehicle->brand ?? '') . ' ' . ($transaction->booking->vehicle->model ?? ''),
            $transaction->booking->vehicle->plate_number ?? 'N/A',
            number_format($transaction->amount, 0, ',', '.'), // Format amount without decimals for rupiah
            ucfirst(str_replace('_', ' ', $transaction->payment_method)),
            ucfirst(str_replace('_', ' ', $transaction->status)),
            $transaction->rejection_reason ?? '-', // Include rejection reason
            $transaction->payment_due_at ? Carbon::parse($transaction->payment_due_at)->format('Y-m-d H:i:s') : '-',
            $transaction->paid_at ? Carbon::parse($transaction->paid_at)->format('Y-m-d H:i:s') : '-',
            $transaction->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Apply styles to the worksheet.
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Style the first row (headings) to be bold.
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
