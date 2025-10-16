<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('booking.user', 'booking.vehicle')->latest()->paginate(10);
        return view('admin.transactions.index', compact('transactions'));
    }

    public function markAsPaid(Transaction $transaction)
    {
        $transaction->status = 'paid';
        $transaction->save();

        return redirect()->route('admin.transactions.index')->with('success', 'Transaction has been marked as paid.');
    }
}
