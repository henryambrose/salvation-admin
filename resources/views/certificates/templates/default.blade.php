<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucfirst($certificate_type) }} Certificate</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 40px;
            line-height: 1.6;
            color: #333;
            background-color: #fff;
        }

        .certificate {
            max-width: 800px;
            margin: 0 auto;
            border: 3px solid #2c4a99;
            padding: 40px;
            text-align: center;
            position: relative;
            min-height: 600px;
        }

        .certificate::before {
            content: '';
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            bottom: 10px;
            border: 1px solid #2c4a99;
            pointer-events: none;
        }

        .logo {
            margin-bottom: 20px;
        }

        .logo img {
            max-width: 100px;
            max-height: 100px;
            object-fit: contain;
        }

        .header {
            font-size: 28px;
            font-weight: bold;
            color: #2c4a99;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            color: #2c4a99;
            margin: 30px 0;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .content {
            font-size: 16px;
            margin: 20px 0;
            text-align: justify;
            line-height: 1.8;
        }

        .member-name {
            font-size: 20px;
            font-weight: bold;
            text-decoration: underline;
            margin: 10px 0;
            text-transform: uppercase;
        }

        .details {
            margin: 30px 0;
            font-size: 14px;
        }

        .details-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        .details-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px dotted #ccc;
        }

        .details-table td:first-child {
            font-weight: bold;
            width: 200px;
        }

        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            align-items: end;
        }

        .signature-left, .signature-right {
            flex: 1;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 12px;
        }

        .certificate-number {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 10px;
            color: #666;
        }

        .issue-date {
            position: absolute;
            bottom: 15px;
            left: 20px;
            font-size: 10px;
            color: #666;
        }

        @media print {
            body {
                padding: 0;
            }
            .certificate {
                margin: 0;
                border: none;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="certificate-number">
            Certificate No: {{ $certificate_number }}
        </div>

        @if(isset($template_config['show_logo']) && $template_config['show_logo'] && !empty($template_config['logo_url']))
            <div class="logo">
                <img src="{{ $template_config['logo_url'] }}" alt="Church Logo">
            </div>
        @endif

        <div class="header">{{ $parish_name }}</div>

        <div class="title">
            Certificate of {{ ucfirst(str_replace('_', ' ', $certificate_type)) }}
        </div>

        <div class="content">
            This is to certify that
        </div>

        <div class="member-name">
            {{ $member_full_name }}
        </div>

        <div class="content">
            @switch($certificate_type)
                @case('baptism')
                    was baptized in this parish according to the rites of the Roman Catholic Church
                    @if($baptism_date)
                        on <strong>{{ $baptism_date }}</strong>
                    @endif
                    @if($baptism_reg_no)
                        and recorded in the Baptismal Register as Entry No. <strong>{{ $baptism_reg_no }}</strong>.
                    @else
                        .
                    @endif
                    @break

                @case('confirmation')
                    was confirmed in this parish according to the rites of the Roman Catholic Church
                    @if($confirmation_date)
                        on <strong>{{ $confirmation_date }}</strong>
                    @endif
                    @if($confirmation_reg_no)
                        and recorded in the Confirmation Register as Entry No. <strong>{{ $confirmation_reg_no }}</strong>.
                    @else
                        .
                    @endif
                    @break

                @case('marriage')
                    was married to <strong>{{ $spouse_name ?? '[SPOUSE NAME]' }}</strong> in this parish
                    @if($marriage_date)
                        on <strong>{{ $marriage_date }}</strong>
                    @endif
                    @if($marriage_reg_no)
                        and recorded in the Marriage Register as Entry No. <strong>{{ $marriage_reg_no }}</strong>.
                    @else
                        .
                    @endif
                    @break

                @case('membership')
                    is a registered member of this parish
                    @if($join_date)
                        since <strong>{{ $join_date }}</strong>
                    @endif
                    @if($community_name)
                        and is an active member of <strong>{{ $community_name }}</strong> community.
                    @else
                        .
                    @endif
                    @break

                @case('death')
                    passed away
                    @if($death_date)
                        on <strong>{{ $death_date }}</strong>
                    @endif
                    @if($deaths_reg_no)
                        and recorded in the Death Register as Entry No. <strong>{{ $deaths_reg_no }}</strong>.
                    @else
                        .
                    @endif
                    @break

                @default
                    has been registered in our parish records.
            @endswitch
        </div>

        @if($certificate_type === 'baptism' && ($godfather_name || $godmother_name))
            <div class="details">
                <table class="details-table">
                    @if($godfather_name)
                        <tr>
                            <td>Godfather:</td>
                            <td>{{ $godfather_name }}</td>
                        </tr>
                    @endif
                    @if($godmother_name)
                        <tr>
                            <td>Godmother:</td>
                            <td>{{ $godmother_name }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        @endif

        @if($certificate_type === 'confirmation' && ($confirmation_name || $sponsor_name || $bishop_name))
            <div class="details">
                <table class="details-table">
                    @if($confirmation_name)
                        <tr>
                            <td>Confirmation Name:</td>
                            <td>{{ $confirmation_name }}</td>
                        </tr>
                    @endif
                    @if($sponsor_name)
                        <tr>
                            <td>Sponsor:</td>
                            <td>{{ $sponsor_name }}</td>
                        </tr>
                    @endif
                    @if($bishop_name)
                        <tr>
                            <td>Bishop:</td>
                            <td>{{ $bishop_name }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        @endif

        @if($certificate_type === 'marriage' && ($witness1_name || $witness2_name || $marriage_type))
            <div class="details">
                <table class="details-table">
                    @if($marriage_type)
                        <tr>
                            <td>Type of Marriage:</td>
                            <td>{{ $marriage_type }}</td>
                        </tr>
                    @endif
                    @if($witness1_name)
                        <tr>
                            <td>First Witness:</td>
                            <td>{{ $witness1_name }}</td>
                        </tr>
                    @endif
                    @if($witness2_name)
                        <tr>
                            <td>Second Witness:</td>
                            <td>{{ $witness2_name }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        @endif

        @if($certificate_type === 'death' && ($burial_date || $burial_place || $last_rites_given))
            <div class="details">
                <table class="details-table">
                    @if($burial_date)
                        <tr>
                            <td>Burial Date:</td>
                            <td>{{ $burial_date }}</td>
                        </tr>
                    @endif
                    @if($burial_place)
                        <tr>
                            <td>Burial Place:</td>
                            <td>{{ $burial_place }}</td>
                        </tr>
                    @endif
                    @if($last_rites_given)
                        <tr>
                            <td>Last Rites:</td>
                            <td>{{ $last_rites_given }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        @endif

        <div class="details">
            <table class="details-table">
                @if($member_dob)
                    <tr>
                        <td>Date of Birth:</td>
                        <td>{{ $member_dob }}</td>
                    </tr>
                @endif
                @if($member_family_no)
                    <tr>
                        <td>Family Number:</td>
                        <td>{{ $member_family_no }}</td>
                    </tr>
                @endif
                @if($member_member_no)
                    <tr>
                        <td>Member Number:</td>
                        <td>{{ $member_member_no }}</td>
                    </tr>
                @endif
            </table>
        </div>

        <div class="signature-section">
            <div class="signature-left">
                <div class="signature-line">
                    Parish Priest
                </div>
            </div>
            <div class="signature-right">
                <div class="signature-line">
                    Parish Seal
                </div>
            </div>
        </div>

        <div class="issue-date">
            Issued on: {{ $issued_date }}
        </div>
    </div>
</body>
</html>