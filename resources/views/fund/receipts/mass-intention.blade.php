<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $receipt_no }}</title>
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
            border-left: 4px solid #9C27B0;
        }

        .amount-section .amount {
            font-size: 32px;
            font-weight: bold;
            color: #9C27B0;
            margin-bottom: 10px;
        }

        .amount-section .amount-words {
            font-size: 14px;
            color: #666;
            font-style: italic;
        }

        .intention-box {
            background: #f0f0f0;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            border-left: 4px solid #9C27B0;
        }

        .intention-box strong {
            display: block;
            color: #666;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .intention-box p {
            color: #333;
            font-size: 16px;
            line-height: 1.6;
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

        .status.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status.confirmed {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status.completed {
            background: #d4edda;
            color: #155724;
        }

        .status.cancelled {
            background: #f8d7da;
            color: #721c24;
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
            background: #9C27B0;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .print-button:hover {
            background: #7B1FA2;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">Print Receipt</button>

    <div class="receipt-container">
        <div class="header">
            <h1>PAYMENT RECEIPT</h1>
            <h2>Mass Intention</h2>
        </div>

        <div class="receipt-info">
            <div>
                <strong>Receipt No:</strong>
                <span>{{ $receipt_no }}</span>
            </div>
            <div>
                <strong>Date:</strong>
                <span>{{ $date }}</span>
            </div>
            <div>
                <strong>Mass Date:</strong>
                <span>{{ $mass_date }}</span>
            </div>
        </div>

        <div class="amount-section">
            <div class="amount">₹ {{ $amount }}</div>
            <div class="amount-words">{{ $amount_words }} Only</div>
        </div>

        <div class="receipt-body">
            <div class="row">
                <div class="label">Received From:</div>
                <div class="value">{{ $received_from }}</div>
            </div>

            @if($phone)
            <div class="row">
                <div class="label">Phone:</div>
                <div class="value">{{ $phone }}</div>
            </div>
            @endif

            <div class="row">
                <div class="label">Mass Type:</div>
                <div class="value">{{ $mass_type }}</div>
            </div>

            <div class="row">
                <div class="label">Intention Type:</div>
                <div class="value">{{ $intention_type }}</div>
            </div>

            @if($intention_for)
            <div class="intention-box">
                <strong>Mass Intention For:</strong>
                <p>{{ $intention_for }}</p>
            </div>
            @endif

            @if($special_instructions)
            <div class="intention-box">
                <strong>Special Instructions:</strong>
                <p>{{ $special_instructions }}</p>
            </div>
            @endif

            <div class="row">
                <div class="label">Payment Method:</div>
                <div class="value">{{ $payment_method }}</div>
            </div>

            <div class="row">
                <div class="label">Status:</div>
                <div class="value">
                    <span class="status {{ strtolower($status) }}">{{ $status }}</span>
                </div>
            </div>

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
            <p>Thank you for your mass intention offering.</p>
        </div>
    </div>

    <script>
        // Auto-print option (uncomment if needed)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
