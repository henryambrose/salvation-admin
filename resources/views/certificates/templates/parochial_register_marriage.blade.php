<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Marriage</title>
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

        .marriage-number {
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

        .detail-value.multi-line {
            border-bottom: none;
        }

        .detail-number-sub {
            margin-left: 45px;
            font-size: 11px;
            margin-top: -3px;
            padding-bottom: 3px;
        }

        .detail-number-sub .detail-value {
            border-bottom: 1px dotted #000;
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
                    <div class="title">Certificate of Marriage</div>
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
                    <div class="marriage-number">
                        Marriage No {{ $marriage_reg_no_short ?? ($marriage_reg_no ? (int)filter_var($marriage_reg_no, FILTER_SANITIZE_NUMBER_INT) : '--') }} of the Year {{ $marriage_year ?? '--' }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="details-list">
            <!-- 1. Date of Marriage -->
            <div class="detail-row">
                <span class="detail-number">1.</span>
                <span class="detail-label">Date of Marriage</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $marriage_date ?? '' }}</span>
            </div>

            <!-- 2. Bridegroom's Name -->
            <div class="detail-row">
                <span class="detail-number">2.</span>
                <span class="detail-label">Bridegroom's Name</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $bridegroom_name ?? '' }}</span>
            </div>

            <!-- 3. Surname -->
            <div class="detail-row">
                <span class="detail-number">3.</span>
                <span class="detail-label">Surname</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $bridegroom_surname ?? '' }}</span>
            </div>

            <!-- 4. Date of Birth -->
            <div class="detail-row">
                <span class="detail-number">4.</span>
                <span class="detail-label">Date of Birth</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $bridegroom_dob ?? '' }}</span>
            </div>

            <!-- 5. Nationality -->
            <div class="detail-row">
                <span class="detail-number">5.</span>
                <span class="detail-label">Nationality</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $bridegroom_nationality ?? '' }}</span>
            </div>

            <!-- 5 sub. Profession -->
                <div class="detail-row">
                    <span class="detail-number">6.</span>
                    <span class="detail-label">Profession</span>
                    <span class="detail-colon">:</span>
                    <span class="detail-value">{{ $bridegroom_profession ?? '' }}</span>
                </div>

            <!-- 6. Residence -->
            <div class="detail-row">
                <span class="detail-number">7.</span>
                <span class="detail-label">Residence</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $bridegroom_residence ?? '' }}</span>
            </div>

            <!-- 8. Father's Name -->
            <div class="detail-row">
                <span class="detail-number">8.</span>
                <span class="detail-label">Father's Name</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $bridegroom_father_name ?? '' }}</span>
            </div>

            <!-- 9. Mother's Name -->
            <div class="detail-row">
                <span class="detail-number">9.</span>
                <span class="detail-label">Mother's Name</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $bridegroom_mother_name ?? '' }}</span>
            </div>

            <!-- 10. Bachelor or Widower -->
            <div class="detail-row">
                <span class="detail-number">10.</span>
                <span class="detail-label">Bachelor or Widower</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $bridegroom_status ?? '' }}</span>
            </div>

            <!-- 11. If Widower, Whose -->
            <div class="detail-row">
                <span class="detail-number">11.</span>
                <span class="detail-label">If Widower, Whose</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $bridegroom_if_widower_whose ?? '' }}</span>
            </div>

            <!-- 12. Bride's Name -->
            <div class="detail-row">
                <span class="detail-number">12.</span>
                <span class="detail-label">Bride's Name</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $bride_name ?? '' }}</span>
            </div>

            <!-- 13. Surname -->
            <div class="detail-row">
                <span class="detail-number">13.</span>
                <span class="detail-label">Surname</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $bride_surname ?? '' }}</span>
            </div>

            <!-- 14. Date of Birth -->
            <div class="detail-row">
                <span class="detail-number">14.</span>
                <span class="detail-label">Date of Birth</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $bride_dob ?? '' }}</span>
            </div>

            <!-- 15. Nationality -->
            <div class="detail-row">
                <span class="detail-number">15.</span>
                <span class="detail-label">Nationality</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $bride_nationality ?? '' }}</span>
            </div>

            <!-- 15 sub. Profession -->
            <div class="detail-row">
                <span class="detail-number">16.</span>
                <span class="detail-label">Profession</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $bride_profession ?? '' }}</span>
            </div>

            <!-- 17. Residence -->
            <div class="detail-row">
                <span class="detail-number">17.</span>
                <span class="detail-label">Residence</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $bride_residence ?? '' }}</span>
            </div>

            <!-- 18. Father's Name -->
            <div class="detail-row">
                <span class="detail-number">18.</span>
                <span class="detail-label">Father's Name</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $bride_father_name ?? '' }}</span>
            </div>

            <!-- 19. Mother's Name -->
            <div class="detail-row">
                <span class="detail-number">19.</span>
                <span class="detail-label">Mother's Name</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $bride_mother_name ?? '' }}</span>
            </div>

            <!-- 20. Spinster or Widow -->
            <div class="detail-row">
                <span class="detail-number">20.</span>
                <span class="detail-label">Spinster or Widow</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $bride_status ?? '' }}</span>
            </div>

            <!-- 21. If Widow, Whose -->
            <div class="detail-row">
                <span class="detail-number">21.</span>
                <span class="detail-label">If Widow, Whose</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $bride_if_widow_whose ?? '' }}</span>
            </div>

            <!-- 22. First Witness' Name -->
            <div class="detail-row">
                <span class="detail-number">22.</span>
                <span class="detail-label">First Witness' Name</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $first_witness_name ?? '' }}</span>
            </div>

            <!-- 23. Residence -->
            <div class="detail-row">
                <span class="detail-number">23.</span>
                <span class="detail-label">Residence</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $first_witness_residence ?? '' }}</span>
            </div>

            <!-- 24. Second Witness' Name -->
            <div class="detail-row">
                <span class="detail-number">24.</span>
                <span class="detail-label">Second Witness' Name</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $second_witness_name ?? '' }}</span>
            </div>

            <!-- 25. Residence -->
            <div class="detail-row">
                <span class="detail-number">25.</span>
                <span class="detail-label">Residence</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $second_witness_residence ?? '' }}</span>
            </div>

            <!-- 26. Minister -->
            <div class="detail-row">
                <span class="detail-number">26.</span>
                <span class="detail-label">Minister</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $minister_name ?? '' }}</span>
            </div>

            <!-- 27. Remarks -->
            <div class="detail-row">
                <span class="detail-number">27.</span>
                <span class="detail-label">Remarks</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $marriage_remarks ?? '' }}</span>
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
