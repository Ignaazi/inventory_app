@extends('admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="mx-auto w-full max-w-5xl pb-12 px-4 sm:px-6" x-data="engMaterialSignatureHandler()">
    
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
        <div>
            <h2 class="text-lg font-extrabold text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-2">
                <span class="h-5 w-1.5 bg-primary rounded-full"></span>
                <span>CONFIRM MATERIAL RECEIVED (ENGINEERING SECTION)</span>
            </h2>
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">PT SIIX EMS INDONESIA • STOCK ENGINEERING INCOMING MODULE</p>
        </div>
        
        <div class="self-start sm:self-center flex gap-2">
            <a href="{{ route('eng.material.receiving.index') }}" class="bg-slate-600 hover:bg-slate-700 text-white font-bold text-xs uppercase py-2 px-4 rounded-lg shadow-sm transition-all active:scale-95">
                Back to List
            </a>
            <button type="button" @click="window.print()" class="flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg px-3 py-1.5 text-xs font-bold uppercase tracking-wide transition-all shadow-sm active:scale-95">
                PRINT REPORT
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl shadow-md overflow-hidden print:hidden mb-10">
        <form id="engMaterialForm" action="{{ route('eng.material.receiving.store') }}" method="POST" @submit.prevent="submitEngForm($event)">
            @csrf
            
            <input type="hidden" name="id" value="{{ $receiving->id }}">
            <input type="hidden" name="action" x-model="formAction">
            <input type="hidden" name="signature_data" x-bind:value="signatureImg">

            {{-- 🌟 HIDDEN INPUT ROLE CONTROL (Sesuai Konsep Simulasi Berjenjang) --}}
            @if($receiving->status === 'submitted_by_costing')
                <input type="hidden" name="signer_role" value="staff">
            @elseif($receiving->status === 'approved_by_spv')
                <input type="hidden" name="signer_role" value="spv">
            @else
                <input type="hidden" name="signer_role" value="staff">
            @endif

            <div class="p-5 sm:p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">PR Reference Code</label>
                            <input type="text" readonly class="w-full rounded-lg border border-stroke bg-slate-100 py-2.5 px-4 text-sm font-bold text-black outline-none dark:border-gray-700 dark:bg-slate-800" value="{{ $receiving->pr_code }}">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Qty From Costing</label>
                                <input type="text" readonly class="w-full rounded-lg border border-stroke bg-slate-100 py-2.5 px-4 text-sm font-bold text-black outline-none dark:border-gray-700 dark:bg-slate-800" value="{{ number_format($receiving->qty_received) }} Pcs">
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Lot / Batch No</label>
                                <input type="text" readonly class="w-full rounded-lg border border-stroke bg-slate-100 py-2.5 px-4 text-sm font-bold text-black outline-none dark:border-gray-700 dark:bg-slate-800" value="{{ $receiving->lot_no ?? '-' }}">
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Engineering Verification Remarks (Catatan Lu)</label>
                            <textarea name="notes" rows="3" class="w-full rounded-lg border border-stroke bg-transparent py-2 px-4 text-sm outline-none focus:border-primary dark:border-gray-700 dark:bg-form-input text-black dark:text-white font-medium" placeholder="Tulis catatan kondisi kedatangan fisik barang disini...">{{ $receiving->engineering_notes }}</textarea>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 bg-slate-50 dark:bg-meta-4 p-4 rounded-xl border border-stroke dark:border-strokedark justify-between relative z-0">
                        <div>
                            <div class="flex items-center justify-between border-b border-stroke dark:border-strokedark pb-2 mb-3">
                                <label class="text-[11px] font-black uppercase text-blue-700 dark:text-blue-400 tracking-wider">
                                    ✍️ OTORISASI: {{ $receiving->status === 'approved_by_spv' ? 'ENGINEERING SUPERVISOR (STEP 3)' : 'ENGINEERING STAFF (STEP 2)' }}
                                </label>
                            </div>

                            <div class="w-full">
                                <p class="text-[10px] text-slate-400 mb-1.5">*Silakan goreskan tanda tangan digital Anda di bawah ini:</p>
                                <div class="relative w-full h-44 bg-white border border-slate-200 rounded-lg overflow-hidden shadow-inner z-0">
                                    <canvas x-ref="canvas" @mousedown="startDrawing" @mousemove="draw" @mouseup="stopDrawing" @mouseleave="stopDrawing" @touchstart="startDrawing" @touchmove="draw" @touchend="stopDrawing" class="w-full h-full cursor-crosshair block"></canvas>
                                    <button type="button" @click="clearCanvas" class="absolute bottom-2 right-2 px-2 py-1 bg-rose-600 text-white rounded text-[9px] font-bold uppercase transition-all shadow z-10 hover:bg-rose-700">Clear</button>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center text-[9px] font-bold uppercase tracking-wider pt-2 border-t border-stroke dark:border-strokedark">
                            <span :class="signatureImg ? 'text-green-600' : 'text-slate-400'">STATUS TTD: <span x-text="signatureImg ? '✓ READY' : 'EMPTY'"></span></span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex flex-col sm:flex-row gap-2.5 sm:justify-end border-t border-stroke dark:border-strokedark pt-5">
                    <button type="submit" @click="formAction = 'reject'" class="rounded-lg bg-rose-600 hover:bg-rose-700 text-white py-2 px-4 text-xs font-bold uppercase tracking-wide shadow-sm transition-all duration-150 active:scale-95">
                        ❌ REJECT / RETURN DATA
                    </button>
                    <button type="submit" @click="formAction = 'confirm'" class="rounded-lg bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 text-xs font-bold uppercase tracking-wide shadow-sm transition-all duration-150 active:scale-95">
                        ✍️ APPROVE & SIGN DOCUMENT
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div id="print-target-box">
        <h3 class="text-xs font-bold uppercase text-slate-400 tracking-wider mb-3 flex items-center gap-2 print:hidden">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            Live Preview Bukti Penerimaan Barang (Alur TTD Berjenjang)
        </h3>
        
        <div class="bg-white text-black p-8 sm:p-12 border border-slate-300 rounded-xl shadow-sm print:border-none print:shadow-none font-sans">
            
            <div class="flex items-center justify-between border-b-4 border-black pb-4 mb-6">
                <div class="flex items-center gap-4">
                    <h1 class="text-lg font-black uppercase tracking-tight text-black">PT. SIIX EMS INDONESIA</h1>
                </div>
                <div class="text-right">
                    <h2 class="text-xs font-black uppercase text-black border border-black px-3 py-1 bg-slate-50 tracking-wide">MATERIAL RECEIVED REPORT</h2>
                    <p class="text-[8px] text-slate-500 font-mono mt-1 font-bold">RECEIVING CODE: {{ $receiving->receiving_code }}</p>
                </div>
            </div>

            <div class="mb-6">
                <table class="w-full border-collapse text-xs border border-black">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black">PR Code Reference</td>
                            <td class="py-2.5 px-4 font-mono font-bold text-black uppercase">{{ $receiving->pr_code }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black">Quantity Received</td>
                            <td class="py-2.5 px-4 font-bold text-black">{{ number_format($receiving->qty_received) }} Pcs</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black">Lot / Batch Number</td>
                            <td class="py-2.5 px-4 font-mono font-bold text-black uppercase">{{ $receiving->lot_no ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="grid grid-cols-3 gap-0 border border-black text-center text-xs mt-8">
                
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 uppercase tracking-wider text-[9px]">1. Issued By (Eng Staff)</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        {{-- 🌟 FIX UTAMA: Jika TTD Staff sudah ada di DB, tampilkan selamanya! --}}
                        @if(($receiving->engineering_signature_path && file_exists(public_path($receiving->engineering_signature_path))) || ($receiving->eng_signature_path && file_exists(public_path($receiving->eng_signature_path))))
                            <img src="{{ asset($receiving->engineering_signature_path ?? $receiving->eng_signature_path) }}" class="max-h-full max-w-full object-contain mx-auto block">
                        @elseif($receiving->status === 'submitted_by_costing')
                            <div class="absolute inset-0 z-10 flex items-center justify-center p-1" x-show="signatureImg">
                                <img :src="signatureImg" class="max-h-full max-w-full object-contain mx-auto block">
                            </div>
                            <span class="text-amber-500 font-bold text-[8px] italic m-auto" x-show="!signatureImg">Sign Required</span>
                        @else
                            <span class="text-slate-300 italic text-[8px] m-auto">( Waiting Staff Sign )</span>
                        @endif
                    </div>
                    <div class="border-t border-slate-200 py-1 px-1 bg-white truncate">
                        <p class="font-bold uppercase text-black underline text-[10px]">
                            {{ $receiving->engineering_staff_name ? $receiving->engineering_staff_name : ($receiving->status === 'submitted_by_costing' ? auth()->user()->name : '_________________') }}
                        </p>
                        <p class="text-[7px] text-slate-400 font-bold uppercase mt-0.5">Engineering Staff</p>
                    </div>
                </div>

                <div class="border-r border-black flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 uppercase tracking-wider text-[9px]">2. Checked (Eng SPV)</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white mx-auto">
                        {{-- 🌟 FIX UTAMA: Jika TTD SPV sudah ada di DB, tampilkan selamanya! --}}
                        @if(($receiving->engineering_spv_signature_path && file_exists(public_path($receiving->engineering_spv_signature_path))) || ($receiving->eng_spv_signature_path && file_exists(public_path($receiving->eng_spv_signature_path))))
                            <img src="{{ asset($receiving->engineering_spv_signature_path ?? $receiving->eng_spv_signature_path) }}" class="max-h-full max-w-full object-contain mx-auto block">
                        @elseif($receiving->status === 'approved_by_spv')
                            <div class="absolute inset-0 z-10 flex items-center justify-center p-1" x-show="signatureImg">
                                <img :src="signatureImg" class="max-h-full max-w-full object-contain mx-auto block">
                            </div>
                            <span class="text-amber-500 font-bold text-[8px] italic m-auto" x-show="!signatureImg">Sign Required</span>
                        @else
                            <span class="text-slate-300 italic text-[8px] m-auto">( Waiting SPV Approval )</span>
                        @endif
                    </div>
                    <div class="border-t border-slate-200 py-1 px-1 bg-white truncate">
                        <p class="font-bold uppercase text-black underline text-[10px]">
                            {{ $receiving->engineering_spv_name ? $receiving->engineering_spv_name : ($receiving->status === 'approved_by_spv' ? auth()->user()->name : '_________________') }}
                        </p>
                        <p class="text-[7px] text-slate-400 font-bold uppercase mt-0.5">Engineering Supervisor</p>
                    </div>
                </div>

                <div class="flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 uppercase tracking-wider text-[9px]">3. Acknowledged (Costing)</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white mx-auto p-1">
                        @if($receiving->costing_signature_path && file_exists(public_path($receiving->costing_signature_path)))
                            <img src="{{ asset($receiving->costing_signature_path) }}" class="max-h-full max-w-full object-contain mx-auto block" alt="Costing Signed Base">
                        @else
                            <span class="text-slate-300 italic text-[8px] m-auto">( No Base Signature )</span>
                        @endif
                    </div>
                    <div class="border-t border-slate-200 py-1 px-1 bg-white truncate">
                        <p class="font-bold text-black uppercase underline text-[10px]">{{ $receiving->created_by_name ?? 'Costing Staff' }}</p>
                        <p class="text-[7px] text-slate-400 font-bold uppercase mt-0.5">Costing Department</p>
                    </div>
                </div>

            </div>

            <div class="mt-4 border border-slate-200 rounded p-3 bg-slate-50 text-xs">
                <span class="font-bold uppercase block text-[9px] text-slate-500">Costing Internal Notes Bawaan:</span>
                <p class="italic text-slate-700 mt-1">" {{ $receiving->costing_notes ?? 'No costing notes attached.' }} "</p>
            </div>
        </div>
    </div>
</div>

<script>
function engMaterialSignatureHandler() {
    return {
        formAction: 'confirm',
        isDrawing: false,
        signatureImg: null, 
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
            if(this.$refs.canvas) {
                this.ctx.clearRect(0, 0, this.$refs.canvas.width, this.$refs.canvas.height);
            }
            this.signatureImg = null;
        },
        submitEngForm(e) {
            if (this.formAction === 'confirm' && !this.signatureImg) {
                Swal.fire({ 
                    icon: 'warning', 
                    title: 'Tanda Tangan Diperlukan!', 
                    text: 'Harap bubuhkan tanda tangan digital Anda di Pad Canvas, bro!', 
                    confirmButtonColor: '#3C50E0' 
                });
                return;
            }

            const alertTitle = this.formAction === 'reject' ? 'Reject/Return Laporan?' : 'Kirim Data Otorisasi?';
            const alertText = this.formAction === 'reject' ? 'Data akan dikembalikan dengan status reject.' : 'Tanda tangan digital Anda akan direkam ke dalam database.';

            Swal.fire({
                title: alertTitle,
                text: alertText,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3C50E0',
                cancelButtonColor: '#f43f5e',
                confirmButtonText: 'Ya, Proses!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formElement = document.getElementById('engMaterialForm');
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
    #print-target-box { position: absolute !important; left: 0 !important; top: 0 !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
    body { background-color: #ffffff !important; }
}
</style>
@endsection