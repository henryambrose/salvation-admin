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
            border-left: 4px solid #2196F3;
        }

        .amount-section .amount {
            font-size: 32px;
            font-weight: bold;
            color: #2196F3;
            margin-bottom: 10px;
        }

        .amount-section .amount-words {
            font-size: 14px;
            color: #666;
            font-style: italic;
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

        .status.recorded {
            background: #fff3cd;
            color: #856404;
        }

        .status.verified {
            background: #d4edda;
            color: #155724;
        }

        .status.archived {
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
            background: #2196F3;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .print-button:hover {
            background: #0b7dda;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">Print Receipt</button>

    <div class="receipt-container">
        <div class="header">
            <h1>PAYMENT RECEIPT</h1>
            <h2>Community Contribution</h2>
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
                <strong>Collection Date:</strong>
                <span>{{ $collection_date }}</span>
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

            <div class="row">
                <div class="label">Contribution Type:</div>
                <div class="value">{{ $contribution_type }}</div>
            </div>

            @if($description)
            <div class="row">
                <div class="label">Description:</div>
                <div class="value">{{ $description }}</div>
            </div>
            @endif

            @if($location)
            <div class="row">
                <div class="label">Location:</div>
                <div class="value">{{ $location }}</div>
            </div>
            @endif

            <div class="row">
                <div class="label">Collected By:</div>
                <div class="value">{{ $collected_by }}</div>
            </div>

            <div class="row">
                <div class="label">Status:</div>
                <div class="value">
                    <span class="status {{ strtolower($status) }}">{{ $status }}</span>
                </div>
            </div>

            @if($notes)
            <div class="row">
                <div class="label">Notes:</div>
                <div class="value">{{ $notes }}</div>
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
            <p>Thank you for your contribution.</p>
        </div>
    </div>

    <script>
        // Auto-print option (uncomment if needed)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
