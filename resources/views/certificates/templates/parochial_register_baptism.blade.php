<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Baptism</title>
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
            /* border: 2px solid #000; */
            padding: 12px; /* keep all sides equal */
            position: relative;
            box-sizing: border-box;
        }

        .certificate::before {
            content: '';
            position: absolute;
            top: 4px;
            left: 4px;
            right: 4px;
            /* remove bottom: 4px; */
            height: calc(100% - 8px); /* 4px top + 4px bottom */
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

        .header {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .parish-address {
            font-size: 12px;
            margin-bottom: 6px;
        }

        .title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 6px 0;
            letter-spacing: 0.8px;
        }

        .subtitle {
            text-align: center;
            font-size: 12px;
            margin-bottom: 4px;
            line-height: 1.8;
        }

        .baptism-number {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 30px;
        }

        .details-list {
            margin: 10px 0;
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

        .detail-value.multi-line {
            border-bottom: none;
        }

        .confirmation-marriage {
            margin-left: 45px;
            font-size: 11px;
            margin-top: -2px;
            padding-bottom: 3px;
            border-bottom: 1px dotted #000;
            line-height: 1.5;
        }

        .footer {
            margin-top: 50px;
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
            margin-top: 60px;
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
                padding: 15px; /* equal in print too */
                /* border: 2px solid #000; */
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
                    <div class="title">Certificate of Baptism</div>
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
                <td class="header-content" @if(isset($template_config['show_logo']) && $template_config['show_logo'] && !empty($template_config['logo_url'])) colspan="1" @endif>
                    <div class="baptism-number">
                         Baptism No {{ $baptism_reg_no_short ?? ($baptism_reg_no ? (int)filter_var($baptism_reg_no, FILTER_SANITIZE_NUMBER_INT) : '--') }} of the Year {{ $baptism_year ?? '--' }}
                    </div>
                </td>
            </tr>
        </table>
        <div class="details-list">
            <!-- 1. Date Baptism -->
            <div class="detail-row">
                <span class="detail-number">1.</span>
                <span class="detail-label">Date of Baptism</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $baptism_date ?? '' }}</span>
            </div>

            <!-- 2. Date of Birth -->
            <div class="detail-row">
                <span class="detail-number">2.</span>
                <span class="detail-label">Date of Birth</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $member_dob ?? '' }}</span>
            </div>

            <!-- 3. Place of Birth -->
            <div class="detail-row">
                <span class="detail-number">3.</span>
                <span class="detail-label">Place of Birth</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $place_of_birth ?? '' }}</span>
            </div>

            <!-- 4. Name -->
            <div class="detail-row">
                <span class="detail-number">4.</span>
                <span class="detail-label">Name</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $member_first_name ?? '' }}</span>
            </div>

            <!-- 5. Surname -->
            <div class="detail-row">
                <span class="detail-number">5.</span>
                <span class="detail-label">Surname</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $member_last_name ?? '' }}</span>
            </div>

            <!-- 6. Father's Name -->
            <div class="detail-row">
                <span class="detail-number">6.</span>
                <span class="detail-label">Father's Name</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $father_name ?? '' }}</span>
            </div>

            <!-- 7. Mother's Name -->
            <div class="detail-row">
                <span class="detail-number">7.</span>
                <span class="detail-label">Mother's Name</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $mother_name ?? '' }}</span>
            </div>

            <!-- 8. Father's Residence -->
            <div class="detail-row">
                <span class="detail-number">8.</span>
                <span class="detail-label">Father's Residence</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $father_residence ?? '' }}</span>
            </div>

            <!-- 9. Father's Profession -->
            <div class="detail-row">
                <span class="detail-number">9.</span>
                <span class="detail-label">Father's Profession</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $father_profession ?? '' }}</span>
            </div>

            <!-- 10. Nationality -->
            <div class="detail-row">
                <span class="detail-number">10.</span>
                <span class="detail-label">Nationality</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $nationality ?? '' }}</span>
            </div>

            <!-- 11. Godfather's Name -->
            <div class="detail-row">
                <span class="detail-number">11.</span>
                <span class="detail-label">Godfather's Name</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $godfather_name ?? '' }}</span>
            </div>

            <!-- 12. Godfather's Resi. -->
            <div class="detail-row">
                <span class="detail-number">12.</span>
                <span class="detail-label">Godfather's Resi.</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $godfather_residence ?? '' }}</span>
            </div>

            <!-- 13. Godmother's Name -->
            <div class="detail-row">
                <span class="detail-number">13.</span>
                <span class="detail-label">Godmother's Name</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $godmother_name ?? '' }}</span>
            </div>

            <!-- 14. Godmother's Resi -->
            <div class="detail-row">
                <span class="detail-number">14.</span>
                <span class="detail-label">Godmother's Resi</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $godmother_residence ?? '' }}</span>
            </div>

            <!-- 15. Place of Baptism -->
            <div class="detail-row">
                <span class="detail-number">15.</span>
                <span class="detail-label">Place of Baptism</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $place_of_baptism ?? $baptism_parish ?? '' }}</span>
            </div>

            <!-- 16. Minister -->
            <div class="detail-row">
                <span class="detail-number">16.</span>
                <span class="detail-label">Minister</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $minister_name ?? '' }}</span>
            </div>

            <!-- 17. Confirmation -->
            <div class="detail-row">
                <span class="detail-number">17.</span>
                <span class="detail-label">Confirmation</span>
                <span class="detail-colon">:</span>
                <span class="detail-value {{ $confirmation_info ? 'multi-line' : '' }}">
                    @if($confirmation_info)
                        On: {{ $confirmation_info['date'] ?? '' }}
                    @endif
                </span>
            </div>
            @if($confirmation_info)
                <div class="confirmation-marriage">
                    At: {{ $confirmation_info['place'] ?? '' }}
                </div>
            @endif

            <!-- 18. Marriage -->
            <div class="detail-row">
                <span class="detail-number">18.</span>
                <span class="detail-label">Marriage</span>
                <span class="detail-colon">:</span>
                <span class="detail-value {{ $marriage_info ? 'multi-line' : '' }}">
                    @if($marriage_info)
                        On: {{ $marriage_info['date'] ?? '' }}
                        <br>At: {{ $marriage_info['place'] ?? '' }}
                        <br>To: {{ $marriage_info['spouse'] }}
                    @endif
                </span>
            </div>
            <!-- @if($marriage_info)
                <div class="confirmation-marriage">
                    At: {{ $marriage_info['place'] ?? '' }}
                    @if(!empty($marriage_info['spouse']))
                        <br>To: {{ $marriage_info['spouse'] }}
                    @endif
                </div>
            @endif -->

            <!-- 19. Remarks -->
            <div class="detail-row">
                <span class="detail-number">19.</span>
                <span class="detail-label">Remarks</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $baptism_remarks ?? '' }}</span>
            </div>
        </div>

        <div class="footer">
            <div class="authenticity">For Authenticity of Extract:</div>
            <div class="date-issued">Date: {{ $issued_date }}</div>

            <div class="signatures">
                <div class="signature-left">
                    <div class="signature-label">SEAL</div>
                </div>
                <div class="signature-right">
                    <div class="signature-label">For PARISH PRIEST</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
