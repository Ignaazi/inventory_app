@extends('admin')

@section('content')
<!-- Import Font Nunito -->
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">

@php
    // Inisialisasi variabel pengaman jika dipanggil tanpa controller
    $pr = $pr ?? null;
@endphp

<style>
    /* Reset & Tipografi Global - Teks Abu-abu Gelap (#334155) */
    .preview-pr-root, .preview-pr-root * {
        font-family: 'Nunito', sans-serif !important;
        font-style: normal !important;
        color: #334155 !important;
    }

    .preview-pr-root {
        padding: 10px 15px;
        margin: 0 auto;
        width: 100%;
    }

    /* Tombol Gradient Biru (Kembali) */
    .btn-blue-gradient {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
        color: #ffffff !important;
        border: none !important;
        padding: 8px 18px;
        font-size: 13px;
        font-weight: 800;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none !important;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: opacity 0.2s ease;
    }

    .btn-blue-gradient * {
        color: #ffffff !important;
    }

    .btn-blue-gradient:hover {
        opacity: 0.9;
    }

    /* Tombol Gradient Merah (Print) */
    .btn-red-gradient {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        color: #ffffff !important;
        border: none !important;
        padding: 8px 18px;
        font-size: 13px;
        font-weight: 800;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none !important;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: opacity 0.2s ease;
    }

    .btn-red-gradient * {
        color: #ffffff !important;
    }

    .btn-red-gradient:hover {
        opacity: 0.9;
    }

    /* Tabel Items Header Biru Cerah (Cerah & Garis Abu-abu) */
    .pr-blue-table {
        width: 100%;
        border-collapse: collapse;
        border: 1.5px solid #94a3b8;
    }

    .pr-blue-table th {
        background-color: #3b82f6 !important; /* Biru cerah */
        border: 1px solid #64748b;
        padding: 10px 12px;
        font-size: 12px;
        font-weight: 800;
        text-align: center;
        text-transform: uppercase;
    }

    .pr-blue-table th * {
        color: #ffffff !important;
    }

    .pr-blue-table td {
        border: 1px solid #cbd5e1;
        padding: 10px 12px;
        font-size: 12px;
        vertical-align: middle;
    }

    /* TTD Signature Box */
    .sig-box {
        border: 1.5px solid #94a3b8;
        background-color: #ffffff;
        padding: 12px;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 160px;
    }

    @media print {
        .no-print, nav, header, sidebar, .sidebar, #sidebar {
            display: none !important;
        }
        body, .preview-pr-root {
            background-color: #ffffff !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .print-paper {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            width: 100% !important;
        }
        .pr-blue-table th {
            background-color: #3b82f6 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        @page {
            size: A4 portrait;
            margin: 10mm;
        }
    }
</style>

