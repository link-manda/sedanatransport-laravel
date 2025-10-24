<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TransactionsExport;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display the reports page with filtered transactions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Start building the query
        $query = Transaction::with(['booking.user', 'booking.vehicle'])->latest(); // Use latest() to order by creation date descending

        // Apply filters if they exist
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Paginate the results
        $transactions = $query->paginate(10)->withQueryString(); // withQueryString() keeps filters in pagination links

        // Pass the paginated transactions to the view
        return view('admin.reports.index', compact('transactions'));
    }

    /**
     * Handle exporting transactions to CSV or PDF.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        // Validate the incoming request (format is required, dates are optional for export but good to validate if present)
        $request->validate([
            'format' => 'required|in:csv,pdf',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|string|in:pending,waiting_confirmation,paid,failed', // Adjust statuses if needed
        ]);

        $format = $request->input('format');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status'); // Get status filter

        // Generate filename with date range if available
        $datePart = '';
        if ($startDate && $endDate) {
            $datePart = '_' . Carbon::parse($startDate)->format('Ymd') . '-' . Carbon::parse($endDate)->format('Ymd');
        } elseif ($startDate) {
            $datePart = '_from_' . Carbon::parse($startDate)->format('Ymd');
        } elseif ($endDate) {
            $datePart = '_until_' . Carbon::parse($endDate)->format('Ymd');
        }
        $filename = 'transactions' . $datePart . '.' . ($format === 'csv' ? 'xlsx' : 'pdf'); // Use xlsx for Excel

        // --- Logic for CSV Export ---
        if ($format === 'csv') {
            // Pass filters to the Export class constructor
            return Excel::download(new TransactionsExport($startDate, $endDate, $status), $filename);
        }

        // --- Logic for PDF Export ---
        if ($format === 'pdf') {
            // Rebuild query with filters for PDF generation
            $query = Transaction::with(['booking.user', 'booking.vehicle']);

            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }
            if ($status) { // Apply status filter for PDF too
                $query->where('status', $status);
            }

            $transactions = $query->orderBy('created_at', 'desc')->get(); // Get all matching records for PDF

            $data = [
                'transactions' => $transactions,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'status' => $status, // Pass status to the view
                'generatedDate' => Carbon::now()->format('d M Y H:i:s'),
            ];

            // Load the view and generate PDF
            $pdf = Pdf::loadView('admin.reports.pdf_template', $data);

            // Optionally set paper size and orientation
            // $pdf->setPaper('a4', 'landscape');

            // Download the PDF
            return $pdf->download($filename);
        }

        // Fallback in case format is invalid (though validation should prevent this)
        return redirect()->back()->with('error', 'Invalid export format requested.');
    }
}
