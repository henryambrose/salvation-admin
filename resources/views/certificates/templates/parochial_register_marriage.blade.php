<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Marriage</title>
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
            width: 30mm;
            padding-top: 2mm;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            position: absolute;
            left: 5mm;
            top: 0;
            z-index: 10;
        }

        .logoframe img {
            height: 20mm;
            width: auto;
            object-fit: contain;
        }

        .headframecontent {
            height: 100%;
            width: 100%;
            padding: 0 11mm 0 38mm;
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
                <div class="cert_nm">CERTIFICATE OF MARRIAGE</div>
                <div class="headframe_bylines">Extracts from the Parochial Register of</div>
                <div class="headframe_parish">{{ strtoupper($parish_name) }}
                </div>
                <div class="headframe_bylines">{{ $parish_address ?? 'Dadar (W), Mumbai - 400 028' }}</div>
                <div style="width: 140.5mm; font-style: italic; font-bold; padding-left: 2mm; margin-top: 5mm;">
                    Marriage No {{ $marriage_reg_no_short ?? ($marriage_reg_no ? (int) filter_var($marriage_reg_no, FILTER_SANITIZE_NUMBER_INT) : '--') }} of the Year {{ $marriage_year ?? '--' }}
                </div>
            </div>
        </div>

        <!-- Body -->
        <div style="width: 100%; padding-left: 10mm; font-size: 11pt; font-weight: 600; font-family: Arial, Helvetica, sans-serif;">
            @php
                // Determine remark value based on pdf_options
                $remarkValue = '---';
                if (isset($pdf_options['include_remark']) && $pdf_options['include_remark']) {
                    $remarkValue = $marriage_remarks ?? '';
                }

                $fields = [
                    ['num' => '1.', 'label' => 'Date of Marriage', 'value' => $marriage_date ?? ''],
                    ['num' => '2.', 'label' => "Bridegroom's Name", 'value' => $bridegroom_name ?? ''],
                    ['num' => '3.', 'label' => 'Surname', 'value' => $bridegroom_surname ?? ''],
                    ['num' => '4.', 'label' => 'Date of Birth', 'value' => $bridegroom_dob ?? ''],
                    ['num' => '5.', 'label' => 'Nationality', 'value' => $bridegroom_nationality ?? ''],
                    ['num' => '6.', 'label' => 'Profession', 'value' => $bridegroom_profession ?? ''],
                    ['num' => '7.', 'label' => 'Residence', 'value' => $bridegroom_residence ?? ''],
                    ['num' => '8.', 'label' => "Father's Name", 'value' => $bridegroom_father_name ?? ''],
                    ['num' => '9.', 'label' => "Mother's Name", 'value' => $bridegroom_mother_name ?? ''],
                    ['num' => '10.', 'label' => 'Bachelor or Widower', 'value' => $bridegroom_status ?? ''],
                    ['num' => '11.', 'label' => 'If Widower, Whose', 'value' => $bridegroom_if_widower_whose ?? ''],
                    ['num' => '12.', 'label' => "Bride's Name", 'value' => $bride_name ?? ''],
                    ['num' => '13.', 'label' => 'Surname', 'value' => $bride_surname ?? ''],
                    ['num' => '14.', 'label' => 'Date of Birth', 'value' => $bride_dob ?? ''],
                    ['num' => '15.', 'label' => 'Nationality', 'value' => $bride_nationality ?? ''],
                    ['num' => '16.', 'label' => 'Profession', 'value' => $bride_profession ?? ''],
                    ['num' => '17.', 'label' => 'Residence', 'value' => $bride_residence ?? ''],
                    ['num' => '18.', 'label' => "Father's Name", 'value' => $bride_father_name ?? ''],
                    ['num' => '19.', 'label' => "Mother's Name", 'value' => $bride_mother_name ?? ''],
                    ['num' => '20.', 'label' => 'Spinster or Widow', 'value' => $bride_status ?? ''],
                    ['num' => '21.', 'label' => 'If Widow, Whose', 'value' => $bride_if_widow_whose ?? ''],
                    ['num' => '22.', 'label' => "First Witness' Name", 'value' => $first_witness_name ?? ''],
                    ['num' => '23.', 'label' => 'Residence', 'value' => $first_witness_residence ?? ''],
                    ['num' => '24.', 'label' => "Second Witness' Name", 'value' => $second_witness_name ?? ''],
                    ['num' => '25.', 'label' => 'Residence', 'value' => $second_witness_residence ?? ''],
                    ['num' => '26.', 'label' => 'Minister', 'value' => $minister_name ?? ''],
                    ['num' => '27.', 'label' => 'Remarks', 'value' => $remarkValue],
                ];
            @endphp

            @foreach($fields as $field)
                <div style="display: flex; min-height: 6mm; align-items: center;">
                    <div style="width: 10mm; padding-right: 2mm;">{{ $field['num'] }}</div>
                    <div style="width: 47mm; padding-right: 2mm;">{{ $field['label'] }}</div>
                    <div style="width: 3mm;">:</div>
                    <div style="flex: 1; padding-left: 2mm; border-bottom: 1px dotted #cccccc;">{{ $field['value'] }}</div>
                </div>
            @endforeach
        </div>

        <!-- Bottom Section -->
        @php
            // Get PDF options with defaults
            $printDate = $pdf_options['print_date'] ?? now()->format('d/m/Y');
            $signByLabel = $pdf_options['sign_by_label'] ?? 'Parish Priest';
            $signeeName = $pdf_options['signee_name'] ?? ($parish_priest_name ?? '');
        @endphp
        <div style="display: flex; width: 100%; margin-top: 15mm; font-family: 'Times New Roman', Times, serif; font-size: 16px; font-weight: 600;">
            <div style="flex: 1; text-align: center;">
                <div style="margin-bottom: 20mm;">For Authenticity of Extract:</div>
            </div>
            <div style="flex: 1; text-align: center;">
                <div style="margin-bottom: 20mm;">Date: {{ $printDate }}</div>
                <div style="font-size: 14px; margin-bottom: 3mm;">{{ strtoupper($signByLabel) }}</div>
                @if($signeeName)
                    <div>{{ $signeeName }}</div>
                @endif
            </div>
        </div>





    </div>
</body>

</html>
