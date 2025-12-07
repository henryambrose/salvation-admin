<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $receipt_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }

        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 20px;
            color: #666;
            font-weight: normal;
        }

        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }

        .receipt-info div {
            flex: 1;
        }

        .receipt-info strong {
            display: block;
            color: #666;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .receipt-info span {
            display: block;
            color: #333;
            font-size: 16px;
            font-weight: bold;
        }

        .receipt-body {
            margin-bottom: 30px;
        }

        .row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .row:last-child {
            border-bottom: none;
        }

        .row .label {
            flex: 0 0 200px;
            color: #666;
            font-weight: bold;
        }

        .row .value {
            flex: 1;
            color: #333;
        }

        .amount-section {
            background: #f9f9f9;
            padding: 20px;
            margin: 30px 0;
            border-left: 4px solid #FF9800;
        }

        .amount-section .amount {
            font-size: 32px;
            font-weight: bold;
            color: #FF9800;
            margin-bottom: 10px;
        }

        .amount-section .amount-words {
            font-size: 14px;
            color: #666;
            font-style: italic;
        }

        .service-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .service-table th {
            background: #f9f9f9;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            color: #666;
            border-bottom: 2px solid #ddd;
        }

        .service-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .service-table tr:last-child td {
            border-bottom: none;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #333;
            text-align: center;
            color: #666;
            font-size: 12px;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
            padding: 0 40px;
        }

        .signature {
            text-align: center;
        }

        .signature-line {
            width: 200px;
            border-top: 1px solid #333;
            margin: 60px 0 10px 0;
        }

        .signature-label {
            color: #666;
            font-size: 14px;
        }

        .status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status.completed {
            background: #d4edda;
            color: #155724;
        }

        .status.partial {
            background: #fff3cd;
            color: #856404;
        }

        .status.pending {
            background: #d1ecf1;
            color: #0c5460;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .receipt-container {
                box-shadow: none;
                padding: 20px;
            }

            .no-print {
                display: none;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #FF9800;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .print-button:hover {
            background: #F57C00;
        }

        .receipt-copy {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .copy-label {
            text-align: center;
            font-size: 10px;
            color: #999;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        @media print {
            @page {
                margin: 0.2in;
                size: A4 portrait;
            }

            body {
                padding: 0;
                margin: 0;
                transform: scale(0.75);
                transform-origin: top center;
            }

            .receipt-container {
                padding: 10px;
                margin-bottom: 0;
                max-width: 700px;
            }

            .receipt-copy {
                margin-bottom: 5px;
            }

            .receipt-copy:last-child {
                margin-bottom: 0;
            }

            .copy-label {
                font-size: 8px;
                margin-bottom: 3px;
            }

            .header h1 {
                font-size: 16px;
                margin-bottom: 1px;
            }

            .header h2 {
                font-size: 12px;
            }

            .header {
                padding-bottom: 5px;
                margin-bottom: 6px;
            }

            .receipt-info {
                margin-bottom: 6px;
                padding-bottom: 5px;
            }

            .receipt-info strong {
                font-size: 9px;
                margin-bottom: 2px;
            }

            .receipt-info span {
                font-size: 11px;
            }

            .amount-section {
                margin: 6px 0;
                padding: 6px;
            }

            .amount-section .amount {
                font-size: 18px;
                margin-bottom: 5px;
            }

            .amount-section .amount-words {
                font-size: 10px;
            }

            .receipt-body {
                margin-bottom: 6px;
            }

            .row {
                padding: 3px 0;
            }

            .row .label {
                font-size: 11px;
            }

            .row .value {
                font-size: 11px;
            }

            .service-table {
                margin: 6px 0;
                font-size: 10px;
            }

            .service-table th {
                padding: 4px;
                font-size: 10px;
            }

            .service-table td {
                padding: 4px;
                font-size: 10px;
            }

            .signature-section {
                margin-top: 12px;
                padding: 0 20px;
            }

            .signature-line {
                margin: 20px 0 3px 0;
                width: 120px;
            }

            .signature-label {
                font-size: 10px;
            }

            .footer {
                margin-top: 8px;
                padding-top: 5px;
                font-size: 9px;
            }
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">Print Receipt</button>

    <!-- First Copy - Office Copy -->
    <div class="receipt-copy">
        <div class="copy-label">Office Copy</div>
        <div class="receipt-container">
        <div class="header">
            <h1>{{ strtoupper(config('app.church_name')) }}</h1>
            <h2>Graveyard Services - Payment Receipt</h2>
        </div>

        <div class="receipt-info">
            <div>
                <strong>Receipt No:</strong>
                <span>{{ $receipt_number }}</span>
            </div>
            <div>
                <strong>Payment Ref:</strong>
                <span>{{ $payment_reference }}</span>
            </div>
            <div>
                <strong>Payment Date:</strong>
                <span>{{ $payment_date }}</span>
            </div>
        </div>

        <div class="amount-section">
            <div class="amount">₹ {{ $paid_amount }}</div>
            <div class="amount-words">{{ $amount_words }} Only</div>
        </div>

        <div class="receipt-body">
            @if($booking_reference)
            <div class="row">
                <div class="label">Booking Reference:</div>
                <div class="value">{{ $booking_reference }}</div>
            </div>
            @endif

            @if($deceased_name)
            <div class="row">
                <div class="label">Deceased Person:</div>
                <div class="value">{{ $deceased_name }}</div>
            </div>
            @endif

            @if($grave_number)
            <div class="row">
                <div class="label">Grave Number:</div>
                <div class="value">{{ $grave_number }}</div>
            </div>
            @endif

            @if($applicant_name)
            <div class="row">
                <div class="label">Applicant:</div>
                <div class="value">{{ $applicant_name }}</div>
            </div>
            @endif

            @if(!empty($service_charges))
            <div class="row">
                <div class="label" style="flex: 1;">Service Details:</div>
            </div>
            <table class="service-table">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Unit Cost</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($service_charges as $service)
                    <tr>
                        <td>{{ $service['service_name'] }}</td>
                        <td style="text-align: center;">{{ $service['quantity'] }}</td>
                        <td style="text-align: right;">₹ {{ number_format($service['unit_cost'], 2) }}</td>
                        <td style="text-align: right;">₹ {{ number_format($service['total_cost'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            <div class="row">
                <div class="label">Total Amount:</div>
                <div class="value">₹ {{ number_format($total_amount, 2) }}</div>
            </div>

            <div class="row">
                <div class="label">Amount Paid:</div>
                <div class="value">₹ {{ number_format($paid_amount, 2) }}</div>
            </div>

            @if($balance_amount > 0)
            <div class="row">
                <div class="label">Balance Amount:</div>
                <div class="value">₹ {{ number_format($balance_amount, 2) }}</div>
            </div>
            @endif

            <div class="row">
                <div class="label">Payment Method:</div>
                <div class="value">{{ $payment_method }}</div>
            </div>

            @if($payment_mode)
            <div class="row">
                <div class="label">Payment Mode:</div>
                <div class="value">{{ ucfirst($payment_mode) }}</div>
            </div>
            @endif

            @if($transaction_reference)
            <div class="row">
                <div class="label">Transaction Reference:</div>
                <div class="value">{{ $transaction_reference }}</div>
            </div>
            @endif

            <div class="row">
                <div class="label">Status:</div>
                <div class="value">
                    <span class="status {{ strtolower($payment_status) }}">{{ ucfirst($payment_status) }}</span>
                </div>
            </div>

            @if($payment_notes)
            <div class="row">
                <div class="label">Notes:</div>
                <div class="value">{{ $payment_notes }}</div>
            </div>
            @endif

            @if($recorded_by)
            <div class="row">
                <div class="label">Recorded By:</div>
                <div class="value">{{ $recorded_by }}</div>
            </div>
            @endif

            <div class="row">
                <div class="label">Transaction Date:</div>
                <div class="value">{{ $created_at }}</div>
            </div>
        </div>

        <div class="signature-section">
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-label">Received By</div>
            </div>
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-label">Authorized Signature</div>
            </div>
        </div>

        <div class="footer">
            <p>This is a computer-generated receipt.</p>
            <p>Thank you for your payment.</p>
        </div>
    </div>
    </div>

    <!-- Second Copy - Customer Copy -->
    <div class="receipt-copy">
        <div class="copy-label">Customer Copy</div>
        <div class="receipt-container">
        <div class="header">
            <h1>{{ strtoupper(config('app.church_name')) }}</h1>
            <h2>Graveyard Services - Payment Receipt</h2>
        </div>

        <div class="receipt-info">
            <div>
                <strong>Receipt No:</strong>
                <span>{{ $receipt_number }}</span>
            </div>
            <div>
                <strong>Payment Ref:</strong>
                <span>{{ $payment_reference }}</span>
            </div>
            <div>
                <strong>Payment Date:</strong>
                <span>{{ $payment_date }}</span>
            </div>
        </div>

        <div class="amount-section">
            <div class="amount">₹ {{ $paid_amount }}</div>
            <div class="amount-words">{{ $amount_words }} Only</div>
        </div>

        <div class="receipt-body">
            @if($booking_reference)
            <div class="row">
                <div class="label">Booking Reference:</div>
                <div class="value">{{ $booking_reference }}</div>
            </div>
            @endif

            @if($deceased_name)
            <div class="row">
                <div class="label">Deceased Person:</div>
                <div class="value">{{ $deceased_name }}</div>
            </div>
            @endif

            @if($grave_number)
            <div class="row">
                <div class="label">Grave Number:</div>
                <div class="value">{{ $grave_number }}</div>
            </div>
            @endif

            @if($applicant_name)
            <div class="row">
                <div class="label">Applicant:</div>
                <div class="value">{{ $applicant_name }}</div>
            </div>
            @endif

            @if(!empty($service_charges))
            <div class="row">
                <div class="label" style="flex: 1;">Service Details:</div>
            </div>
            <table class="service-table">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Unit Cost</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($service_charges as $service)
                    <tr>
                        <td>{{ $service['service_name'] }}</td>
                        <td style="text-align: center;">{{ $service['quantity'] }}</td>
                        <td style="text-align: right;">₹ {{ number_format($service['unit_cost'], 2) }}</td>
                        <td style="text-align: right;">₹ {{ number_format($service['total_cost'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            <div class="row">
                <div class="label">Total Amount:</div>
                <div class="value">₹ {{ number_format($total_amount, 2) }}</div>
            </div>

            <div class="row">
                <div class="label">Amount Paid:</div>
                <div class="value">₹ {{ number_format($paid_amount, 2) }}</div>
            </div>

            @if($balance_amount > 0)
            <div class="row">
                <div class="label">Balance Amount:</div>
                <div class="value">₹ {{ number_format($balance_amount, 2) }}</div>
            </div>
            @endif

            <div class="row">
                <div class="label">Payment Method:</div>
                <div class="value">{{ $payment_method }}</div>
            </div>

            @if($payment_mode)
            <div class="row">
                <div class="label">Payment Mode:</div>
                <div class="value">{{ ucfirst($payment_mode) }}</div>
            </div>
            @endif

            @if($transaction_reference)
            <div class="row">
                <div class="label">Transaction Reference:</div>
                <div class="value">{{ $transaction_reference }}</div>
            </div>
            @endif

            <div class="row">
                <div class="label">Status:</div>
                <div class="value">
                    <span class="status {{ strtolower($payment_status) }}">{{ ucfirst($payment_status) }}</span>
                </div>
            </div>

            @if($payment_notes)
            <div class="row">
                <div class="label">Notes:</div>
                <div class="value">{{ $payment_notes }}</div>
            </div>
            @endif

            @if($recorded_by)
            <div class="row">
                <div class="label">Recorded By:</div>
                <div class="value">{{ $recorded_by }}</div>
            </div>
            @endif

            <div class="row">
                <div class="label">Transaction Date:</div>
                <div class="value">{{ $created_at }}</div>
            </div>
        </div>

        <div class="signature-section">
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-label">Received By</div>
            </div>
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-label">Authorized Signature</div>
            </div>
        </div>

        <div class="footer">
            <p>This is a computer-generated receipt.</p>
            <p>Thank you for your payment.</p>
        </div>
    </div>
    </div>

    <script>
        // Auto-print option (uncomment if needed)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
