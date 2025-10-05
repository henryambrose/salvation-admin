<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $payment->receipt_number }}</title>
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

        .status.completed {
            background: #d4edda;
            color: #155724;
        }

        .status.failed {
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
            <h2>Obituary Page Service</h2>
        </div>

        <div class="receipt-info">
            <div>
                <strong>Receipt No:</strong>
                <span>{{ $payment->receipt_number }}</span>
            </div>
            <div>
                <strong>Date:</strong>
                <span>{{ now()->format('d/m/Y') }}</span>
            </div>
            <div>
                <strong>Payment Ref:</strong>
                <span>{{ $payment->payment_reference }}</span>
            </div>
        </div>

        <div class="amount-section">
            <div class="amount">₹ {{ number_format($payment->amount, 2) }}</div>
            <div class="amount-words">
                @php
                    function numberToWords($number) {
                        $amount = number_format($number, 2, '.', '');
                        list($rupees, $paise) = explode('.', $amount);

                        $words = '';
                        if ($rupees > 0) {
                            $words = convertNumberToWords((int)$rupees) . ' Rupees';
                        }
                        if ($paise > 0) {
                            $words .= ($words ? ' and ' : '') . convertNumberToWords((int)$paise) . ' Paise';
                        }

                        return $words ?: 'Zero Rupees';
                    }

                    function convertNumberToWords($number) {
                        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
                        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
                        $teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];

                        if ($number < 10) {
                            return $ones[$number];
                        } elseif ($number < 20) {
                            return $teens[$number - 10];
                        } elseif ($number < 100) {
                            return $tens[intval($number / 10)] . ' ' . $ones[$number % 10];
                        } elseif ($number < 1000) {
                            return $ones[intval($number / 100)] . ' Hundred ' . convertNumberToWords($number % 100);
                        } elseif ($number < 100000) {
                            return convertNumberToWords(intval($number / 1000)) . ' Thousand ' . convertNumberToWords($number % 1000);
                        } elseif ($number < 10000000) {
                            return convertNumberToWords(intval($number / 100000)) . ' Lakh ' . convertNumberToWords($number % 100000);
                        } else {
                            return convertNumberToWords(intval($number / 10000000)) . ' Crore ' . convertNumberToWords($number % 10000000);
                        }
                    }
                @endphp
                {{ numberToWords($payment->amount) }} Only
            </div>
        </div>

        <div class="receipt-body">
            <div class="row">
                <div class="label">Service Plan:</div>
                <div class="value">{{ $payment->obituaryPlan->name ?? 'N/A' }}</div>
            </div>

            <div class="row">
                <div class="label">Deceased Name:</div>
                <div class="value">{{ $payment->obituaryPage->deceased_name ?? 'N/A' }}</div>
            </div>

            <div class="row">
                <div class="label">Payment Method:</div>
                <div class="value">{{ $payment->paymentMethod->name ?? 'N/A' }}</div>
            </div>

            <div class="row">
                <div class="label">Payment Date:</div>
                <div class="value">{{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : 'N/A' }}</div>
            </div>

            <div class="row">
                <div class="label">Status:</div>
                <div class="value">
                    <span class="status {{ strtolower($payment->payment_status) }}">{{ ucfirst($payment->payment_status) }}</span>
                </div>
            </div>

            @if($payment->expires_at)
            <div class="row">
                <div class="label">Expires On:</div>
                <div class="value">{{ $payment->expires_at->format('d/m/Y') }}</div>
            </div>
            @else
            <div class="row">
                <div class="label">Duration:</div>
                <div class="value">Lifetime</div>
            </div>
            @endif

            @if($payment->notes)
            <div class="row">
                <div class="label">Notes:</div>
                <div class="value">{{ $payment->notes }}</div>
            </div>
            @endif

            <div class="row">
                <div class="label">Transaction Date:</div>
                <div class="value">{{ $payment->created_at->format('d/m/Y H:i A') }}</div>
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
            <p>Thank you for using our obituary services.</p>
        </div>
    </div>

    <script>
        // Auto-print option (uncomment if needed)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
