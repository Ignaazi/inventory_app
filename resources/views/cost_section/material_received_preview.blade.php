@extends('admin')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;900&display=swap" rel="stylesheet">

<style>
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

@php
    // Logika Pemecahan Status QTY Balance Secara Presisi
    $isClosed = str_contains(strtoupper($mr->qty_status ?? ''), 'CLOSE');
    $gapText = '0 Pcs';
    $gapValue = 0;
    
    if (!$isClosed && preg_match('/-(\d+)/', $mr->qty_status, $matches)) {
        $gapValue = intval($matches[1]);
        $gapText = '-' . number_format($gapValue) . ' Pcs';
    }
    
    // Kalkulasi Rekonstruksi Saldo Awal Terbuka Sebelum Pengiriman Ini
    $qtyPrOpenBalance = ($mr->qty_received ?? 0) + $gapValue;
@endphp

<div class="mx-auto w-full max-w-7xl pb-12 px-4 sm:px-6 font-nunito text-black dark:text-white">
    
    <!-- HEADER ACTION BUTTON PANEL (HIDDEN WHEN PRINTING) -->
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 font-nunito print:hidden">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-black dark:text-white tracking-tight flex items-center gap-2">
                <span class="h-5 w-1.5 bg-indigo-600 rounded-full"></span>
                <span>PREVIEW MATERIAL RECEIVED</span>
            </h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-600 dark:text-slate-400">Costing Electronic Authorization Platform</p>
        </div>
        
        <div class="flex gap-2 self-start sm:self-center">
            <a href="{{ route('costing.material.list') }}" class="bg-slate-600 hover:bg-slate-700 text-white font-bold text-xs uppercase py-2.5 px-4 rounded-md shadow-sm transition-all active:scale-95">
                Back to List
            </a>
            <button type="button" onclick="window.print()" class="flex items-center gap-1.5 bg-gradient-to-r from-red-700 via-red-600 to-red-500 hover:opacity-90 text-white rounded-md px-4 py-2 text-xs font-bold uppercase tracking-wide transition-all shadow-sm active:scale-95 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4H7v4a2 2 0 002 2zM9 9V5a2 2 0 012-2h2a2 2 0 012 2v4M7 13h10" />
                </svg>
                DOWNLOAD PDF / PRINT
            </button>
        </div>
    </div>

    <!-- 100% PRECISE TARGET BOX RENDERING -->
    <div id="print-target-box" class="print:m-0 print:p-0">
        <div class="bg-white text-black p-8 sm:p-10 border border-slate-300 rounded-md shadow-sm print:border-none print:shadow-none print:p-0 font-nunito">
            
            <!-- COPORATE TOP HEADER -->
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
                    <p class="text-[9px] text-black font-mono font-bold mt-1">Doc No: {{ $mr->no_mr ?? 'MR000001' }}</p>
                </div>
            </div>

            <!-- AUDIT PROPERTY METRICS TABLE -->
            <div class="mb-6">
                <table class="w-full border-collapse text-xs border border-black">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">NIK COSTING STAFF</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase">{{ optional($mr->user)->nik ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">NAME</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase">{{ optional($mr->user)->name ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">PR REFERENCE NO</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase">{{ optional($mr->purchaseRequest)->no_pr ?? '-' }}</td>
                        </tr>
                        
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">SPAREPART ID</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase">{{ optional($mr->purchaseRequest->sparepart)->sparepart_id ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">PART NUMBER</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase">{{ optional($mr->purchaseRequest->sparepart)->part_number ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">SAP CODE</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase">{{ optional($mr->purchaseRequest->sparepart)->sap_code ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">CATEGORY</td>
                            <td class="py-2.5 px-4 font-black text-black uppercase">{{ optional($mr->purchaseRequest->sparepart)->category ?? '-' }}</td>
                        </tr>
                        
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">QTY PR OPEN BALANCE</td>
                            <td class="py-2.5 px-4 font-black text-black">{{ number_format($qtyPrOpenBalance) }} Pcs</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">QTY RECEIVED</td>
                            <td class="py-2.5 px-4 font-black text-indigo-600 font-extrabold">{{ number_format($mr->qty_received) }} Pcs</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">QTY GAP</td>
                            <td class="py-2.5 px-4 font-black font-extrabold flex items-center justify-between {{ $isClosed ? 'text-emerald-600' : 'text-amber-600' }}">
                                <span>{{ $gapText }}</span>
                                <span class="px-2 py-0.5 rounded text-[9px] font-black tracking-wider uppercase print:inline-block {{ $isClosed ? 'border border-emerald-600 bg-emerald-50 text-emerald-700' : 'border border-amber-500 bg-amber-50 text-amber-700' }}">
                                    STATUS: {{ $isClosed ? 'CLOSED' : 'OPEN' }}
                                </span>
                            </td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-black uppercase bg-slate-50 px-3 border-r border-black text-black">REMARK / CONDITION</td>
                            <td class="py-2.5 px-4 font-mono font-black text-black tracking-wider">{{ $mr->remark ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- AUTHORIZATION DIGITAL SIGNATURE STEPS -->
            <div class="grid grid-cols-3 gap-0 border border-black text-center text-xs mt-8 rounded-sm overflow-hidden">
                
                <!-- 1. Costing Staff (Prepared) -->
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white relative z-0">
                    <div class="bg-slate-50 font-black border-b border-black py-1 uppercase tracking-wider text-[9px] text-black">Prepared By (Costing)</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        @if($mr->prepared_signature)
                            <div class="absolute inset-0 z-10 flex items-center justify-center p-1">
                                <img src="{{ asset('storage/' . $mr->prepared_signature) }}?v={{ time() }}" class="max-h-full max-w-full object-contain mx-auto my-auto block" alt="Prepared Signature">
                            </div>
                        @else
                            <div class="z-30 px-2 my-auto">
                                <div class="text-green-600 font-mono text-[9px] font-black uppercase tracking-tighter border border-green-300 bg-green-50 py-0.5 rounded mx-auto max-w-[130px]">
                                    VERIFIED
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-black uppercase text-black tracking-wide truncate">{{ optional($mr->user)->name ?? '( Costing Staff )' }}</p>
                        <p class="text-[9px] text-black font-black uppercase mt-0.5">Costing Section</p>
                    </div>
                </div>

                <!-- 2. Engineering Staff (Checked) -->
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white">
                    <div class="bg-slate-50 font-black border-b border-black py-1 uppercase tracking-wider text-[9px] text-black">Checked By (Engineering / Admin)</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        @if($mr->checked_signature)
                            <div class="absolute inset-0 z-10 flex items-center justify-center p-1">
                                <img src="{{ asset('storage/' . $mr->checked_signature) }}?v={{ time() }}" class="max-h-full max-w-full object-contain mx-auto my-auto block" alt="Checked Signature">
                            </div>
                        @elseif(strtolower($mr->status ?? '') === 'rejected')
                            <span class="text-red-600 font-black text-[10px] border border-red-500 bg-red-50 px-2.5 py-0.5 rounded my-auto uppercase tracking-wide">REJECTED</span>
                        @else
                            <div class="text-slate-500 text-[9px] font-black my-auto italic">( Pending Stage )</div>
                        @endif
                    </div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-black uppercase text-black truncate">
                            {{ str_contains(strtolower($mr->status ?? ''), 'checked') || str_contains(strtolower($mr->status ?? ''), 'approved') ? 'Staff Engineering' : '( _________________ )' }}
                        </p>
                    </div>
                </div>

                <!-- 3. Admin (Approved) -->
                <div class="flex flex-col justify-between h-36 bg-white">
                    <div class="bg-slate-50 font-black border-b border-black py-1 uppercase tracking-wider text-[9px] text-black">Approved By (Admin)</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden mx-auto">
                        @if($mr->approved_signature)
                            <div class="absolute inset-0 z-10 flex items-center justify-center p-1">
                                <img src="{{ asset('storage/' . $mr->approved_signature) }}?v={{ time() }}" class="max-h-full max-w-full object-contain mx-auto my-auto block" alt="Approved Signature">
                            </div>
                        @elseif(strtolower($mr->status ?? '') === 'rejected')
                            <span class="text-slate-400 text-[9px] font-black my-auto italic">- Cancelled -</span>
                        @else
                            <div class="text-slate-500 text-[9px] font-black my-auto italic">( Pending Stage )</div>
                        @endif
                    </div>
                    <div class="border-t border-slate-200 py-1.5 px-1 bg-white">
                        <p class="font-black uppercase text-black truncate">
                            {{ str_contains(strtolower($mr->status ?? ''), 'approved') ? 'Admin' : '( _________________ )' }}
                        </p>
                    </div>
                </div>

            </div>
            
        </div>
    </div>
</div>
@endsection
