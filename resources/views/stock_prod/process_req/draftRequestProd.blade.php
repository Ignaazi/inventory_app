@extends('admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="mx-auto w-full max-w-5xl pb-12 px-4 sm:px-6" x-data="signatureFormHandler()">
    
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
        <div>
            <h2 class="text-lg font-extrabold text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-2">
                <span class="h-5 w-1.5 bg-amber-500 rounded-full"></span>
                <span>EDIT DRAFT SPAREPART REQUEST</span>
            </h2>
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">PT SIIX EMS KARAWANG</p>
        </div>
        
        <div class="self-start sm:self-center">
            <button type="button" @click="generatePDF()" class="flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg px-3 py-1.5 text-xs font-bold uppercase tracking-wide transition-all shadow-sm shadow-red-600/10 active:scale-95">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 4C4 2.89543 4.89543 2 6 2H14L20 8V20C20 21.1046 19.1046 22 18 22H6C4.89543 22 4 21.1046 4 20V4Z" fill="#E2E8F0"/>
                    <path d="M14 2V8H20L14 2Z" fill="#CBD5E1"/>
                    <rect x="3" y="11" width="14" height="8" rx="1.5" fill="#EF4444"/>
                    <text x="4.5" y="17" fill="white" font-size="5.5" font-family="sans-serif" font-weight="900" letter-spacing="-0.2">PDF</text>
                </svg>
                DOWNLOAD PDF
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-900/50 text-green-700 dark:text-green-400 font-semibold text-sm flex items-center gap-2 shadow-sm print:hidden">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl shadow-md overflow-hidden print:hidden mb-10">
        <form id="requestForm" action="{{ route('prod.request.update_draft', $requestData->id) }}" method="POST" @submit.prevent="handleFormAction($event)">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="action_type" x-model="actionType">
            <input type="hidden" name="signature_data" x-bind:value="signatureImg">
            <input type="hidden" name="stamp_data" x-bind:value="stampImg">

            <div class="p-5 sm:p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Name</label>
                            <input type="text" name="requestor" x-model="requestor" class="w-full rounded-lg border border-stroke bg-transparent py-2.5 px-4 text-sm font-medium outline-none transition focus:border-primary active:border-primary dark:border-gray-700 dark:bg-form-input dark:text-white">
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Line</label>
                            <input type="text" name="line_machine" x-model="line_machine" class="w-full rounded-lg border border-stroke bg-transparent py-2.5 px-4 text-sm font-medium outline-none transition focus:border-primary active:border-primary dark:border-gray-700 dark:bg-form-input dark:text-white">
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">No Nozle</label>
                            <input type="text" name="sparepart_name" x-model="sparepart_name" class="w-full rounded-lg border border-stroke bg-transparent py-2.5 px-4 text-sm font-medium outline-none transition focus:border-primary active:border-primary dark:border-gray-700 dark:bg-form-input dark:text-white">
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-2 flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">SAP Code</label>
                                <div class="relative">
                                    <input type="text" name="sap_code" x-model="sap_code" class="w-full rounded-lg border border-stroke bg-transparent py-2.5 pl-4 pr-10 text-sm font-bold text-primary outline-none transition focus:border-primary active:border-primary dark:border-gray-700 dark:bg-form-input">
                                </div>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Qty Req</label>
                                <input type="number" name="qty_req" x-model="qty_req" min="1" class="w-full rounded-lg border border-stroke bg-transparent py-2.5 px-4 text-sm font-medium outline-none transition focus:border-primary active:border-primary dark:border-gray-700 dark:bg-form-input dark:text-white">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 bg-slate-50 dark:bg-meta-4 p-4 rounded-xl border border-stroke dark:border-strokedark justify-between relative z-0">
                        <div>
                            <div class="flex items-center justify-between border-b border-stroke dark:border-strokedark pb-2 mb-3">
                                <label class="text-[10px] font-bold uppercase text-slate-700 dark:text-slate-300 tracking-wider">Otorisasi Digital</label>
                                <div class="flex gap-1 bg-slate-200 dark:bg-boxdark p-1 rounded-md text-[9px] font-bold">
                                    <button type="button" @click="activeTab = 'draw'" :class="activeTab === 'draw' ? 'bg-primary text-white' : 'text-slate-600 dark:text-slate-400'" class="px-2.5 py-1 rounded transition-all">PAD TTD</button>
                                    <button type="button" @click="activeTab = 'upload'" :class="activeTab === 'upload' ? 'bg-primary text-white' : 'text-slate-600 dark:text-slate-400'" class="px-2.5 py-1 rounded transition-all">UPLOAD STAMP</button>
                                </div>
                            </div>

                            <div x-show="activeTab === 'draw'" class="w-full">
                                <p class="text-[10px] text-slate-400 mb-1.5">*Gunakan trackpad / mouse / touchscreen untuk tanda tangan:</p>
                                <div class="relative w-full h-40 bg-white border border-slate-200 rounded-lg overflow-hidden shadow-inner z-0">
                                    <canvas x-ref="canvas" @mousedown="startDrawing" @mousemove="draw" @mouseup="stopDrawing" @mouseleave="stopDrawing" @touchstart="startDrawing" @touchmove="draw" @touchend="stopDrawing" class="w-full h-full cursor-crosshair block"></canvas>
                                    <button type="button" @click="clearCanvas" class="absolute bottom-2 right-2 px-2 py-1 bg-rose-600 text-white rounded text-[9px] font-bold uppercase transition-all shadow z-10 hover:bg-rose-700">
                                        Clear
                                    </button>
                                </div>
                            </div>

                            <div x-show="activeTab === 'upload'" class="w-full" x-cloak>
                                <p class="text-[10px] text-slate-400 mb-1.5">*Unggah file gambar stempel departemen (Rekomendasi format .png):</p>
                                <div class="relative w-full h-40 bg-white dark:bg-form-input border-2 border-dashed border-stroke dark:border-strokedark hover:border-primary rounded-lg flex flex-col items-center justify-center p-4 transition-all z-0">
                                    <input type="file" @change="handleFileUpload" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <div class="flex flex-col items-center text-center pointer-events-none" x-show="!stampImg">
                                        <span class="p-2 bg-slate-100 dark:bg-boxdark rounded-full mb-1 text-primary">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        </span>
                                        <p class="text-xs font-bold text-slate-600 dark:text-slate-400">Pilih File Stempel</p>
                                    </div>
                                    <div class="flex flex-col items-center justify-center h-full w-full relative" x-show="stampImg" x-cloak>
                                        <img :src="stampImg" class="max-h-24 object-contain mx-auto mix-blend-multiply dark:mix-blend-normal">
                                        <button type="button" @click.stop="clearStamp" class="mt-2 px-2 py-0.5 bg-rose-600 text-white rounded text-[9px] font-bold uppercase z-20 hover:bg-rose-700">Hapus</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center text-[9px] font-bold uppercase tracking-wider pt-2 border-t border-stroke dark:border-strokedark">
                            <span :class="signatureImg ? 'text-green-600' : 'text-slate-400'">TTD: <span x-text="signatureImg ? '✓ READY' : 'EMPTY'"></span></span>
                            <span :class="stampImg ? 'text-green-600' : 'text-slate-400'">STAMP: <span x-text="stampImg ? '✓ READY' : 'EMPTY'"></span></span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex flex-col sm:flex-row gap-2.5 sm:justify-end border-t border-stroke dark:border-strokedark pt-5">
                    
                    <a href="{{ route('prod.request.list') }}" class="flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 py-2 px-4 text-xs font-bold uppercase tracking-wide hover:bg-slate-200 dark:hover:bg-slate-700 transition-all duration-150 active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                        </svg>
                        Batal
                    </a>
                    
                    <button type="button" @click="submitAs('draft')" class="flex items-center justify-center gap-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white py-2 px-4 text-xs font-bold uppercase tracking-wide shadow-sm transition-all duration-150 active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        Update Draft
                    </button>

                    <button type="button" @click="submitAs('submit')" class="flex items-center justify-center gap-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white py-2 px-5 text-xs font-bold uppercase tracking-wide shadow-sm transition-all duration-150 active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                        </svg>
                        Kirim Request
                    </button>
                </div>

            </div>
        </form>
    </div>

    <div id="print-target-box" class="print:m-0 print:p-0">
        <h3 class="text-xs font-bold uppercase text-slate-400 tracking-wider mb-3 flex items-center gap-2 print:hidden">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            Live Preview Form 
        </h3>
        
        <div class="bg-white text-black p-8 sm:p-12 border border-slate-300 rounded-xl shadow-sm print:border-none print:shadow-none print:p-0 font-sans">
            
            <div class="flex items-center justify-between border-b-4 border-black pb-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 flex items-center justify-center overflow-hidden">
                        <img src="/images/logo-siix.png" class="max-h-full max-w-full object-contain" alt="Logo SIIX">
                    </div>  
                    <div>
                        <h1 class="text-lg font-black uppercase tracking-tight text-black">PT. SIIX EMS KARAWANG</h1>
                        <p class="text-[9px] font-bold text-slate-500 tracking-wider uppercase">Electronic Manufacturing Services </p>
                    </div>
                </div>
                <div class="text-right">
                    <h2 class="text-sm font-black uppercase text-black border border-black px-3 py-1 bg-slate-50 tracking-wide">FORM REQUEST NOZZLE</h2>
                    <p class="text-[8px] text-slate-500 font-mono mt-1" x-text="'Doc No: ' + request_no"></p>
                </div>
            </div>

            <div class="mb-6">
                <table class="w-full border-collapse text-xs border border-black">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black text-slate-800">Nama</td>
                            <td class="py-2.5 px-4 font-bold text-black uppercase" x-text="requestor || '-'">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black text-slate-800">LINE</td>
                            <td class="py-2.5 px-4 font-bold text-black uppercase" x-text="line_machine || '-'">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black text-slate-800">NO NOZLE</td>
                            <td class="py-2.5 px-4 font-bold text-black uppercase" x-text="sparepart_name || '-'">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black text-slate-800">SAP CODE   </td>
                            <td class="py-2.5 px-4 font-mono font-bold text-black tracking-wider" x-text="sap_code || '-'">-</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black text-slate-800">Quantity Requested</td>
                            <td class="py-2.5 px-4 font-bold text-black" x-text="qty_req + ' Pcs'">1 Pcs</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="grid grid-cols-3 gap-0 border border-black text-center text-xs mt-8">
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 uppercase tracking-wider text-[9px] text-slate-800">Requested By</div>
                    
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        <div class="absolute inset-0 z-10 flex items-center justify-center p-1" x-show="signatureImg">
                            <img :src="signatureImg" class="max-h-full max-w-full object-contain mx-auto my-auto block" alt="Signature">
                        </div>
                        
                        <div class="absolute inset-0 z-20 flex items-center justify-center p-0 pointer-events-none" x-show="stampImg">
                            <img :src="stampImg" class="max-h-full max-w-full object-contain mx-auto my-auto block mix-blend-multiply opacity-95" alt="Company Stamp">
                        </div>

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

                <div class="border-r border-black flex flex-col justify-between h-36 bg-white">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 uppercase tracking-wider text-[9px] text-slate-800">Checked By</div>
                    <div class="text-slate-300 italic text-[8px] font-medium my-auto">( No Signature )</div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-bold uppercase text-black">( _________________ )</p>
                        <p class="text-[8px] text-slate-500 font-bold uppercase mt-0.5">Staff Engineering</p>
                    </div>
                </div>

                <div class="flex flex-col justify-between h-36 bg-white">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 uppercase tracking-wider text-[9px] text-slate-800">Approved By</div>
                    <div class="text-slate-300 italic text-[8px] font-medium my-auto">( No Signature )</div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-bold uppercase text-black">( _________________ )</p>
                        <p class="text-[8px] text-slate-500 font-bold uppercase mt-0.5">SPV Engineering</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 border-t border-dashed border-slate-300 pt-4 text-center print:block hidden">
                <p class="text-[8px] text-slate-400 font-mono uppercase tracking-widest">SIIX-NOZZLE-TRACKING-SYSTEM • CONFIDENTIAL DOCUMENT</p>
            </div>

        </div>
    </div>
</div>

<script>
function signatureFormHandler() {
    return {
        // UPDATE: Bind langsung data lama hasil kiriman dari Controller
        requestor: '{{ old('requestor', $requestData->requestor ?? '') }}',
        line_machine: '{{ old('line_machine', $requestData->line_machine ?? '') }}',
        sparepart_name: '{{ old('sparepart_name', $requestData->sparepart_name ?? '') }}',
        sap_code: '{{ old('sap_code', $requestData->sap_code ?? '') }}',
        qty_req: {{ old('qty_req', $requestData->qty_req ?? 1) }},
        request_no: '{{ $requestData->request_no ?? '' }}',

        activeTab: 'draw', 
        isDrawing: false,
        
        // UPDATE: Tarik tanda tangan & stempel lama dari database draf jika ada
        signatureImg: '{{ $requestData->production_signature ?? '' }}' || null, 
        stampImg: '{{ $requestData->production_stamp ?? '' }}' || null,     
        ctx: null,
        actionType: 'draft', // Default set ke draft untuk amannya

        init() {
            this.$nextTick(() => {
                this.initCanvas();
            });
            
            this.$watch('activeTab', value => {
                if (value === 'draw') {
                    this.$nextTick(() => this.initCanvas());
                }
            });
        },

        initCanvas() {
            const canvas = this.$refs.canvas;
            if (canvas) {
                this.ctx = canvas.getContext('2d');
                canvas.width = canvas.parentNode.clientWidth;
                canvas.height = canvas.parentNode.clientHeight;
                
                this.ctx.strokeStyle = '#000000'; 
                this.ctx.lineWidth = 2.5;         
                this.ctx.lineCap = 'round';
                
                // Menggambar ulang tanda tangan draf lama ke dalam canvas baru
                if (this.signatureImg) {
                    const img = new Image();
                    img.onload = () => this.ctx.drawImage(img, 0, 0);
                    img.src = this.signatureImg;
                }
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
                this.updateLivePreview(); 
            }
        },
        getMousePos(e) {
            const canvas = this.$refs.canvas;
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return {
                x: clientX - rect.left,
                y: clientY - rect.top
            };
        },
        clearCanvas() {
            if(this.$refs.canvas) {
                this.ctx.clearRect(0, 0, this.$refs.canvas.width, this.$refs.canvas.height);
            }
            this.signatureImg = null;
        },
        clearStamp() {
            this.stampImg = null;
        },
        updateLivePreview() {
            if (this.$refs.canvas) {
                this.signatureImg = this.$refs.canvas.toDataURL();
            }
        },

        handleFileUpload(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    this.stampImg = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        generatePDF() {
            window.print();
        },

        submitAs(type) {
            this.actionType = type;
            document.getElementById('requestForm').dispatchEvent(new Event('submit'));
        },

        handleFormAction(e) {
            if (this.activeTab === 'draw' && this.$refs.canvas) {
                const blankCanvas = document.createElement('canvas');
                blankCanvas.width = this.$refs.canvas.width;
                blankCanvas.height = this.$refs.canvas.height;
                if (this.$refs.canvas.toDataURL() !== blankCanvas.toDataURL()) {
                    this.signatureImg = this.$refs.canvas.toDataURL();
                }
            }

            if (!this.requestor || !this.line_machine || !this.sparepart_name || !this.sap_code || !this.qty_req) {
                Swal.fire({
                    icon: 'error',
                    title: 'Data Belum Lengkap!',
                    text: 'Semua kolom input data draf wajib diisi terlebih dahulu, coy!',
                    confirmButtonColor: '#F59E0B'
                });
                return false;
            }

            if (this.actionType === 'draft') {
                Swal.fire({
                    title: 'Update sebagai Draft?',
                    text: "Perubahan akan disimpan ulang sebagai dokumen DRAFT internal.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#F59E0B',
                    cancelButtonColor: '#cbd5e1',
                    confirmButtonText: 'Ya, Update Draft!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('requestForm').submit();
                    }
                });
            } else {
                if (!this.signatureImg && !this.stampImg) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Otorisasi Wajib!',
                        text: 'Harap berikan Tanda Tangan atau Upload Stampel dulu sebelum mengajukan permohonan resmi.',
                        confirmButtonColor: '#3C50E0'
                    });
                    return false;
                }

                Swal.fire({
                    title: 'Kirim Request Resmi?',
                    text: "Dokumen draf ini akan dikunci dan langsung dikirim ke Engineering Dept.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3C50E0',
                    cancelButtonColor: '#f43f5e',
                    confirmButtonText: 'Ya, Kirim Sekarang!',
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