<div class="preview-pr-root">
    
    <!-- TOP NAVIGATION (NO PRINT) -->
    <div class="mb-4 no-print flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-black uppercase text-slate-700 tracking-tight mb-2">Document Preview</h1>
            <a href="javascript:history.back()" class="btn-blue-gradient">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>

        <div class="flex items-center">
            <button onclick="window.print()" class="btn-red-gradient">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print / Cetak Dokumen
            </button>
        </div>
    </div>

    <!-- MAIN PRINTABLE PAPER CONTAINER -->
    <div class="print-paper w-full bg-white border-2 border-slate-400 p-6 md:p-8 shadow-lg">
        
        <!-- HEADER DOKUMEN -->
        <div class="flex justify-between items-center border-b-2 border-slate-400 pb-4 mb-6">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logosidebar.png') }}" alt="SIIX Logo" class="h-14 w-auto object-contain">
            </div>

            <div class="text-center">
                <h2 class="text-2xl font-black uppercase tracking-tight text-slate-700">PURCHASE REQUEST</h2>
                <p class="text-xs font-bold text-slate-700 uppercase tracking-widest mt-0.5">ENGINEERING DEPARTMENT</p>
            </div>

            <div class="border-2 border-slate-400 px-4 py-1.5 text-center font-black text-xs uppercase tracking-wider bg-white">
                STATUS: {{ strtoupper(optional($pr)->status ?? 'PENDING') }}
            </div>
        </div>

        <!-- INFO GRID (2 KOLOM) -->
        <div class="grid grid-cols-2 gap-x-10 gap-y-2 text-xs mb-6">
            <div class="space-y-2">
                <div class="flex justify-between border-b border-slate-300 pb-1">
                    <span class="font-bold">No. PR Reference</span>
                    <span class="font-black">{{ optional($pr)->no_pr ?? '-' }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-300 pb-1">
                    <span class="font-bold">Requester Name</span>
                    <span class="font-black">{{ optional(optional($pr)->user)->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-300 pb-1">
                    <span class="font-bold">NIK</span>
                    <span class="font-black">{{ optional(optional($pr)->user)->nik ?? '-' }}</span>
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex justify-between border-b border-slate-300 pb-1">
                    <span class="font-bold">Priority Level</span>
                    <span class="font-black uppercase">{{ optional($pr)->priority ?? 'Normal' }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-300 pb-1">
                    <span class="font-bold">Request Date</span>
                    <span class="font-black">{{ optional($pr)->request_date ? \Carbon\Carbon::parse($pr->request_date)->format('d/m/Y H:i') : '-' }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-300 pb-1">
                    <span class="font-bold">Expected Arrival</span>
                    <span class="font-black">{{ optional($pr)->expected_arrival_date ? \Carbon\Carbon::parse($pr->expected_arrival_date)->format('d/m/Y H:i') : '-' }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-300 pb-1">
                    <span class="font-bold">Destination</span>
                    <span class="font-black">{{ optional($pr)->destination ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- TABEL ITEMS -->
        <div class="mb-6">
            <h3 class="text-xs font-black uppercase text-slate-700 mb-2 tracking-wider">Requested Item Details</h3>
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
                        <td class="font-black text-center">{{ optional(optional($pr)->sparepart)->sparepart_id ?? optional($pr)->sparepart_id ?? '-' }}</td>
                        <td>{{ optional(optional($pr)->sparepart)->part_number ?? '-' }}</td>
                        <td>{{ optional(optional($pr)->sparepart)->sap_code ?? '-' }}</td>
                        <td>{{ optional(optional($pr)->sparepart)->category ?? '-' }}</td>
                        <td class="text-center font-black">{{ optional($pr)->qty_pr ?? 1 }} Pcs</td>
                        <td>{{ optional($pr)->destination ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- REMARK NOTES -->
        <div class="mb-8">
            <h3 class="text-xs font-black uppercase text-slate-700 mb-1 tracking-wider">Internal Notes / Reason:</h3>
            <div class="p-3 border border-slate-300 bg-white text-xs font-bold text-slate-700 min-h-[50px]">
                {{ optional($pr)->remark ?? 'No additional internal notes provided.' }}
            </div>
        </div>

        <!-- APPROVAL WORKFLOW SIGNATURES (3 STEP TTD) -->
        <div class="pt-4 border-t-2 border-slate-400">
            <h3 class="text-xs font-black uppercase text-slate-700 mb-4 text-center tracking-widest">APPROVAL WORKFLOW SIGNATURES</h3>
            
            <div class="grid grid-cols-3 gap-6 text-center">
                @php
                    $getSigUrl = function($path) {
                        if (!$path) return null;
                        return (str_contains($path, 'uploads/') || str_contains($path, 'storage/')) 
                            ? asset($path) 
                            : asset('storage/' . $path);
                    };
                @endphp

                <!-- STEP 1: PREPARED BY -->
                <div class="sig-box">
                    <div>
                        <span class="block text-[10px] font-black uppercase tracking-wider">STEP 1: PREPARED BY</span>
                        <span class="block text-[9px] font-bold uppercase">REQUESTER (ENGINEERING)</span>
                    </div>

                    <div class="h-16 flex items-center justify-center my-1">
                        @if(optional($pr)->prepared_signature)
                            <img src="{{ $getSigUrl($pr->prepared_signature) }}" alt="Prepared Signature" class="max-h-14 object-contain">
                        @elseif(optional(optional($pr)->user)->signature_path || optional(optional($pr)->user)->signature)
                            <img src="{{ $getSigUrl($pr->user->signature_path ?? $pr->user->signature) }}" alt="User Signature" class="max-h-14 object-contain">
                        @else
                            <span class="px-2 py-1 border border-slate-300 text-[10px] font-black uppercase">SYSTEM GENERATED</span>
                        @endif
                    </div>

                    <div>
                        <span class="block font-black text-xs text-slate-700">{{ optional(optional($pr)->user)->name ?? '-' }}</span>
                        <span class="block text-[10px] font-bold text-slate-700 uppercase mt-0.5">Engineering Dept</span>
                    </div>
                </div>

                <!-- STEP 2: CHECKED BY -->
                <div class="sig-box">
                    <div>
                        <span class="block text-[10px] font-black uppercase tracking-wider">STEP 2: CHECKED BY</span>
                        <span class="block text-[9px] font-bold uppercase">ADMIN ENGINEERING</span>
                    </div>

                    <div class="h-16 flex items-center justify-center my-1">
                        @if(optional($pr)->checked_signature)
                            <img src="{{ $getSigUrl($pr->checked_signature) }}" alt="Checked Signature" class="max-h-14 object-contain">
                        @elseif(in_array(strtolower(optional($pr)->status ?? ''), ['checked', 'approved']))
                            <span class="px-2 py-1 border border-slate-300 text-[10px] font-black uppercase">CHECKED & VERIFIED</span>
                        @else
                            <span class="text-[10px] font-black uppercase text-slate-400">WAITING VERIFICATION</span>
                        @endif
                    </div>

                    <div>
                        <span class="block font-black text-xs text-slate-700">
                            {{ in_array(strtolower(optional($pr)->status ?? ''), ['checked', 'approved']) ? (optional(optional($pr)->checker)->name ?? 'Admin Engineering') : 'Admin Engineering' }}
                        </span>
                        <span class="block text-[10px] font-bold text-slate-700 uppercase mt-0.5">Engineering Dept</span>
                    </div>
                </div>

                <!-- STEP 3: APPROVED BY -->
                <div class="sig-box">
                    <div>
                        <span class="block text-[10px] font-black uppercase tracking-wider">STEP 3: APPROVED BY</span>
                        <span class="block text-[9px] font-bold uppercase">COSTING DEPARTMENT</span>
                    </div>

                    <div class="h-16 flex items-center justify-center my-1">
                        @if(optional($pr)->approved_signature)
                            <img src="{{ $getSigUrl($pr->approved_signature) }}" alt="Approved Signature" class="max-h-14 object-contain">
                        @elseif(strtolower(optional($pr)->status ?? '') == 'approved')
                            <span class="px-2 py-1 border border-slate-300 text-[10px] font-black uppercase">OFFICIALLY APPROVED</span>
                        @else
                            <span class="text-[10px] font-black uppercase text-slate-400">WAITING APPROVAL</span>
                        @endif
                    </div>

                    <div>
                        <span class="block font-black text-xs text-slate-700">
                            {{ strtolower(optional($pr)->status ?? '') == 'approved' ? (optional(optional($pr)->approver)->name ?? 'Costing Approver') : 'Costing Approver' }}
                        </span>
                        <span class="block text-[10px] font-bold text-slate-700 uppercase mt-0.5">Costing Dept</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- FOOTER TANGGAL CETAK -->
        <div class="mt-8 pt-3 border-t border-slate-300 flex justify-between items-center text-[10px] font-bold text-slate-600">
            <span>Printed automatically via System Inventory Engineering</span>
            <span>Date Printed: {{ now()->format('d/m/Y H:i') }} WIB</span>
        </div>

    </div>

</div>
@endsection