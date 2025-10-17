    <!DOCTYPE html>
    <html>

    <head>
        <title>Transaction Report</title>
        <style>
            body {
                font-family: sans-serif;
                margin: 20px;
            }

            h1 {
                font-size: 20px;
                text-align: center;
                margin-bottom: 20px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th,
            td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
                font-size: 12px;
            }

            th {
                background-color: #f2f2f2;
            }

            .footer {
                position: fixed;
                bottom: 0;
                width: 100%;
                text-align: center;
                font-size: 10px;
                color: #888;
            }
        </style>
    </head>

    <body>
        <h1>Transaction Report ({{ $startDate }} to {{ $endDate }})</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Vehicle</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->id }}</td>
                    <td>{{ $transaction->booking->user->name }}</td>
                    <td>{{ $transaction->booking->vehicle->brand }} {{ $transaction->booking->vehicle->model }}</td>
                    <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($transaction->status) }}</td>
                    <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No transactions found in this period.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="footer">
            Generated on {{ date('Y-m-d H:i:s') }}
        </div>
    </body>

    </html>