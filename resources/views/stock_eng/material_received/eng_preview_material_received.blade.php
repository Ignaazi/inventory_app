@extends('admin')

@section('content')
<div class="mx-auto w-full max-w-5xl pb-12 px-4 sm:px-6">
    
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
        <div>
            <h2 class="text-lg font-extrabold text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-2">
                <span class="h-5 w-1.5 bg-primary rounded-full"></span>
                <span>PREVIEW ENG MATERIAL RECEIVING</span>
            </h2>
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">PT SIIX EMS KARAWANG • ENGINEERING SECTION</p>
        </div>
        
        <div class="flex items-center gap-2">
            <a href="{{ route('eng.material.receiving.index') }}" class="bg-slate-600 hover:bg-slate-700 text-white font-bold text-xs uppercase py-2 px-4 rounded-lg shadow-sm transition-all active:scale-95">
                Back to List
            </a>
            <button type="button" onclick="window.print()" class="flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg px-4 py-2 text-xs font-bold uppercase transition-all active:scale-95">
                PRINT DOKUMEN
            </button>
        </div>
    </div>

    <div id="print-target-box" class="print:m-0 print:p-0">
        <div class="bg-white text-black p-8 sm:p-12 border border-slate-300 rounded-xl shadow-sm print:border-none print:shadow-none font-sans">
            
            <div class="flex items-center justify-between border-b-4 border-black pb-4 mb-6">
                <div class="flex items-center gap-4">
                    <img src="/images/logo-siix.png" class="w-16 h-16 object-contain" alt="Logo" onerror="this.style.display='none'">
                    <div>
                        <h1 class="text-lg font-black uppercase text-black">PT. SIIX EMS KARAWANG</h1>
                        <p class="text-[9px] font-bold text-slate-500 tracking-wider uppercase">Electronic Manufacturing Services</p>
                    </div>
                </div>
                <div class="text-right">
                    <h2 class="text-sm font-black text-black border border-black px-3 py-1 bg-slate-50">FORM MATERIAL RECEIVED</h2>
                    <p class="text-[8px] text-slate-500 font-mono mt-1 font-bold">RECEIVING CODE: {{ $receiving->receiving_code }}</p>
                </div>
            </div>

            <div class="mb-6">
                <table class="w-full border-collapse text-xs border border-black">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black">Receiving Code Form</td>
                            <td class="py-2.5 px-4 font-mono font-bold text-black">{{ $receiving->receiving_code }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black">Purchase Request (PR) Reference</td>
                            <td class="py-2.5 px-4 font-mono font-bold text-black">{{ $receiving->pr_code }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black">Item / Material Name</td>
                            <td class="py-2.5 px-4 font-bold text-black uppercase">{{ $receiving->item_name }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black">Quantity Received</td>
                            <td class="py-2.5 px-4 font-bold text-black">{{ number_format($receiving->qty_received) }} Pcs</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black">LOT / BATCH NO</td>
                            <td class="py-2.5 px-4 font-mono font-bold text-black">{{ $receiving->lot_no ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2.5 font-bold uppercase bg-slate-50 px-3 border-r border-black">Tracking Status TTD</td>
                            <td class="py-2.5 px-4 font-bold uppercase">
                                <span class="px-2 py-0.5 rounded text-[10px] border {{ $receiving->status === 'completed' ? 'bg-green-100 text-green-800 border-green-300' : 'bg-amber-100 text-amber-800 border-amber-300' }}">
                                    {{ str_replace('_', ' ', $receiving->status) }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="grid grid-cols-3 gap-0 border border-black text-center text-xs mt-8">
                <div class="border-r border-black flex flex-col justify-between h-36 bg-white">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 text-[9px] text-slate-800 uppercase">1. Prepared By</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden p-1">
                        @if($receiving->eng_signature_path && file_exists(public_path($receiving->eng_signature_path)))
                            <img src="{{ asset($receiving->eng_signature_path) }}?v={{ time() }}" class="max-h-full max-w-full object-contain mx-auto block" alt="Eng Staff Signature">
                        @else
                            <span class="text-slate-300 italic text-[8px] m-auto">( No Signature )</span>
                        @endif
                    </div>
                    <div class="border-t border-slate-200 py-1.5 bg-white font-bold uppercase underline truncate px-1">
                        {{ $receiving->created_by_name ? '( ' . $receiving->created_by_name . ' )' : '( _________________ )' }}
                    </div>
                </div>

                <div class="border-r border-black flex flex-col justify-between h-36 bg-white">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 text-[9px] text-slate-800 uppercase">2. Checked By</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden p-1">
                        @if($receiving->status === 'approved_by_spv' || $receiving->status === 'completed')
                            <span class="text-green-700 font-bold text-[10px] m-auto">🟢 SIGNED BY SPV</span>
                        @elseif($receiving->status === 'rejected')
                            <span class="text-red-500 font-black text-[10px] border border-red-500 bg-red-50 px-2 py-0.5 rounded m-auto">REJECTED</span>
                        @else
                            <span class="text-slate-300 italic text-[8px] m-auto">( Waiting SPV Eng )</span>
                        @endif
                    </div>
                    <div class="border-t border-slate-200 py-1.5 bg-white font-bold uppercase truncate px-1">
                        ( Supervisor Engineering )
                    </div>
                </div>

                <div class="flex flex-col justify-between h-36 bg-white">
                    <div class="bg-slate-50 font-bold border-b border-black py-1 text-[9px] text-slate-800 uppercase">3. Acknowledged By</div>
                    <div class="relative flex items-center justify-center h-20 w-full bg-white overflow-hidden p-1">
                        @if($receiving->status === 'completed')
                            <span class="text-emerald-700 font-bold text-[10px] m-auto">🟢 FULL APPROVED</span>
                        @elseif($receiving->status === 'rejected')
                            <span class="text-slate-400 italic text-[8px] m-auto">- Stopped -</span>
                        @else
                            <span class="text-slate-300 italic text-[8px] m-auto">( Waiting Costing Section )</span>
                        @endif
                    </div>
                    <div class="border-t border-slate-200 py-1.5 bg-white font-bold uppercase truncate px-1">
                        ( Costing Department )
                    </div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 print:grid-cols-2">
                <div class="border border-slate-300 rounded p-3 bg-slate-50/50">
                    <h4 class="text-[10px] font-bold text-slate-700 uppercase">Engineering Notes:</h4>
                    <p class="text-xs italic text-slate-600 mt-1">" {{ $receiving->engineering_notes ?? 'No internal notes recorded.' }} "</p>
                </div>
                <div class="border border-slate-300 rounded p-3 bg-slate-50/50">
                    <h4 class="text-[10px] font-bold text-slate-700 uppercase">Costing Notes:</h4>
                    <p class="text-xs italic text-slate-600 mt-1">" {{ $receiving->costing_notes ?? 'No costing remarks recorded.' }} "</p>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden !important; }
    #print-target-box, #print-target-box * { visibility: visible !important; }
    #print-target-box { position: absolute !important; left: 0; top: 0; width: 100%; margin: 0; padding: 0; }
}
</style>
@endsection