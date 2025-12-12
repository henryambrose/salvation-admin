<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $documentTitle ?? 'Valid_Members_' . $graveIdentifier }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 0.75in;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 18px;
            color: #1e40af;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .header h2 {
            font-size: 14px;
            color: #64748b;
            font-weight: normal;
        }

        .header p {
            font-size: 10px;
            color: #64748b;
            margin-top: 5px;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e5e7eb;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            font-weight: bold;
            color: #475569;
            padding: 4px 10px 4px 0;
            width: 30%;
        }

        .info-value {
            display: table-cell;
            color: #1e293b;
            padding: 4px 0;
        }

        .members-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .members-table th {
            background-color: #2563eb;
            color: white;
            font-weight: bold;
            padding: 8px 6px;
            text-align: left;
            font-size: 10px;
            border: 1px solid #1e40af;
        }

        .members-table td {
            padding: 6px;
            border: 1px solid #d1d5db;
            font-size: 10px;
        }

        .members-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .members-table tr:hover {
            background-color: #f3f4f6;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-living {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-deceased {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-member {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-external {
            background-color: #fef3c7;
            color: #92400e;
        }

        .summary-stats {
            display: table;
            width: 100%;
            margin-top: 15px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 10px;
        }

        .stat-row {
            display: table-row;
        }

        .stat-label {
            display: table-cell;
            font-weight: bold;
            color: #475569;
            padding: 3px 10px 3px 0;
            width: 40%;
        }

        .stat-value {
            display: table-cell;
            color: #1e293b;
            padding: 3px 0;
            font-weight: bold;
        }

        .signatures {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-container {
            display: table;
            width: 100%;
            margin-top: 20px;
        }

        .signature-box {
            display: table-cell;
            width: 48%;
            border: 1px solid #d1d5db;
            padding: 15px;
            vertical-align: top;
        }

        .signature-box:first-child {
            margin-right: 4%;
        }

        .signature-label {
            font-weight: bold;
            color: #475569;
            margin-bottom: 30px;
            display: block;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 40px;
            padding-top: 5px;
            text-align: center;
            font-size: 9px;
            color: #64748b;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            font-size: 9px;
            color: #64748b;
            text-align: center;
        }

        .no-members {
            text-align: center;
            padding: 30px;
            color: #64748b;
            font-style: italic;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }

        @media print {
            body {
                padding: 0.5in;
            }
        }
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #2563eb;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 14px;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
            z-index: 1000;
        }

        .print-button:hover {
            background: #1d4ed8;
        }

        .no-print {
            display: block;
        }

        @media print {
            body {
                padding: 0.3in;
            }

            .no-print {
                display: none !important;
            }

            @page {
                margin: 0.3in;
                size: A4 portrait;
            }
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">Print PDF</button>
    <script>
        // Auto-open print dialog when page loads
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
    <!-- Header -->
    <div class="header">
        <h1>OUR LADY OF SALVATION CHURCH</h1>
        <h2>Valid Members Document</h2>
        <p>{{ $graveType }} - {{ $graveIdentifier }}</p>
    </div>

    <!-- Grave Information Section -->
    <div class="section">
        <div class="section-title">{{ $graveType }} Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Type:</div>
                <div class="info-value">{{ $graveType }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Identifier:</div>
                <div class="info-value">{{ $graveIdentifier }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Location:</div>
                <div class="info-value">{{ $location }}</div>
            </div>
            @if($oldNumber)
            <div class="info-row">
                <div class="info-label">Old Number:</div>
                <div class="info-value">{{ $oldNumber }}</div>
            </div>
            @endif
            @if($plotSize)
            <div class="info-row">
                <div class="info-label">{{ $graveType === 'Niche' ? 'Dimensions:' : 'Plot Size:' }}</div>
                <div class="info-value">{{ $plotSize }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Registration Date:</div>
                <div class="info-value">{{ $registrationDate }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Owner Name:</div>
                <div class="info-value">{{ $ownerName }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Contact Number:</div>
                <div class="info-value">{{ $contactNo }}</div>
            </div>
        </div>
    </div>

    <!-- Valid Members Section -->
    <div class="section">
        <div class="section-title">Valid Members</div>

        @if($validMembers && $validMembers->count() > 0)
            <table class="members-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 22%;">Name</th>
                        <th style="width: 15%;">Relationship</th>
                        <th style="width: 10%;">Gender</th>
                        <th style="width: 12%;">Date of Birth</th>
                        <th style="width: 12%;">Type</th>
                        <th style="width: 12%;">Status</th>
                        <th style="width: 12%;">Death Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($validMembers as $index => $member)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $member->full_name }}</strong>
                            @if($member->member)
                                <br><span style="font-size: 9px; color: #64748b;">ID: {{ $member->member->member_no }}</span>
                            @endif
                        </td>
                        <td>
                            @if($member->relationship)
                                {{ $member->relationship->name }}
                            @else
                                {{ $member->relationship_value ?? 'N/A' }}
                            @endif
                        </td>
                        <td>
                            @if($member->gender)
                                {{ $member->gender->name }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if($member->date_of_birth)
                                {{ \Carbon\Carbon::parse($member->date_of_birth)->format('d/m/Y') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if($member->member_type === 'member')
                                <span class="badge badge-member">Parish Member</span>
                            @else
                                <span class="badge badge-external">External</span>
                            @endif
                        </td>
                        <td>
                            @if($member->is_deceased)
                                <span class="badge badge-deceased">Deceased</span>
                            @else
                                <span class="badge badge-living">Living</span>
                            @endif
                        </td>
                        <td>
                            @if($member->death_date)
                                {{ \Carbon\Carbon::parse($member->death_date)->format('d/m/Y') }}
                                @if($member->burial_date)
                                    <br><span style="font-size: 9px; color: #64748b;">Burial: {{ \Carbon\Carbon::parse($member->burial_date)->format('d/m/Y') }}</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Summary Statistics -->
            <div class="summary-stats">
                <div class="stat-row">
                    <div class="stat-label">Total Valid Members:</div>
                    <div class="stat-value">{{ $validMembers->count() }}</div>
                </div>
                <div class="stat-row">
                    <div class="stat-label">Parish Members:</div>
                    <div class="stat-value">{{ $validMembers->where('member_type', 'member')->count() }}</div>
                </div>
                <div class="stat-row">
                    <div class="stat-label">External Members:</div>
                    <div class="stat-value">{{ $validMembers->where('member_type', 'external')->count() }}</div>
                </div>
                <div class="stat-row">
                    <div class="stat-label">Living Members:</div>
                    <div class="stat-value">{{ $validMembers->where('is_deceased', false)->count() }}</div>
                </div>
                <div class="stat-row">
                    <div class="stat-label">Deceased Members:</div>
                    <div class="stat-value">{{ $validMembers->where('is_deceased', true)->count() }}</div>
                </div>
            </div>
        @else
            <div class="no-members">
                No valid members are currently associated with this {{ strtolower($graveType) }}.
            </div>
        @endif
    </div>

    <!-- Signatures Section -->
    <div class="signatures">
        <div class="section-title">Verification & Authorization</div>
        <div class="signature-container">
            <div class="signature-box">
                <span class="signature-label">Verified By:</span>
                <div class="signature-line">Signature & Date</div>
            </div>
            <div class="signature-box" style="margin-left: 4%;">
                <span class="signature-label">Authorized By:</span>
                <div class="signature-line">Signature & Date</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Document generated on {{ $generatedDate }} by {{ $generatedBy }}</p>
        <p>This is an official document of Our Lady of Salvation Church</p>
    </div>
</body>
</html>
