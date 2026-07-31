@extends('admin')

@section('content')
{{-- TRICK BULLETPROOF: Deteksi otomatis variabel dari Controller --}}
@php
    $log = $log ?? null;
    $req = $req ?? $requestData ?? $productionRequest ?? $data ?? null;
    $hasData = $log || $req;
    $isHistory = (bool)$log;
@endphp

@if(!$hasData)
    <div class="mx-auto max-w-lg mt-10 p-6 bg-red-50 border border-red-200 rounded-md text-center font-nunito">
        <h3 class="text-red-800 font-black uppercase">Variabel Data Tidak Ditemukan!</h3>
        <p class="text-xs text-slate-600 mt-2">
            Data tidak dapat dimuat. Pastikan Controller melempar variabel data dengan benar.
        </p>
    </div>
@else

{{-- NORMALISASI DATA --}}
@php
    if ($isHistory && $log instanceof \App\Models\Engineering\HistoryApproval) {
        $targetId = $log->production_request_id ?? $log->id;
        $reqNo = $log->request_no ?? optional($log->productionRequest)->request_no ?? 'REQPROD001';
        $nik = $log->nik ?? optional(optional($log->productionRequest)->user)->nik ?? '-';
        $name = $log->approver_name ?? optional(optional($log->productionRequest)->user)->name ?? '-';
        
        $lineMachine = $log->line_machine ?? '';
        if (!empty($lineMachine) && str_contains($lineMachine, ' - ')) {
            $parts = explode(' - ', $lineMachine, 2);
            $line = $parts[0];
            $machineName = $parts[1];
        } else {
            $line = !empty($lineMachine) ? $lineMachine : (optional(optional($log->productionRequest)->lineProduction)->no_line ?? '-');
            $machineName = optional(optional($log->productionRequest)->lineProduction)->name_machine ?? '-';
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
        $targetId = $req->id ?? 0;
        $reqNo = $req->request_no ?? 'REQPROD001';
        $nik = optional($req->user)->nik ?? '-';
        $name = optional($req->user)->name ?? '-';
        $line = optional($req->lineProduction)->no_line ?? '-';
        $machineName = optional($req->lineProduction)->name_machine ?? '-';
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

<!-- CONTAINER GEDE 7XL BIAR PADAT DAN KETAT GAK BANYAK RENGGANGAN KOSONG -->
<div class="mx-auto w-full max-w-7xl pb-8 px-4 sm:px-6 font-nunito text-black dark:text-white" x-data="engineeringApprovalHandler()" x-cloak>
    
    <!-- TOP BAR HEADER & BUTTON ACTIONS -->
    <div class="mb-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 print:hidden">
        {{-- HEADER POJOK KIRI ATAS SESUAI IMAGE MOCKUP --}}
        <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-black dark:text-white">Preview Form Request Sparepart</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-bold mt-0.5 tracking-wide">Real-Time Authorization Progress</p>
        </div>
        
        {{-- BUTTON ACTIONS KANAN --}}
        <div class="flex items-center gap-3 self-end sm:self-auto">
            <a href="{{ route('eng.in') }}" 
               class="inline-flex items-center gap-2 rounded-md bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-500 px-4 py-2 text-xs font-bold text-white shadow-md hover:opacity-90 transition-all active:scale-95 uppercase tracking-wider no-underline">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
            
            <button type="button" @click="printDocument()" 
                    class="flex items-center gap-1.5 bg-gradient-to-r from-red-700 via-red-600 to-red-500 hover:opacity-90 text-white rounded-md px-4 py-2 text-xs font-bold uppercase tracking-wide transition-all shadow-sm active:scale-95 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4H7v4a2 2 0 002 2zM9 9V5a2 2 0 012-2h2a2 2 0 012 2v4M7 13h10" />
                </svg>
                DOWNLOAD PDF
            </button>
        </div>
    </div>

    <!-- TIMELINE FLOW DENGAN IDENTITAS WARNA PER FASE (KUNING -> BIRU -> IJO / MERAH JIKA REJECT) -->
    <div class="mb-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm print:hidden">
        <div class="flex items-center justify-center w-full max-w-3xl mx-auto">
            
            <!-- Step 1: Requested (Selalu Kuning sebagai tanda awal buletan produksi) -->
            <div class="flex flex-col items-center flex-1 relative">
                <div class="w-11 h-11 rounded-full bg-amber-500 text-white flex items-center justify-center font-black shadow-md border-4 border-amber-100 dark:border-amber-950 text-base">
                    ✓
                </div>
                <span class="text-[10px] font-black uppercase tracking-wider text-amber-600 mt-2 text-center">Requested</span>
                <span class="text-[9px] text-slate-400 font-bold mt-0.5">Production</span>
            </div>

            <!-- Connector Line 1 (Garis permanen tidak putus) -->
            <div class="w-16 sm:w-28 h-1.5 rounded-full -mt-5 transition-all duration-300 mx-2" 
                 :class="{
                    'bg-blue-500': currentStatus === 'checked' || (currentStatus === 'rejected' && staffSignatureImg) || currentStatus === 'approved' || currentStatus === 'success' || currentStatus === 'finished',
                    'bg-rose-500': currentStatus === 'rejected' && !staffSignatureImg,
                    'bg-slate-300 dark:bg-slate-700': currentStatus === 'request' || currentStatus === 'pending'
                 }"></div>
            
            <!-- Step 2: Checked By Staff (Kuning/Abu jika belum, Biru jika checked, Merah jika reject staff) -->
            <div class="flex flex-col items-center flex-1 relative">
                <div class="w-11 h-11 rounded-full flex items-center justify-center font-black text-base transition-all duration-300 border-4 shadow-sm"
                     :class="{
                        'bg-slate-300 text-slate-500 border-slate-100': currentStatus === 'request' || currentStatus === 'pending',
                        'bg-blue-500 text-white border-blue-100': currentStatus === 'checked' || (currentStatus === 'rejected' && staffSignatureImg) || currentStatus === 'approved' || currentStatus === 'success' || currentStatus === 'finished',
                        'bg-rose-500 text-white border-rose-100': currentStatus === 'rejected' && !staffSignatureImg
                     }">
                    <span x-text="(currentStatus === 'rejected' && !staffSignatureImg) ? '✕' : ((currentStatus === 'checked' || currentStatus === 'approved' || currentStatus === 'success' || currentStatus === 'finished' || (currentStatus === 'rejected' && staffSignatureImg)) ? '✓' : '2')"></span>
                </div>
                <span class="text-[10px] font-black uppercase tracking-wider mt-2 text-center transition-colors duration-300" 
                      :class="{
                        'text-slate-400': currentStatus === 'request' || currentStatus === 'pending',
                        'text-blue-600': currentStatus === 'checked' || (currentStatus === 'rejected' && staffSignatureImg) || currentStatus === 'approved' || currentStatus === 'success' || currentStatus === 'finished',
                        'text-rose-600': currentStatus === 'rejected' && !staffSignatureImg
                      }" x-text="(currentStatus === 'rejected' && !staffSignatureImg) ? 'Rejected' : 'Checked'">Checked</span>
                <span class="text-[9px] text-slate-400 font-bold mt-0.5">Eng Staff</span>
            </div>
            
            <!-- Connector Line 2 (Garis permanen tidak putus) -->
            <div class="w-16 sm:w-28 h-1.5 rounded-full -mt-5 transition-all duration-300 mx-2" 
                 :class="{
                    'bg-emerald-500': currentStatus === 'approved' || currentStatus === 'success' || currentStatus === 'finished',
                    'bg-rose-500': currentStatus === 'rejected' && staffSignatureImg,
                    'bg-slate-300 dark:bg-slate-700': currentStatus === 'request' || currentStatus === 'pending' || currentStatus === 'checked' || (currentStatus === 'rejected' && !staffSignatureImg)
                 }"></div>
            
            <!-- Step 3: Approved Final (Hijau jika approve, Merah jika reject SPV, Abu jika belum) -->
            <div class="flex flex-col items-center flex-1 relative">
                <div class="w-11 h-11 rounded-full flex items-center justify-center font-black text-base transition-all duration-300 border-4 shadow-sm"
                     :class="{
                        'bg-emerald-500 text-white border-emerald-100': currentStatus === 'approved' || currentStatus === 'success' || currentStatus === 'finished',
                        'bg-rose-500 text-white border-rose-100': currentStatus === 'rejected' && staffSignatureImg,
                        'bg-slate-300 text-slate-400 border-slate-100': currentStatus === 'request' || currentStatus === 'pending' || currentStatus === 'checked' || (currentStatus === 'rejected' && !staffSignatureImg)
                     }">
                    <span x-text="(currentStatus === 'rejected' && staffSignatureImg) ? '✕' : ((currentStatus === 'approved' || currentStatus === 'success' || currentStatus === 'finished') ? '✓' : '3')"></span>
                </div>
                <span class="text-[10px] font-black uppercase tracking-wider mt-2 text-center transition-colors duration-300" 
                      :class="{
                        'text-emerald-600': currentStatus === 'approved' || currentStatus === 'success' || currentStatus === 'finished',
                        'text-rose-600': currentStatus === 'rejected' && staffSignatureImg,
                        'text-slate-400': currentStatus === 'request' || currentStatus === 'pending' || currentStatus === 'checked' || (currentStatus === 'rejected' && !staffSignatureImg)
                      }" x-text="(currentStatus === 'rejected' && staffSignatureImg) ? 'Rejected' : 'Approved'">Approved</span>
                <span class="text-[9px] text-slate-400 font-bold mt-0.5">Admin / SPV</span>
            </div>
        </div>
    </div>

    <!-- INTERACTIVE ACTION PANEL -->
    @if(!$isHistory && $status !== 'approved' && $status !== 'rejected' && $status !== 'success' && $status !== 'finished' && $status !== 'checked')
        <div class="mb-4 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg p-4 print:hidden">
            <form action="{{ Route::has('eng.request.process') ? route('eng.request.process', $targetId) : url('engineering/request/process/' . $targetId) }}" method="POST" class="space-y-3">
                @csrf
                <div class="flex gap-3">
                    <button type="button" @click="setAction('approve')" 
                            class="flex-1 inline-flex items-center justify-center gap-2 rounded py-2 px-4 text-xs font-black uppercase tracking-wider transition-all border active:scale-95"
                            :class="chosenAction === 'approve' ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-white dark:bg-boxdark text-slate-700 dark:text-white border-slate-200 hover:bg-slate-50'">
                        ✓ Setujui & Tempel TTD
                    </button>

                    <button type="button" @click="setAction('reject')" 
                            class="flex-1 inline-flex items-center justify-center gap-2 rounded py-2 px-4 text-xs font-black uppercase tracking-wider transition-all border active:scale-95"
                            :class="chosenAction === 'reject' ? 'bg-rose-600 text-white border-rose-600 shadow-sm' : 'bg-white dark:bg-boxdark text-slate-700 dark:text-white border-slate-200 hover:bg-slate-50'">
                        🛑 Tolak Permohonan
                    </button>
                </div>

                <input type="hidden" name="status" :value="chosenAction">

                <div x-show="chosenAction === 'reject'" x-transition class="bg-white dark:bg-boxdark p-3 rounded border border-rose-200 space-y-1.5">
                    <label class="block text-[11px] font-black text-rose-700 uppercase">Alasan Penolakan *</label>
                    <textarea name="reject_remark" required rows="2" class="w-full text-xs font-semibold p-2 border border-slate-300 rounded bg-slate-50 text-black dark:text-white dark:bg-slate-800 focus:outline-none focus:ring-1 focus:ring-rose-500" placeholder="Tulis alasan singkat penolakan..."></textarea>
                </div>

                <div x-show="chosenAction" x-transition class="flex justify-end">
                    <button type="submit" class="bg-slate-900 hover:bg-black text-white text-[11px] font-black uppercase tracking-wider px-4 py-2 rounded shadow transition-all active:scale-95 cursor-pointer">
                        Submit Keputusan
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- LIVE PREVIEW FORM AREA (PRINT VIEW TARGET - DIKUNCI TOTAL TANPA PERUBAHAN STRUKTUR INTERN) -->
    <div id="print-target-box" class="print:m-0 print:p-0">
        <div class="bg-white text-black p-8 sm:p-10 border border-slate-300 rounded-md shadow-sm print:border-none print:shadow-none print:p-0 font-nunito">
            
            <!-- HEAD KOP SURAT FORM -->
            <div class="flex items-center justify-between border-b-4 border-black pb-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 flex items-center justify-center overflow-hidden">
                        <img src="/images/logo-siix.png" class="max-h-full max-w-full object-contain" alt="Logo SIIX" onerror="this.style.display='none'">
                    </div>  
                    <div>
                        <h1 class="text-lg font-black uppercase tracking-tight text-black">PT. SIIX EMS KARAWANG</h1>
                        <p class="text-[10px] font-black text-black tracking-wider uppercase">Electronic Manufacturing Services</p>
                    </div>
                </div>
                <div class="text-right">
                    <h2 class="text-xs font-black uppercase text-black border border-black px-3 py-1 bg-slate-50 tracking-wide rounded-sm">FORM SPAREPART REQUEST</h2>
                    <p class="text-[9px] text-black font-mono font-bold mt-1">Doc No: {{ $reqNo }}</p>
                </div>
            </div>

            <!-- DETAIL CONTENT DATA TABLE -->
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
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">LINE</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase">{{ $line }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">MACHINE NAME</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase">{{ $machineName }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">SPAREPART ID</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase">{{ $sparepartId }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">PART NUMBER</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase">{{ $partNumber }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">SAP CODE</td>
                            <td class="py-2.5 px-4 font-mono font-black text-black tracking-wider">{{ $sapCode }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">Remark</td>
                            <td class="py-2.5 px-4 font-semibold text-black tracking-wide whitespace-normal break-words leading-normal">{{ $remark }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">Quantity Requested</td>
                            <td class="py-2.5 px-4 font-black text-black">{{ $qtyReq }} Pcs</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- DIGITAL SIGNATURE MATRIX GRID -->
            <div class="grid grid-cols-3 gap-0 border border-black text-center text-xs mt-8 rounded-sm overflow-hidden">
                
                <!-- 1. Requested By (Production Department) -->
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-black border-b border-black py-1 uppercase tracking-wider text-[9px] text-black">Requested By</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        <div class="absolute inset-0 z-10 flex items-center justify-center p-1" x-show="prodSignatureImg">
                            <img :src="prodSignatureImg" class="max-h-full max-w-full object-contain mx-auto my-auto block">
                        </div>
                        <div class="z-30 px-2 my-auto" x-show="!prodSignatureImg">
                            <div class="text-green-600 font-mono text-[9px] font-black uppercase tracking-tighter border border-green-300 bg-green-50 py-0.5 rounded mx-auto max-w-[130px]">
                                VERIFIED
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-black uppercase text-black tracking-wide truncate">
                            {{ $name !== '-' ? $name : '( _________________ )' }}
                        </p>
                        <p class="text-[9px] text-black font-black uppercase mt-0.5">Production Department</p>
                    </div>
                </div>

                <!-- 2. Checked By (Engineering Staff) -->
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-black border-b border-black py-1 uppercase tracking-wider text-[9px] text-black">Checked By</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        <div class="absolute inset-0 z-10 flex items-center justify-center p-1" x-show="staffSignatureImg">
                            <img :src="staffSignatureImg" class="max-h-full max-w-full object-contain mx-auto my-auto block">
                        </div>
                        <div class="z-30 my-auto px-2" x-show="!staffSignatureImg">
                            <template x-if="currentStatus === 'rejected'">
                                <div class="inline-block border-4 border-double border-red-600 text-red-600 font-black text-[12px] uppercase tracking-widest px-2.5 py-0.5 rounded transform -rotate-12 shadow-[0_2px_4px_rgba(220,38,38,0.15)] bg-white/90 font-mono scale-110">
                                    REJECTED
                                </div>
                            </template>
                            <template x-if="currentStatus !== 'rejected'">
                                <span class="text-slate-400 text-[9px] font-black">( Pending Stage )</span>
                            </template>
                        </div>
                    </div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-black uppercase text-black tracking-wide truncate">
                            {{ $isHistory && $log->approver_name && str_contains(strtolower($log->status), 'check') ? $log->approver_name : ( !empty($engSign) ? 'Verified Staff' : '( _________________ )' ) }}
                        </p>
                        <p class="text-[9px] text-black font-black uppercase mt-0.5">Staff Engineering</p>
                    </div>
                </div>

                <!-- 3. Approved By (Engineering Admin / SPV) -->
                <div class="flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-black border-b border-black py-1 uppercase tracking-wider text-[9px] text-black">Approved By</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        <div class="absolute inset-0 z-10 flex items-center justify-center p-1" x-show="spvSignatureImg">
                            <img :src="spvSignatureImg" class="max-h-full max-w-full object-contain mx-auto my-auto block">
                        </div>
                        <div class="z-30 my-auto px-2" x-show="!spvSignatureImg">
                            <template x-if="currentStatus === 'rejected'">
                                <div class="inline-block border-4 border-double border-red-600 text-red-600 font-black text-[12px] uppercase tracking-widest px-2.5 py-0.5 rounded transform -rotate-12 shadow-[0_2px_4px_rgba(220,38,38,0.15)] bg-white/90 font-mono scale-110">
                                    REJECTED
                                </div>
                            </template>
                            <template x-if="currentStatus !== 'rejected'">
                                <span class="text-slate-400 text-[9px] font-black">( Pending Stage )</span>
                            </template>
                        </div>
                    </div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-black uppercase text-black tracking-wide truncate">
                            {{ $isHistory && $log->approver_name && str_contains(strtolower($log->status), 'appr') ? $log->approver_name : ( !empty($spvSign) ? 'Verified Admin' : '( _________________ )' ) }}
                        </p>
                        <p class="text-[9px] text-black font-black uppercase mt-0.5">Admin / SPV Engineering</p>
                    </div>
                </div>
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