@extends('admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Menentukan mode halaman secara otomatis berdasarkan keberadaan data binding $receiving --}}
@php
    $isEditMode = isset($receiving); 
    $statusMode = $isEditMode ? $receiving->status : 'create';
@endphp

<div class="mx-auto w-full max-w-5xl pb-12 px-4 sm:px-6" x-data="materialSignatureWorkflowHandler()">
    
    {{-- HEADER HALAMAN & TOMBOL PRINT --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
        <div>
            <h2 class="text-lg font-extrabold text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-2">
                <span class="h-5 w-1.5 bg-blue-600 rounded-full"></span>
                <span>
                    @if($statusMode === 'create') SUBMIT MATERIAL RECEIVED (COSTING SECTION)
                    @elseif($statusMode === 'submitted_by_costing') ⚡ VERIFY GOODS (ENGINEERING STAFF)
                    @elseif($statusMode === 'approved_by_spv') 🔑 FINAL APPROVAL (ENGINEERING SUPERVISOR)
                    @else PREVIEW MATERIAL RECEIVED REPORT
                    @endif
                </span>
            </h2>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest mt-0.5">
                PT SIIX EMS INDONESIA • @if($isEditMode) TRANSACTION ID: {{ $receiving->receiving_code }} @else FORWARD TO INCOMING ENGINEERING MODULE @endif
            </p>
        </div>
        
        <div class="self-start sm:self-center flex gap-2">
            <button type="button" @click="window.print()" class="flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg px-3 py-1.5 text-xs font-bold uppercase tracking-wide transition-all shadow-sm active:scale-95">
                PRINT REPORT
            </button>
        </div>
    </div>

    {{-- CONTAINER FORM UTAMA --}}
    {{-- Form disembunyikan otomatis jika status sudah completed atau rejected --}}
    @if($statusMode === 'create' || $statusMode === 'submitted_by_costing' || $statusMode === 'approved_by_spv')
    <div class="bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl shadow-md overflow-hidden print:hidden mb-10">
        
        {{-- TARGET ACTION DIBUAT DINAMIS MENGIKUTI WORKFLOW --}}
        <form id="workflowMaterialForm" 
              action="{{ $statusMode === 'create' ? route('costing.signature.store') : ($statusMode === 'submitted_by_costing' ? route('eng.material.receiving.verify', $receiving->id) : route('eng.material.receiving.approve', $receiving->id)) }}" 
              method="POST" 
              @submit.prevent="submitWorkflowForm($event)">
            @csrf
            @if($isEditMode) @method('POST') @endif
            
            {{-- Hidden Field Pengirim Base64 dataURL --}}
            <input type="hidden" name="signature_data" x-bind:value="signatureImg">
            <input type="hidden" name="stamp_data" x-bind:value="stampImg">
            <input type="hidden" name="action" x-bind:value="actionDecision">

            <div class="p-5 sm:p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    {{-- SISI KIRI: INPUT KONDISI MATERIAL --}}
                    <div class="flex flex-col gap-4">
                        
                        {{-- 1. PILIH PR ATAU DISKLAIMER INFO DARI COSTING --}}
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Purchase Request Code</label>
                            @if($statusMode === 'create')
                                <select name="pr_code" x-model="pr_code" required class="w-full rounded-lg border border-stroke bg-transparent py-2.5 px-4 text-sm font-medium outline-none transition focus:border-primary dark:border-gray-700 dark:bg-form-input dark:text-white">
                                    <option value="">-- Pilih PR Approved / Done --</option>
                                    @foreach($availablePRs as $pr)
                                        <option value="{{ $pr->pr_code }}">{{ $pr->pr_code }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" name="pr_code" value="{{ $receiving->pr_code }}" readonly class="w-full rounded-lg border border-slate-200 bg-slate-100 dark:bg-slate-800 py-2.5 px-4 text-sm font-mono font-bold outline-none">
                            @endif
                        </div>

                        {{-- 2. QTY RECEIVED & LOT NO --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Qty Received</label>
                                <input type="number" name="qty_received" x-model="qty_received" required min="1" {{ $isEditMode ? 'readonly' : '' }} class="w-full rounded-lg border border-stroke bg-transparent {{ $isEditMode ? 'bg-slate-100 dark:bg-slate-800' : '' }} py-2.5 px-4 text-sm font-medium outline-none">
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Lot / Serial No</label>
                                <input type="text" name="lot_no" x-model="lot_no" required {{ $isEditMode ? 'readonly' : '' }} class="w-full rounded-lg border border-stroke bg-transparent {{ $isEditMode ? 'bg-slate-100 dark:bg-slate-800' : '' }} py-2.5 px-4 text-sm font-medium outline-none">
                            </div>
                        </div>

                        {{-- 3. INPUT TAMBAHAN KHUSUS UNTUK ENGINEERING STAFF (ITEM NAME & SUPPLIER) --}}
                        @if($statusMode === 'submitted_by_costing')
                        <div class="grid grid-cols-2 gap-3 border-l-4 border-amber-500 pl-3 my-1">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold uppercase text-amber-600 tracking-wider">Item Name (Verify)</label>
                                <input type="text" name="item_name" required placeholder="Input Nama Material" class="w-full rounded-lg border border-amber-400 bg-transparent py-2.5 px-4 text-sm font-medium outline-none">
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold uppercase text-amber-600 tracking-wider">Supplier Name (Verify)</label>
                                <input type="text" name="supplier_name" required placeholder="Input Supplier" class="w-full rounded-lg border border-amber-400 bg-transparent py-2.5 px-4 text-sm font-medium outline-none">
                            </div>
                        </div>
                        @endif

                        {{-- 4. REMARKS / NOTES SECTION --}}
                        <div class="flex flex-col gap-1.5">
                            @if($statusMode === 'create')
                                <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Costing Staff Remarks (Kondisi Awal)</label>
                                <textarea name="costing_notes" x-model="costing_notes" rows="3" class="w-full rounded-lg border border-stroke bg-transparent py-2 px-4 text-sm outline-none placeholder:text-slate-400" placeholder="Tuliskan catatan kondisi packaging / item saat pertama kali tiba..."></textarea>
                            @else
                                <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Engineering Remarks (Catatan Verifikasi)</label>
                                <textarea name="engineering_notes" rows="3" {{ $statusMode === 'approved_by_spv' ? 'readonly' : '' }} class="w-full rounded-lg border border-stroke bg-transparent {{ $statusMode === 'approved_by_spv' ? 'bg-slate-100 dark:bg-slate-800' : '' }} py-2 px-4 text-sm outline-none" placeholder="{{ $statusMode === 'approved_by_spv' ? 'No Engineering staff notes' : 'Tuliskan catatan verifikasi fisik / jika ditemukan ketidaksesuaian...' }}">{{ $isEditMode ? $receiving->engineering_notes : '' }}</textarea>
                            @endif
                        </div>
                    </div>

                    {{-- SISI KANAN: PAD TTD WORKFLOW --}}
                    <div class="flex flex-col gap-4 bg-slate-50 dark:bg-meta-4 p-4 rounded-xl border border-stroke dark:border-strokedark justify-between relative z-0">
                        <div>
                            <div class="flex items-center justify-between border-b border-stroke dark:border-strokedark pb-2 mb-3">
                                <label class="text-[10px] font-bold uppercase text-slate-700 dark:text-slate-300 tracking-wider">
                                    @if($statusMode === 'create') Otorisasi Costing Staff
                                    @elseif($statusMode === 'submitted_by_costing') Otorisasi Engineering Staff
                                    @else Otorisasi Engineering Supervisor
                                    @endif
                                </label>
                                @if($statusMode === 'create')
                                <div class="flex gap-1 bg-slate-200 dark:bg-boxdark p-1 rounded-md text-[9px] font-bold">
                                    <button type="button" @click="activeTab = 'draw'" :class="activeTab === 'draw' ? 'bg-blue-600 text-white' : 'text-slate-600 dark:text-slate-400'" class="px-2.5 py-1 rounded transition-all">PAD TTD</button>
                                    <button type="button" @click="activeTab = 'upload'" :class="activeTab === 'upload' ? 'bg-blue-600 text-white' : 'text-slate-600 dark:text-slate-400'" class="px-2.5 py-1 rounded transition-all">UPLOAD STEMPEL</button>
                                </div>
                                @endif
                            </div>

                            {{-- CANVAS PAD SIGNATURE --}}
                            <div x-show="activeTab === 'draw'" class="w-full">
                                <p class="text-[10px] text-slate-400 mb-1.5">*Goreskan tanda tangan otorisasi simulasi Anda di bawah ini:</p>
                                <div class="relative w-full h-40 bg-white border border-slate-200 rounded-lg overflow-hidden shadow-inner z-0">
                                    <canvas x-ref="canvas" @mousedown="startDrawing" @mousemove="draw" @mouseup="stopDrawing" @mouseleave="stopDrawing" @touchstart="startDrawing" @touchmove="draw" @touchend="stopDrawing" class="w-full h-full cursor-crosshair block"></canvas>
                                    <button type="button" @click="clearCanvas" class="absolute bottom-2 right-2 px-2 py-1 bg-rose-600 text-white rounded text-[9px] font-bold uppercase transition-all shadow z-10 hover:bg-rose-700">Clear</button>
                                </div>
                            </div>

                            {{-- UPLOAD STEMPEL COSTING (ONLY ACTIVE ON STEP 1) --}}
                            @if($statusMode === 'create')
                            <div x-show="activeTab === 'upload'" class="w-full" x-cloak>
                                <p class="text-[10px] text-slate-400 mb-1.5">*Unggah stempel departemen Costing (.png):</p>
                                <div class="relative w-full h-40 bg-white dark:bg-form-input border-2 border-dashed border-stroke dark:border-strokedark hover:border-primary rounded-lg flex flex-col items-center justify-center p-4 transition-all z-0">
                                    <input type="file" @change="handleFileUpload" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <div class="flex flex-col items-center text-center pointer-events-none" x-show="!stampImg">
                                        <svg class="w-6 h-6 mb-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        <p class="text-xs font-bold text-slate-600 dark:text-slate-400">Pilih Berkas Stempel</p>
                                    </div>
                                    <div class="flex flex-col items-center justify-center h-full w-full relative" x-show="stampImg" x-cloak>
                                        <img :src="stampImg" class="max-h-24 object-contain mx-auto mix-blend-multiply">
                                        <button type="button" @click.stop="clearStamp" class="mt-2 px-2 py-0.5 bg-rose-600 text-white rounded text-[9px] font-bold uppercase z-20 hover:bg-rose-700">Hapus</button>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="flex justify-between items-center text-[9px] font-bold uppercase tracking-wider pt-2 border-t border-stroke dark:border-strokedark">
                            <span :class="signatureImg ? 'text-green-600' : 'text-slate-400'">TTD STATUS: <span x-text="signatureImg ? '✓ READY' : 'EMPTY'"></span></span>
                            @if($statusMode === 'create')
                            <span :class="stampImg ? 'text-green-600' : 'text-slate-400'">STEMPEL STATUS: <span x-text="stampImg ? '✓ READY' : 'EMPTY'"></span></span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- SUBMIT DECISION BUTTONS ALUR BERJENJANG --}}
                <div class="mt-8 flex flex-col sm:flex-row gap-2.5 sm:justify-end border-t border-stroke dark:border-strokedark pt-5">
                    @if($statusMode === 'create')
                        <button type="button" @click="resetAll" class="rounded-lg border border-slate-200 bg-slate-100 text-slate-700 py-2 px-4 text-xs font-bold uppercase tracking-wide hover:bg-slate-200 transition-all">Reset</button>
                        <button type="submit" class="rounded-lg bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 text-xs font-bold uppercase tracking-wide shadow transition-all">SUBMIT TO ENGINEERING</button>
                    @elseif($statusMode === 'submitted_by_costing')
                        {{-- DECISION UTK ENGINEERING STAFF --}}
                        <button type="submit" @click="actionDecision = 'discrepancy'" class="rounded-lg bg-rose-600 hover:bg-rose-700 text-white py-2 px-4 text-xs font-bold uppercase tracking-wide transition-all">⚠️ REPORT DISCREPANCY</button>
                        <button type="submit" @click="actionDecision = 'verify'" class="rounded-lg bg-amber-500 hover:bg-amber-600 text-white py-2 px-6 text-xs font-bold uppercase tracking-wide transition-all">✔ VERIFY & ACCEPT GOODS</button>
                    @elseif($statusMode === 'approved_by_spv')
                        {{-- DECISION UTK SUPERVISOR --}}
                        <button type="submit" @click="actionDecision = 'approve'" class="rounded-lg bg-purple-600 hover:bg-purple-700 text-white py-2 px-8 text-xs font-bold uppercase tracking-wide transition-all">🔑 CONFIRM FULL APPROVAL</button>
                    @endif
                </div>
            </div>
        </form>
    </div>
    @endif

    {{-- LIVE PREVIEW DOKUMEN REPORT LENGKAP (SIAP CETAK) --}}
    <div id="print-target-box">
        <h3 class="text-xs font-bold uppercase text-slate-400 tracking-wider mb-3 flex items-center gap-2 print:hidden">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            Live Document Tracker (PT SIIX Production Environment)
        </h3>
        
        <div class="bg-white text-black p-8 sm:p-12 border border-slate-300 rounded-xl shadow-sm print:border-none print:shadow-none print:p-0 font-sans">
            
            <div class="flex items-center justify-between border-b-4 border-black pb-4 mb-6">
                <div>
                    <h1 class="text-lg font-black uppercase tracking-tight text-black">PT. SIIX EMS INDONESIA</h1>
                    <p class="text-[9px] font-bold text-slate-600">INCOMING MATERIAL TRACKING REPORT</p>
                </div>
                <div class="text-right">
                    <h2 class="text-xs font-black uppercase text-black border border-black px-3 py-1 bg-slate-50 tracking-wide">MATERIAL RECEIVED REPORT</h2>
                    <p class="text-[8px] text-slate-500 font-mono mt-1" x-text="'PR CODE: ' + (pr_code || 'PR-ENG-XXXXXX')"></p>
                </div>
            </div>

            <div class="mb-6">
                <table class="w-full border-collapse text-xs border border-black">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black">PR Code Reference</td>
                            <td class="py-2.5 px-4 font-mono font-bold text-black uppercase" x-text="pr_code || '-'">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black">Item Name / Desc</td>
                            <td class="py-2.5 px-4 font-bold text-black uppercase">{{ $isEditMode ? ($receiving->item_name ?? 'WAITING ENG VERIFICATION') : 'INPUTED BY ENG ON STEP 2' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black">Quantity Received</td>
                            <td class="py-2.5 px-4 font-bold text-black" x-text="qty_received ? qty_received + ' Pcs' : '-'">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black">Lot / Serial Number</td>
                            <td class="py-2.5 px-4 font-mono font-bold text-black uppercase" x-text="lot_no || '-'">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black">Supplier Vendor</td>
                            <td class="py-2.5 px-4 font-bold text-black uppercase">{{ $isEditMode ? ($receiving->supplier_name ?? '-') : '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- WORKFLOW BOX SIGNATURE (3 KOLOM BERJENJANG AKTIF) --}}
            <div class="grid grid-cols-3 gap-0 border border-black text-center text-xs mt-8">
                
                {{-- 1. COSTING COLUMN --}}
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white relative">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 uppercase tracking-wider text-[9px]">1. Issued By (Costing)</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        @if($isEditMode && $receiving->costing_signature_path)
                            <img src="{{ asset($receiving->costing_signature_path) }}" class="max-h-full max-w-full object-contain mx-auto my-auto block">
                            @if($receiving->costing_stamp_path)
                                <img src="{{ asset($receiving->costing_stamp_path) }}" class="absolute inset-0 max-h-full max-w-full object-contain mx-auto my-auto block mix-blend-multiply opacity-90">
                            @endif
                        @else
                            <div class="absolute inset-0 flex items-center justify-center p-1" x-show="signatureImg">
                                <img :src="signatureImg" class="max-h-full max-w-full object-contain mx-auto block">
                            </div>
                            <div class="absolute inset-0 flex items-center justify-center p-0 pointer-events-none" x-show="stampImg">
                                <img :src="stampImg" class="max-h-full max-w-full object-contain mx-auto block mix-blend-multiply">
                            </div>
                        @endif
                    </div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-bold uppercase text-black underline truncate">{{ $isEditMode ? $receiving->created_by_name : auth()->user()->name }}</p>
                        <p class="text-[7px] text-slate-500 font-bold uppercase mt-0.5">Costing Department</p>
                    </div>
                </div>

                {{-- 2. ENGINEERING STAFF COLUMN --}}
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white relative">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 uppercase tracking-wider text-[9px]">2. Checked (Eng Staff)</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        @if($isEditMode && $receiving->eng_signature_path)
                            <img src="{{ asset($receiving->eng_signature_path) }}" class="max-h-full max-w-full object-contain mx-auto my-auto block">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center p-1" x-show="signatureImg && statusMode === 'submitted_by_costing'">
                                <img :src="signatureImg" class="max-h-full max-w-full object-contain mx-auto block">
                            </div>
                            <span class="text-slate-300 italic text-[8px] m-auto" x-show="statusMode === 'create'">( Waiting Step 2 )</span>
                        @endif
                    </div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-bold uppercase text-black truncate">{{ $isEditMode && $receiving->eng_signature_path ? 'VERIFIED' : '( Pending Verification )' }}</p>
                        <p class="text-[7px] text-slate-500 font-bold uppercase mt-0.5">Engineering Staff</p>
                    </div>
                </div>

                {{-- 3. ENGINEERING SUPERVISOR COLUMN --}}
                <div class="flex flex-col justify-between h-36 bg-white relative">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 uppercase tracking-wider text-[9px]">3. Approval (Eng SPV)</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        @if($isEditMode && $receiving->eng_spv_signature_path)
                            <img src="{{ asset($receiving->eng_spv_signature_path) }}" class="max-h-full max-w-full object-contain mx-auto my-auto block">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center p-1" x-show="signatureImg && statusMode === 'approved_by_spv'">
                                <img :src="signatureImg" class="max-h-full max-w-full object-contain mx-auto block">
                            </div>
                            <span class="text-slate-300 italic text-[8px] m-auto" x-show="statusMode !== 'approved_by_spv'">( Waiting SPV )</span>
                        @endif
                    </div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-bold uppercase text-black truncate">{{ $isEditMode && $receiving->eng_spv_signature_path ? 'APPROVED' : '( Pending Approval )' }}</p>
                        <p class="text-[7px] text-slate-500 font-bold uppercase mt-0.5">Engineering Supervisor</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function materialSignatureWorkflowHandler() {
    return {
        pr_code: '{{ $isEditMode ? $receiving->pr_code : "" }}',
        qty_received: '{{ $isEditMode ? $receiving->qty_received : "" }}',
        lot_no: '{{ $isEditMode ? $receiving->lot_no : "" }}',
        costing_notes: '{{ $isEditMode ? $receiving->costing_notes : "" }}',
        
        statusMode: '{{ $statusMode }}',
        actionDecision: 'verify', // Default action untuk step 2

        activeTab: 'draw', 
        isDrawing: false,
        signatureImg: null, 
        stampImg: null,     
        ctx: null,

        init() {
            this.$nextTick(() => { this.initCanvas(); });
        },

        initCanvas() {
            const canvas = this.$refs.canvas;
            if (canvas) {
                this.ctx = canvas.getContext('2d');
                canvas.width = canvas.offsetWidth;
                canvas.height = canvas.offsetHeight;
                this.ctx.strokeStyle = '#000000'; 
                this.ctx.lineWidth = 2.5;         
                this.ctx.lineCap = 'round';
            }
        },

        startDrawing(e) {
            this.isDrawing = true;
            const pos = this.getMousePos(e);
            this.ctx.beginPath();
            this.ctx.moveTo(pos.x, pos.y);
        },
        draw(e) {
            if (!this.isDrawing) return;
            e.preventDefault();
            const pos = this.getMousePos(e);
            this.ctx.lineTo(pos.x, pos.y);
            this.ctx.stroke();
        },
        stopDrawing() {
            if (this.isDrawing) {
                this.isDrawing = false;
                this.signatureImg = this.$refs.canvas.toDataURL(); 
            }
        },
        getMousePos(e) {
            const canvas = this.$refs.canvas;
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return { x: clientX - rect.left, y: clientY - rect.top };
        },
        clearCanvas() {
            if(this.$refs.canvas) { this.ctx.clearRect(0, 0, this.$refs.canvas.width, this.$refs.canvas.height); }
            this.signatureImg = null;
        },
        clearStamp() { this.stampImg = null; },
        handleFileUpload(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => { this.stampImg = event.target.result; };
                reader.readAsDataURL(file);
            }
        },
        resetAll() {
            this.clearCanvas();
            this.stampImg = null;
        },

        submitWorkflowForm(e) {
            // Validasi Canvas TTD Wajib terisi untuk tombol non-Discrepancy
            if (!this.signatureImg && this.actionDecision !== 'discrepancy') {
                Swal.fire({ icon: 'warning', title: 'Tanda Tangan Diperlukan!', text: 'Harap goreskan TTD otorisasi pada pad canvas, bro!', confirmButtonColor: '#2563eb' });
                return;
            }

            let confirmTitle = 'Kirim Data ke Engineering?';
            if (this.actionDecision === 'discrepancy') confirmTitle = 'Laporkan Ketidaksesuaian (Return)?';
            if (this.actionDecision === 'verify') confirmTitle = 'Sahkan Verifikasi Fisik Barang?';
            if (this.actionDecision === 'approve') confirmTitle = 'Selesaikan Full Approval Dokumen?';

            Swal.fire({
                title: confirmTitle,
                text: "Aksi simulasi ini akan langsung mengubah record status log database.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#f43f5e',
                confirmButtonText: 'Ya, Proses!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formElement = document.getElementById('workflowMaterialForm');
                    formElement.querySelector('input[name="signature_data"]').value = this.signatureImg;
                    formElement.submit();
                }
            });
        }
    }
}
</script>

<style>
@page { size: portrait; margin: 10mm; }
@media print {
    body * { visibility: hidden !important; }
    #print-target-box, #print-target-box * { visibility: visible !important; }
    #print-target-box { position: absolute !important; left: 0 !important; top: 0 !important; width: 100% !important; margin: 0 !important; }
}
[x-cloak] { display: none !important; }
</style>
@endsection