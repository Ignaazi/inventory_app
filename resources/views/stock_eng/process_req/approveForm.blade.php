@extends('admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="mx-auto w-full max-w-5xl pb-12 px-4 sm:px-6" x-data="approvalFormHandler()">
    
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
        <div>
            <h2 class="text-lg font-extrabold text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-2">
                <span class="h-5 w-1.5 bg-indigo-600 rounded-full"></span>
                <span>REVIEW & APPROVAL SPAREPART REQUEST</span>
            </h2>
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">PT SIIX EMS KARAWANG • ENGINEERING DEPARTMENT</p>
        </div>
        
        <div class="flex gap-2 self-start sm:self-center">
            <a href="{{ route('eng.approval') }}" class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg px-3 py-1.5 text-xs font-bold uppercase tracking-wide transition-all border border-slate-200 dark:border-slate-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Kembali
            </a>
            <button type="button" @click="generatePDF()" class="flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg px-3 py-1.5 text-xs font-bold uppercase tracking-wide transition-all shadow-sm active:scale-95">
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

    <div class="bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl shadow-md overflow-hidden print:hidden mb-10">
        <form id="approvalForm" action="{{ route('eng.approval.approve', $req->id) }}" method="POST" @submit.prevent="handleApprovalSubmit($event)">
            @csrf
            
            @php 
                $hasStaffSigned = !empty($req->staff_signature);
                $isStaffTurn = !$hasStaffSigned && ($req->status === 'Pending');
                $hasSpvSigned = !empty($req->spv_signature);
                $isSpvTurn = $hasStaffSigned && !$hasSpvSigned && ($req->status === 'Checked by Staff');
            @endphp

            <input type="hidden" name="signer_role" value="{{ $isStaffTurn ? 'staff' : 'spv' }}">
            <input type="hidden" name="signature_image" x-model="signatureImg">
            <input type="hidden" name="stamp_image" x-model="stampImg">

            <div class="p-5 sm:p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <div class="flex flex-col gap-4 opacity-90">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Name</label>
                            <input type="text" value="{{ $req->requestor }}" readonly class="w-full rounded-lg border border-stroke bg-slate-50 dark:bg-meta-4/30 py-2.5 px-4 text-sm font-bold outline-none dark:border-gray-700 dark:bg-form-input dark:text-white cursor-not-allowed">
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Line</label>
                            <input type="text" value="{{ $req->line_machine }}" readonly class="w-full rounded-lg border border-stroke bg-slate-50 dark:bg-meta-4/30 py-2.5 px-4 text-sm font-bold outline-none dark:border-gray-700 dark:bg-form-input dark:text-white cursor-not-allowed">
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">No Nozle</label>
                            <input type="text" value="{{ $req->sparepart_name }}" readonly class="w-full rounded-lg border border-stroke bg-slate-50 dark:bg-meta-4/30 py-2.5 px-4 text-sm font-bold text-indigo-600 dark:text-indigo-400 outline-none dark:border-gray-700 dark:bg-form-input cursor-not-allowed">
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-2 flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Remark / SAP Code</label>
                                <input type="text" value="{{ $req->sap_code ?? $req->remark }}" readonly class="w-full rounded-lg border border-stroke bg-slate-50 dark:bg-meta-4/30 py-2.5 px-4 text-sm font-mono font-bold text-primary outline-none dark:border-gray-700 dark:bg-form-input cursor-not-allowed">
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Qty Req</label>
                                <input type="text" value="{{ $req->qty_req }}" readonly class="w-full rounded-lg border border-stroke bg-slate-50 dark:bg-meta-4/30 py-2.5 px-4 text-sm font-bold text-center outline-none dark:border-gray-700 dark:bg-form-input dark:text-white cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 bg-slate-50 dark:bg-meta-4 p-4 rounded-xl border border-stroke dark:border-strokedark justify-between relative z-0">
                        @if($hasSpvSigned)
                            <div class="my-auto text-center p-6">
                                <div class="w-12 h-12 bg-green-100 dark:bg-green-950/50 text-green-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                </div>
                                <p class="text-sm font-black text-slate-800 dark:text-white uppercase">DOKUMEN SELESAI</p>
                                <p class="text-[10px] text-slate-400 font-bold mt-1">PROSES APPROVAL SUDAH LENGKAP (STAFF & SPV)</p>
                            </div>
                        @elseif($req->status === 'Rejected')
                            <div class="my-auto text-center p-6">
                                <div class="w-12 h-12 bg-rose-100 dark:bg-rose-950/50 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                                <p class="text-sm font-black text-rose-600 uppercase">FORM REJECTED</p>
                                <p class="text-[10px] text-slate-400 font-bold mt-1">{{ $req->reject_remark ?? 'Ditolak oleh Engineering' }}</p>
                            </div>
                        @else
                            <div>
                                <div class="flex items-center justify-between border-b border-stroke dark:border-strokedark pb-2 mb-3">
                                    <label class="text-[10px] font-bold uppercase text-slate-700 dark:text-slate-300 tracking-wider flex items-center gap-1.5">
                                        <span class="inline-block w-2 h-2 rounded-full animate-pulse" :class="isStaffTurn ? 'bg-indigo-600' : 'bg-emerald-600'"></span>
                                        Otorisasi Approval: <span class="text-primary font-black" x-text="isStaffTurn ? 'STAFF' : 'SPV'"></span>
                                    </label>
                                    <div class="flex gap-1 bg-slate-200 dark:bg-boxdark p-1 rounded-md text-[9px] font-bold">
                                        <button type="button" @click="activeTab = 'draw'" :class="activeTab === 'draw' ? 'bg-primary text-white' : 'text-slate-600 dark:text-slate-400'" class="px-2.5 py-1 rounded transition-all">PAD TTD</button>
                                        <button type="button" @click="activeTab = 'upload'" :class="activeTab === 'upload' ? 'bg-primary text-white' : 'text-slate-600 dark:text-slate-400'" class="px-2.5 py-1 rounded transition-all">UPLOAD STAMP</button>
                                    </div>
                                </div>

                                <div x-show="activeTab === 'draw'" class="w-full">
                                    <p class="text-[10px] text-slate-400 mb-1.5" x-text="isStaffTurn ? '*Gunakan trackpad / mouse untuk tanda tangan Staff Engineering:' : '*Gunakan trackpad / mouse untuk tanda tangan SPV Engineering:'"></p>
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
                                <span class="text-slate-500">USER: <span class="text-slate-800 dark:text-white underline">{{ auth()->user()->name }}</span></span>
                                <div class="flex gap-3">
                                    <span :class="signatureImg ? 'text-green-600' : 'text-slate-400'">TTD: <span x-text="signatureImg ? '✓ READY' : 'EMPTY'"></span></span>
                                    <span :class="stampImg ? 'text-green-600' : 'text-slate-400'">STAMP: <span x-text="stampImg ? '✓ READY' : 'EMPTY'"></span></span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if(!$hasSpvSigned && $req->status !== 'Rejected')
                <div class="mt-8 flex flex-col sm:flex-row gap-2.5 sm:justify-end border-t border-stroke dark:border-strokedark pt-5">
                    
                    {{-- 🔴 BUTTON REJECT: DOMINAN MERAH GRADIENT KUNING --}}
                    <button type="button" @click="triggerReject()" class="flex items-center justify-center gap-1.5 rounded-lg bg-gradient-to-r from-red-600 via-red-500 to-amber-500 hover:from-red-700 hover:to-amber-600 text-white py-2 px-4 text-xs font-bold uppercase tracking-wide shadow-sm transition-all duration-150 active:scale-95 border-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Reject Request
                    </button>

                    {{-- 🔵 BUTTON APPROVE: GRADIENT BIRU & BIRU STABILO --}}
                    <button type="submit" class="flex items-center justify-center gap-1.5 rounded-lg text-white py-2 px-5 text-xs font-bold uppercase tracking-wide shadow-md transition-all duration-150 active:scale-95 bg-gradient-to-r from-blue-600 via-cyan-500 to-emerald-400 hover:from-blue-700 hover:to-cyan-600 border-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                        </svg>
                        <span x-text="isStaffTurn ? 'Approve & Sign As Staff' : 'Final Approve As SPV'"></span>
                    </button>

                </div>
                @endif
            </div>
        </form>
    </div>

    <!-- LIVE VIEW PRINT TARGET BOX -->
    <div id="print-target-box" class="print:m-0 print:p-0">
        <h3 class="text-xs font-bold uppercase text-slate-400 tracking-wider mb-3 flex items-center gap-2 print:hidden">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            Live Preview Form 
        </h3>
        
        <div class="bg-white text-black p-8 sm:p-12 border border-slate-300 rounded-xl shadow-sm print:border-none print:shadow-none print:p-0 font-sans">
            
            <div class="flex items-center justify-between border-b-4 border-black pb-4 mb-6">
                <div class="flex items-center gap-4">
                    {{-- 💡 FIX LOGO SIIX --}}
                    <div class="w-16 h-16 flex items-center justify-center overflow-hidden">
                        <img src="/images/logo-siix.png" class="max-h-full max-w-full object-contain" alt="Logo SIIX" </img>
                    </div>  
                    <div>
                        <h1 class="text-lg font-black uppercase tracking-tight text-black">PT. SIIX EMS KARAWANG</h1>
                        <p class="text-[9px] font-bold text-slate-500 tracking-wider uppercase">Electronic Manufacturing Services</p>
                    </div>
                </div>
                <div class="text-right">
                    <h2 class="text-sm font-black uppercase text-black border border-black px-3 py-1 bg-slate-50 tracking-wide">FORM REQUEST NOZZLE</h2>
                    {{-- 💡 FIX DOC NO SESUAI DATABASE --}}
                    <p class="text-[8px] text-slate-500 font-mono mt-1">Doc No: {{ $req->request_no ?? 'REQ-PRD-SIIX-001' }}</p>
                </div>
            </div>

            <div class="mb-6">
                <table class="w-full border-collapse text-xs border border-black">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black text-slate-800">Nama</td>
                            <td class="py-2.5 px-4 font-bold text-black uppercase">{{ $req->requestor }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black text-slate-800">LINE</td>
                            <td class="py-2.5 px-4 font-bold text-black uppercase">{{ $req->line_machine }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black text-slate-800">NO NOZLE</td>
                            <td class="py-2.5 px-4 font-bold text-black uppercase">{{ $req->sparepart_name }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black text-slate-800">Remark / SAP Code</td>
                            <td class="py-2.5 px-4 font-mono font-bold text-black tracking-wider">{{ $req->sap_code ?? $req->remark }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black text-slate-800">Quantity Requested</td>
                            <td class="py-2.5 px-4 font-bold text-black">{{ $req->qty_req }} Pcs</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- THREE COLUMN SIGNATURE WORKFLOW -->
            <div class="grid grid-cols-3 gap-0 border border-black text-center text-xs mt-8">
                
                <!-- KOLOM 1: REQUESTED BY (PRODUCTION) -->
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 uppercase tracking-wider text-[9px] text-slate-800">Requested By</div>
                    
                    <div class="relative flex items-center justify-center h-20 w-full overflow-hidden p-1">
                        @if($req->production_signature && $req->production_signature !== 'null' && str_starts_with($req->production_signature, 'data:image'))
                            <div class="absolute inset-0 z-10 flex items-center justify-center p-1">
                                <img src="{{ $req->production_signature }}" class="max-h-full max-w-full object-contain mx-auto my-auto block" alt="Prod Signature">
                            </div>
                        @endif
                        
                        @if($req->production_stamp && $req->production_stamp !== 'null' && str_starts_with($req->production_stamp, 'data:image'))
                            <div class="absolute inset-0 z-20 flex items-center justify-center p-1 pointer-events-none">
                                <img src="{{ $req->production_stamp }}" class="max-h-full max-w-full object-contain mx-auto my-auto block mix-blend-multiply opacity-95" alt="Prod Stamp">
                            </div>
                        @endif

                        @if(!$req->production_signature && !$req->production_stamp)
                            <div class="z-30 px-2 my-auto">
                                <div class="text-green-600 font-mono text-[8px] uppercase tracking-tighter border border-green-200 bg-green-50/50 py-0.5 rounded mx-auto max-w-[130px]">
                                    ✓ System Verified<br>
                                    <span class="text-[7px] font-sans">By: {{ $req->requestor }}</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-bold uppercase text-black underline tracking-wide truncate">{{ $req->requestor }}</p>
                        <p class="text-[8px] text-slate-500 font-bold uppercase mt-0.5">Production Department</p>
                    </div>
                </div>

                <!-- KOLOM 2: CHECKED BY (STAFF ENGINEERING) -->
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 uppercase tracking-wider text-[9px] text-slate-800">Checked By</div>
                    <div class="relative flex items-center justify-center h-20 w-full overflow-hidden p-1">
                        
                        <!-- Live Render TTD jika Staff menggambar -->
                        <template x-if="isStaffTurn && signatureImg">
                            <div class="absolute inset-0 z-10 flex items-center justify-center p-1">
                                <img :src="signatureImg" class="max-h-full max-w-full object-contain mx-auto my-auto block">
                            </div>
                        </template>

                        <!-- Live Render Stamp jika Staff upload file -->
                        <template x-if="isStaffTurn && stampImg">
                            <div class="absolute inset-0 z-20 flex items-center justify-center p-1 pointer-events-none">
                                <img :src="stampImg" class="max-h-full max-w-full object-contain mx-auto my-auto block mix-blend-multiply opacity-95">
                            </div>
                        </template>
                        
                        <!-- Render dari DB jika database sudah menyimpan data TTD Staff -->
                        @if($hasStaffSigned)
                            <div class="absolute inset-0 z-10 flex items-center justify-center p-1">
                                <img src="{{ $req->staff_signature }}" class="max-h-full max-w-full object-contain mx-auto my-auto block" alt="Staff Sign">
                            </div>
                            @if($req->staff_stamp)
                                <div class="absolute inset-0 z-20 flex items-center justify-center p-1 pointer-events-none">
                                    <img src="{{ $req->staff_stamp }}" class="max-h-full max-w-full object-contain mx-auto my-auto block mix-blend-multiply opacity-95">
                                </div>
                            @endif
                        @elseif(!$hasStaffSigned)
                            <span x-show="!isStaffTurn || (!signatureImg && !stampImg)" class="text-slate-300 italic text-[8px] font-medium my-auto">( Waiting Staff Checked )</span>
                        @endif
                    </div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-bold uppercase text-black underline tracking-wide truncate">
                            {{ $req->staff_name ?? ($isStaffTurn && auth()->check() ? auth()->user()->name : '( _________________ )') }}
                        </p>
                        <p class="text-[8px] text-slate-500 font-bold uppercase mt-0.5">Staff Engineering</p>
                    </div>
                </div>

                <!-- KOLOM 3: APPROVED BY (SPV ENGINEERING) -->
                <div class="flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 uppercase tracking-wider text-[9px] text-slate-800">Approved By</div>
                    <div class="relative flex items-center justify-center h-20 w-full overflow-hidden p-1">
                        
                        <!-- Live Render TTD jika SPV menggambar -->
                        <template x-if="isSpvTurn && signatureImg">
                            <div class="absolute inset-0 z-10 flex items-center justify-center p-1">
                                <img :src="signatureImg" class="max-h-full max-w-full object-contain mx-auto my-auto block">
                            </div>
                        </template>

                        <!-- Live Render Stamp jika SPV upload file -->
                        <template x-if="isSpvTurn && stampImg">
                            <div class="absolute inset-0 z-20 flex items-center justify-center p-1 pointer-events-none">
                                <img :src="stampImg" class="max-h-full max-w-full object-contain mx-auto my-auto block mix-blend-multiply opacity-95">
                            </div>
                        </template>
                        
                        <!-- Render dari DB jika database sudah menyimpan data TTD SPV -->
                        @if($hasSpvSigned)
                            <div class="absolute inset-0 z-10 flex items-center justify-center p-1">
                                <img src="{{ $req->spv_signature }}" class="max-h-full max-w-full object-contain mx-auto my-auto block" alt="SPV Sign">
                            </div>
                            @if($req->spv_stamp)
                                <div class="absolute inset-0 z-20 flex items-center justify-center p-1 pointer-events-none">
                                    <img src="{{ $req->spv_stamp }}" class="max-h-full max-w-full object-contain mx-auto my-auto block mix-blend-multiply opacity-95">
                                </div>
                            @endif
                        @elseif(!$hasSpvSigned)
                            <span x-show="!isSpvTurn || (!signatureImg && !stampImg)" class="text-slate-300 italic text-[8px] font-medium my-auto">( Waiting Final Approval )</span>
                        @endif
                    </div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-bold uppercase text-black underline tracking-wide truncate">
                            {{ $req->spv_name ?? ($isSpvTurn && auth()->check() ? auth()->user()->name : '( _________________ )') }}
                        </p>
                        <p class="text-[8px] text-slate-500 font-bold uppercase mt-0.5">SPV Engineering</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 border-t border-dashed border-slate-300 pt-4 text-center print:block hidden">
                <p class="text-[8px] text-slate-400 font-mono uppercase tracking-widest">SIIX-NOZZLE-TRACKING-SYSTEM • CONFIDENTIAL DOCUMENT</p>
            </div>

        </div>
    </div>

    <form id="rejectFormAction" action="{{ route('eng.approval.reject', $req->id) }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="reason" id="rejectReasonField">
    </form>
</div>

<script>
function approvalFormHandler() {
    return {
        isStaffTurn: @json($isStaffTurn),
        isSpvTurn: @json($isSpvTurn),
        
        activeTab: 'draw',
        isDrawing: false,
        signatureImg: null,     
        stampImg: null,
        ctx: null,

        init() {
            if (this.isStaffTurn || this.isSpvTurn) {
                this.$nextTick(() => {
                    this.initCanvas();
                });
            }
            
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
                const rect = canvas.parentNode.getBoundingClientRect();
                canvas.width = rect.width;
                canvas.height = rect.height;
                
                this.ctx.strokeStyle = '#0f172a'; 
                this.ctx.lineWidth = 3;         
                this.ctx.lineCap = 'round';
                this.ctx.lineJoin = 'round';
            }
        },

        startDrawing(e) {
            this.isDrawing = true;
            const pos = this.getMousePos(e);
            this.ctx.beginPath();
            this.ctx.moveTo(pos.x, pos.y);
            if (e.cancelable) e.preventDefault();
        },
        draw(e) {
            if (!this.isDrawing) return;
            const pos = this.getMousePos(e);
            this.ctx.lineTo(pos.x, pos.y);
            this.ctx.stroke();
            if (e.cancelable) e.preventDefault();
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
            const clientX = e.clientX || (e.touches && e.touches[0] ? e.touches[0].clientX : 0);
            const clientY = e.clientY || (e.touches && e.touches[0] ? e.touches[0].clientY : 0);
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

        handleApprovalSubmit(e) {
            if (this.activeTab === 'draw' && this.$refs.canvas) {
                const blankCanvas = document.createElement('canvas');
                blankCanvas.width = this.$refs.canvas.width;
                blankCanvas.height = this.$refs.canvas.height;
                
                if (this.$refs.canvas.toDataURL() === blankCanvas.toDataURL()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tanda Tangan Kosong!',
                        text: 'Silakan bubuhkan tanda tangan verifikasi Anda terlebih dahulu pada Pad!',
                        confirmButtonColor: '#3C50E0'
                    });
                    return false;
                }
            }

            if (this.activeTab === 'upload' && !this.stampImg) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stempel Kosong!',
                    text: 'Silakan upload file stempel otorisasi Anda terlebih dahulu!',
                    confirmButtonColor: '#3C50E0'
                });
                return false;
            }

            Swal.fire({
                title: 'Konfirmasi Approval?',
                text: "Otorisasi digital Anda akan disimpan pada log berkas cetak SIIX.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: 'Ya, Approve!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('approvalForm').submit();
                }
            });
        },

        triggerReject() {
            Swal.fire({
                title: 'Tolak Request Sparepart?',
                text: 'Berikan alasan singkat penolakan berkas:',
                input: 'text',
                inputPlaceholder: 'Contoh: Nozzle Stock Kosong / Salah Model...',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: 'Ya, Tolak!',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Alasan reject wajib diisi, bro!'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('rejectReasonField').value = result.value;
                    document.getElementById('rejectFormAction').submit();
                }
            });
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