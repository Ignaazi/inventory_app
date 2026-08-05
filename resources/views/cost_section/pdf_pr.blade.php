<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Purchase Request - {{ optional($pr)->no_pr ?? 'PR' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 8mm; /* Margin Kertas Presisi & Simetris */
        }
        
        /* Reset & Tipografi Global - Teks Abu-abu Gelap (#334155) */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #334155;
            line-height: 1.2;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* MASTER WRAPPER (Solusi Posisi Tengah Presisi DomPDF) */
        .master-table {
            width: 100%;
            border-collapse: collapse;
        }

        .master-border-td {
            border: 2px solid #94a3b8;
            padding: 16px;
            vertical-align: top;
        }

        /* Header Dokumen */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #94a3b8;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .header-title {
            text-align: center;
        }

        .header-title h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 900;
            text-transform: uppercase;
            color: #334155;
            letter-spacing: -0.5px;
        }

        .header-title p {
            margin: 3px 0 0 0;
            font-size: 9px;
            font-weight: 700;
            color: #334155;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .status-box {
            border: 2px solid #94a3b8;
            padding: 5px 8px;
            text-align: center;
            font-weight: 900;
            font-size: 10px;
            text-transform: uppercase;
            background-color: #ffffff;
            color: #334155;
        }

        /* Info Grid 2 Kolom */
        .info-grid-table {
            width: 100%;
            margin-bottom: 14px;
        }

        .info-sub-table {
            width: 100%;
        }

        .info-sub-table td {
            padding: 3px 0;
            font-size: 10px;
            border-bottom: 1px solid #cbd5e1;
        }

        .info-label {
            font-weight: 700;
            color: #334155;
            width: 45%;
        }

        .info-value {
            font-weight: 900;
            color: #334155;
            text-align: right;
        }

        /* Tabel Items Header Biru Cerah */
        .pr-blue-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #94a3b8;
            margin-top: 4px;
            margin-bottom: 14px;
        }

        .pr-blue-table th {
            background-color: #3b82f6;
            color: #ffffff;
            border: 1px solid #64748b;
            padding: 7px 8px;
            font-size: 10px;
            font-weight: 800;
            text-align: center;
            text-transform: uppercase;
        }

        .pr-blue-table td {
            border: 1px solid #cbd5e1;
            padding: 7px 8px;
            font-size: 10px;
            color: #334155;
            vertical-align: middle;
        }

        /* Remark Notes */
        .remark-container {
            margin-bottom: 16px;
        }

        .remark-title {
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            color: #334155;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .remark-box {
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
            padding: 8px 10px;
            font-size: 10px;
            font-weight: 700;
            color: #334155;
            min-height: 30px;
        }

        /* TTD Signature Matrix (3 Columns) */
        .workflow-title {
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            color: #334155;
            text-align: center;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .sig-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
        }

        .sig-box {
            border: 1.5px solid #94a3b8;
            background-color: #ffffff;
            padding: 6px;
            text-align: center;
            vertical-align: top;
            height: 125px;
        }

        .sig-header-step {
            display: block;
            font-size: 8.5px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #334155;
        }

        .sig-header-role {
            display: block;
            font-size: 7.5px;
            font-weight: 700;
            text-transform: uppercase;
            color: #334155;
        }

        .sig-image-container {
            height: 50px;
            margin: 4px 0;
        }

        .sig-image {
            max-height: 48px;
            max-width: 120px;
        }

        .badge-status {
            display: inline-block;
            padding: 2px 5px;
            border: 1px solid #cbd5e1;
            font-size: 8.5px;
            font-weight: 900;
            text-transform: uppercase;
            color: #334155;
            margin-top: 12px;
        }

        .badge-rejected {
            border-color: #fca5a5;
            color: #dc2626;
        }

        .sig-footer-name {
            display: block;
            font-weight: 900;
            font-size: 10px;
            color: #334155;
        }

        .sig-footer-dept {
            display: block;
            font-size: 8px;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            margin-top: 1px;
        }

        /* Footer Tanggal Cetak */
        .footer-table {
            width: 100%;
            margin-top: 14px;
            padding-top: 6px;
            border-top: 1px solid #cbd5e1;
            font-size: 8.5px;
            font-weight: 700;
            color: #475569;
        }
    </style>
</head>
<body>

