@extends('admin')

@section('content')
<div class="-m-4 md:-m-6 2xl:-m-10 bg-slate-50 dark:bg-boxdark-2 min-h-[calc(100vh-80px)] font-sans">
    
    <div class="p-4 md:p-8 2xl:p-10">
        
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-slate-900 dark:text-white">
            <div>
                <h1 class="text-xl md:text-2xl font-black tracking-tight uppercase">Material Received</h1>
                <nav class="flex text-[9px] md:text-[10px] font-black uppercase tracking-widest text-slate-400 mt-1">
                    <span>Costing</span>
                    <span class="mx-2 text-slate-300">/</span>
                    <span class="text-indigo-600 font-bold uppercase">Inbound Materials</span>
                </nav>
            </div>
            <div class="text-left sm:text-right">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Arrival History</p>
                <p class="text-xs md:text-sm font-bold text-slate-700 dark:text-white uppercase">{{ date('d M Y') }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark shadow-sm overflow-hidden text-slate-900 dark:text-white">
            
            <div class="p-4 md:p-6 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 border-b border-slate-100 dark:border-strokedark">
                <div class="w-full xl:w-auto">
                    <h3 class="text-base md:text-lg font-bold uppercase">Received Log</h3>
                    <p class="text-[10px] md:text-xs text-slate-500 font-bold uppercase tracking-tight mt-1">List of spareparts successfully received into the main warehouse.</p>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full xl:w-auto">
                    <button class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 border border-slate-200 dark:border-strokedark rounded-xl text-[10px] font-black text-slate-700 dark:text-white hover:bg-slate-50 transition-all uppercase tracking-widest">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Export
                    </button>
                    <button class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 rounded-xl text-[10px] font-black text-white hover:bg-indigo-700 shadow-md shadow-indigo-100 dark:shadow-none transition-all uppercase tracking-widest">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        New Arrival
                    </button>
                </div>
            </div>

            <div class="p-4 md:p-6 flex flex-col lg:flex-row justify-between items-center gap-4">
                <div class="relative w-full lg:w-96">
                    <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <input type="text" placeholder="SEARCH PO OR SPAREPART..." class="w-full pl-11 pr-4 py-3 border border-slate-200 dark:border-strokedark rounded-xl focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500 outline-none transition-all dark:bg-meta-4 text-[11px] font-black uppercase">
                </div>
                <div class="w-full lg:w-auto flex flex-wrap items-center gap-2">
                    <button class="flex-1 lg:flex-none flex items-center justify-center gap-2 px-4 py-2.5 border border-slate-200 dark:border-strokedark rounded-xl text-[10px] font-black text-slate-700 dark:text-white hover:bg-slate-50 transition-all uppercase tracking-widest">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.5a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Date Filter
                    </button>
                </div>
            </div>

            <div class="max-w-full overflow-x-auto scrollbar-hide">
                <table class="w-full text-left border-collapse min-w-[1000px]">
                    <thead>
                        <tr class="border-y border-slate-100 dark:border-strokedark bg-slate-50/50 dark:bg-meta-4/20">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Receipt No.</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Sparepart Info</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">SAP Code</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Qty Received</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Arrival</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Status</th>
                            <th class="px-6 py-4 w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-strokedark font-bold">
                        {{-- Data Row Example --}}
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-meta-4/10 transition-all">
                            <td class="px-6 py-6 text-[11px] text-slate-400 font-black tracking-widest uppercase">REC-26-0098</td>
                            <td class="px-6 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-meta-4 flex-shrink-0 flex items-center justify-center border border-slate-100 dark:border-strokedark text-indigo-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" stroke-width="2"/></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs md:text-sm font-black uppercase leading-none">Nozzle Yamaha 221</span>
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1.5 font-sans italic opacity-70">Ref PO: PO-YMH-2026-01</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-300 px-3 py-1 rounded text-[10px] font-black uppercase">SAP-NOZ-YMH01</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="text-xs font-black uppercase text-emerald-600">+ 100 PCS</span>
                            </td>
                            <td class="px-6 py-6 text-center text-[10px] font-black uppercase text-slate-500 tracking-tighter">
                                31 Mar 2026 <br> <span class="text-[8px] opacity-50 font-sans">21:45 WIB</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="px-3 py-1 rounded-full text-[9px] font-black bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase tracking-widest">In Stock</span>
                            </td>
                            <td class="px-6 py-6 text-right">
                                <button class="text-slate-300 hover:text-indigo-600 transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-4 md:p-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-slate-50 dark:border-strokedark bg-white dark:bg-boxdark">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest order-2 md:order-1">Showing <span class="text-slate-900 dark:text-white">1 to 2</span> of 45 Records</p>
                <div class="flex items-center gap-1.5 order-1 md:order-2">
                    <button class="w-8 h-8 flex items-center justify-center border border-slate-100 dark:border-strokedark rounded-lg text-slate-400 hover:bg-slate-50 transition-all"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                    <button class="w-8 h-8 flex items-center justify-center bg-indigo-600 rounded-lg text-white text-[10px] font-black shadow-sm">1</button>
                    <button class="w-8 h-8 flex items-center justify-center border border-slate-100 dark:border-strokedark rounded-lg text-slate-400 hover:bg-slate-50 transition-all"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection