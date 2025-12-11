<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Burial</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 0;
            color: #000;
            background-color: #fff;
        }

        .certificate {
            max-width: 100%;
            margin: 0;
            padding: 12px;
            position: relative;
            box-sizing: border-box;
        }

        .certificate::before {
            content: '';
            position: absolute;
            top: 4px;
            left: 4px;
            right: 4px;
            height: calc(100% - 8px);
            border: 1px solid #000;
            pointer-events: none;
            box-sizing: border-box;
        }

        .header-table {
            width: 100%;
            margin-bottom: 5px;
            border-collapse: collapse;
        }

        .logo-cell {
            width: 70px;
            vertical-align: top;
            padding-right: 10px;
        }

        .logo-cell img {
            max-width: 60px;
            max-height: 60px;
            object-fit: contain;
        }

        .header-content {
            vertical-align: top;
            text-align: center;
        }

        .title {
            text-align: center;
            font-size: 19px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 0.8px;
        }

        .subtitle {
            text-align: center;
            font-size: 11px;
            margin: 2px 0;
            line-height: 1.6;
        }

        .burial-number {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin-top: 4px;
            margin-bottom: 20px;
        }

        .details-list {
            font-size: 12px;
            line-height: 1.6;
        }

        .detail-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .detail-number {
            display: table-cell;
            width: 35px;
            vertical-align: top;
            font-weight: normal;
        }

        .detail-label {
            display: table-cell;
            width: 160px;
            vertical-align: top;
            font-weight: normal;
        }

        .detail-colon {
            display: table-cell;
            width: 10px;
            vertical-align: top;
        }

        .detail-value {
            display: table-cell;
            vertical-align: top;
            border-bottom: 1px dotted #000;
            min-height: 18px;
            padding-left: 5px;
        }

        .footer {
            margin-top: 30px;
            font-size: 11px;
        }

        .authenticity {
            text-align: left;
            margin-bottom: 12px;
        }

        .date-issued {
            text-align: right;
            font-weight: bold;
        }

        .signatures {
            display: table;
            width: 100%;
            margin-top: 40px;
        }

        .signature-left, .signature-right {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }

        .signature-label {
            font-size: 10px;
            text-transform: uppercase;
            margin-top: 40px;
        }

        @page {
            size: A4;
            margin: 15mm 15mm;
        }

        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .certificate {
                margin: 0;
                padding: 15px;
                page-break-after: avoid;
                page-break-inside: avoid;
                min-height: 260mm;
            }

            .certificate::before {
                top: 4px;
                left: 4px;
                right: 4px;
                height: calc(100% - 8px);
            }
        }
    </style>
</head>
<body>
    <div class="certificate">
        <table class="header-table">
            <tr>
                @if(isset($template_config['show_logo']) && $template_config['show_logo'] && !empty($template_config['logo_url']))
                    <td class="logo-cell" rowspan="5">
                        <img src="{{ $template_config['logo_url'] }}" alt="Church Logo">
                    </td>
                @endif
                <td class="header-content">
                    <div class="title">Certificate of Burial</div>
                </td>
            </tr>
            <tr>
                <td class="header-content">
                    <div class="subtitle">Extracts from the Parochial Register of</div>
                </td>
            </tr>
            <tr>
                <td class="header-content">
                    <div class="subtitle" style="font-weight: bold;">{{ strtoupper($parish_name) }}</div>
                </td>
            </tr>
            <tr>
                <td class="header-content">
                    <div class="subtitle" style="font-size: 12px; margin-bottom: 0px;">{{ $parish_address ?? 'Dadar (W), Mumbai - 400 028' }}</div>
                </td>
            </tr>
            <tr>
                <td class="header-content">
                    <div class="burial-number">
                        Burial No {{ $burial_reg_no_short ?? ($burial_reg_no ? (int)filter_var($burial_reg_no, FILTER_SANITIZE_NUMBER_INT) : '--') }} of the Year {{ $burial_year ?? '--' }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="details-list">
            <!-- 1. Date of Death -->
            <div class="detail-row">
                <span class="detail-number">1.</span>
                <span class="detail-label">Date of Death</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $death_date ?? '' }}</span>
            </div>

            <!-- 2. Date of Burial -->
            <div class="detail-row">
                <span class="detail-number">2.</span>
                <span class="detail-label">Date of Burial</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $burial_date ?? '' }}</span>
            </div>

            <!-- 3. Name -->
            <div class="detail-row">
                <span class="detail-number">3.</span>
                <span class="detail-label">Name</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $deceased_name ?? '' }}</span>
            </div>

            <!-- 4. Surname -->
            <div class="detail-row">
                <span class="detail-number">4.</span>
                <span class="detail-label">Surname</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $deceased_surname ?? '' }}</span>
            </div>

            <!-- 5. Relationship -->
            <div class="detail-row">
                <span class="detail-number">5.</span>
                <span class="detail-label">Relationship</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $relationship ?? '' }}</span>
            </div>

            <!-- 6. Residence -->
            <div class="detail-row">
                <span class="detail-number">6.</span>
                <span class="detail-label">Residence</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $residence ?? '' }}</span>
            </div>

            <!-- 7. Age -->
            <div class="detail-row">
                <span class="detail-number">7.</span>
                <span class="detail-label">Age</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $age ? $age . ' Years' : '' }}</span>
            </div>

            <!-- 8. Nationality -->
            <div class="detail-row">
                <span class="detail-number">8.</span>
                <span class="detail-label">Nationality</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $nationality ?? '' }}</span>
            </div>

            <!-- 9. Cause of Death -->
            <div class="detail-row">
                <span class="detail-number">9.</span>
                <span class="detail-label">Cause of Death</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $cause_of_death ?? '' }}</span>
            </div>

            <!-- 10. Place of Burial -->
            <div class="detail-row">
                <span class="detail-number">10.</span>
                <span class="detail-label">Place of Burial</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $place_of_burial ?? '' }}</span>
            </div>

            <!-- 11. Minister -->
            <div class="detail-row">
                <span class="detail-number">11.</span>
                <span class="detail-label">Minister</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $minister_name ?? '' }}</span>
            </div>

            <!-- 12. Remarks -->
            <div class="detail-row">
                <span class="detail-number">12.</span>
                <span class="detail-label">Remarks</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $death_remarks ?? '' }}</span>
            </div>
        </div>

        <div class="footer">
            <div class="authenticity">For Authenticity of Extract:</div>
            <div class="date-issued">Dated: {{ $issued_date }}</div>

            <div class="signatures">
                <div class="signature-left">
                    <div class="signature-label">SEAL</div>
                </div>
                <div class="signature-right">
                    <div class="signature-label">For Parish Priest</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
