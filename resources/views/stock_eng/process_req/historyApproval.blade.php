@extends('admin')

@section('content')
<div class="-m-4 md:-m-6 2xl:-m-10 bg-slate-50 dark:bg-boxdark-2 min-h-[calc(100vh-80px)] font-sans">
    
    <div class="p-4 md:p-8 2xl:p-10">
        
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-slate-900 dark:text-white">
            <div>
                <h1 class="text-xl md:text-2xl font-black tracking-tight uppercase">Approval History</h1>
                <nav class="flex text-[9px] md:text-[10px] font-black uppercase tracking-widest text-slate-400 mt-1">
                    <span>Engineering</span>
                    <span class="mx-2 text-slate-300">/</span>
                    <span class="text-indigo-600 font-bold uppercase">History Approval</span>
                </nav>
            </div>
            <div class="text-left sm:text-right">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Logged Records</p>
                <p class="text-xs md:text-sm font-bold text-slate-700 dark:text-white uppercase">{{ date('d M Y') }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark shadow-sm overflow-hidden text-slate-900 dark:text-white">
            
            <div class="p-4 md:p-6 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 border-b border-slate-100 dark:border-strokedark">
                <div class="w-full xl:w-auto">
                    <h3 class="text-base md:text-lg font-bold uppercase leading-tight">Archived Verification Logs</h3>
                    <p class="text-[10px] md:text-xs text-slate-500 font-bold uppercase tracking-tight mt-1">Review previously approved or rejected sparepart requests.</p>
                </div>
            </div>

            <div class="p-4 md:p-6 bg-white dark:bg-boxdark border-b border-slate-50 dark:border-strokedark">
                <form action="{{ route('approval.history') }}" method="GET" class="relative w-full lg:w-96">
                    <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="SEARCH REQ NUMBER OR LINE..." class="w-full pl-11 pr-4 py-3 border border-slate-200 dark:border-strokedark rounded-xl focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500 outline-none transition-all dark:bg-meta-4 text-[11px] font-black uppercase">
                </form>
            </div>

            <div class="max-w-full overflow-x-auto scrollbar-hide">
                <table class="w-full text-left border-collapse min-w-[1100px]">
                    <thead>
                        <tr class="border-y border-slate-100 dark:border-strokedark bg-slate-50/50 dark:bg-meta-4/20">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">REQ No.</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Sparepart Info</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Qty</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Machine/Line</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Requestor</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Approver / Action By</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Signature</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Processed Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-strokedark font-bold">
                        @forelse($history as $log)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-meta-4/10 transition-all">
                            <td class="px-6 py-4 text-[11px] text-slate-400 font-black tracking-widest uppercase">
                                {{ $log->request_no }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-meta-4 flex-shrink-0 flex items-center justify-center border border-slate-100 dark:border-strokedark text-slate-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black uppercase leading-none text-slate-700 dark:text-white">{{ $log->sparepart_name }}</span>
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1 leading-none">{{ $log->sap_code }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center text-xs font-black uppercase text-indigo-600">
                                {{ sprintf("%02d", $log->qty_req) }} PCS
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-[10px] font-black uppercase tracking-tight">{{ $log->line_machine }}</span>
                            </td>
                            <td class="px-6 py-4 text-center uppercase text-[10px] text-slate-500 font-black tracking-widest">
                                {{ $log->requestor }}
                            </td>
                            <td class="px-6 py-4 text-center uppercase text-[10px] text-slate-700 dark:text-white font-black tracking-widest">
                                {{ $log->approved_by }}
                            </td>
                            <td class="px-6 py-2 text-center">
                                @if($log->signature_image)
                                    <div class="inline-block bg-white p-1 rounded border border-slate-100 dark:border-strokedark">
                                        <img src="{{ $log->signature_image }}" alt="Signature" class="h-8 w-auto max-w-[100px] object-contain mix-blend-multiply">
                                    </div>
                                @else
                                    <span class="text-[9px] text-slate-400 font-black uppercase tracking-widest">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($log->status === 'APPROVED')
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 text-[9px] font-black tracking-wider uppercase">
                                        Approved
                                    </span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-400 text-[9px] font-black tracking-wider uppercase">
                                        Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-[10px] text-slate-400 font-black uppercase whitespace-nowrap">
                                {{ $log->processed_at->format('d M Y H:i') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="p-12 text-center text-xs font-bold uppercase text-slate-400 tracking-widest">
                                No history records found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($history->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-strokedark flex justify-between items-center bg-slate-50/50 dark:bg-meta-4/20 text-xs font-bold text-slate-500 uppercase">
                <div>
                    Showing {{ $history->firstItem() }} to {{ $history->lastItem() }} of {{ $history->total() }} Records
                </div>
                <div class="flex items-center gap-2">
                    @if ($history->onFirstPage())
                        <span class="p-2 bg-slate-100 dark:bg-meta-4 text-slate-300 rounded-lg cursor-not-allowed">❮</span>
                    @else
                        <a href="{{ $history->previousPageUrl() }}" class="p-2 bg-white dark:bg-boxdark border border-slate-200 dark:border-strokedark hover:bg-slate-50 rounded-lg text-slate-700 dark:text-white">❮</a>
                    @endif

                    @if ($history->hasMorePages())
                        <a href="{{ $history->nextPageUrl() }}" class="p-2 bg-white dark:bg-boxdark border border-slate-200 dark:border-strokedark hover:bg-slate-50 rounded-lg text-slate-700 dark:text-white">❯</a>
                    @else
                        <span class="p-2 bg-slate-100 dark:bg-meta-4 text-slate-300 rounded-lg cursor-not-allowed">❯</span>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection