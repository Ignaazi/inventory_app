@extends('admin')

@section('content')
<!-- Memastikan font Nunito ter-load dengan baik jika belum ada di template utama -->
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;900&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
[x-cloak] { display: none !important; }

/* CSS Khusus Cetak: Menyembunyikan semua elemen kecuali preview form utama */
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

<div class="mx-auto w-full max-w-7xl pb-12 px-4 sm:px-6 font-nunito text-black dark:text-white" x-data="signatureFormHandler()" x-cloak>
    
    <!-- HEADER SECTION -->
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 font-nunito print:hidden">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-black dark:text-white tracking-tight" x-text="draft_id ? 'EDIT DRAFT SPAREPART REQUEST' : 'CREATE SPAREPART REQUEST'"></h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-600 dark:text-slate-400">Production Electronic Authorization Platform</p>
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

    @if(session('success'))
        <div class="mb-5 p-3 text-xs text-green-900 rounded-md bg-green-50 font-bold border border-green-200 print:hidden">
            {{ session('success') }}
        </div>
    @endif

    <!-- MAIN INPUT FORM CARD -->
    <div class="bg-white dark:bg-boxdark border border-slate-300 dark:border-strokedark rounded-md shadow-sm overflow-hidden print:hidden mb-10">
        <form id="requestForm" :action="getFormAction()" method="POST" @submit.prevent="handleFormAction">
            @csrf
            
            <input type="hidden" :name="draft_id ? '_method' : ''" value="PUT">
            <input type="hidden" name="action_type" x-model="actionType">
            <input type="hidden" name="draft_id" x-model="draft_id">
            <input type="hidden" name="signature_data" x-bind:value="signatureImg">

            <div class="p-5 sm:p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- LEFT SIDE: PARAMETER DATA INPUTS -->
                    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- 1. NIK -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-black uppercase text-black tracking-wider">NIK</label>
                            <input type="text" x-model="requestor_nik" readonly class="w-full rounded-md border border-slate-300 bg-slate-100 py-2 px-3 text-xs font-bold text-black cursor-not-allowed outline-none dark:bg-meta-4/30">
                        </div>

                        <!-- 2. NAME -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-black uppercase text-black tracking-wider">Name</label>
                            <input type="text" x-model="requestor_name" readonly class="w-full rounded-md border border-slate-300 bg-slate-100 py-2 px-3 text-xs font-bold text-black cursor-not-allowed outline-none dark:bg-meta-4/30">
                        </div>

                        <!-- 3. Line (SEKARANG DROPDOWN DINAMIS BERDASARKAN MASTER DATA LINE) -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-black uppercase text-black tracking-wider">Line</label>
                            <select name="list_line_production_id" x-model="selected_line_id" @change="updateLineDetails()" required class="w-full rounded-md border border-slate-400 bg-white py-2 px-3 text-xs font-bold text-black outline-none transition focus:border-indigo-600 dark:bg-slate-900 dark:text-white">
                                <option value="" class="text-black">-- Pilih Line Production --</option>
                                @foreach($productionLines as $line)
                                    <option value="{{ $line->id }}" class="text-black">{{ $line->no_line }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 4. Machine Name (OTOMATIS TERISI READ-ONLY SAAT LINE DIPILIH) -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-black uppercase text-black tracking-wider">Machine Name</label>
                            <input type="text" x-model="machine_name" readonly class="w-full rounded-md border border-slate-300 bg-slate-100 py-2 px-3 text-xs font-bold text-black cursor-not-allowed outline-none dark:bg-meta-4/30">
                        </div>

                        <!-- 5. Dropdown Sparepart (Hanya ID) -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-black uppercase text-black tracking-wider">Sparepart ID</label>
                            <select name="sparepart_id" x-model="selected_id" @change="updateSparepartDetails()" required class="w-full rounded-md border border-slate-400 bg-white py-2 px-3 text-xs font-bold text-black outline-none transition focus:border-indigo-600 dark:bg-slate-900 dark:text-white">
                                <option value="" class="text-black">-- Pilih Sparepart ID --</option>
                                @foreach($spareparts as $part)
                                    <option value="{{ $part->id }}" class="text-black">{{ $part->sparepart_id }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 6. SAP Code -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-black uppercase text-black tracking-wider">SAP Code</label>
                            <input type="text" x-model="sap_code" readonly class="w-full rounded-md border border-slate-300 bg-slate-100 py-2 px-3 text-xs font-bold text-black cursor-not-allowed outline-none dark:bg-meta-4/30">
                        </div>

                        <!-- 7. Part Number -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-black uppercase text-black tracking-wider">Part Number</label>
                            <input type="text" x-model="part_number" readonly class="w-full rounded-md border border-slate-300 bg-slate-100 py-2 px-3 text-xs font-bold text-black cursor-not-allowed outline-none dark:bg-meta-4/30">
                        </div>

                        <!-- 8. Quantity Requested -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-black uppercase text-black tracking-wider">Quantity Requested</label>
                            <input type="number" name="qty_req" x-model="qty_req" min="1" required class="w-full rounded-md border border-slate-400 bg-white py-2 px-3 text-xs font-bold text-black outline-none transition focus:border-indigo-600 dark:bg-transparent dark:text-white">
                        </div>

                        <!-- 9. Remark / Keterangan -->
                        <div class="flex flex-col gap-1.5 sm:col-span-2">
                            <label class="text-xs font-black uppercase text-black tracking-wider">Remark</label>
                            <textarea name="remark" x-model="remark" rows="2" placeholder="Reason for change..." required class="w-full rounded-md border border-slate-400 bg-white py-2 px-3 text-xs font-bold resize-none text-black outline-none transition focus:border-indigo-600 dark:bg-transparent dark:text-white"></textarea>
                        </div>
                    </div>

                    <!-- RIGHT SIDE: LIVE DATABASE DIGITAL AUTHORIZATION DISPLAY -->
                    <div class="flex flex-col justify-between bg-slate-50 dark:bg-slate-900/40 p-4 rounded-md border border-slate-300 dark:border-strokedark relative z-0">
                        <div>
                            <div class="border-b border-slate-300 dark:border-strokedark pb-2 mb-3">
                                <label class="text-xs font-black uppercase tracking-wider text-black dark:text-gray-200">Sign Employee</label>
                                <p class="text-[10px] text-black font-bold mt-0.5">Otorisasi digital terikat otomatis dengan sistem</p>
                            </div>

                            <div class="relative w-full h-28 bg-white dark:bg-slate-950 border border-slate-300 dark:border-gray-800 rounded-md flex items-center justify-center p-2 overflow-hidden shadow-inner">
                                <div class="absolute inset-0 z-10 flex items-center justify-center p-2" x-show="signatureImg">
                                    <img :src="signatureImg" class="max-h-full max-w-full object-contain mx-auto my-auto block">
                                </div>
                                
                                <div class="z-30 text-center" x-show="!signatureImg">
                                    <div class="text-indigo-600 dark:text-indigo-400 font-mono text-[9px] uppercase tracking-wider border border-indigo-200 dark:border-indigo-900 bg-indigo-50 px-2.5 py-1.5 rounded-md">
                                        Secure E-Sign Dynamic<br>
                                        <span class="text-[8px] text-black font-sans font-bold tracking-normal" x-text="requestor_name ? 'Linked to: ' + requestor_name : 'Waiting for name...'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-wider pt-2 border-t border-slate-300 dark:border-strokedark mt-3">
                            <span class="text-black dark:text-white">TTD DATA: 
                                <span :class="signatureImg ? 'bg-emerald-600 text-white' : 'bg-slate-600 text-white'" class="ml-1 px-2.5 py-1 rounded text-[9px] font-black tracking-wide" x-text="signatureImg ? 'ACTIVE' : 'NONE'"></span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- CONTAINER ACTION FOOTER BUTTONS -->
                <div class="mt-6 pt-4 border-t border-slate-200 dark:border-strokedark flex flex-col sm:flex-row justify-end gap-2.5">
                    <button type="button" @click="resetAll" class="w-full sm:w-auto bg-slate-100 hover:bg-slate-200 text-black border-2 border-slate-500 rounded-md px-3 py-1.5 text-xs font-black uppercase tracking-wider transition-all active:scale-95 cursor-pointer">
                        Reset Form
                    </button>
                    
                    <button type="button" @click="submitAs('draft')" class="w-full sm:w-auto bg-gradient-to-r from-orange-600 via-orange-500 to-amber-500 hover:opacity-90 text-white rounded-md px-4 py-1.5 text-xs font-black uppercase tracking-wider transition-all active:scale-95 border-none cursor-pointer shadow-sm">
                        Draft
                    </button>

                    <button type="button" @click="submitAs('submit')" class="w-full sm:w-auto bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-500 hover:opacity-90 text-white rounded-md px-4 py-1.5 text-xs font-black uppercase tracking-wider transition-all active:scale-95 border-none cursor-pointer shadow-md">
                        Submit
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- LIVE PREVIEW FORM AREA -->
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
                    <h2 class="text-xs font-black uppercase text-black border border-black px-3 py-1 bg-slate-50 tracking-wide rounded-sm">FORM REQUEST NOZZLE</h2>
                    <p class="text-[9px] text-black font-mono font-bold mt-1" x-text="request_no ? 'Doc No: ' + request_no : 'Doc No: REQPROD001'"></p>
                </div>
            </div>

            <div class="mb-6">
                <table class="w-full border-collapse text-xs border border-black">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">NIK</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase" x-text="requestor_nik || ''">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">NAME</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase" x-text="requestor_name || ''">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">LINE</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase" x-text="line_no || ''">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">MACHINE NAME</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase" x-text="machine_name || ''">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">SPAREPART ID</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase" x-text="sparepart_id_text || ''">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">PART NUMBER</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase" x-text="part_number || ''">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">SAP CODE</td>
                            <td class="py-2.5 px-4 font-mono font-black text-black tracking-wider" x-text="sap_code || ''">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">Remark</td>
                            <td class="py-2.5 px-4 font-mono font-black text-black tracking-wider" x-text="remark || ''">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">Quantity Requested</td>
                            <td class="py-2.5 px-4 font-black text-black" x-text="qty_req + ' Pcs'">1 Pcs</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="grid grid-cols-3 gap-0 border border-black text-center text-xs mt-8 rounded-sm overflow-hidden">
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-black border-b border-black py-1 uppercase tracking-wider text-[9px] text-black">Requested By</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        <div class="absolute inset-0 z-10 flex items-center justify-center p-1" x-show="signatureImg">
                            <img :src="signatureImg" class="max-h-full max-w-full object-contain mx-auto my-auto block">
                        </div>
                        <div class="z-30 px-2 my-auto" x-show="requestor_name && !signatureImg">
                            <div class="text-green-600 font-mono text-[9px] font-black uppercase tracking-tighter border border-green-300 bg-green-50 py-0.5 rounded mx-auto max-w-[130px]">
                                VERIFIED
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-black uppercase text-black tracking-wide truncate" x-text="requestor_name || '( _________________ )'"></p>
                        <p class="text-[9px] text-black font-black uppercase mt-0.5">Production Department</p>
                    </div>
                </div>
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white">
                    <div class="bg-slate-50 font-black border-b border-black py-1 uppercase tracking-wider text-[9px] text-black">Checked By</div>
                    <div class="text-black  text-[9px] font-black my-auto">( Pending Stage )</div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white"><p class="font-black uppercase text-black">( _________________ )</p></div>
                </div>
                <div class="flex flex-col justify-between h-36 bg-white">
                    <div class="bg-slate-50 font-black border-b border-black py-1 uppercase tracking-wider text-[9px] text-black">Approved By</div>
                    <div class="text-black  text-[9px] font-black my-auto">( Pending Stage )</div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white"><p class="font-black uppercase text-black">( _________________ )</p></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function signatureFormHandler() {
        // Data User
        const userNik = "{{ optional($requestData ?? null)->user ? $requestData->user->nik : (auth()->check() ? auth()->user()->nik : '') }}";
        const userName = "{{ optional($requestData ?? null)->user ? $requestData->user->name : (auth()->check() ? auth()->user()->name : '') }}";
        
        // Data Master List Line Integrasi Alpine
        const productionLinesList = {!! json_encode($productionLines ?? []) !!};
        const initialLineId = '{{ old('list_line_production_id', optional($requestData ?? null)->list_line_production_id ?? ($activeLine ? $activeLine->id : '')) }}';

        // Set default text awal
        let lineNoData = "{{ optional($requestData ?? null)->lineProduction ? $requestData->lineProduction->no_line : ($activeLine ? $activeLine->no_line : '-') }}";
        let machineNameData = "{{ optional($requestData ?? null)->lineProduction ? $requestData->lineProduction->name_machine : ($activeLine ? $activeLine->name_machine : '-') }}";

        // Sinkronisasi data awal jika ID line ditemukan
        if (initialLineId) {
            const matchedLine = productionLinesList.find(item => item.id == initialLineId);
            if (matchedLine) {
                lineNoData = matchedLine.no_line;
                machineNameData = matchedLine.name_machine;
            }
        }

        const userSignature = "{{ auth()->check() && auth()->user()->signature_path ? auth()->user()->signature_path : '' }}";
        const requestSignature = "{{ optional($requestData ?? null)->production_signature ?? '' }}";
        const activeSignaturePath = requestSignature || userSignature;
        
        const sparepartsList = {!! json_encode($spareparts ?? []) !!};
        const initialId = '{{ old('sparepart_id', optional($requestData ?? null)->sparepart_id ?? '') }}';
        
        let initialPartNumber = '-';
        let initialSapCode = '-';
        let initialSparepartIdText = '-';

        if (initialId) {
            const matched = sparepartsList.find(item => item.id == initialId);
            if (matched) {
                initialPartNumber = matched.part_number ? matched.part_number : '-';
                initialSapCode = matched.sap_code ? matched.sap_code : '-';
                initialSparepartIdText = matched.sparepart_id;
            }
        }
    
        return {
            requestor_nik: userNik,
            requestor_name: userName,
            selected_line_id: initialLineId,
            linesList: productionLinesList,
            line_no: lineNoData,
            machine_name: machineNameData,
            selected_id: initialId,
            sparepart_id_text: initialSparepartIdText,
            sap_code: initialSapCode,
            part_number: initialPartNumber, 
            remark: '{{ old('remark', optional($requestData ?? null)->remark ?? '') }}',
            qty_req: {{ old('qty_req', optional($requestData ?? null)->qty_req ?? 1) }},
            draft_id: '{{ optional($requestData ?? null)->id ?? '' }}',
            request_no: '{{ optional($requestData ?? null)->request_no ?? '' }}',
            signatureImg: activeSignaturePath ? (activeSignaturePath.startsWith('http') ? activeSignaturePath : "{{ asset('storage') }}/" + activeSignaturePath.replace(/^\/?(storage\/)?/, '')) : null,
            actionType: 'submit',

            // Fungsi update data Line & Nama Mesin secara dinamis otomatis
            updateLineDetails() {
                const line = this.linesList.find(item => item.id == this.selected_line_id);
                if (line) {
                    this.line_no = line.no_line;
                    this.machine_name = line.name_machine;
                } else {
                    this.line_no = '-';
                    this.machine_name = '-';
                }
            },

            updateSparepartDetails() {
                const part = sparepartsList.find(item => item.id == this.selected_id);
                if (part) {
                    this.sparepart_id_text = part.sparepart_id;
                    this.sap_code = part.sap_code ? part.sap_code : '-';
                    this.part_number = part.part_number ? part.part_number : '-';
                } else {
                    this.sparepart_id_text = '-'; this.sap_code = '-'; this.part_number = '-';
                }
            },
    
            generatePDF() { window.print(); },
    
            getFormAction() {
                if (this.draft_id) {
                    return "{{ route('prod.request.updateDraft', ':id') }}".replace(':id', this.draft_id);
                }
                return "{{ route('prod.request.store') }}";
            },
    
            resetAll() {
                this.selected_id = ''; this.sparepart_id_text = '-'; this.sap_code = '-';
                this.part_number = '-'; this.remark = ''; this.qty_req = 1;
                this.selected_line_id = ''; this.line_no = '-'; this.machine_name = '-';
            },
    
            submitAs(type) {
                this.actionType = type;
                const form = document.getElementById('requestForm');
                if (form.reportValidity()) {
                    this.handleFormAction();
                }
            },
    
            handleFormAction() {
                const form = document.getElementById('requestForm');
                const isDraft = this.actionType === 'draft';
                
                Swal.fire({
                    title: isDraft ? 'Simpan sebagai Draft?' : 'Kirim Request Sekarang?',
                    text: isDraft ? "Data akan disimpan di list internal dengan status DRAFT." : "Request akan langsung dikirim ke Engineering Staff.",
                    icon: isDraft ? 'question' : 'warning',
                    showCancelButton: true,
                    confirmButtonColor: isDraft ? '#64748b' : '#2563eb',
                    cancelButtonColor: '#cbd5e1',
                    confirmButtonText: isDraft ? 'Ya, Simpan Draft!' : 'Ya, Kirim!',
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