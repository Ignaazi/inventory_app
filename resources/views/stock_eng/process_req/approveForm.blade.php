@extends('admin')

@section('content')
<!-- Memastikan font Nunito ter-load dengan baik jika belum ada di template utama -->
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;900&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="mx-auto w-full max-w-7xl pb-12 px-4 sm:px-6 font-nunito text-black dark:text-white" x-data="approvalFormHandler()" x-cloak>
    
    <!-- HEADER SECTION -->
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 font-nunito print:hidden">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-black dark:text-white tracking-tight uppercase">
                REVIEW & APPROVAL SPAREPART REQUEST
            </h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-600 dark:text-slate-400">Engineering Electronic Authorization Platform</p>
        </div>
        
        <div class="flex gap-2 self-start sm:self-center">
            <a href="{{ route('eng.approval') }}" class="inline-flex items-center justify-center gap-1.5 bg-gradient-to-r from-slate-700 via-slate-600 to-slate-500 hover:opacity-90 text-white rounded-md px-4 py-2 text-xs font-black uppercase tracking-wider transition-all shadow-md active:scale-95 border-none">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Kembali
            </a>
            <button type="button" @click="generatePDF()" class="flex items-center gap-1.5 bg-gradient-to-r from-red-700 via-red-600 to-red-500 hover:opacity-90 text-white rounded-md px-4 py-2 text-xs font-bold uppercase tracking-wide transition-all shadow-md active:scale-95 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4H7v4a2 2 0 002 2zM9 9V5a2 2 0 012-2h2a2 2 0 012 2v4M7 13h10" />
                </svg>
                DOWNLOAD PDF
            </button>
        </div>
    </div>

    <!-- MAIN INPUT FORM CARD -->
    <div class="bg-white dark:bg-boxdark border border-slate-300 dark:border-strokedark rounded-md shadow-sm overflow-hidden print:hidden mb-10">
        <form id="approvalForm" action="{{ route('eng.approval.approve', $req->id) }}" method="POST" @submit.prevent="handleApprovalSubmit">
            @csrf
            
            @php 
                $hasStaffSigned = !empty($req->staff_signature);
                $isStaffTurn = !$hasStaffSigned && ($req->status === 'Pending');
                
                $hasSpvSigned = !empty($req->spv_signature);
                $isSpvTurn = $hasStaffSigned && !$hasSpvSigned && ($req->status === 'Checked by Staff');
            @endphp

            <input type="hidden" name="signer_role" value="{{ $isStaffTurn ? 'staff' : 'spv' }}">
            <input type="hidden" name="signature_image" x-bind:value="signatureImg">
            <input type="hidden" name="stamp_image" x-bind:value="stampImg">

            <div class="p-5 sm:p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- LEFT SIDE: PARAMETER DATA INPUTS -->
                    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- 1. Requestor -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-black uppercase text-black tracking-wider">NIK / Requestor</label>
                            <input type="text" value="{{ $req->requestor }}" readonly class="w-full rounded-md border border-slate-300 bg-slate-100 py-2 px-3 text-xs font-bold text-black cursor-not-allowed outline-none dark:bg-meta-4/30">
                        </div>

                        <!-- 2. Line Machine -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-black uppercase text-black tracking-wider">Line</label>
                            <input type="text" value="{{ $req->line_machine }}" readonly class="w-full rounded-md border border-slate-300 bg-slate-100 py-2 px-3 text-xs font-bold text-black cursor-not-allowed outline-none dark:bg-meta-4/30">
                        </div>

                        <!-- 3. Sparepart ID -->
                        <div class="flex flex-col gap-1.5 sm:col-span-2">
                            <label class="text-xs font-black uppercase text-black tracking-wider">Sparepart ID</label>
                            <input type="text" value="{{ $req->sparepart_name }}" readonly class="w-full rounded-md border border-slate-300 bg-slate-100 py-2 px-3 text-xs font-bold text-black cursor-not-allowed outline-none dark:bg-meta-4/30">
                        </div>

                        <!-- 4. SAP Code -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-black uppercase text-black tracking-wider">SAP Code</label>
                            <input type="text" value="{{ $req->sap_code ?? '-' }}" readonly class="w-full rounded-md border border-slate-300 bg-slate-100 py-2 px-3 text-xs font-bold text-black cursor-not-allowed outline-none dark:bg-meta-4/30">
                        </div>

                        <!-- 5. Quantity -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-black uppercase text-black tracking-wider">Quantity Requested</label>
                            <input type="text" value="{{ $req->qty_req }}" readonly class="w-full rounded-md border border-slate-300 bg-slate-100 py-2 px-3 text-xs font-bold text-black cursor-not-allowed outline-none dark:bg-meta-4/30">
                        </div>

                        <!-- 6. Remark -->
                        <div class="flex flex-col gap-1.5 sm:col-span-2">
                            <label class="text-xs font-black uppercase text-black tracking-wider">Remark</label>
                            <textarea readonly rows="2" class="w-full rounded-md border border-slate-300 bg-slate-100 py-2 px-3 text-xs font-bold text-black cursor-not-allowed resize-none outline-none dark:bg-meta-4/30">{{ $req->remark }}</textarea>
                        </div>
                    </div>

                    <!-- RIGHT SIDE: LIVE DATABASE DIGITAL AUTHORIZATION DISPLAY -->
                    <div class="flex flex-col justify-between bg-slate-50 dark:bg-slate-900/40 p-4 rounded-md border border-slate-300 dark:border-strokedark relative z-0">
                        @if($hasSpvSigned)
                            <div class="my-auto text-center p-4">
                                <span class="bg-emerald-600 text-white px-3 py-1.5 rounded text-[10px] font-black tracking-wide uppercase">APPROVAL COMPLETED</span>
                                <p class="text-[10px] text-slate-500 font-bold mt-2">Otorisasi form telah selesai diproses.</p>
                            </div>
                        @elseif($req->status === 'Rejected')
                            <div class="my-auto text-center p-4">
                                <span class="bg-rose-600 text-white px-3 py-1.5 rounded text-[10px] font-black tracking-wide uppercase">FORM REJECTED</span>
                                <p class="text-[10px] text-slate-500 font-bold mt-2">{{ $req->reject_remark ?? 'Ditolak' }}</p>
                            </div>
                        @else
                            <div>
                                <div class="border-b border-slate-300 dark:border-strokedark pb-2 mb-3">
                                    <label class="text-xs font-black uppercase tracking-wider text-black dark:text-gray-200">Sign Employee</label>
                                    <p class="text-[10px] text-black font-bold mt-0.5">Otorisasi digital terikat otomatis dengan sistem</p>
                                </div>

                                <div class="relative w-full h-28 bg-white dark:bg-slate-950 border border-slate-300 dark:border-gray-800 rounded-md flex items-center justify-center p-2 overflow-hidden shadow-inner">
                                    <div class="absolute inset-0 z-10 flex items-center justify-center p-2" x-show="signatureImg">
                                        <img :src="signatureImg" class="max-h-full max-w-full object-contain mx-auto my-auto block">
                                    </div>
                                    <div class="absolute inset-0 z-20 flex items-center justify-center p-0 pointer-events-none" x-show="stampImg">
                                        <img :src="stampImg" class="max-h-full max-w-full object-contain mx-auto my-auto block mix-blend-multiply opacity-80">
                                    </div>
                                    
                                    <div class="z-30 text-center" x-show="!signatureImg && !stampImg">
                                        <div class="text-indigo-600 dark:text-indigo-400 font-mono text-[9px] uppercase tracking-wider border border-indigo-200 dark:border-indigo-900 bg-indigo-50 px-2.5 py-1.5 rounded-md">
                                            Secure E-Sign Dynamic<br>
                                            <span class="text-[8px] text-black font-sans font-bold tracking-normal">Linked to: {{ auth()->user()->name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-wider pt-2 border-t border-slate-300 dark:border-strokedark mt-3">
                                <span class="text-black dark:text-white">TTD DATA: 
                                    <span :class="signatureImg ? 'bg-emerald-600 text-white' : 'bg-slate-600 text-white'" class="ml-1 px-2.5 py-1 rounded text-[9px] font-black tracking-wide" x-text="signatureImg ? 'ACTIVE' : 'NONE'"></span>
                                </span>
                                <span class="text-slate-500 font-black">ROLE: <span class="text-indigo-600">{{ $isStaffTurn ? 'STAFF' : 'SPV' }}</span></span>
                            </div>
                        @endif
                    </div>
                </div>

                @if(!$hasSpvSigned && $req->status !== 'Rejected')
                <!-- CONTAINER ACTION FOOTER BUTTONS -->
                <div class="mt-6 pt-4 border-t border-slate-200 dark:border-strokedark flex flex-col sm:flex-row justify-end gap-2.5">
                    <button type="button" @click="triggerReject()" class="w-full sm:w-auto bg-slate-100 hover:bg-slate-200 text-black border-2 border-slate-500 rounded-md px-3 py-1.5 text-xs font-black uppercase tracking-wider transition-all active:scale-95 cursor-pointer">
                        Reject
                    </button>

                    <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-500 hover:opacity-90 text-white rounded-md px-4 py-1.5 text-xs font-black uppercase tracking-wider transition-all active:scale-95 border-none cursor-pointer shadow-md">
                        <svg class="w-3.5 h-3.5 inline-block mr-1 align-middle -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        <span class="align-middle" x-text="isStaffTurn ? 'Checked' : 'Approved'"></span>
                    </button>
                </div>
                @endif
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
                    <p class="text-[9px] text-black font-mono font-bold mt-1">Doc No: {{ $req->request_no ?? 'REQ-PRD-SIIX-001' }}</p>
                </div>
            </div>

            <div class="mb-6">
                <table class="w-full border-collapse text-xs border border-black">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">NIK</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase">{{ $req->requestor }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">LINE</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase">{{ $req->line_machine }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">SPAREPART ID</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase">{{ $req->sparepart_name }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">SAP CODE</td>
                            <td class="py-2.5 px-4 font-mono font-black text-black tracking-wider">{{ $req->sap_code ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">Remark</td>
                            <td class="py-2.5 px-4 font-mono font-black text-black tracking-wider">{{ $req->remark }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">Quantity Requested</td>
                            <td class="py-2.5 px-4 font-black text-black">{{ $req->qty_req }} Pcs</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- DIGITAL SIGNATURE MATRIX GRID -->
            <div class="grid grid-cols-3 gap-0 border border-black text-center text-xs mt-8 rounded-sm overflow-hidden">
                
                <!-- 1. Requested By -->
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-black border-b border-black py-1 uppercase tracking-wider text-[9px] text-black">Requested By</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        <div class="absolute inset-0 z-10 flex items-center justify-center p-1" x-show="prodSignatureImg">
                            <img :src="prodSignatureImg" class="max-h-full max-w-full object-contain mx-auto my-auto block">
                        </div>
                        <div class="absolute inset-0 z-20 flex items-center justify-center p-0 pointer-events-none" x-show="prodStampImg">
                            <img :src="prodStampImg" class="max-h-full max-w-full object-contain mx-auto my-auto block mix-blend-multiply opacity-95">
                        </div>
                        <div class="z-30 px-2 my-auto" x-show="!prodSignatureImg && !prodStampImg">
                            <div class="text-green-600 font-mono text-[9px] font-black uppercase tracking-tighter border border-green-300 bg-green-50 py-0.5 rounded mx-auto max-w-[130px]">
                                VERIFIED
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-black uppercase text-black tracking-wide truncate">{{ $req->requestor }}</p>
                        <p class="text-[9px] text-black font-black uppercase mt-0.5">Production Department</p>
                    </div>
                </div>

                <!-- 2. Checked By (Engineering Staff) -->
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-black border-b border-black py-1 uppercase tracking-wider text-[9px] text-black">Checked By</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        
                        <!-- Live preview ketika Staff sedang proses ttd -->
                        <template x-if="isStaffTurn && signatureImg">
                            <div class="absolute inset-0 z-10 flex items-center justify-center p-1">
                                <img :src="signatureImg" class="max-h-full max-w-full object-contain mx-auto my-auto block">
                            </div>
                        </template>
                        
                        <div class="absolute inset-0 z-10 flex items-center justify-center p-1" x-show="staffSignatureImg">
                            <img :src="staffSignatureImg" class="max-h-full max-w-full object-contain mx-auto my-auto block">
                        </div>
                        <div class="absolute inset-0 z-20 flex items-center justify-center p-1 pointer-events-none" x-show="staffStampImg">
                            <img :src="staffStampImg" class="max-h-full max-w-full object-contain mx-auto my-auto block mix-blend-multiply opacity-95">
                        </div>

                        <span x-show="!staffSignatureImg && (!isStaffTurn || !signatureImg)" class="text-black text-[9px] font-black my-auto">( Pending Stage )</span>
                    </div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-black uppercase text-black tracking-wide truncate">
                            {{ $req->staff_name ?? ($isStaffTurn && auth()->check() ? auth()->user()->name : '( _________________ )') }}
                        </p>
                        <p class="text-[9px] text-black font-black uppercase mt-0.5">Staff Engineering</p>
                    </div>
                </div>

                <!-- 3. Approved By (Engineering SPV) -->
                <div class="flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-black border-b border-black py-1 uppercase tracking-wider text-[9px] text-black">Approved By</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        
                        <!-- Live preview ketika SPV sedang proses ttd -->
                        <template x-if="isSpvTurn && signatureImg">
                            <div class="absolute inset-0 z-10 flex items-center justify-center p-1">
                                <img :src="signatureImg" class="max-h-full max-w-full object-contain mx-auto my-auto block">
                            </div>
                        </template>
                        
                        <div class="absolute inset-0 z-10 flex items-center justify-center p-1" x-show="spvSignatureImg">
                            <img :src="spvSignatureImg" class="max-h-full max-w-full object-contain mx-auto my-auto block">
                        </div>
                        <div class="absolute inset-0 z-20 flex items-center justify-center p-1 pointer-events-none" x-show="spvStampImg">
                            <img :src="spvStampImg" class="max-h-full max-w-full object-contain mx-auto my-auto block mix-blend-multiply opacity-95">
                        </div>

                        <span x-show="!spvSignatureImg && (!isSpvTurn || !signatureImg)" class="text-black text-[9px] font-black my-auto">( Pending Stage )</span>
                    </div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-black uppercase text-black tracking-wide truncate">
                            {{ $req->spv_name ?? ($isSpvTurn && auth()->check() ? auth()->user()->name : '( _________________ )') }}
                        </p>
                        <p class="text-[9px] text-black font-black uppercase mt-0.5">SPV Engineering</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- REJECT SUBMISSION FORM -->
    <form id="rejectFormAction" action="{{ route('eng.approval.reject', $req->id) }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="reason" id="rejectReasonField">
    </form>
</div>

<style>
[x-cloak] { display: none !important; }

/* Menghilangkan CSS cetak global berbahaya untuk mengamankan Sidebar & Navbar Dashboard Monitor */
@media print {
    .print\:hidden {
        display: none !important;
    }
}
</style>

<script>
function approvalFormHandler() {
    const prodSignPath = "{{ $req->production_signature ?? '' }}";
    const prodStampPath = "{{ $req->production_stamp ?? '' }}";
    const staffSignPath = "{{ $req->staff_signature ?? '' }}";
    const staffStampPath = "{{ $req->staff_stamp ?? '' }}";
    const spvSignPath = "{{ $req->spv_signature ?? '' }}";
    const spvStampPath = "{{ $req->spv_stamp ?? '' }}";

    const userAuthSignaturePath = "{{ auth()->check() && auth()->user()->signature_path ? auth()->user()->signature_path : '' }}";

    return {
        isStaffTurn: @json($isStaffTurn),
        isSpvTurn: @json($isSpvTurn),
        stampImg: null,

        signatureImg: userAuthSignaturePath ? (userAuthSignaturePath.startsWith('http') ? userAuthSignaturePath : "{{ asset('storage') }}/" + userAuthSignaturePath.replace(/^\/?(storage\/)?/, '')) : null,

        prodSignatureImg: prodSignPath ? (prodSignPath.startsWith('http') ? prodSignPath : "{{ asset('storage') }}/" + prodSignPath.replace(/^\/?(storage\/)?/, '')) : null,
        prodStampImg: prodStampPath ? (prodStampPath.startsWith('http') ? prodStampPath : "{{ asset('storage') }}/" + prodStampPath.replace(/^\/?(storage\/)?/, '')) : null,
        
        staffSignatureImg: staffSignPath ? (staffSignPath.startsWith('http') ? staffSignPath : "{{ asset('storage') }}/" + staffSignPath.replace(/^\/?(storage\/)?/, '')) : null,
        staffStampImg: staffStampPath ? (staffStampPath.startsWith('http') ? staffStampPath : "{{ asset('storage') }}/" + staffStampPath.replace(/^\/?(storage\/)?/, '')) : null,
        
        spvSignatureImg: spvSignPath ? (spvSignPath.startsWith('http') ? spvSignPath : "{{ asset('storage') }}/" + spvSignPath.replace(/^\/?(storage\/)?/, '')) : null,
        spvStampImg: spvStampPath ? (spvStampPath.startsWith('http') ? spvStampPath : "{{ asset('storage') }}/" + spvStampPath.replace(/^\/?(storage\/)?/, '')) : null,

        generatePDF() {
            window.print();
        },
        triggerReject() {
            Swal.fire({
                title: 'Alasan Tolak Permohonan',
                input: 'textarea',
                inputPlaceholder: 'Tulis alasan penolakan sparepart request disini...',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: 'Ya, Tolak!',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (!value) return 'Alasan wajib diisi!'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('rejectReasonField').value = result.value;
                    document.getElementById('rejectFormAction').submit();
                }
            });
        },
        handleApprovalSubmit() {
            if (!this.signatureImg) {
                Swal.fire({
                    icon: 'error',
                    title: 'Otorisasi Gagal',
                    text: 'Akun Anda belum memiliki file tanda tangan digital di database sistem! Silakan upload TTD di profil terlebih dahulu.',
                    confirmButtonColor: '#2563eb'
                });
                return false;
            }
            
            const isStaff = this.isStaffTurn;
            Swal.fire({
                title: isStaff ? 'Konfirmasi Checked?' : 'Konfirmasi Approval?',
                text: isStaff ? "Request akan ditandai sebagai CHECKED dan diteruskan ke SPV." : "Request akan langsung ditandai sebagai APPROVED.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: 'Ya, Proses!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('approvalForm').submit();
                }
            });
        }
    }
}
</script>
@endsection