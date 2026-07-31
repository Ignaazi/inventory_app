@extends('admin')

@section('content')
{{-- TRICK BULLETPROOF: Deteksi otomatis variabel dari Controller (Log History vs Direct Request) --}}
@php
    $log = $log ?? null;
    $req = $req ?? $requestData ?? $productionRequest ?? $data ?? null;
    $hasData = $log || $req;
    $isHistory = (bool)$log; // Deteksi apakah diakses dari modul History Log
@endphp

@if(!$hasData)
    <div class="mx-auto max-w-lg mt-10 p-6 bg-red-50 border border-red-200 rounded-md text-center font-nunito">
        <h3 class="text-red-800 font-black uppercase">Variabel Data Tidak Ditemukan!</h3>
        <p class="text-xs text-slate-600 mt-2">
            Controller Engineering belum mengirimkan data ke view ini. Pastikan di Controller lo memakai: <br>
            <code class="bg-slate-200 px-1 py-0.5 rounded font-mono text-red-600">return view('...', compact('log'));</code>
        </p>
    </div>
@else

{{-- NORMALISASI DATA: Memastikan data ter-ekstrak dengan aman walau data history lama tidak punya relasi --}}
@php
    if ($isHistory && $log instanceof \App\Models\Engineering\HistoryApproval) {
        $targetId = $log->production_request_id ?? $log->id;
        $reqNo = $log->request_no ?? optional($log->productionRequest)->request_no ?? '-';
        $nik = $log->nik ?? optional(optional($log->productionRequest)->user)->nik ?? '-';
        $name = $log->approver_name ?? optional(optional($log->productionRequest)->user)->name ?? '-';
        
        $lineMachine = $log->line_machine ?? '-';
        if (($lineMachine === '-' || empty($lineMachine)) && $log->productionRequest && $log->productionRequest->lineProduction) {
            $lineMachine = (optional($log->productionRequest->lineProduction)->no_line ?? '') . ' - ' . (optional($log->productionRequest->lineProduction)->name_machine ?? '');
        }
        
        $sparepartId = $log->sparepart_id ?? (optional($log->productionRequest)->sparepart ? (optional($log->productionRequest->sparepart)->sparepart_id ?? optional($log->productionRequest->sparepart)->id) : '-');
        $partNumber = $log->part_number ?? optional(optional($log->productionRequest)->sparepart)->part_number ?? '-';
        $sapCode = $log->sap_code ?? optional(optional($log->productionRequest)->sparepart)->sap_code ?? '-';
        $qtyReq = (int)($log->qty_req ?? optional($log->productionRequest)->qty_req ?? 0);
        $remark = $log->remark ?? optional($log->productionRequest)->remark ?? '-';
        $status = strtolower($log->status ?? optional($log->productionRequest)->status ?? 'pending');
        
        $prodSign = $log->production_signature ?? optional($log->productionRequest)->production_signature ?? '';
        $engSign = $log->engineering_signature ?? optional($log->productionRequest)->engineering_signature ?? '';
        $spvSign = $log->spv_signature ?? optional($log->productionRequest)->spv_signature ?? '';
        $userSignPath = optional(optional($log->productionRequest)->user)->signature_path ?? '';
        $activeProdPath = $prodSign ?: $userSignPath;
        
        $rejectRemark = $log->reject_remark ?? optional($log->productionRequest)->reject_remark ?? '';
    } else {
        // Fallback jika diakses dari modul request reguler aktif
        $targetId = $req->id ?? 0;
        $reqNo = $req->request_no ?? '-';
        $nik = optional($req->user)->nik ?? '-';
        $name = optional($req->user)->name ?? '-';
        $lineMachine = (optional($req->lineProduction)->no_line ?? '-') . ' - ' . (optional($req->lineProduction)->name_machine ?? '-');
        $sparepartId = optional($req->sparepart)->sparepart_id ?? $req->sparepart_id ?? '-';
        $partNumber = optional($req->sparepart)->part_number ?? '-';
        $sapCode = optional($req->sparepart)->sap_code ?? '-';
        $qtyReq = (int)($req->qty_req ?? 0);
        $remark = $req->remark ?? '-';
        $status = strtolower($req->status ?? 'pending');
        
        $prodSign = $req->production_signature ?? '';
        $engSign = $req->engineering_signature ?? '';
        $spvSign = $req->spv_signature ?? '';
        $userSignPath = optional($req->user)->signature_path ?? '';
        $activeProdPath = $prodSign ?: $userSignPath;
        
        $rejectRemark = $req->reject_remark ?? '';
    }
@endphp

<link href="https://fonts.googleapis.com/css2?family=Nunito:wght=400;600;700;900&display=swap" rel="stylesheet">

<style>
[x-cloak] { display: none !important; }

@media print {
    body * {
        visibility: hidden !important;
        background: white !important;
        color: black !important;
    }
    #print-target-box, #print-target-box * {
        visibility: visible !important;
    }
    #print-target-box {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        box-shadow: none !important;
    }
}
</style>

<div class="mx-auto w-full max-w-5xl pb-12 px-4 sm:px-6 font-nunito text-black dark:text-white" x-data="engineeringApprovalHandler()" x-cloak>
    
    <!-- TOP HEADER & ACTION BUTTONS -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
        <div>
            <h2 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-2">
                <span class="h-5 w-1.5 bg-amber-500 rounded-full"></span>
                <span>Engineering Otorisasi Detail</span>
            </h2>
            <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">
                {{ $isHistory ? 'Mode Arsip / Riwayat Laporan Persetujuan' : 'Review & Authorize Sparepart Request' }}
            </p>
        </div>
        
        <div class="flex items-center gap-2 self-start sm:self-center">
            <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center gap-1.5 bg-slate-700 hover:bg-slate-800 text-white rounded-md px-4 py-2 text-xs font-black uppercase tracking-wider transition-all shadow-sm active:scale-95">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Kembali
            </a>
            <button type="button" @click="printDocument()" class="flex items-center gap-1.5 bg-gradient-to-r from-red-600 to-rose-600 hover:opacity-90 text-white rounded-md px-4 py-2 text-xs font-bold uppercase tracking-wide transition-all shadow-sm active:scale-95 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4H7v4a2 2 0 002 2zM9 9V5a2 2 0 012-2h2a2 2 0 012 2v4M7 13h10" />
                </svg>
                Print / Download PDF
            </button>
        </div>
    </div>

    <!-- LIVE VISUAL PROGRESS TIMELINE -->
    <div class="mb-6 bg-white dark:bg-boxdark border border-slate-200 dark:border-strokedark rounded-xl p-5 shadow-sm print:hidden">
        <h4 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-4">Live Tracking Status Flow</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-0 relative">
            <!-- STEP 1: PRODUCTION REQUEST -->
            <div class="flex items-center gap-3 md:flex-col md:text-center md:px-4 relative">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-black text-xs transition-all bg-emerald-600 text-white ring-4 ring-emerald-100 dark:ring-emerald-950">
                    ✓
                </div>
                <div class="mt-1">
                    <p class="text-xs font-black uppercase text-slate-800 dark:text-white">Requested</p>
                    <p class="text-[10px] text-slate-400 font-bold leading-tight mt-0.5">Oleh Production</p>
                </div>
                <div class="hidden md:block absolute top-4 left-[60%] w-[80%] h-[2px] bg-slate-200 dark:bg-slate-700 -z-10" :class="staffSignatureImg ? 'bg-emerald-500' : ''"></div>
            </div>

            <!-- STEP 2: STAFF ENGINEERING CHECK -->
            <div class="flex items-center gap-3 md:flex-col md:text-center md:px-4 relative mt-3 md:mt-0">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-black text-xs transition-all"
                     :class="staffSignatureImg ? 'bg-emerald-600 text-white ring-4 ring-emerald-100 dark:ring-emerald-950' : (currentStatus === 'rejected' ? 'bg-rose-600 text-white' : 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-400')">
                    <span x-text="staffSignatureImg ? '✓' : '2'"></span>
                </div>
                <div class="mt-1">
                    <p class="text-xs font-black uppercase" :class="staffSignatureImg ? 'text-slate-800 dark:text-white' : 'text-slate-400'">Checked by Staff</p>
                    <p class="text-[10px] text-slate-400 font-bold leading-tight mt-0.5" x-text="staffSignatureImg ? 'Verified by Eng Staff' : (currentStatus === 'rejected' ? 'Proses Terhenti' : 'Menunggu Staff Engineering')"></p>
                </div>
                <div class="hidden md:block absolute top-4 left-[60%] w-[80%] h-[2px] bg-slate-200 dark:bg-slate-700 -z-10" :class="spvSignatureImg ? 'bg-emerald-500' : ''"></div>
            </div>

            <!-- STEP 3: FINAL APPROVAL SPV/ADMIN -->
            <div class="flex items-center gap-3 md:flex-col md:text-center md:px-4 relative mt-3 md:mt-0">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-black text-xs transition-all"
                     :class="spvSignatureImg ? 'bg-emerald-600 text-white ring-4 ring-emerald-100 dark:ring-emerald-950' : 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-400'">
                    <span x-text="spvSignatureImg ? '✓' : '3'"></span>
                </div>
                <div class="mt-1">
                    <p class="text-xs font-black uppercase" :class="spvSignatureImg ? 'text-slate-800 dark:text-white' : 'text-slate-400'">Final Approved</p>
                    <p class="text-[10px] text-slate-400 font-bold leading-tight mt-0.5" x-text="spvSignatureImg ? 'Selesai / Approved' : 'Menunggu Admin/SPV'"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- INTERACTIVE ACTION CENTER PANEL -->
    <div class="mb-8 bg-slate-50 dark:bg-slate-900 border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-xl p-6 print:hidden">
        <div class="flex items-center gap-2 mb-4">
            <span class="flex h-2 w-2 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
            </span>
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-500">Status & Action Panel</h3>
        </div>

        @if($isHistory || $status === 'approved' || $status === 'rejected' || $status === 'success' || $status === 'finished' || $status === 'checked')
            <!-- PREVENT ACTION: BILA DIAKSES DARI MENU HISTORY / DATA SUDAH FINISH -->
            <div class="bg-white dark:bg-boxdark p-4 rounded-lg border border-slate-200 text-center">
                <p class="text-sm font-black uppercase tracking-wide" :class="currentStatus === 'approved' || currentStatus === 'success' || currentStatus === 'checked' || currentStatus === 'finished' ? 'text-emerald-600' : (currentStatus === 'rejected' ? 'text-rose-600' : 'text-blue-600')">
                    Document Status: <span class="uppercase" x-text="currentStatus"></span>
                </p>
                <p class="text-[11px] text-slate-400 font-semibold mt-1">
                    @if($isHistory)
                        Dokumen ini dibuka melalui riwayat data (History Log Archive - Read Only).
                    @else
                        Permohonan ini sudah diproses dan tidak membutuhkan tindakan otorisasi lebih lanjut.
                    @endif
                </p>
            </div>
        @else
            <!-- FORM PROSES APPROVAL/REJECT (Hanya aktif jika request statusnya murni pending/draft) -->
            <form action="{{ Route::has('eng.request.process') ? route('eng.request.process', $targetId) : url('engineering/request/process/' . $targetId) }}" method="POST" class="space-y-4">
                @csrf
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button" @click="setAction('approve')" 
                            class="flex-1 min-w-[150px] inline-flex items-center justify-center gap-2 rounded-lg py-3 px-4 text-xs font-black uppercase tracking-wider transition-all border active:scale-95"
                            :class="chosenAction === 'approve' ? 'bg-emerald-600 text-white border-emerald-600 shadow-md ring-2 ring-emerald-200' : 'bg-white dark:bg-boxdark text-slate-700 dark:text-white border-slate-200 hover:bg-slate-100'">
                        ✓ Setujui & TTD
                    </button>

                    <button type="button" @click="setAction('reject')" 
                            class="flex-1 min-w-[150px] inline-flex items-center justify-center gap-2 rounded-lg py-3 px-4 text-xs font-black uppercase tracking-wider transition-all border active:scale-95"
                            :class="chosenAction === 'reject' ? 'bg-rose-600 text-white border-rose-600 shadow-md ring-2 ring-rose-200' : 'bg-white dark:bg-boxdark text-slate-700 dark:text-white border-slate-200 hover:bg-slate-100'">
                        🛑 Tolak Dokumen
                    </button>
                </div>

                <input type="hidden" name="status" :value="chosenAction">

                <div x-show="chosenAction === 'reject'" x-transition class="bg-white dark:bg-boxdark p-4 rounded-lg border border-rose-200 space-y-2">
                    <label class="block text-xs font-black text-rose-700 uppercase tracking-wide">Alasan Penolakan (Reject Remark) *</label>
                    <textarea name="reject_remark" required rows="3" class="w-full text-xs font-semibold p-2.5 border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-rose-500 bg-slate-50 text-black dark:text-white dark:bg-slate-800" placeholder="Tulis alasan kenapa sparepart ini ditolak..."></textarea>
                </div>

                <div x-show="chosenAction === 'approve'" x-transition class="bg-white dark:bg-boxdark p-4 rounded-lg border border-emerald-200">
                    <p class="text-[11px] text-slate-600 dark:text-slate-400 font-bold leading-relaxed">
                        Dengan memproses dokumen ini, sistem akan otomatis menempelkan tanda tangan digital akun Engineering Anda pada kolom matriks otorisasi di bawah ini secara resmi.
                    </p>
                </div>

                <div x-show="chosenAction" x-transition class="flex justify-end pt-2">
                    <button type="submit" class="bg-slate-900 hover:bg-black text-white text-xs font-black uppercase tracking-widest px-6 py-2.5 rounded shadow transition-all active:scale-95 cursor-pointer">
                        Submit Keputusan
                    </button>
                </div>
            </form>
        @endif
    </div>

    <!-- DOCUMENT LIVE PREVIEW AREA -->
    <div id="print-target-box" class="w-full">
        <div class="bg-white text-black p-8 sm:p-12 border border-slate-300 rounded-md shadow-sm print:border-none print:shadow-none print:p-0 font-sans">
            
            <!-- KOP SURAT FORM FISIK -->
            <div class="flex items-center justify-between border-b-4 border-black pb-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 flex items-center justify-center overflow-hidden">
                        <img src="/images/logo-siix.png" class="max-h-full max-w-full object-contain" alt="Logo SIIX" onerror="this.style.display='none'">
                    </div>  
                    <div>
                        <h1 class="text-lg font-black uppercase tracking-tight text-black">PT. SIIX EMS KARAWANG</h1>
                        <p class="text-[10px] font-black text-slate-500 tracking-wider uppercase">Electronic Manufacturing Services</p>
                    </div>
                </div>
                <div class="text-right">
                    <h2 class="text-xs font-black uppercase text-black border border-black px-3 py-1 bg-slate-50 tracking-wide rounded-sm">FORM SPAREPART REQUEST</h2>
                    <p class="text-[9px] text-black font-mono font-bold mt-1">Doc No: {{ $reqNo }}</p>
                </div>
            </div>

            <!-- DETAIL CONTENT TABLE -->
            <div class="mb-6">
                <table class="w-full border-collapse text-xs border border-black">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">NIK</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase">{{ $nik }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">NAME</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase">{{ $name }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">LINE & MACHINE</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase">{{ $lineMachine }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">SPAREPART ID</td>
                            <td class="py-2.5 px-4 font-mono font-black text-black uppercase">{{ $sparepartId }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">PART NUMBER</td>
                            <td class="py-2.5 px-4 font-mono font-black text-black uppercase">{{ $partNumber }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">SAP CODE</td>
                            <td class="py-2.5 px-4 font-mono font-black text-black tracking-wider">{{ $sapCode }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">Quantity Requested</td>
                            <td class="py-2.5 px-4 font-black text-black">{{ $qtyReq }} Pcs</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">Remark / Purpose</td>
                            <td class="py-2.5 px-4 font-semibold text-slate-900 tracking-wide whitespace-normal break-words leading-normal">{{ $remark }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- LIVE SIGNATURE MATRIX GRID -->
            <div class="grid grid-cols-3 gap-0 border border-black text-center text-xs mt-8 rounded-sm overflow-hidden">
                
                <!-- 1. Requested By -->
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-black border-b border-black py-1 uppercase tracking-wider text-[9px] text-black">Requested By</div>
                    
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        <div class="absolute inset-0 z-10 flex items-center justify-center p-1" x-show="prodSignatureImg">
                            <img :src="prodSignatureImg" class="max-h-full max-w-full object-contain mx-auto my-auto block">
                        </div>
                        <div class="z-30 px-2 my-auto" x-show="!prodSignatureImg">
                            <div class="text-green-600 font-mono text-[8px] font-black uppercase tracking-tighter border border-green-300 bg-green-50 py-1 rounded mx-auto max-w-[120px]">
                                ✓ SYSTEM VERIFIED
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-black uppercase text-black tracking-wide truncate px-1">
                            {{ $name !== '-' ? '( ' . $name . ' )' : '( _________________ )' }}
                        </p>
                        <p class="text-[9px] text-slate-500 font-black uppercase mt-0.5">Production Department</p>
                    </div>
                </div>

                <!-- 2. Checked By -->
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-black border-b border-black py-1 uppercase tracking-wider text-[9px] text-black">Checked By</div>
                    
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        <div class="absolute inset-0 z-10 flex items-center justify-center p-1" x-show="staffSignatureImg">
                            <img :src="staffSignatureImg" class="max-h-full max-w-full object-contain mx-auto my-auto block">
                        </div>
                        <div class="z-30 my-auto px-2" x-show="!staffSignatureImg">
                            <template x-if="currentStatus === 'rejected'">
                                <span class="text-rose-600 border border-rose-200 bg-rose-50 px-2 py-0.5 rounded font-bold text-[8px] uppercase tracking-wide">🛑 Stopped</span>
                            </template>
                            <template x-if="currentStatus !== 'rejected'">
                                <span class="text-amber-500 font-mono text-[8px] border border-amber-200 bg-amber-50/60 px-2 py-0.5 rounded animate-pulse">🕒 Waiting Staff</span>
                            </template>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-black uppercase text-black tracking-wide truncate px-1" x-text="staffSignatureImg ? '( Checked by Staff )' : '( _________________ )'">
                        </p>
                        <p class="text-[9px] text-slate-500 font-black uppercase mt-0.5">Staff Engineering</p>
                    </div>
                </div>

                <!-- 3. Approved By -->
                <div class="flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-black border-b border-black py-1 uppercase tracking-wider text-[9px] text-black">Approved By</div>
                    
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        <div class="absolute inset-0 z-10 flex items-center justify-center p-1" x-show="spvSignatureImg">
                            <img :src="spvSignatureImg" class="max-h-full max-w-full object-contain mx-auto my-auto block">
                        </div>
                        <div class="z-30 my-auto px-2" x-show="!spvSignatureImg">
                            <template x-if="currentStatus === 'rejected'">
                                <span class="text-rose-600 border border-rose-200 bg-rose-50 px-2 py-0.5 rounded font-bold text-[8px] uppercase tracking-wide">🛑 Stopped</span>
                            </template>
                            <template x-if="currentStatus !== 'rejected'">
                                <span class="text-slate-400 italic text-[9px]" x-text="staffSignatureImg ? '🕒 Waiting Admin' : '( Queue )'"></span>
                            </template>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-black uppercase text-black tracking-wide truncate px-1" x-text="spvSignatureImg ? '( Approved by Admin )' : '( _________________ )'">
                        </p>
                        <p class="text-[9px] text-slate-500 font-black uppercase mt-0.5">Admin / SPV Engineering</p>
                    </div>
                </div>
            </div>

            <!-- DETAIL REMARK REJECT DI BAWAH DOKUMEN -->
            @if($status === 'rejected' || !empty($rejectRemark))
                <div class="mt-6 border-2 border-red-400 bg-red-50 p-4 rounded-md print:border-black print:bg-white">
                    <h4 class="text-xs font-black text-red-700 uppercase tracking-wide print:text-black">REJECTION REMARK:</h4>
                    <p class="text-xs font-bold text-slate-700 mt-1 italic print:text-black">" {{ $rejectRemark ?: 'Permohonan ditolak oleh tim Engineering.' }} "</p>
                </div>
            @endif

            <div class="mt-12 border-t border-dashed border-slate-300 pt-4 text-center print:block hidden">
                <p class="text-[8px] text-slate-400 font-mono uppercase tracking-widest">SIIX-SPAREPART-TRACKING-SYSTEM • LIVE CONFIDENTIAL DOCUMENT</p>
            </div>

        </div>
    </div>
</div>

<script>
function engineeringApprovalHandler() {
    const activeProdPath = "{{ $activeProdPath }}";
    const staffSignPath = "{{ $engSign }}"; 
    const spvSignPath = "{{ $spvSign }}";

    return {
        currentStatus: "{{ $status }}",
        chosenAction: null,
        
        prodSignatureImg: activeProdPath ? (activeProdPath.startsWith('http') || activeProdPath.startsWith('data:image') ? activeProdPath : "{{ asset('storage') }}/" + activeProdPath.replace(/^\/?(storage\/)?/, '')) : null,
        staffSignatureImg: staffSignPath ? (staffSignPath.startsWith('http') || staffSignPath.startsWith('data:image') ? staffSignPath : "{{ asset('storage') }}/" + staffSignPath.replace(/^\/?(storage\/)?/, '')) : null,
        spvSignatureImg: spvSignPath ? (spvSignPath.startsWith('http') || spvSignPath.startsWith('data:image') ? spvSignPath : "{{ asset('storage') }}/" + spvSignPath.replace(/^\/?(storage\/)?/, '')) : null,

        setAction(type) {
            this.chosenAction = type;
        },

        printDocument() {
            window.print();
        }
    }
}
</script>
@endif
@endsection