<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Approved</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .header {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .content {
            padding: 20px 0;
        }

        .footer {
            margin-top: 20px;
            font-size: 0.8em;
            text-align: center;
            color: #777;
        }

        .booking-details {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        .booking-details td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .booking-details tr td:first-child {
            font-weight: bold;
            width: 30%;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Booking Approved!</h2>
        </div>
        <div class="content">
            <p>Hello, {{ $booking->user->name }}!</p>
            <p>We are happy to inform you that your booking for the <strong>{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</strong> has been approved.</p>
            <p>Here are the details of your booking:</p>

            <table class="booking-details">
                <tr>
                    <td>Vehicle</td>
                    <td>{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</td>
                </tr>
                <tr>
                    <td>Start Date</td>
                    <td>{{ $booking->start_date->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td>End Date</td>
                    <td>{{ $booking->end_date->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td>Total Price</td>
                    <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                </tr>
            </table>

            <p>Please proceed with the payment to complete your reservation. Thank you for choosing our service!</p>
            <p>Best regards,<br>{{ config('app.name') }}</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>