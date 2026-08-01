@extends('admin')

@section('content')
<div class="mx-auto w-full max-w-5xl pb-12 px-4 sm:px-6 font-sans text-black">
    
    {{-- BUTTON PANEL (HIDDEN WHEN PRINTING) --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
        <div>
            <h2 class="text-lg font-extrabold text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-2">
                <span class="h-5 w-1.5 bg-orange-500 rounded-full"></span>
                <span>PREVIEW MATERIAL RECEIVED</span>
            </h2>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest mt-0.5">PT SIIX EMS KARAWANG • COSTING SECTION</p>
        </div>
        
        <div class="flex items-center gap-2">
            <a href="{{ route('costing.material.list') }}" class="bg-slate-600 hover:bg-slate-700 text-white font-bold text-xs uppercase py-2 px-4 rounded-lg shadow-sm transition-all active:scale-95">
                Back to List
            </a>
            <button type="button" onclick="window.print()" class="flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg px-4 py-2 text-xs font-bold uppercase transition-all active:scale-95 cursor-pointer">
                PRINT DOKUMEN
            </button>
        </div>
    </div>

    {{-- PRINT CONTAINER AREA --}}
    <div id="print-target-box" class="print:m-0 print:p-0">
        <div class="bg-white text-black p-8 sm:p-12 border border-slate-300 rounded-xl shadow-sm print:border-none print:shadow-none font-sans">
            
            {{-- DOKUMEN HEADER COPORATE --}}
            <div class="flex items-center justify-between border-b-4 border-black pb-4 mb-6">
                <div class="flex items-center gap-4">
                    <img src="/images/logo-siix.png" class="w-16 h-16 object-contain" alt="Logo" onerror="this.style.display='none'">
                    <div>
                        <h1 class="text-lg font-black uppercase text-black tracking-tight">PT. SIIX EMS KARAWANG</h1>
                        <p class="text-[9px] font-bold text-slate-500 tracking-wider uppercase">Electronic Manufacturing Services</p>
                    </div>
                </div>
                <div class="text-right">
                    <h2 class="text-sm font-black text-black border border-black px-3 py-1 bg-slate-50">FORM MATERIAL RECEIVED</h2>
                    <p class="text-[9px] text-orange-600 font-mono mt-1 font-black">MR NO: {{ $mr->no_mr ?? 'MR-SYSTEM-GEN' }}</p>
                </div>
            </div>

            {{-- DETAIL INFORMASI UTAMA DOKUMEN --}}
            <div class="mb-6">
                <table class="w-full border-collapse text-xs border border-black">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black">PR Reference Number</td>
                            <td class="py-2.5 px-4 font-mono font-bold text-black text-sm">{{ $mr->purchaseRequest->no_pr ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black">Sparepart ID & SAP Code</td>
                            <td class="py-2.5 px-4 font-bold text-black uppercase">
                                {{ $mr->purchaseRequest->sparepart->sparepart_id ?? '-' }} 
                                <span class="text-indigo-600 font-mono ml-2">({{ $mr->purchaseRequest->sparepart->sap_code ?? '-' }})</span>
                            </td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black">Part Number & Category</td>
                            <td class="py-2.5 px-4 font-bold text-black uppercase">
                                {{ $mr->purchaseRequest->sparepart->part_number ?? '-' }} 
                                <span class="text-slate-500 font-normal text-[11px] ml-2">/ Category: {{ $mr->purchaseRequest->sparepart->category ?? '-' }}</span>
                            </td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black">Quantity PR Allocation</td>
                            <td class="py-2.5 px-4 font-extrabold text-slate-900">{{ number_format($mr->purchaseRequest->qty_pr ?? 0) }} Pcs</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black">Quantity Received (This Delivery)</td>
                            <td class="py-2.5 px-4 font-black text-orange-600 text-sm">{{ number_format($mr->qty_received) }} Pcs</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black">LOT / BATCH NO</td>
                            <td class="py-2.5 px-4 font-mono font-bold text-black">{{ $mr->lot_no ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black">Quantity Status Balance</td>
                            <td class="py-2.5 px-4 font-bold uppercase text-black">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black border {{ str_contains(strtoupper($mr->qty_status), 'CLOSE') ? 'bg-emerald-50 text-emerald-950 border-emerald-300' : 'bg-amber-50 text-amber-950 border-amber-300' }}">
                                    {{ $mr->qty_status ?? 'OPEN' }}
                                </span>
                            </td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black">Document Workflow Status</td>
                            <td class="py-2.5 px-4 font-bold uppercase">
                                @php
                                    $rawStatus = strtolower($mr->status ?? 'pending');
                                    if(str_contains($rawStatus, 'pending')) {
                                        $badgeStyle = 'bg-amber-50 text-amber-950 border-amber-300';
                                    } elseif(str_contains($rawStatus, 'checked')) {
                                        $badgeStyle = 'bg-blue-50 text-blue-950 border-blue-300';
                                    } elseif(str_contains($rawStatus, 'approved')) {
                                        $badgeStyle = 'bg-emerald-50 text-emerald-950 border-emerald-300';
                                    } else {
                                        $badgeStyle = 'bg-rose-50 text-rose-950 border-rose-300';
                                    }
                                @endphp
                                <span class="px-2 py-0.5 rounded text-[10px] font-black border {{ $badgeStyle }}">
                                    {{ $rawStatus }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- AREA TANDA TANGAN DIGITAL TIGA BERJENJANG --}}
            <div class="grid grid-cols-3 gap-0 border border-black text-center text-xs mt-8">
                
                {{-- 1. PREPARED BY (COSTING STAFF) --}}
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 text-[9px] text-slate-800 uppercase">Prepared By</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden p-1">
                        @if($mr->prepared_signature)
                            <img src="{{ asset('storage/' . $mr->prepared_signature) }}?v={{ time() }}" class="max-h-full max-w-full object-contain mx-auto block" alt="Costing Signature">
                        @else
                            <span class="text-slate-300 italic text-[8px] m-auto">( No Signature )</span>
                        @endif
                    </div>
                    <div class="border-t border-slate-200 py-1.5 bg-white font-bold uppercase underline truncate px-1 text-[10px]">
                        {{ optional($mr->user)->name ? '( ' . $mr->user->name . ' )' : '( Costing Staff )' }}
                    </div>
                </div>

                {{-- 2. CHECKED BY (ENGINEERING STAFF) --}}
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 text-[9px] text-slate-800 uppercase">Checked By</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden p-1">
                        @if($mr->checked_signature)
                            <img src="{{ asset('storage/' . $mr->checked_signature) }}?v={{ time() }}" class="max-h-full max-w-full object-contain mx-auto block" alt="Staff Eng Signature">
                        @elseif(strtolower($mr->status) === 'rejected')
                            <span class="text-red-500 font-black text-[10px] border border-red-500 bg-red-50 px-2 py-0.5 rounded m-auto">REJECTED</span>
                        @else
                            <span class="text-slate-300 italic text-[8px] m-auto">( Waiting Staff Eng )</span>
                        @endif
                    </div>
                    <div class="border-t border-slate-200 py-1.5 bg-white font-bold uppercase truncate px-1 text-[10px]">
                        {{ str_contains(strtolower($mr->status), 'checked') || str_contains(strtolower($mr->status), 'approved') ? '( Staff Engineering )' : '( _________________ )' }}
                    </div>
                </div>

                {{-- 3. APPROVED BY (ENGINEERING SUPERVISOR) --}}
                <div class="flex flex-col justify-between h-36 bg-white">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 text-[9px] text-slate-800 uppercase">Approved By</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden p-1">
                        @if($mr->approved_signature)
                            <img src="{{ asset('storage/' . $mr->approved_signature) }}?v={{ time() }}" class="max-h-full max-w-full object-contain mx-auto block" alt="SPV Signature">
                        @elseif(strtolower($mr->status) === 'rejected')
                            <span class="text-slate-400 italic text-[8px] m-auto">- Stopped -</span>
                        @else
                            <span class="text-slate-300 italic text-[8px] m-auto">( Waiting SPV Eng )</span>
                        @endif
                    </div>
                    <div class="border-t border-slate-200 py-1.5 bg-white font-bold uppercase truncate px-1 text-[10px]">
                        {{ str_contains(strtolower($mr->status), 'approved') ? '( Admin / SPV Engineering )' : '( _________________ )' }}
                    </div>
                </div>

            </div>

            {{-- REMARK / AUDIT NOTES SECTION --}}
            <div class="mt-6">
                <div class="border border-black rounded p-3 bg-slate-50/50">
                    <h4 class="text-[10px] font-bold text-slate-800 uppercase tracking-wide">Material Received System Remarks & Notes:</h4>
                    <p class="text-xs italic text-slate-700 mt-1">" {{ $mr->remark ?? 'No explicit internal notes recorded.' }} "</p>
                </div>
            </div>

            {{-- FOOTER TIME RECOGNITION --}}
            <div class="mt-8 text-left text-[9px] text-slate-400 font-mono print:block hidden">
                Printed System Log Check: {{ now()->format('d/m/Y H:i:s') }} WIB | System Generated Document.
            </div>

        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden !important; background-color: #ffffff !important; color: #000000 !important; }
    #print-target-box, #print-target-box * { visibility: visible !important; }
    #print-target-box { position: absolute !important; left: 0; top: 0; width: 100%; margin: 0; padding: 0; }
}
</style>
@endsection