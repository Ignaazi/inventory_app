@extends('admin')

@section('content')
<div class="-m-4 md:-m-6 2xl:-m-10 bg-slate-50 dark:bg-boxdark-2 min-h-[calc(100vh-80px)] font-sans">
    
    <div class="p-4 md:p-8 2xl:p-10">
        
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-slate-900 dark:text-white">
            <div>
                <h1 class="text-xl md:text-2xl font-black tracking-tight uppercase">PR Approval</h1>
                <nav class="flex text-[9px] md:text-[10px] font-black uppercase tracking-widest text-slate-400 mt-1">
                    <span>Costing</span>
                    <span class="mx-2 text-slate-300">/</span>
                    <span class="text-indigo-600">Pending Requests</span>
                </nav>
            </div>
            <div class="text-left sm:text-right">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Awaiting Verification</p>
                <p class="text-xs md:text-sm font-bold text-slate-700 dark:text-white uppercase">{{ date('d M Y') }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark shadow-sm overflow-hidden">
            
            <div class="p-4 md:p-6 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 border-b border-slate-100 dark:border-strokedark">
                <div class="w-full xl:w-auto">
                    <h3 class="text-base md:text-lg font-bold text-slate-800 dark:text-white uppercase leading-tight">Waiting Approval</h3>
                    <p class="text-[10px] md:text-xs text-slate-500 font-bold uppercase tracking-tight mt-1">Review and verify purchase requests from engineering.</p>
                </div>
                <div class="flex items-center gap-3 w-full xl:w-auto">
                    <button class="flex-1 xl:flex-none flex items-center justify-center gap-2 px-4 py-2.5 border border-slate-200 dark:border-strokedark rounded-xl text-[10px] font-black text-slate-700 dark:text-white hover:bg-slate-50 transition-all uppercase tracking-widest">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Export PR
                    </button>
                </div>
            </div>

            <div class="p-4 md:p-6 flex flex-col lg:flex-row justify-between items-center gap-4 bg-white dark:bg-boxdark">
                <div class="relative w-full lg:w-96">
                    <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <input type="text" placeholder="Search PR Number..." class="w-full pl-11 pr-4 py-3 border border-slate-200 dark:border-strokedark rounded-xl focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500 outline-none transition-all dark:bg-meta-4 text-[11px] font-black uppercase">
                </div>
                <button class="w-full lg:w-auto flex items-center justify-center gap-2 px-4 py-3 border border-slate-200 dark:border-strokedark rounded-xl text-[10px] font-black text-slate-700 dark:text-white hover:bg-slate-50 transition-all uppercase tracking-widest">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.5a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Urgency Filter
                </button>
            </div>

            <div class="max-w-full overflow-x-auto scrollbar-hide">
                <table class="w-full text-left border-collapse min-w-[1000px]">
                    <thead>
                        <tr class="border-y border-slate-100 dark:border-strokedark bg-slate-50/50 dark:bg-meta-4/20">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">PR No.</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Item Info</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Urgency</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Qty Req</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Requested By</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Verification</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-strokedark font-bold">
                        {{-- Row 1 --}}
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-meta-4/10 transition-all text-slate-900 dark:text-white">
                            <td class="px-6 py-6 text-[11px] text-slate-400 font-black tracking-widest uppercase">PR-2026-001</td>
                            <td class="px-6 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-slate-100 dark:bg-meta-4 flex-shrink-0 flex items-center justify-center border border-slate-100 dark:border-strokedark text-slate-400">
                                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke-width="1.5"/></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs md:text-sm font-black uppercase leading-none">Nozzle Yamaha 221</span>
                                        <span class="text-[9px] font-black text-indigo-600 uppercase tracking-widest mt-1.5 leading-none">SAP-NOZ-YMH01</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="bg-rose-50 dark:bg-rose-500/10 text-rose-600 px-3 py-1 rounded text-[9px] font-black uppercase tracking-widest border border-rose-100/50">Urgent</span>
                            </td>
                            <td class="px-6 py-6 text-center text-xs font-black uppercase">50 PCS</td>
                            <td class="px-6 py-6 text-center uppercase text-[10px] text-slate-500 font-black tracking-widest">ENGINEERING DEPT</td>
                            <td class="px-6 py-6">
                                <div class="flex items-center justify-center gap-3">
                                    <button class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-all shadow-lg shadow-rose-100 dark:shadow-none border border-rose-600">
                                        Reject
                                    </button>
                                    <button class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-all shadow-lg shadow-emerald-100 dark:shadow-none border border-emerald-600">
                                        Approve
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Row 2 --}}
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-meta-4/10 transition-all text-slate-900 dark:text-white">
                            <td class="px-6 py-6 text-[11px] text-slate-400 font-black tracking-widest uppercase">PR-2026-002</td>
                            <td class="px-6 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-slate-100 dark:bg-meta-4 flex-shrink-0 flex items-center justify-center border border-slate-100 dark:border-strokedark text-slate-400">
                                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke-width="1.5"/></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs md:text-sm font-black uppercase leading-none">Feeder 8mm Yamaha</span>
                                        <span class="text-[9px] font-black text-indigo-600 uppercase tracking-widest mt-1.5 leading-none">SAP-FDR-YMH08</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="bg-slate-50 dark:bg-meta-4 text-slate-500 px-3 py-1 rounded text-[9px] font-black uppercase tracking-widest border border-slate-200">Normal</span>
                            </td>
                            <td class="px-6 py-6 text-center text-xs font-black uppercase">10 UNIT</td>
                            <td class="px-6 py-6 text-center uppercase text-[10px] text-slate-500 font-black tracking-widest">ENGINEERING DEPT</td>
                            <td class="px-6 py-6">
                                <div class="flex items-center justify-center gap-3">
                                    <button class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-all shadow-lg shadow-rose-100 dark:shadow-none border border-rose-600">
                                        Reject
                                    </button>
                                    <button class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-all shadow-lg shadow-emerald-100 dark:shadow-none border border-emerald-600">
                                        Approve
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-4 md:p-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-slate-50 dark:border-strokedark bg-white dark:bg-boxdark">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest order-2 md:order-1">Showing <span class="text-slate-900 dark:text-white">1 to 2</span> of 12 Pending PR</p>
                <div class="flex items-center gap-1.5 order-1 md:order-2">
                    <button class="w-8 h-8 flex items-center justify-center border border-slate-100 dark:border-strokedark rounded-lg text-slate-400 hover:bg-slate-50 transition-all"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                    <button class="w-8 h-8 flex items-center justify-center bg-indigo-600 rounded-lg text-white text-[10px] font-black">1</button>
                    <button class="w-8 h-8 flex items-center justify-center border border-slate-100 dark:border-strokedark rounded-lg text-slate-400 hover:bg-slate-50 transition-all"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection