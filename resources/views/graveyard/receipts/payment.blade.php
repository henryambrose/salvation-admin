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
            padding: 10px;
            background: #f5f5f5;
        }

        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-bottom: 1px dotted #aaaaaa;
            page-break-inside: avoid;
        }

        .header {
            display: flex;
            flex-direction: row;
            border-bottom: 2px solid #333;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }

        .imagediv {
            width: 25mm;
            height: 23.5mm;
        }

        .headerdiv {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .headerdiv > div:nth-child(1) {
            font-family: 'Arial Narrow';
            font-size: 24px;
            font-weight: 700;
            text-align: center;
        }

        .headerdiv > div:nth-child(2),
        .headerdiv > div:nth-child(3) {
            font-family: 'Arial';
            font-size: 12px;
            text-align: center;
            margin-top: 1px;
        }

        .headerdiv > div:nth-child(4) {
            font-family: 'Arial';
            font-size: 16px;
            text-align: center;
            margin-top: 5px;
            color: #666;
            font-weight: normal;
        }

        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #ddd;
        }

        .receipt-info div {
            flex: 1;
        }

        .receipt-info strong {
            display: block;
            color: #666;
            font-size: 11px;
            margin-bottom: 3px;
        }

        .receipt-info span {
            display: block;
            color: #333;
            font-size: 13px;
            font-weight: bold;
        }

        .receipt-body {
            margin-bottom: 10px;
        }

        .row {
            display: flex;
            padding: 5px 0;
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
            padding: 10px;
            margin: 10px 0;
            border-left: 4px solid #FF9800;
        }

        .amount-section .amount {
            font-size: 20px;
            font-weight: bold;
            color: #FF9800;
            margin-bottom: 4px;
        }

        .amount-section .amount-words {
            font-size: 12px;
            color: #666;
            font-style: italic;
        }

        .service-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
        }

        .service-table th {
            background: #f9f9f9;
            padding: 6px;
            text-align: left;
            font-weight: bold;
            color: #666;
            border-bottom: 2px solid #ddd;
            font-size: 12px;
        }

        .service-table td {
            padding: 6px;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }

        .service-table tr:last-child td {
            border-bottom: none;
        }

        .footer {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 2px solid #333;
            text-align: center;
            color: #666;
            font-size: 10px;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding: 0 30px;
        }

        .signature {
            text-align: center;
        }

        .signature-line {
            width: 180px;
            border-top: 1px solid #333;
            margin: 20px 0 8px 0;
        }

        .signature-label {
            color: #666;
            font-size: 12px;
        }

        .status {
            display: inline-block;
            padding: 0;
            border-radius: 0;
            font-size: 14px;
            font-weight: normal;
            text-transform: uppercase;
        }

        .status.completed {
            color: #155724;
        }

        .status.partial {
            color: #856404;
        }

        .status.pending {
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
            margin-bottom: 8px;
            page-break-inside: avoid;
        }

        .copy-label {
            text-align: center;
            font-size: 9px;
            color: #999;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        @media print {
            @page {
                margin: 0.3in;
                size: A4 portrait;
            }

            body {
                padding: 0;
                margin: 0;
                background: white;
            }

            .receipt-container {
                padding: 8px;
                margin-bottom: 0;
                box-shadow: none;
            }

            .receipt-copy {
                margin-bottom: 5px;
            }

            .receipt-copy:last-child {
                margin-bottom: 0;
                border-bottom: none;
            }

            .copy-label {
                font-size: 8px;
                margin-bottom: 3px;
            }

            .header {
                padding-bottom: 4px;
                margin-bottom: 6px;
            }

            .imagediv {
                width: 18mm;
                height: 17mm;
            }

            .headerdiv > div:nth-child(1) {
                font-size: 16px;
            }

            .headerdiv > div:nth-child(2),
            .headerdiv > div:nth-child(3) {
                font-size: 10px;
                margin-top: 1px;
            }

            .headerdiv > div:nth-child(4) {
                font-size: 12px;
                margin-top: 3px;
            }

            .receipt-info {
                margin-bottom: 6px;
                padding-bottom: 4px;
            }

            .receipt-info strong {
                font-size: 9px;
                margin-bottom: 2px;
            }

            .receipt-info span {
                font-size: 10px;
            }

            .amount-section {
                margin: 6px 0;
                padding: 6px;
            }

            .amount-section .amount {
                font-size: 16px;
                margin-bottom: 3px;
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
                font-size: 10px;
            }

            .row .value {
                font-size: 10px;
            }

            .service-table {
                margin: 4px 0;
                font-size: 9px;
            }

            .service-table th {
                padding: 3px;
                font-size: 9px;
            }

            .service-table td {
                padding: 3px;
                font-size: 9px;
            }

            .signature-section {
                margin-top: 8px;
                padding: 0 20px;
            }

            .signature-line {
                margin: 12px 0 4px 0;
                width: 120px;
            }

            .signature-label {
                font-size: 9px;
            }

            .footer {
                margin-top: 6px;
                padding-top: 4px;
                font-size: 8px;
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
            <div class="imagediv"><img src="{{ asset('images/olos-logo-gray.svg') }}"></div>
            <div class="headerdiv">
                <div>CHURCH OF OUR LADY OF SALVATION</div>
                <div>S.K. Bole Road, Dadar (West), Mumbai - 400 028 + Tel.: 7021099816</div>
                <div>A Public Trust registered under the Public Trust Act under No D-143(BOM)</div>
                <div>Graveyard Services - Payment Receipt</div>
            </div>
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
            <div class="imagediv"><img src="{{ asset('images/olos-logo-gray.svg') }}"></div>
            <div class="headerdiv">
                <div>CHURCH OF OUR LADY OF SALVATION</div>
                <div>S.K. Bole Road, Dadar (West), Mumbai - 400 028 + Tel.: 7021099816</div>
                <div>A Public Trust registered under the Public Trust Act under No D-143(BOM)</div>
                <div>Graveyard Services - Payment Receipt</div>
            </div>
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
