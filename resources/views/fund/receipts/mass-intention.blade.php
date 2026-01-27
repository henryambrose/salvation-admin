<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mass Receipt - {{ $receipt_no }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            /* padding: 20px; */
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            flex-direction: column;

        }

        .receipt-container {
            margin: 0 auto;
            background: white;
            padding: 8.5mm 0;
            display: flex;
            height: 130mm;
            width: 180mm;
            flex-direction: column;
            border-bottom: 1px dotted #aaaaaa !important;
            position: relative;
        }

        .receipt-container>div {
            width: 100%;
            display: flex;
            flex-direction: row;
        }

        .imagediv {
            width: 25mm;
            height: 23.5mm;
        }

        .headerdiv {
            width: 155mm;
            height: 23.5mm;
        }

        .headerdiv>div:nth-child(1) {
            font-family: 'Arial Narrow';
            font-size: 28px;
            font-weight: 700;
            text-align: center;
        }

        .headerdiv>div:nth-child(2),
        .headerdiv>div:nth-child(3) {
            font-family: 'Arial';
            font-size: 14px;
            text-align: center;
            margin-top: 2px;
        }

        .receiptdiv {
            font-family: Arial;
            font-size: 16px;
            font-weight: 700;
            height: 7.5mm;
            border-bottom: 2px solid #333333;
            margin-top: 5px;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .receiptinfodiv {
            height: 20mm;
            border-bottom: 1px solid #dddddd;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: row;
            font-family: Arial;
            font-size: 12px;
            /* line-height: 14px; */
        }

        .receiptinfodiv p {
            line-height: 18px;
            width: 60mm;
        }

        .amountdiv {
            height: 19mm;
            width: 100%;
            border-left: 4px solid #4CAF50;
            display: flex;
            font-family: Arial;
            margin: 2.5mm 0;
            background-color: #f9f9f9;
        }

        .amountdiv>div:nth-child(1) {
            margin-top: 3mm;
            padding-left: 3mm;
            height: 7.5mm;
            width: 100%;
            font-size: 24px;
            font-weight: 700;
            color: #4CAF50;
        }

        .amountdiv>div:nth-child(2) {
            margin-top: 1mm;
            padding-left: 3mm;
            font-size: 14px;
            font-style: italic;
            color: #666
        }

        .detaildiv {
            height: 7.5mm;
            display: flex;
            flex-direction: row;
            font-family: Arial;
            font-size: 14px;
            align-items: center;
            text-align: left;
        }

        .detaildiv>div:nth-child(1),
        .detaildiv2>div:nth-child(1) {
            font-weight: 700;
            width: 40mm;
            color: #666666;
        }

        .detaildiv>div:nth-child(2) {
            width: 140mm;
            color: #333333;
        }

        .detaildiv2 {
            height: 9.5mm;
            display: flex;
            flex-direction: row;
            font-family: Arial;
            font-size: 14px;
            align-items: start;
            text-align: left;
            height: 14mm;
            margin-top: 1.5mm;
            margin-bottom: 5mm;
            border-bottom: 2px solid #333333;
            padding-bottom: 2mm;
        }

        .detaildiv2>div:nth-child(2) {
            width: 75mm;
            color: #333333;
        }

        .detaildiv2>div:nth-child(3) {
            width: 65mm;
            height: 13mm;
            display: flex;
            align-items: flex-end;
        }

        .disclaimer {
            width: 100%;
            margin: 0 !important;
            padding: 0 !important;
            font-size: 11px;
            color: #777777;
            text-align: center;
            display: flex;
            align-items: start;
            justify-content: center;
            position: absolute;
            bottom: 9.5mm;
        }

        .signature {
            width: 100%;
            height: 7.5mm;
            border-top: 1px solid #cccccc;
            color: #333333;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            text-align: center
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #4CAF50;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .print-button:hover {
            background: #45a049;
        }
    </style>
</head>

<body>
    <button class="print-button no-print" onclick="window.print()">Print Receipt</button>

    <div class="receipt-container" style="">
        <div style="">
            <div class="imagediv" style=""><img src="{{ asset('images/olos-logo-gray.svg') }}"></div>
            <div class="headerdiv" style="">
                <div style="">CHURCH OF OUR LADY OF SALVATION</div>
                <div style="">S.K. Bole Road, Dadar (West), Mumbai - 400 028 + Tel.: 7021099816</div>
                <div style="">A Public Trust registered under the Public Trust Act under No D-143(BOM)</div>
            </div>
        </div>
        <div class="receiptdiv">MASS RECEIPT</div>
        <div class="receiptinfodiv" style="">
            <div style="">
                <p><b>Receipt No:</b><br>{{ $receipt_no }}</p>
            </div>
            <div style="">
                <p><b>Date:</b><br>{{ $date }}</p>
            </div>
            <div style="">
                <p><b>Family No:</b><br>{{ $family_no }}</p>
            </div>
        </div>
        <div class="amountdiv" style="flex-direction: column;">
            <div style="">₹ {{ $amount }}</div>
            <div style="">{{ $amount_words }}</div>
        </div>
        <div class="receivedfromdiv detaildiv" style="">
            <div style="">Received From:</div>
            <div style="">{{ $received_from }} ({{ $received_from_memberno }})</div>
        </div>
        <div class="detaildiv" style="">
            <div style="">Towards:</div>
            <div style="">{{ $intention_for }}</div> 
        </div>
        <div class="detaildiv" style="">
            <div style="">Notes:</div>
            <div style="">{{ $special_instructions }}</div>
        </div>
        <div class="detaildiv" style="">
            <div style="">Payment Method:</div>
            <div style="">{{ $payment_method }}</div>
        </div>
        <div class="detaildiv2" style="">
            <div style="">Transaction Info:</div>
            <div style="">{{ $transaction_info }}</div> 
            <div style="">
                <div class="signature"
                    style="width: 100%; height:7.5mm; border-top:1px solid #cccccc; color: #333333; display: flex; align-items:center; justify-content: center; font-weight:600; text-align: center">
                    For
                    Church
                    of Our Lady of Salvation</div>
            </div>
        </div>
        <div class="disclaimer">
            This document becomes a valid receipt for payment only when the cheque covered by it is realised.
        </div>
    </div>
    <br>
    <div class="receipt-container" style="border: none !important">
        <div style="">
            <div class="imagediv" style=""><img src="{{ asset('images/olos-logo-gray.svg') }}"></div>
            <div class="headerdiv" style="">
                <div style="">CHURCH OF OUR LADY OF SALVATION</div>
                <div style="">S.K. Bole Road, Dadar (West), Mumbai - 400 028 + Tel.: 7021099816</div>
                <div style="">A Public Trust registered under the Public Trust Act under No D-143(BOM)</div>
            </div>
        </div>
        <div class="receiptdiv">MASS RECEIPT</div>
               <div class="receiptinfodiv" style="">
            <div style="">
                <p><b>Receipt No:</b><br>{{ $receipt_no }}</p>
            </div>
            <div style="">
                <p><b>Date:</b><br>{{ $date }}</p>
            </div>
            <div style="">
                <p><b>Family No:</b><br>{{ $family_no }}</p>
            </div>
        </div>
        <div class="amountdiv" style="flex-direction: column;">
            <div style="">₹ {{ $amount }}</div>
            <div style="">{{ $amount_words }}</div>
        </div>
        <div class="receivedfromdiv detaildiv" style="">
            <div style="">Received From:</div>
            <div style="">{{ $received_from }} ({{ $received_from_memberno }})</div>
        </div>
        <div class="detaildiv" style="">
            <div style="">Towards:</div>
            <div style="">{{ $intention_for }}</div> 
        </div>
        <div class="detaildiv" style="">
            <div style="">Notes:</div>
            <div style="">{{ $special_instructions }}</div>
        </div>
        <div class="detaildiv" style="">
            <div style="">Payment Method:</div>
            <div style="">{{ $payment_method }}</div>
        </div>
        <div class="detaildiv2" style="">
            <div style="">Transaction Info:</div>
            <div style="">{{ $transaction_info }}</div> 
            <div style="">
                <div class="signature"
                    style="width: 100%; height:7.5mm; border-top:1px solid #cccccc; color: #333333; display: flex; align-items:center; justify-content: center; font-weight:600; text-align: center">
                    For
                    Church
                    of Our Lady of Salvation</div>
            </div>
        </div>
            <div style="">
                <div class="signature"
                    style="width: 100%; height:7.5mm; border-top:1px solid #cccccc; color: #333333; display: flex; align-items:center; justify-content: center; font-weight:600; text-align: center">
                    For
                    Church
                    of Our Lady of Salvation</div>
            </div>
        </div>

    </div>

















    <script>
        // Auto-print option (uncomment if needed)
        // window.onload = function() { window.print(); }
    </script>
</body>

</html>
