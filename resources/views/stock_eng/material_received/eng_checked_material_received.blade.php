@extends('admin')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;900&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
[x-cloak] { display: none !important; }

@media print {
    body * {
        visibility: hidden;
        background: white !important;
        color: black !important;
    }
    #print-target-box, #print-target-box * {
        visibility: visible;
    }
    #print-target-box {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        box-shadow: none !important;
    }
}
</style>

<div class="mx-auto w-full max-w-7xl pb-12 px-4 sm:px-6 font-nunito text-black dark:text-white" x-data="materialReceivedCheckHandler()" x-cloak>
    
    <!-- HEADER SECTION -->
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 font-nunito print:hidden">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-black dark:text-white tracking-tight">CHECK MATERIAL RECEIVED</h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-600 dark:text-slate-400">Engineering Routing Verification Platform</p>
        </div>
        
        <div class="flex gap-2 self-start sm:self-center">
            <button type="button" @click="generatePDF()" class="flex items-center gap-1.5 bg-gradient-to-r from-red-700 via-red-600 to-red-500 hover:opacity-90 text-white rounded-md px-4 py-2 text-xs font-bold uppercase tracking-wide transition-all shadow-sm active:scale-95 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4H7v4a2 2 0 002 2zM9 9V5a2 2 0 012-2h2a2 2 0 012 2v4M7 13h10" />
                </svg>
                DOWNLOAD PDF
            </button>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-5 p-3 text-xs text-red-900 rounded-md bg-red-50 font-bold border border-red-200 print:hidden">
            <ul class="list-disc pl-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- MAIN INPUT FORM CARD -->
    <div class="bg-white dark:bg-boxdark border border-slate-300 dark:border-strokedark rounded-md shadow-sm overflow-hidden print:hidden mb-10">
        <form id="materialCheckForm" action="{{ route('eng.material.receiving.update', $receiving->id) }}" method="POST" @submit.prevent="submitForm">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="checked_signature" x-bind:value="engSignaturePathHidden">

            <div class="p-5 sm:p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- LEFT SIDE: PARAMETER DATA INPUTS -->
                    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- 1. NIK -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-black uppercase text-black tracking-wider">1. NIK COSTING (PREPARER)</label>
                            <input type="text" x-model="costing_nik" readonly class="w-full rounded-md border border-slate-300 bg-slate-100 py-2 px-3 text-xs font-bold text-black cursor-not-allowed outline-none dark:bg-meta-4/30">
                        </div>

                        <!-- 2. NAME -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-black uppercase text-black tracking-wider">2. NAME</label>
                            <input type="text" x-model="costing_name" readonly class="w-full rounded-md border border-slate-300 bg-slate-100 py-2 px-3 text-xs font-bold text-black cursor-not-allowed outline-none dark:bg-meta-4/30">
                        </div>

                        <!-- 3. PR Reference No -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-black uppercase text-black tracking-wider">3. PR REFERENCE NO</label>
                            <input type="text" x-model="pr_no_text" readonly class="w-full rounded-md border border-slate-300 bg-slate-100 py-2 px-3 text-xs font-bold text-black cursor-not-allowed outline-none dark:bg-meta-4/30">
                        </div>

                        <!-- 4a. Sparepart ID -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-black uppercase text-black tracking-wider">4. SPAREPART ID</label>
                            <input type="text" x-model="sparepart_id" readonly class="w-full rounded-md border border-slate-300 bg-slate-100 py-2 px-3 text-xs font-bold text-black cursor-not-allowed outline-none dark:bg-meta-4/30">
                        </div>

                        <!-- 4b. Part Number -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-black uppercase text-black tracking-wider">PART NUMBER</label>
                            <input type="text" x-model="part_number" readonly class="w-full rounded-md border border-slate-300 bg-slate-100 py-2 px-3 text-xs font-bold text-black cursor-not-allowed outline-none dark:bg-meta-4/30">
                        </div>

                        <!-- 4c. SAP Code -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-black uppercase text-black tracking-wider">SAP CODE</label>
                            <input type="text" x-model="sap_code" readonly class="w-full rounded-md border border-slate-300 bg-slate-100 py-2 px-3 text-xs font-bold text-black cursor-not-allowed outline-none dark:bg-meta-4/30">
                        </div>

                        <!-- 4d. Category -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-black uppercase text-black tracking-wider">CATEGORY</label>
                            <input type="text" x-model="category" readonly class="w-full rounded-md border border-slate-300 bg-slate-100 py-2 px-3 text-xs font-bold text-black cursor-not-allowed outline-none dark:bg-meta-4/30">
                        </div>

                        <!-- 5. Qty PR Open Balance -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-black uppercase text-black tracking-wider">5. QTY PR OPEN BALANCE</label>
                            <input type="text" x-model="qty_pr_req" readonly class="w-full rounded-md border border-slate-300 bg-slate-100 py-2 px-3 text-xs font-bold text-slate-700 cursor-not-allowed outline-none dark:bg-meta-4/30">
                        </div>

                        <!-- 6. Qty Received -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-black uppercase text-black tracking-wider">6. QTY RECEIVED</label>
                            <input type="number" name="qty_received" x-model="qty_received" required @input="calculateGap()" class="w-full rounded-md border border-slate-400 bg-white py-2 px-3 text-xs font-bold text-black outline-none transition focus:border-indigo-600 dark:bg-transparent dark:text-white">
                        </div>

                        <!-- 7. Qty Gap -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-black uppercase text-black tracking-wider">7. QTY GAP (REMAINING BALANCE)</label>
                            <div class="w-full rounded-md border py-2 px-3 text-xs font-black outline-none transition shadow-sm flex items-center justify-between"
                                 :class="qty_gap == 0 ? 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/20 dark:text-amber-300'">
                                <span x-text="qty_gap == 0 ? '0 Pcs' : '-' + qty_gap + ' Pcs'">0 Pcs</span>
                                <span :class="qty_gap == 0 ? 'bg-emerald-600 text-white' : 'bg-amber-500 text-white'" 
                                      class="px-2.5 py-0.5 rounded text-[10px] font-black tracking-wider transition-all" 
                                      x-text="qty_gap == 0 ? 'CLOSED' : 'OPEN'">
                                </span>
                            </div>
                        </div>

                        <!-- Remark / Condition -->
                        <div class="flex flex-col gap-1.5 sm:col-span-2">
                            <label class="text-xs font-black uppercase text-black tracking-wider">REMARK / CONDITION</label>
                            <textarea name="remark" x-model="remark" rows="2" placeholder="Tulis catatan kondisi material kedatangan barang..." required class="w-full rounded-md border border-slate-400 bg-white py-2 px-3 text-xs font-bold resize-none text-black outline-none transition focus:border-indigo-600 dark:bg-transparent dark:text-white"></textarea>
                        </div>
                    </div>

                    <!-- RIGHT SIDE: E-SIGNATURE AUTHORIZATION DISPLAY -->
                    <div class="flex flex-col justify-between bg-slate-50 dark:bg-slate-900/40 p-4 rounded-md border border-slate-300 dark:border-strokedark relative z-0">
                        <div>
                            <div class="border-b border-slate-300 dark:border-strokedark pb-2 mb-3">
                                <label class="text-xs font-black uppercase tracking-wider text-black dark:text-gray-200">Checked By Engineering / Admin</label>
                                <p class="text-[10px] text-black font-bold mt-0.5">Otorisasi pemeriksaan digital terikat dengan akun Anda</p>
                            </div>

                            <div class="relative w-full h-28 bg-white dark:bg-slate-950 border border-slate-300 dark:border-gray-800 rounded-md flex items-center justify-center p-2 overflow-hidden shadow-inner">
                                <div class="absolute inset-0 z-10 flex items-center justify-center p-2" x-show="engSignatureImg">
                                    <img :src="engSignatureImg" class="max-h-full max-w-full object-contain mx-auto my-auto block">
                                </div>
                                
                                <div class="z-30 text-center" x-show="!engSignatureImg">
                                    <div class="text-indigo-600 dark:text-indigo-400 font-mono text-[9px] uppercase tracking-wider border border-indigo-200 dark:border-indigo-900 bg-indigo-50 px-2.5 py-1.5 rounded-md">
                                        Secure E-Sign Dynamic<br>
                                        <span class="text-[8px] text-black font-sans font-bold tracking-normal" x-text="eng_name ? 'Linked to: ' + eng_name : 'Waiting...'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-wider pt-2 border-t border-slate-300 dark:border-strokedark mt-3">
                            <span class="text-black dark:text-white">STATUS VERIFIKASI: 
                                <span :class="engSignatureImg ? 'bg-emerald-600 text-white' : 'bg-slate-600 text-white'" class="ml-1 px-2.5 py-1 rounded text-[9px] font-black tracking-wide" x-text="engSignatureImg ? 'READY TO VERIFY' : 'NONE'"></span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- CONTAINER ACTION FOOTER BUTTONS -->
                <div class="mt-6 pt-4 border-t border-slate-200 dark:border-strokedark flex flex-col sm:flex-row justify-end gap-2.5">
                    <a href="{{ route('eng.material.receiving.index') }}" class="w-full sm:w-auto text-center bg-slate-100 hover:bg-slate-200 text-black border-2 border-slate-500 rounded-md px-4 py-1.5 text-xs font-black uppercase tracking-wider transition-all active:scale-95 cursor-pointer">
                        Back To List
                    </a>

                    <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-500 hover:opacity-90 text-white rounded-md px-4 py-1.5 text-xs font-black uppercase tracking-wider transition-all active:scale-95 border-none cursor-pointer shadow-md">
                        Checked MR Document
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- LIVE PREVIEW FORM AREA (PRINT TARGET) -->
    <div id="print-target-box" class="print:m-0 print:p-0">
        <div class="bg-white text-black p-8 sm:p-10 border border-slate-300 rounded-md shadow-sm print:border-none print:shadow-none print:p-0 font-nunito">
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
                    <h2 class="text-xs font-black uppercase text-black border border-black px-3 py-1 bg-slate-50 tracking-wide rounded-sm">MATERIAL RECEIVED REPORT</h2>
                    <p class="text-[9px] text-black font-mono font-bold mt-1" x-text="mr_no ? 'Doc No: ' + mr_no : 'Doc No: MR000000'"></p>
                </div>
            </div>

            <div class="mb-6">
                <table class="w-full border-collapse text-xs border border-black">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">NIK COSTING STAFF</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase" x-text="costing_nik || ''">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">NAME</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase" x-text="costing_name || ''">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">PR REFERENCE NO</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase" x-text="pr_no_text || ''">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">SPAREPART ID</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase" x-text="sparepart_id || ''">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">PART NUMBER</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase" x-text="part_number || ''">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">SAP CODE</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase" x-text="sap_code || ''">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">CATEGORY</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase" x-text="category || ''">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">QTY PR OPEN BALANCE</td>
                            <td class="py-2.5 px-4 font-black text-black" x-text="qty_pr_req ? qty_pr_req + ' Pcs' : '-'">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">QTY RECEIVED</td>
                            <td class="py-2.5 px-4 font-black text-indigo-600 font-extrabold" x-text="qty_received ? qty_received + ' Pcs' : '0 Pcs'">0 Pcs</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">QTY GAP</td>
                            <td class="py-2.5 px-4 font-black font-extrabold flex items-center justify-between" :class="qty_gap == 0 ? 'text-emerald-600' : 'text-amber-600'">
                                <span x-text="qty_gap == 0 ? '0 Pcs' : '-' + qty_gap + ' Pcs'">0 Pcs</span>
                                <span :class="qty_gap == 0 ? 'border border-emerald-600 bg-emerald-50 text-emerald-700' : 'border border-amber-500 bg-amber-50 text-amber-700'" 
                                      class="px-2 py-0.5 rounded text-[9px] font-black tracking-wider uppercase print:inline-block" 
                                      x-text="qty_gap == 0 ? 'STATUS: CLOSED' : 'STATUS: OPEN'">
                                </span>
                            </td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">REMARK / CONDITION</td>
                            <td class="py-2.5 px-4 font-mono font-black text-black tracking-wider" x-text="remark || ''">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- DIGITAL SIGNATURE FOOTER STEP BY STEP -->
            <div class="grid grid-cols-3 gap-0 border border-black text-center text-xs mt-8 rounded-sm overflow-hidden">
                <!-- 1. Costing (Prepared) -->
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-black border-b border-black py-1 uppercase tracking-wider text-[9px] text-black">Prepared By (Costing)</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        <div class="absolute inset-0 z-10 flex items-center justify-center p-1" x-show="costingSignatureImg">
                            <img :src="costingSignatureImg" class="max-h-full max-w-full object-contain mx-auto my-auto block">
                        </div>
                        <div class="z-30 px-2 my-auto" x-show="costing_name && !costingSignatureImg">
                            <div class="text-green-600 font-mono text-[9px] font-black uppercase tracking-tighter border border-green-300 bg-green-50 py-0.5 rounded mx-auto max-w-[130px]">
                                VERIFIED
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-black uppercase text-black tracking-wide truncate" x-text="costing_name || '-'"></p>
                        <p class="text-[9px] text-black font-black uppercase mt-0.5">Costing Section</p>
                    </div>
                </div>

                <!-- 2. Engineering Staff / Admin (Checked) -->
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-black border-b border-black py-1 uppercase tracking-wider text-[9px] text-black">Checked By (Engineering / Admin)</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        <div class="absolute inset-0 z-10 flex items-center justify-center p-1" x-show="engSignatureImg">
                            <img :src="engSignatureImg" class="max-h-full max-w-full object-contain mx-auto my-auto block">
                        </div>
                        <div class="z-30 px-2 my-auto" x-show="eng_name && !engSignatureImg">
                            <div class="text-indigo-600 font-mono text-[9px] font-black uppercase tracking-tighter border border-indigo-300 bg-indigo-50 py-0.5 rounded mx-auto max-w-[130px]">
                                CHECKING STAGE
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-black uppercase text-black tracking-wide truncate" x-text="eng_name || '( _________________ )'"></p>
                        <p class="text-[9px] text-black font-black uppercase mt-0.5">Engineering / Admin</p>
                    </div>
                </div>

                <!-- 3. Admin (Approved) -->
                <div class="flex flex-col justify-between h-36 bg-white">
                    <div class="bg-slate-50 font-black border-b border-black py-1 uppercase tracking-wider text-[9px] text-black">Approved By (Admin)</div>
                    <div class="text-slate-500 text-[9px] font-black my-auto italic">( Pending Stage )</div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white"><p class="font-black uppercase text-black">( _________________ )</p></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function materialReceivedCheckHandler() {
        const currentEngNik = "{{ auth()->check() ? auth()->user()->nik : '' }}";
        const currentEngName = "{{ auth()->check() ? auth()->user()->name : '' }}";
        const currentEngSignature = "{{ auth()->check() && auth()->user()->signature_path ? auth()->user()->signature_path : '' }}";
        
        return {
            costing_nik: "{{ $receiving->user->nik ?? '-' }}",
            costing_name: "{{ $receiving->user->name ?? '-' }}",
            mr_no: "{{ $receiving->no_mr ?? '' }}",
            pr_no_text: "{{ $receiving->purchaseRequest->no_pr ?? '-' }}",
            
            sparepart_id: "{{ $receiving->purchaseRequest->sparepart->sparepart_id ?? '-' }}",
            part_number: "{{ $receiving->purchaseRequest->sparepart->part_number ?? '-' }}",
            sap_code: "{{ $receiving->purchaseRequest->sparepart->sap_code ?? '-' }}",
            category: "{{ $receiving->purchaseRequest->sparepart->category ?? '-' }}",

            // Mengunci Qty PR Open Balance dasar dokumen asli
            qty_pr_req: parseInt("{{ $receiving->qty_pr_open_balance ?? ($receiving->purchaseRequest->qty_pr ?? 0) }}") || 0,
            qty_received: parseInt("{{ old('qty_received', $receiving->qty_received) }}"), 
            qty_gap: 0,
            remark: {!! json_encode(old('remark', $receiving->remark ?? '')) !!},
            
            // Tanda Tangan Costing (Diambil dari berkas lama DB)
            costingSignatureImg: "{{ $receiving->prepared_signature ? (str_starts_with($receiving->prepared_signature, 'http') ? $receiving->prepared_signature : asset('storage/' . $receiving->prepared_signature)) : null }}",

            // Tanda Tangan Engineering Staff (User Aktif Saat Ini)
            eng_nik: currentEngNik,
            eng_name: currentEngName,
            engSignaturePathHidden: currentEngSignature,
            engSignatureImg: currentEngSignature ? (currentEngSignature.startsWith('http') ? currentEngSignature : "{{ asset('storage') }}/" + currentEngSignature.replace(/^\/?(storage\/)?/, '')) : null,

            init() {
                this.calculateGap();
            },

            calculateGap() {
                let req = parseInt(this.qty_pr_req) || 0;
                let act = parseInt(this.qty_received) || 0;
                
                if (act < 0) {
                    this.qty_received = 0;
                    act = 0;
                }
                
                this.qty_gap = Math.max(0, req - act);
            },
    
            generatePDF() { window.print(); },
    
            submitForm() {
                const form = document.getElementById('materialCheckForm');
                if (!form.reportValidity()) return;

                let req = parseInt(this.qty_pr_req) || 0;
                let act = parseInt(this.qty_received) || 0;

                if (act > req) {
                    Swal.fire({
                        title: 'Batas Qty Terlewati!',
                        text: `QTY RECEIVED (${act} Pcs) tidak boleh melebihi jumlah QTY PR OPEN BALANCE (${req} Pcs).`,
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                    return;
                }
                
                Swal.fire({
                    title: 'Verifikasi & Simpan Check Dokumen?',
                    text: "Dokumen ini akan diteruskan ke Admin untuk persetujuan akhir.",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#cbd5e1',
                    confirmButtonText: 'Ya, Tandai Checked!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        HTMLFormElement.prototype.submit.call(form);
                    }
                });
            }
        }
    }
</script>
@endsection