@php
    // Normalisasi data email pengirim & penerima
    $targetEmail = optional($pr)->notification_email ?? optional(optional($pr)->user)->email ?? '-';
    $senderUser  = Auth::user();
    $senderEmail = !empty($senderUser->email) ? $senderUser->email : config('mail.from.address');
    $senderName  = !empty($senderUser->name) ? $senderUser->name : 'Costing Department';

    // Helper penanganan path gambar lokal khusus DomPDF
    $getSigPath = function($path) {
        if (!$path) return null;
        $clean = ltrim(str_replace(['public/', 'storage/'], '', $path), '/');
        $full = storage_path('app/public/' . $clean);
        return file_exists($full) ? $full : null;
    };

    $preparedSigPath = $getSigPath(optional($pr)->prepared_signature ?? optional(optional($pr)->user)->signature_path ?? optional(optional($pr)->user)->signature);
    $checkedSigPath  = $getSigPath(optional($pr)->checked_signature);
    $approvedSigPath = $getSigPath(optional($pr)->approved_signature);

    // Path Logo
    $logoPath = public_path('images/logosidebar.png');
    if (!file_exists($logoPath)) {
        $logoPath = public_path('images/logo-siix.png');
    }
@endphp

<!-- TABEL UTAMA PEMBUNGKUS (AGAR BORDER SIMETRIS & PRESISI DI TENGAH) -->
<table class="master-table">
    <tr>
        <td class="master-border-td">
            
            <!-- HEADER DOKUMEN -->
            <table class="header-table">
                <tr>
                    <td style="width: 25%; vertical-align: middle;">
                        @if(file_exists($logoPath))
                            <img src="{{ $logoPath }}" style="height: 42px; width: auto;">
                        @else
                            <strong style="font-size: 13px;">PT. SIIX EMS KARAWANG</strong>
                        @endif
                    </td>

                    <td class="header-title" style="width: 50%; vertical-align: middle;">
                        <h2>PURCHASE REQUEST</h2>
                        <p>ENGINEERING DEPARTMENT</p>
                    </td>

                    <td style="width: 25%; text-align: right; vertical-align: middle;">
                        <div class="status-box">
                            STATUS: {{ strtoupper(optional($pr)->status ?? 'PENDING') }}
                        </div>
                    </td>
                </tr>
            </table>

            <!-- INFO GRID (2 KOLOM) -->
            <table class="info-grid-table">
                <tr>
                    <!-- KOLOM KIRI -->
                    <td style="width: 48%; vertical-align: top;">
                        <table class="info-sub-table">
                            <tr>
                                <td class="info-label">No. PR Reference</td>
                                <td class="info-value">{{ optional($pr)->no_pr ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Requester Name</td>
                                <td class="info-value">{{ optional(optional($pr)->user)->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">NIK</td>
                                <td class="info-value">{{ optional(optional($pr)->user)->nik ?? optional(optional($pr)->user)->nim ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Sender Email (Costing)</td>
                                <td class="info-value" style="font-family: monospace; font-size: 9.5px; color: #475569;">{{ $senderEmail }}</td>
                            </tr>
                        </table>
                    </td>

                    <!-- SPACING -->
                    <td style="width: 4%;"></td>

                    <!-- KOLOM KANAN -->
                    <td style="width: 48%; vertical-align: top;">
                        <table class="info-sub-table">
                            <tr>
                                <td class="info-label">Priority Level</td>
                                <td class="info-value" style="text-transform: uppercase;">{{ optional($pr)->priority ?? 'Normal' }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Request Date</td>
                                <td class="info-value">{{ optional($pr)->created_at ? \Carbon\Carbon::parse($pr->created_at)->format('d/m/Y H:i') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Destination</td>
                                <td class="info-value">{{ optional($pr)->destination ?? 'Costing Dept & Purchasing Dept' }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Sent Target Email</td>
                                <td class="info-value" style="font-family: monospace; font-size: 9.5px; color: #4338ca;">{{ $targetEmail }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- TABEL ITEMS -->
            <div>
                <div style="font-size: 10px; font-weight: 900; text-transform: uppercase; color: #334155; letter-spacing: 0.5px;">Requested Item Details</div>
                <table class="pr-blue-table">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Sparepart ID</th>
                            <th style="width: 20%;">Part Number</th>
                            <th style="width: 20%;">SAP Code</th>
                            <th style="width: 15%;">Category</th>
                            <th style="width: 10%;">QTY</th>
                            <th style="width: 20%;">Destination</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="font-weight: 900; text-align: center;">{{ optional(optional($pr)->sparepart)->sparepart_id ?? optional($pr)->sparepart_id ?? '-' }}</td>
                            <td>{{ optional(optional($pr)->sparepart)->part_number ?? '-' }}</td>
                            <td>{{ optional(optional($pr)->sparepart)->sap_code ?? '-' }}</td>
                            <td>{{ optional(optional($pr)->sparepart)->category ?? '-' }}</td>
                            <td style="text-align: center; font-weight: 900;">{{ optional($pr)->qty_pr ?? 1 }} Pcs</td>
                            <td>{{ optional($pr)->destination ?? 'Costing Dept & Purchasing Dept' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- REMARK NOTES -->
            <div class="remark-container">
                <div class="remark-title">Internal Notes / Reason:</div>
                <div class="remark-box">
                    {{ optional($pr)->remark ?? 'No additional internal notes provided.' }}
                </div>
            </div>

            <!-- APPROVAL WORKFLOW SIGNATURES (3 STEP TTD) -->
            <div style="padding-top: 8px; border-top: 2px solid #94a3b8;">
                <div class="workflow-title">APPROVAL WORKFLOW SIGNATURES</div>
                
                <table class="sig-table">
                    <tr>
                        <!-- STEP 1: PREPARED BY -->
                        <td style="width: 33.33%;">
                            <div class="sig-box">
                                <div>
                                    <span class="sig-header-step">STEP 1: PREPARED BY</span>
                                    <span class="sig-header-role">REQUESTER (ENGINEERING)</span>
                                </div>

                                <div class="sig-image-container">
                                    @if($preparedSigPath)
                                        <img src="{{ $preparedSigPath }}" class="sig-image">
                                    @else
                                        <span class="badge-status">SYSTEM GENERATED</span>
                                    @endif
                                </div>

                                <div>
                                    <span class="sig-footer-name">{{ optional(optional($pr)->user)->name ?? '-' }}</span>
                                    <span class="sig-footer-dept">Engineering Dept</span>
                                </div>
                            </div>
                        </td>

                        <!-- STEP 2: CHECKED BY -->
                        <td style="width: 33.33%;">
                            <div class="sig-box">
                                <div>
                                    <span class="sig-header-step">STEP 2: CHECKED BY</span>
                                    <span class="sig-header-role">ADMIN ENGINEERING</span>
                                </div>

                                <div class="sig-image-container">
                                    @if($checkedSigPath)
                                        <img src="{{ $checkedSigPath }}" class="sig-image">
                                    @elseif(in_array(strtolower(optional($pr)->status ?? ''), ['checked', 'approved']))
                                        <span class="badge-status">CHECKED & VERIFIED</span>
                                    @else
                                        <span class="badge-status" style="color: #94a3b8;">WAITING VERIFICATION</span>
                                    @endif
                                </div>

                                <div>
                                    <span class="sig-footer-name">Administrator</span>
                                    <span class="sig-footer-dept">Engineering Dept</span>
                                </div>
                            </div>
                        </td>

                        <!-- STEP 3: APPROVED BY -->
                        <td style="width: 33.33%;">
                            <div class="sig-box">
                                <div>
                                    <span class="sig-header-step">STEP 3: APPROVED BY</span>
                                    <span class="sig-header-role">COSTING DEPARTMENT</span>
                                </div>

                                <div class="sig-image-container">
                                    @if($approvedSigPath)
                                        <img src="{{ $approvedSigPath }}" class="sig-image">
                                    @elseif(strtolower(optional($pr)->status ?? '') == 'approved')
                                        <span class="badge-status">OFFICIALLY APPROVED</span>
                                    @elseif(strtolower(optional($pr)->status ?? '') == 'rejected')
                                        <span class="badge-status badge-rejected">REJECTED</span>
                                    @else
                                        <span class="badge-status" style="color: #94a3b8;">WAITING APPROVAL</span>
                                    @endif
                                </div>

                                <div>
                                    <span class="sig-footer-name">{{ $senderName }}</span>
                                    <span class="sig-footer-dept">Costing Dept</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- FOOTER TANGGAL CETAK -->
            <table class="footer-table">
                <tr>
                    <td style="text-align: left;">Printed automatically via System Inventory Engineering</td>
                    <td style="text-align: right;">Date Printed: {{ now()->format('d/m/Y H:i') }} WIB</td>
                </tr>
            </table>

        </td>
    </tr>
</table>

</body>
</html>