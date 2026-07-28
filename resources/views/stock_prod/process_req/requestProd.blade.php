@extends('admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10 text-slate-900 dark:text-white" x-data="signatureFormHandler()" x-cloak>
    
    <!-- HEADER BAR ACTION -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
        <div>
            <h2 class="text-lg font-bold text-slate-950 dark:text-white" x-text="draft_id ? 'EDIT DRAFT SPAREPART REQUEST' : 'CREATE SPAREPART REQUEST'"></h2>
            <p class="text-xs text-gray-500 mt-1">PT SIIX EMS KARAWANG • Production Electronic Authorization Platform</p>
        </div>
        
        <div class="self-start sm:self-center">
            <button type="button" @click="generatePDF()" class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-red-600 to-rose-500 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:opacity-90 transition-all active:scale-95 uppercase tracking-wider cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4H7v4a2 2 0 002 2zM9 9V5a2 2 0 012-2h2a2 2 0 012 2v4M7 13h10" />
                </svg>
                Cetak / Download PDF
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-5 p-4 text-sm text-green-800 rounded-lg bg-green-50 font-bold border border-green-200 print:hidden">
            {{ session('success') }}
        </div>
    @endif

    <!-- MAIN INPUT FORM CARD -->
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 md:p-8 dark:border-gray-800 dark:bg-white/[0.03] print:hidden mb-10 shadow-sm">
        <form id="requestForm" :action="getFormAction()" method="POST" @submit.prevent="handleFormAction($event)">
            @csrf
            
            <template x-if="draft_id">
                <input type="hidden" name="_method" value="PUT">
            </template>
            
            <input type="hidden" name="action_type" x-model="actionType">
            <input type="hidden" name="draft_id" x-model="draft_id">
            
            <input type="hidden" name="signature_data" x-bind:value="signatureImg">
            <input type="hidden" name="stamp_data" x-bind:value="stampImg">

            <!-- Hidden Foreign Key ID fields untuk migration baru -->
            <input type="hidden" name="list_line_production_id" value="{{ $activeLine->id ?? '' }}">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- LEFT SIDE: PARAMETER DATA INPUTS -->
                <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">
                    
                    <!-- 1. Requestor (Otomatis & Readonly) -->
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Name / Requestor (Auto)</label>
                        <input type="text" name="requestor" x-model="requestor" readonly class="w-full bg-slate-100 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-bold text-gray-500 cursor-not-allowed outline-none">
                    </div>

                    <!-- 2. Line Machine (Otomatis dari NIK & Readonly) -->
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Line Machine / Area (Auto)</label>
                        <input type="text" name="line_machine" x-model="line_machine" readonly class="w-full bg-slate-100 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-bold text-gray-500 cursor-not-allowed outline-none">
                    </div>

                    <!-- 3. Dropdown Sparepart (Pilih ID, isi data nama otomatis) -->
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Pilih Sparepart ID</label>
                        <select name="sparepart_id" x-model="selected_sparepart_id" @change="updateSparepartDetails()" required class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium text-black dark:text-white outline-none transition focus:border-blue-500">
                            <option value="">-- Pilih Sparepart ID --</option>
                            @foreach($spareparts as $part)
                                <option value="{{ $part->id }}">ID: {{ $part->sparepart_id }} | {{ $part->part_number }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="sparepart_name" x-model="sparepart_name">
                    </div>

                    <!-- 4. SAP Code (Otomatis dari Pilihan Sparepart & Readonly) -->
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold text-slate-700 dark:text-gray-300">SAP Code (Auto)</label>
                        <input type="text" name="sap_code" x-model="sap_code" readonly class="w-full bg-slate-100 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-bold text-gray-500 cursor-not-allowed outline-none">
                    </div>

                    <!-- 5. Quantity Requested (Manual) -->
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Quantity Requested</label>
                        <input type="number" name="qty_req" x-model="qty_req" min="1" required class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-bold text-black dark:text-white outline-none transition focus:border-blue-500">
                    </div>

                    <!-- 6. Remark / Keterangan (Manual) -->
                    <div class="flex flex-col gap-2 sm:col-span-2">
                        <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Remark / Keterangan</label>
                        <textarea name="remark" x-model="remark" rows="2" placeholder="Reason for change..." required class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium resize-none text-black dark:text-white outline-none transition focus:border-blue-500"></textarea>
                    </div>
                </div>

                <!-- RIGHT SIDE: LIVE DATABASE DIGITAL AUTHORIZATION DISPLAY -->
                <div class="flex flex-col justify-between bg-slate-50 dark:bg-slate-900/40 p-5 rounded-xl border border-gray-200 dark:border-gray-800 relative z-0">
                    <div>
                        <div class="border-b border-gray-200 dark:border-gray-800 pb-2 mb-3">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-gray-200">Database Authorization</label>
                            <p class="text-[10px] text-slate-400 mt-0.5">Otorisasi digital terikat otomatis dengan sistem</p>
                        </div>

                        <!-- Tampilan Status TTD Database Aktif -->
                        <div class="relative w-full h-32 bg-white dark:bg-slate-950 border border-gray-200 dark:border-gray-800 rounded-lg flex items-center justify-center p-2 overflow-hidden shadow-inner">
                            <div class="absolute inset-0 z-10 flex items-center justify-center p-2" x-show="signatureImg">
                                <img :src="signatureImg" class="max-h-full max-w-full object-contain mx-auto my-auto block">
                            </div>
                            <div class="absolute inset-0 z-20 flex items-center justify-center p-0 pointer-events-none" x-show="stampImg">
                                <img :src="stampImg" class="max-h-full max-w-full object-contain mx-auto my-auto block mix-blend-multiply opacity-80">
                            </div>
                            
                            <!-- Fallback Status Tanda Tangan Aktif -->
                            <div class="z-30 text-center" x-show="!signatureImg && !stampImg">
                                <div class="text-blue-600 dark:text-blue-400 font-mono text-[9px] uppercase tracking-wider border border-blue-200 dark:blue-900 bg-blue-50/50 dark:bg-blue-950/30 px-3 py-1.5 rounded-lg">
                                    ● Secure E-Sign Dynamic<br>
                                    <span class="text-[8px] text-gray-400 font-sans tracking-normal" x-text="requestor ? 'Linked to: ' + requestor : 'Waiting for requestor name...'"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Status Indicators -->
                    <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-wider pt-2 border-t border-gray-200 dark:border-gray-800 mt-3">
                        <span :class="signatureImg ? 'text-green-600' : 'text-gray-400'">TTD DATA: <span x-text="signatureImg ? '✓ ACTIVE' : 'NONE'"></span></span>
                        <span :class="stampImg ? 'text-green-600' : 'text-gray-400'">STAMP: <span x-text="stampImg ? '✓ ATTACHED' : 'NONE'"></span></span>
                    </div>
                </div>
            </div>

            <!-- CONTAINER ACTION FOOTER BUTTONS -->
            <div class="mt-8 pt-5 border-t border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row justify-end gap-3">
                <button type="button" @click="resetAll" class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-gray-300 px-5 py-2.5 rounded-lg text-sm font-bold transition-all active:scale-95 uppercase tracking-wider cursor-pointer">
                    Reset Form
                </button>
                
                <button type="button" @click="submitAs('draft')" class="w-full sm:w-auto bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition-all active:scale-95 uppercase tracking-wider cursor-pointer shadow-sm">
                    Save As Draft
                </button>

                <button type="button" @click="submitAs('submit')" class="w-full sm:w-auto bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-500 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-md hover:opacity-90 transition-all active:scale-95 uppercase tracking-wider cursor-pointer">
                    Submit Request
                </button>
            </div>
        </form>
    </div>

    <!-- LIVE PREVIEW FORM AREA (PRINT TEMPLATE TARGET) -->
    <div id="print-target-box" class="print:m-0 print:p-0">
        <h3 class="text-xs font-bold uppercase text-slate-400 tracking-wider mb-3 flex items-center gap-2 print:hidden">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            Live Preview Form Dokumen Cetak
        </h3>
        
        <div class="bg-white text-black p-8 sm:p-12 border border-slate-300 rounded-xl shadow-sm print:border-none print:shadow-none print:p-0 font-sans">
            
            <!-- Company Letterhead Doc -->
            <div class="flex items-center justify-between border-b-4 border-black pb-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 flex items-center justify-center overflow-hidden">
                        <img src="/images/logo-siix.png" class="max-h-full max-w-full object-contain" alt="Logo SIIX" onerror="this.style.display='none'">
                    </div>  
                    <div>
                        <h1 class="text-lg font-black uppercase tracking-tight text-black">PT. SIIX EMS KARAWANG</h1>
                        <p class="text-[9px] font-bold text-slate-500 tracking-wider uppercase">Electronic Manufacturing Services </p>
                    </div>
                </div>
                <div class="text-right">
                    <h2 class="text-sm font-black uppercase text-black border border-black px-3 py-1 bg-slate-50 tracking-wide">FORM REQUEST NOZZLE</h2>
                    <p class="text-[8px] text-slate-500 font-mono mt-1" x-text="request_no ? 'Doc No: ' + request_no : 'Doc No: REQ-PRD-SIIX-001'"></p>
                </div>
            </div>

            <!-- Parameters Table Structure -->
            <div class="mb-6">
                <table class="w-full border-collapse text-xs border border-black">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black text-slate-800">Nama Peminta</td>
                            <td class="py-2.5 px-4 font-bold text-black uppercase" x-text="requestor || '-'">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black text-slate-800">LINE / AREA</td>
                            <td class="py-2.5 px-4 font-bold text-black uppercase" x-text="line_machine || '-'">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black text-slate-800">NO NOZLE SPAREPART</td>
                            <td class="py-2.5 px-4 font-bold text-black uppercase" x-text="sparepart_name || '-'">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black text-slate-800">SAP CODE</td>
                            <td class="py-2.5 px-4 font-mono font-bold text-black tracking-wider" x-text="sap_code || '-'">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black text-slate-800">Remark / Keterangan</td>
                            <td class="py-2.5 px-4 font-mono font-bold text-black tracking-wider" x-text="remark || '-'">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black text-slate-800">Quantity Requested</td>
                            <td class="py-2.5 px-4 font-bold text-black" x-text="qty_req + ' Pcs'">1 Pcs</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Approval Box Section Layout -->
            <div class="grid grid-cols-3 gap-0 border border-black text-center text-xs mt-8">
                <!-- Box 1: Requested By (Production) -->
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 uppercase tracking-wider text-[9px] text-slate-800">Requested By</div>
                    
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        <div class="absolute inset-0 z-10 flex items-center justify-center p-1" x-show="signatureImg">
                            <img :src="signatureImg" class="max-h-full max-w-full object-contain mx-auto my-auto block" alt="Signature">
                        </div>
                        
                        <div class="absolute inset-0 z-20 flex items-center justify-center p-0 pointer-events-none" x-show="stampImg">
                            <img :src="stampImg" class="max-h-full max-w-full object-contain mx-auto my-auto block mix-blend-multiply opacity-95" alt="Company Stamp">
                        </div>

                        <!-- Fallback Otomatis System Verified jika data gambar kosong -->
                        <div class="z-30 px-2 my-auto" x-show="requestor && !signatureImg && !stampImg">
                            <div class="text-green-600 font-mono text-[8px] uppercase tracking-tighter border border-green-200 bg-green-50/50 py-0.5 rounded mx-auto max-w-[130px]">
                                ✓ System Verified<br>
                                <span class="text-[7px] font-sans" x-text="'By: ' + requestor"></span>
                            </div>
                        </div>

                        <span x-show="!requestor && !signatureImg && !stampImg" class="text-slate-300 italic text-[9px] m-auto">( No Signature )</span>
                    </div>

                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-bold uppercase text-black underline tracking-wide truncate" x-text="requestor || '( _________________ )'"></p>
                        <p class="text-[8px] text-slate-500 font-bold uppercase mt-0.5">Production Department</p>
                    </div>
                </div>

                <!-- Box 2: Checked By (Engineering Staff) -->
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 uppercase tracking-wider text-[9px] text-slate-800">Checked By</div>
                    <div class="text-slate-300 italic text-[8px] font-medium my-auto">( Pending Stage )</div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-bold uppercase text-black">( _________________ )</p>
                        <p class="text-[8px] text-slate-500 font-bold uppercase mt-0.5">Staff Engineering</p>
                    </div>
                </div>

                <!-- Box 3: Approved By (Engineering Supervisor) -->
                <div class="flex flex-col justify-between h-36 bg-white">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 uppercase tracking-wider text-[9px] text-slate-800">Approved By</div>
                    <div class="text-slate-300 italic text-[8px] font-medium my-auto">( Pending Stage )</div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-bold uppercase text-black">( _________________ )</p>
                        <p class="text-[8px] text-slate-500 font-bold uppercase mt-0.5">SPV Engineering</p>
                    </div>
                </div>
            </div>

            <!-- Footer Dashed Print Info -->
            <div class="mt-8 border-t border-dashed border-slate-300 pt-4 text-center print:block hidden">
                <p class="text-[8px] text-slate-400 font-mono uppercase tracking-widest">SIIX-NOZZLE-TRACKING-SYSTEM • CONFIDENTIAL QUALITY DOCUMENT</p>
            </div>

        </div>
    </div>
</div>

<script>
    function signatureFormHandler() {
        const userSignature = "{{ auth()->check() && auth()->user()->signature_path ? auth()->user()->signature_path : '' }}";
        const requestSignature = "{{ optional($requestData ?? null)->production_signature ?? '' }}";
        const activeSignaturePath = requestSignature || userSignature;

        // Parsing data Master Sparepart dari controller ke dalam array JavaScript
        const sparepartsList = {!! json_encode($spareparts ?? []) !!};
    
        return {
            // Otomatis isi data profile user login[cite: 2]
            requestor: '{{ old('requestor', optional($requestData ?? null)->requestor ?? (auth()->check() ? auth()->user()->name : '')) }}',
            
            // Otomatis gabungkan string Line dari data controller[cite: 4]
            line_machine: '{{ old('line_machine', optional($requestData ?? null)->line_machine ?? ($activeLine ? "LINE " . $activeLine->no_line . " - " . $activeLine->name_machine : "")) }}',
            
            // State untuk manajemen data spareparts otomatis[cite: 5, 6]
            selected_sparepart_id: '{{ old('sparepart_id', optional($requestData ?? null)->sparepart_id ?? '') }}',
            sparepart_name: '{{ old('sparepart_name', optional($requestData ?? null)->sparepart_name ?? '') }}',
            sap_code: '{{ old('sap_code', optional($requestData ?? null)->sap_code ?? '') }}',
            
            remark: '{{ old('remark', optional($requestData ?? null)->remark ?? '') }}',
            qty_req: {{ old('qty_req', optional($requestData ?? null)->qty_req ?? 1) }},
            
            draft_id: '{{ optional($requestData ?? null)->id ?? '' }}',
            request_no: '{{ optional($requestData ?? null)->request_no ?? '' }}',
    
            signatureImg: activeSignaturePath 
                ? (activeSignaturePath.startsWith('http') 
                    ? activeSignaturePath 
                    : "{{ asset('storage') }}/" + activeSignaturePath.replace(/^\/?(storage\/)?/, '')) 
                : null,
                
            stampImg: '{{ optional($requestData ?? null)->production_stamp ?? '' }}' 
                ? '{{ asset('storage/' . optional($requestData ?? null)->production_stamp) }}' 
                : null,     
                
            actionType: 'submit',

            // Trigger pembaruan field text saat opsi select id berubah[cite: 5]
            updateSparepartDetails() {
                const part = sparepartsList.find(item => item.id == this.selected_sparepart_id);
                if (part) {
                    this.sparepart_name = part.category + ' (' + part.part_number + ')';
                    this.sap_code = part.sap_code ? part.sap_code : '-';
                } else {
                    this.sparepart_name = '';
                    this.sap_code = '';
                }
            },
    
            generatePDF() {
                window.print();
            },
    
            getFormAction() {
                if (this.draft_id) {
                    return "/production-request/update-draft/" + this.draft_id;
                }
                return "{{ route('prod.request.store') }}";
            },
    
            resetAll() {
                this.requestor = '{{ auth()->check() ? auth()->user()->name : "" }}';
                this.line_machine = '{{ $activeLine ? "LINE " . $activeLine->no_line . " - " . $activeLine->name_machine : "" }}';
                this.selected_sparepart_id = '';
                this.sparepart_name = '';
                this.sap_code = '';
                this.remark = '';
                this.qty_req = 1;
            },
    
            submitAs(type) {
                this.actionType = type;
                document.getElementById('requestForm').dispatchEvent(new Event('submit'));
            },
    
            handleFormAction(e) {
                if (!this.requestor || !this.line_machine || !this.selected_sparepart_id || !this.remark || !this.qty_req) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Data Belum Lengkap!',
                        text: 'Semua kolom input data sparepart wajib diisi terlebih dahulu, Bos!',
                        confirmButtonColor: '#3C50E0'
                    });
                    return false;
                }
    
                if (this.actionType === 'draft') {
                    Swal.fire({
                        title: 'Simpan sebagai Draft?',
                        text: "Data akan disimpan di list internal dengan status DRAFT.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#4A5568',
                        cancelButtonColor: '#cbd5e1',
                        confirmButtonText: 'Ya, Simpan Draft!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('requestForm').submit();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Kirim Request Sekarang?',
                        text: "Request dengan otorisasi digital database Anda akan langsung dikirim ke Engineering Staff.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3C50E0',
                        cancelButtonColor: '#f43f5e',
                        confirmButtonText: 'Ya, Kirim!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('requestForm').submit();
                        }
                    });
                }
            }
        }
    }
</script>

<style>
@page {
    size: portrait;
    margin: 10mm;
}

@media print {
    body * {
        visibility: hidden !important;
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
    }
    body {
        background-color: #ffffff !important;
    }
}
[x-cloak] { display: none !important; }
</style>
@endsection