<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Details - {{ $full_name }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        @media print {
            @page {
                margin: 10mm;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 10mm;
        }

        .header {
            display: flex;
            gap: 30px;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            align-items: center;
            justify-content: center;
        }

        .logo-box {
            flex-shrink: 0;
            width: 120px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo {
            width: 100px;
            height: 100px;
        }

        .header-content {
            text-align: center;
        }

        .parish-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .parish-address {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }

        .document-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
            text-decoration: underline;
        }

        .section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 5px 10px;
            border-left: 4px solid #333;
            margin-bottom: 10px;
        }

        .field-row {
            display: flex;
            margin-bottom: 8px;
            border-bottom: 1px dotted #ccc;
            padding-bottom: 5px;
        }

        .field-label {
            font-weight: bold;
            width: 180px;
            flex-shrink: 0;
        }

        .field-value {
            flex: 1;
            color: #333;
        }

        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .notes-section {
            background-color: #f9f9f9;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            white-space: pre-wrap;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #333;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .print-button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Print</button>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <!-- Logo Box -->
            <div class="logo-box">
                @if($template_config['show_logo'] && $template_config['logo_url'])
                <img src="{{ $template_config['logo_url'] }}" alt="Parish Logo" class="logo">
                @endif
            </div>

            <!-- Header Content -->
            <div class="header-content">
                <div class="parish-name">{{ $parish_name }}</div>
                <div class="parish-address">{{ $parish_address }}</div>
                <div class="document-title">MEMBER DETAILS</div>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="section">
            <div class="section-title">Basic Information</div>
            <div class="field-row">
                <div class="field-label">Member Number:</div>
                <div class="field-value">{{ $member_no }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Family Number:</div>
                <div class="field-value">{{ $family_no }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Full Name:</div>
                <div class="field-value">{{ $full_name }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Date of Birth:</div>
                <div class="field-value">{{ $date_of_birth }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Gender:</div>
                <div class="field-value">{{ $gender }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Marital Status:</div>
                <div class="field-value">{{ $marital_status }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Blood Group:</div>
                <div class="field-value">{{ $blood_group }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Relationship:</div>
                <div class="field-value">{{ $relationship }}</div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="section">
            <div class="section-title">Contact Information</div>
            <div class="field-row">
                <div class="field-label">Contact Number 1:</div>
                <div class="field-value">{{ $contact_no_1 }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Contact Number 2:</div>
                <div class="field-value">{{ $contact_no_2 }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Email:</div>
                <div class="field-value">{{ $email }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Aadhar:</div>
                <div class="field-value">{{ $aadhar }}</div>
            </div>
        </div>

        <!-- Addresses -->
        <div class="two-column">
            <!-- Permanent Address -->
            <div class="section">
                <div class="section-title">Permanent Address</div>
                @if($permanent_add1 || $permanent_add2 || $permanent_add3)
                <div class="field-row">
                    <div class="field-label">Address:</div>
                    <div class="field-value">
                        {{ $permanent_add1 }}<br>
                        {{ $permanent_add2 }}<br>
                        {{ $permanent_add3 }}
                    </div>
                </div>
                @endif
                @if($permanent_town)
                <div class="field-row">
                    <div class="field-label">Town:</div>
                    <div class="field-value">{{ $permanent_town }}</div>
                </div>
                @endif
                @if($permanent_city)
                <div class="field-row">
                    <div class="field-label">City:</div>
                    <div class="field-value">{{ $permanent_city }}</div>
                </div>
                @endif
                @if($permanent_state)
                <div class="field-row">
                    <div class="field-label">State:</div>
                    <div class="field-value">{{ $permanent_state }}</div>
                </div>
                @endif
                @if($permanent_country)
                <div class="field-row">
                    <div class="field-label">Country:</div>
                    <div class="field-value">{{ $permanent_country }}</div>
                </div>
                @endif
                @if($permanent_pincode)
                <div class="field-row">
                    <div class="field-label">Pincode:</div>
                    <div class="field-value">{{ $permanent_pincode }}</div>
                </div>
                @endif
            </div>

            <!-- Current Address -->
            <div class="section">
                <div class="section-title">Current Address</div>
                @if($current_add1 || $current_add2 || $current_add3)
                <div class="field-row">
                    <div class="field-label">Address:</div>
                    <div class="field-value">
                        {{ $current_add1 }}<br>
                        {{ $current_add2 }}<br>
                        {{ $current_add3 }}
                    </div>
                </div>
                @endif
                @if($current_town)
                <div class="field-row">
                    <div class="field-label">Town:</div>
                    <div class="field-value">{{ $current_town }}</div>
                </div>
                @endif
                @if($current_city)
                <div class="field-row">
                    <div class="field-label">City:</div>
                    <div class="field-value">{{ $current_city }}</div>
                </div>
                @endif
                @if($current_state)
                <div class="field-row">
                    <div class="field-label">State:</div>
                    <div class="field-value">{{ $current_state }}</div>
                </div>
                @endif
                @if($current_country)
                <div class="field-row">
                    <div class="field-label">Country:</div>
                    <div class="field-value">{{ $current_country }}</div>
                </div>
                @endif
                @if($current_pincode)
                <div class="field-row">
                    <div class="field-label">Pincode:</div>
                    <div class="field-value">{{ $current_pincode }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Community Information -->
        <div class="section">
            <div class="section-title">Community Information</div>
            <div class="field-row">
                <div class="field-label">Community:</div>
                <div class="field-value">{{ $community }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Community Cluster:</div>
                <div class="field-value">{{ $community_cluster }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Parish:</div>
                <div class="field-value">{{ $parish }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Status:</div>
                <div class="field-value">{{ $status }}</div>
            </div>
        </div>

        <!-- Education and Professional Information -->
        @if($school_name || $college_name || $latest_qualifications || $company_name || $designation || $income_range)
        <div class="section">
            <div class="section-title">Education & Professional Information</div>
            @if($school_name)
            <div class="field-row">
                <div class="field-label">School:</div>
                <div class="field-value">{{ $school_name }}</div>
            </div>
            @endif
            @if($college_name)
            <div class="field-row">
                <div class="field-label">College:</div>
                <div class="field-value">{{ $college_name }}</div>
            </div>
            @endif
            @if($latest_qualifications)
            <div class="field-row">
                <div class="field-label">Latest Qualifications:</div>
                <div class="field-value">{{ $latest_qualifications }}</div>
            </div>
            @endif
            @if($company_name)
            <div class="field-row">
                <div class="field-label">Company:</div>
                <div class="field-value">{{ $company_name }}</div>
            </div>
            @endif
            @if($designation)
            <div class="field-row">
                <div class="field-label">Designation:</div>
                <div class="field-value">{{ $designation }}</div>
            </div>
            @endif
            @if($income_range)
            <div class="field-row">
                <div class="field-label">Income Range:</div>
                <div class="field-value">{{ $income_range }}</div>
            </div>
            @endif
        </div>
        @endif

        <!-- Family Relations -->
        @if($father_name || $mother_name || $spouse_name)
        <div class="section">
            <div class="section-title">Family Relations</div>
            @if($father_name)
            <div class="field-row">
                <div class="field-label">Father:</div>
                <div class="field-value">{{ $father_name }}</div>
            </div>
            @endif
            @if($mother_name)
            <div class="field-row">
                <div class="field-label">Mother:</div>
                <div class="field-value">{{ $mother_name }}</div>
            </div>
            @endif
            @if($spouse_name)
            <div class="field-row">
                <div class="field-label">Spouse:</div>
                <div class="field-value">{{ $spouse_name }}</div>
            </div>
            @endif
        </div>
        @endif

        <!-- Member Associations -->
        @if(count($cells_and_associations) > 0)
        <div class="section">
            <div class="section-title">Member Associations</div>
            <div class="field-row">
                <div class="field-label">Associations:</div>
                <div class="field-value">{{ implode(', ', $cells_and_associations) }}</div>
            </div>
        </div>
        @endif

        <!-- Sacramental Information -->
        @if($baptism_date || $confirmation_date || $marriage_date || $death_date)
        <div class="section">
            <div class="section-title">Sacramental Information</div>

            @if($baptism_date)
            <div class="field-row">
                <div class="field-label">Baptism Date:</div>
                <div class="field-value">{{ $baptism_date }}</div>
            </div>
            @endif
            @if($baptism_reg_no)
            <div class="field-row">
                <div class="field-label">Baptism Reg. No:</div>
                <div class="field-value">{{ $baptism_reg_no }}</div>
            </div>
            @endif
            @if($baptism_parish)
            <div class="field-row">
                <div class="field-label">Baptism Parish:</div>
                <div class="field-value">{{ $baptism_parish }}</div>
            </div>
            @endif

            @if($confirmation_date)
            <div class="field-row">
                <div class="field-label">Confirmation Date:</div>
                <div class="field-value">{{ $confirmation_date }}</div>
            </div>
            @endif
            @if($confirmation_reg_no)
            <div class="field-row">
                <div class="field-label">Confirmation Reg. No:</div>
                <div class="field-value">{{ $confirmation_reg_no }}</div>
            </div>
            @endif
            @if($confirmation_parish)
            <div class="field-row">
                <div class="field-label">Confirmation Parish:</div>
                <div class="field-value">{{ $confirmation_parish }}</div>
            </div>
            @endif

            @if($marriage_date)
            <div class="field-row">
                <div class="field-label">Marriage Date:</div>
                <div class="field-value">{{ $marriage_date }}</div>
            </div>
            @endif
            @if($marriage_reg_no)
            <div class="field-row">
                <div class="field-label">Marriage Reg. No:</div>
                <div class="field-value">{{ $marriage_reg_no }}</div>
            </div>
            @endif
            @if($marriage_parish)
            <div class="field-row">
                <div class="field-label">Marriage Parish:</div>
                <div class="field-value">{{ $marriage_parish }}</div>
            </div>
            @endif

            @if($death_date)
            <div class="field-row">
                <div class="field-label">Death Date:</div>
                <div class="field-value">{{ $death_date }}</div>
            </div>
            @endif
            @if($death_reg_no)
            <div class="field-row">
                <div class="field-label">Death Reg. No:</div>
                <div class="field-value">{{ $death_reg_no }}</div>
            </div>
            @endif
            @if($death_parish)
            <div class="field-row">
                <div class="field-label">Death Parish:</div>
                <div class="field-value">{{ $death_parish }}</div>
            </div>
            @endif
        </div>
        @endif

        <!-- Notes -->
        @if($notes)
        <div class="section">
            <div class="section-title">Additional Notes</div>
            <div class="notes-section">{{ $notes }}</div>
        </div>
        @endif

        <!-- Footer -->
        <!-- <div class="footer">
            <p>Document generated on {{ $issued_date }}</p>
            <p>{{ $parish_name }} - {{ $parish_address }}</p>
            <p style="margin-top: 10px; font-weight: bold;">{{ $parish_priest_name ?? 'Parish Priest' }}</p>
            <p style="font-size: 9px;">Parish Priest</p>
        </div> -->
    </div>
</body>

</html>
