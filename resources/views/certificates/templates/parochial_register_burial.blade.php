<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Burial</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        @media print {
            @page {
                margin: 0mm;
                padding: 0mm;
                margin-top: 10mm;

            }

            body {
                margin: 0;
                padding: 0;
            }
        }

        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            min-height: 100vh;
        }

        .masterframe {
            width: 180mm;
            min-height: 280mm;
            display: flex;
            flex-direction: column;
        }

        .headframe {
            width: 100%;
            min-height: 32mm;
            display: flex;
            position: relative;
            margin-bottom: 5mm;
        }

        .logoframe {
            height: 100%;
            width: 36.5mm;
            padding-top: 5mm;
            display: flex;
            justify-content: end;
            position: absolute;
            left: 10mm;
            top: 0;
            z-index: 10;

        }

        .logoframeimg {
            height: 24mm;
            width: 24mm;
        }

        .headframecontent {
            height: 100%;
            width: 243.5mm;
            padding: 0 11mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            font-weight: 800;
        }

        .cert_nm {
            font-size: 24px;
            display: inline-flex width: 100%;
            height: 28px;
            justify-content: center;
            text-align: center;
            font-family: 'Times New Roman', Times, serif;
            line-height: 28px;
        }

        .headframe_bylines {
            font-size: 12px;
            display: inline-flex;
            width: 100%;
            height: 14px;
            justify-content: center;
            text-align: center;
            font-family: 'Times New Roman', Times, serif;
        }

        .headframe_parish {
            font-size: 16px;
            display: inline-flex;
            width: 100%;
            margin: 2.5mm 0;
            height: 18px;
            justify-content: center;
            text-align: center;
            font-family: 'Times New Roman', Times, serif;
        }

        .bodyframe {
            /* height: 245mm; */
            width: 100%;
        }

        .brow {
            width: 100%;
            height: 7.5mm;
            display: flex;
            font-size: 11pt;
            font-weight: 600;
            font-family: Arial, Helvetica, sans-serif;
        }

        .bcell1 {
            width: 10mm;
            padding-right: 2mm;
            height: 100%;
            padding-top: 2mm;
            display: flex;
            justify-content: start;
            align-items: center;
            font-family: Arial, Helvetica, sans-serif;
        }

        .bcell12 {
            width: 47mm;
            padding-right: 2mm;
            height: 100%;
            padding-top: 2mm;
            display: flex;
            justify-content: start;
            align-items: center;
        }

        .bcell13 {
            width: 3mm;
            height: 100%;
            padding-top: 2mm;
            display: flex;
            justify-content: start;
            align-items: center;
        }

        .bcell14 {
            width: 118.5mm;
            padding-left: 2mm;
            height: 100%;
            padding-top: 2mm;
            margin-left: 1.5mm;
            display: flex;
            justify-content: start;
            align-items: center;
            border-bottom: 1px dotted #cccccc;
        }

        .bottomframe {
            width: 100%;
            margin-top: 15mm;
            display: flex;
            flex-direction: row;
            font-family: 'Times New Roman', Times, serif;
            text-align: center;
        }

        .authenticity {
            display: flex;
            width: 50%;
            justify-content: center;
            align-items: center;
            font-weight: 600;
            font-size: 16px;
            display: flex;
            flex-direction: column;
        }

        .signature {
            display: flex;
            width: 50%;
            justify-content: center;
            align-items: center;
            font-weight: 600;
            font-size: 16px;
            display: flex;
            flex-direction: column;
        }

        .bottom_width {
            width: 100%;
        }

        .authenticity_text,
        .signature_date {
            margin-bottom: 20mm;
        }


        .jrow {}

        .jrowa {}

        .jrowb {}

        .jrowc {}

        .jrowd {}
    </style>
</head>

<body>
    <div class="masterframe">
        <div class="headframe">
            <div class="logoframe">
                @if (isset($template_config['show_logo']) && $template_config['show_logo'] && !empty($template_config['logo_url']))
                    <img src="{{ $template_config['logo_url'] }}" alt="Church Logo">
                @endif
            </div>
            <div class="headframecontent">
                <div class="cert_nm">CERTIFICATE OF BURIAL</div>
                <div class="headframe_bylines">Extracts from the Parochial Register of</div>
                <div class="headframe_parish">{{ strtoupper($parish_name) }}
                </div>
                <div class="headframe_bylines">{{ $parish_address ?? 'Dadar (W), Mumbai - 400 028' }}</div>
                <div style="width: 140.5mm; font-style: italic; font-bold; padding-left: 2mm; ">
                    Burial No {{ $burial_reg_no_short ?? ($burial_reg_no ? (int) filter_var($burial_reg_no, FILTER_SANITIZE_NUMBER_INT) : '--') }} of the Year {{ $burial_year ?? '--' }}
                </div>
            </div>
        </div>

        <!-- Body -->
        <div style="width: 100%; padding-left: 10mm; font-size: 11pt; font-weight: 600; font-family: Arial, Helvetica, sans-serif;">
            @php
                $fields = [
                    ['num' => '1.', 'label' => 'Date of Death', 'value' => $death_date ?? ''],
                    ['num' => '2.', 'label' => 'Date of Burial', 'value' => $burial_date ?? ''],
                    ['num' => '3.', 'label' => 'Name', 'value' => $deceased_name ?? ''],
                    ['num' => '4.', 'label' => 'Surname', 'value' => $deceased_surname ?? ''],
                    ['num' => '5.', 'label' => 'Relationship', 'value' => $relationship ?? ''],
                    ['num' => '6.', 'label' => 'Residence', 'value' => $residence ?? ''],
                    ['num' => '7.', 'label' => 'Age', 'value' => ($age ? $age . ' Years' : '')],
                    ['num' => '8.', 'label' => 'Nationality', 'value' => $nationality ?? ''],
                    ['num' => '9.', 'label' => 'Cause of Death', 'value' => $cause_of_death ?? ''],
                    ['num' => '10.', 'label' => 'Place of Burial', 'value' => $place_of_burial ?? ''],
                    ['num' => '11.', 'label' => 'Minister', 'value' => $minister_name ?? ''],
                    ['num' => '12.', 'label' => 'Remarks', 'value' => $death_remarks ?? ''],
                ];
            @endphp

            @foreach($fields as $field)
                <div style="display: flex; min-height: 7.5mm; align-items: center;">
                    <div style="width: 10mm; padding-right: 2mm;">{{ $field['num'] }}</div>
                    <div style="width: 47mm; padding-right: 2mm;">{{ $field['label'] }}</div>
                    <div style="width: 3mm;">:</div>
                    <div style="flex: 1; padding-left: 2mm; border-bottom: 1px dotted #cccccc;">{{ $field['value'] }}</div>
                </div>
            @endforeach
        </div>

        <!-- Bottom Section -->
        <div style="display: flex; width: 100%; margin-top: 15mm; font-family: 'Times New Roman', Times, serif; font-size: 16px; font-weight: 600;">
            <div style="flex: 1; text-align: center;">
                <div style="margin-bottom: 20mm;">For Authenticity of Extract:</div>
            </div>
            <div style="flex: 1; text-align: right; padding-right: 10mm;">
                <div style="margin-bottom: 20mm;">Date: {{ $issued_date ?? now()->format('jS F Y') }}</div>
                <div style="margin-bottom: 3mm;">{{ $parish_priest_name ?? 'Parish Priest' }}</div>
                <div style="font-size: 14px;">PARISH PRIEST</div>
            </div>
        </div>





    </div>
</body>

</html>
