@extends('admin')

@section('content')
<div class="mx-auto w-full max-w-5xl pb-12 px-4 sm:px-6">
    
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
        <div>
            <h2 class="text-lg font-extrabold text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-2">
                <span class="h-5 w-1.5 bg-primary rounded-full"></span>
                <span>PREVIEW SPAREPART REQUEST</span>
            </h2>
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">PT SIIX EMS KARAWANG</p>
        </div>
        
        <div class="flex items-center gap-2 self-start sm:self-center">
            <a href="{{ route('prod.request.list') }}" class="bg-gradient-to-r from-blue-600 via-indigo-500 to-amber-400 hover:from-blue-700 hover:via-indigo-600 hover:to-amber-500 text-white font-bold text-xs uppercase py-2 px-4 rounded-lg transition-all duration-150 active:scale-95 shadow-md shadow-blue-600/10 dark:shadow-none tracking-wide">
                Back to List
            </a>
            <button type="button" onclick="window.print()" class="flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg px-3 py-2 text-xs font-bold uppercase tracking-wide transition-all shadow-sm shadow-red-600/10 active:scale-95">
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

    <div id="print-target-box" class="print:m-0 print:p-0">
        <h3 class="text-xs font-bold uppercase text-slate-400 tracking-wider mb-3 flex items-center gap-2 print:hidden">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
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
                    <h2 class="text-sm font-black uppercase text-black border border-black px-3 py-1 bg-slate-50 tracking-wide">FORM TARGET REQUEST NOZZLE</h2>
                    <p class="text-[8px] text-slate-500 font-mono mt-1 font-bold">
                        Doc No: {{ $requestData->request_no ?? 'REQ-PRD-SIIX-001' }}
                    </p>
                </div>
            </div>

            <div class="mb-6">
                <table class="w-full border-collapse text-xs border border-black">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black text-slate-800">Nama</td>
                            <td class="py-2.5 px-4 font-bold text-black uppercase">{{ $requestData->requestor ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black text-slate-800">LINE</td>
                            <td class="py-2.5 px-4 font-bold text-black uppercase">{{ $requestData->line_machine ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black text-slate-800">NO NOZLE</td>
                            <td class="py-2.5 px-4 font-bold text-black uppercase">{{ $requestData->sparepart_name ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black text-slate-800">Remark</td>
                            <td class="py-2.5 px-4 font-mono font-bold text-black tracking-wider">{{ $requestData->remark ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black text-slate-800">Quantity Requested</td>
                            <td class="py-2.5 px-4 font-bold text-black">{{ $requestData->qty_req ?? 0 }} Pcs</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black text-slate-800">Current Status</td>
                            <td class="py-2.5 px-4 font-bold uppercase">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono border 
                                    {{ $requestData->status === 'Approved' ? 'bg-green-100 text-green-800 border-green-300' : 
                                      ($requestData->status === 'Checked by Staff' ? 'bg-blue-100 text-blue-800 border-blue-300' : 
                                      ($requestData->status === 'Rejected' ? 'bg-red-100 text-red-800 border-red-300' : 'bg-amber-100 text-amber-800 border-amber-300')) }}">
                                    {{ $requestData->status ?? 'Draft' }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="grid grid-cols-3 gap-0 border border-black text-center text-xs mt-8">
                
                <!-- KOLOM 1: REQUESTED BY (PRODUCTION) -->
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 uppercase tracking-wider text-[9px] text-slate-800">Requested By</div>
                    
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        @if($requestData->production_signature && $requestData->production_signature !== 'null')
                            <div class="absolute inset-0 z-10 flex items-center justify-center p-1">
                                <img src="{{ $requestData->production_signature }}" class="max-h-full max-w-full object-contain mx-auto my-auto block" alt="Signature">
                            </div>
                        @endif
                        
                        @if($requestData->production_stamp && $requestData->production_stamp !== 'null')
                            <div class="absolute inset-0 z-20 flex items-center justify-center p-0 pointer-events-none">
                                <img src="{{ $requestData->production_stamp }}" class="max-h-full max-w-full object-contain mx-auto my-auto block mix-blend-multiply opacity-95" alt="Company Stamp">
                            </div>
                        @endif

                        @if($requestData->requestor && (!$requestData->production_signature || $requestData->production_signature === 'null'))
                            <div class="z-30 px-2 my-auto">
                                <div class="text-green-600 font-mono text-[8px] uppercase tracking-tighter border border-green-200 bg-green-50/50 py-0.5 rounded mx-auto max-w-[130px]">
                                    ✓ System Verified<br>
                                    <span class="text-[7px] font-sans">By: {{ $requestData->requestor }}</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-bold uppercase text-black underline tracking-wide truncate">
                            {{ $requestData->requestor ? '( ' . $requestData->requestor . ' )' : '( _________________ )' }}
                        </p>
                        <p class="text-[8px] text-slate-500 font-bold uppercase mt-0.5">Production Department</p>
                    </div>
                </div>

                <!-- KOLOM 2: CHECKED BY (STAFF ENGINEERING) -->
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 uppercase tracking-wider text-[9px] text-slate-800">Checked By</div>
                    
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        @if($requestData->staff_signature && $requestData->staff_signature !== 'null')
                            <!-- Render TTD Staff -->
                            <div class="absolute inset-0 z-10 flex items-center justify-center p-1">
                                <img src="{{ str_starts_with($requestData->staff_signature, 'http') || str_starts_with($requestData->staff_signature, 'data:image') ? $requestData->staff_signature : asset($requestData->staff_signature) }}" class="max-h-full max-w-full object-contain mx-auto" alt="Staff Sign">
                            </div>

                            <!-- Render Stempel Staff (Jika Ada) -->
                            @if($requestData->staff_stamp && $requestData->staff_stamp !== 'null')
                                <div class="absolute inset-0 z-20 flex items-center justify-center p-1 pointer-events-none">
                                    <img src="{{ str_starts_with($requestData->staff_stamp, 'http') || str_starts_with($requestData->staff_stamp, 'data:image') ? $requestData->staff_stamp : asset($requestData->staff_stamp) }}" class="max-h-full max-w-full object-contain mx-auto mix-blend-multiply opacity-95" alt="Staff Stamp">
                                </div>
                            @endif
                        @else
                            <span class="text-slate-300 italic text-[8px] font-medium m-auto">( Waiting Staff )</span>
                        @endif
                    </div>

                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-bold uppercase text-black underline tracking-wide truncate">
                            {{ $requestData->staff_name ? '( ' . $requestData->staff_name . ' )' : '( _________________ )' }}
                        </p>
                        <p class="text-[8px] text-slate-500 font-bold uppercase mt-0.5">Staff Engineering</p>
                    </div>
                </div>

                <!-- KOLOM 3: APPROVED BY (SPV ENGINEERING) -->
                <div class="flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 uppercase tracking-wider text-[9px] text-slate-800">Approved By</div>
                    
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        @if($requestData->spv_signature && $requestData->spv_signature !== 'null')
                            <!-- Render TTD SPV -->
                            <div class="absolute inset-0 z-10 flex items-center justify-center p-1">
                                <img src="{{ str_starts_with($requestData->spv_signature, 'http') || str_starts_with($requestData->spv_signature, 'data:image') ? $requestData->spv_signature : asset($requestData->spv_signature) }}" class="max-h-full max-w-full object-contain mx-auto" alt="SPV Sign">
                            </div>

                            <!-- Render Stempel SPV (Jika Ada) -->
                            @if($requestData->spv_stamp && $requestData->spv_stamp !== 'null')
                                <div class="absolute inset-0 z-20 flex items-center justify-center p-1 pointer-events-none">
                                    <img src="{{ str_starts_with($requestData->spv_stamp, 'http') || str_starts_with($requestData->spv_stamp, 'data:image') ? $requestData->spv_stamp : asset($requestData->spv_stamp) }}" class="max-h-full max-w-full object-contain mx-auto mix-blend-multiply opacity-95" alt="SPV Stamp">
                                </div>
                            @endif
                        @else
                            <span class="text-slate-300 italic text-[8px] font-medium m-auto">( Waiting SPV )</span>
                        @endif
                    </div>

                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-bold uppercase text-black underline tracking-wide truncate">
                            {{ $requestData->spv_name ? '( ' . $requestData->spv_name . ' )' : '( _________________ )' }}
                        </p>
                        <p class="text-[8px] text-slate-500 font-bold uppercase mt-0.5">SPV Engineering</p>
                    </div>
                </div>
            </div>

            <!-- BLOK REMARK PENOLAKAN (Muncul otomatis jika status REJECTED) -->
            @if($requestData->status === 'Rejected' || !empty($requestData->reject_remark))
                <div class="mt-6 border border-red-300 bg-red-50 p-4 rounded-lg print:border-black print:bg-white">
                    <h4 class="text-xs font-black text-red-700 uppercase tracking-wide print:text-black">REJECTION REMARK / ALASAN PENOLAKAN:</h4>
                    <p class="text-xs font-bold text-slate-700 mt-1 italic print:text-black">" {{ $requestData->reject_remark ?? 'Ditolak oleh Engineering.' }} "</p>
                </div>
            @endif

            <div class="mt-8 border-t border-dashed border-slate-300 pt-4 text-center print:block hidden">
                <p class="text-[8px] text-slate-400 font-mono uppercase tracking-widest">SIIX-NOZZLE-TRACKING-SYSTEM • CONFIDENTIAL DOCUMENT</p>
            </div>

        </div>
    </div>
</div>

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
</style>
@endsection