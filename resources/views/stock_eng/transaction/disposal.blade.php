@extends('admin')

@section('content')
<div class="-m-4 md:-m-6 2xl:-m-10 bg-slate-50 dark:bg-boxdark-2 min-h-[calc(100vh-80px)] font-sans">
    
    <div class="p-4 md:p-8 2xl:p-10">
        
        <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 text-slate-900 dark:text-white">
            <div>
                <h1 class="text-2xl font-black tracking-tight uppercase">Sparepart Disposal</h1>
                <nav class="flex text-[10px] font-black uppercase tracking-widest text-slate-400 mt-1">
                    <span>Engineering</span>
                    <span class="mx-2 text-slate-300">/</span>
                    <span class="text-amber-600 font-black">Scrap Management</span>
                </nav>
            </div>
            <div class="text-right hidden md:block">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Disposal Authority</p>
                <p class="text-sm font-bold text-slate-700 dark:text-white uppercase">{{ date('d M Y') }}</p>
            </div>
        </div>

        <div class="mb-6 bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark shadow-sm p-6 text-slate-900 dark:text-white">
            <div class="flex flex-col md:flex-row items-center gap-6">
                <div class="w-full md:w-1/4 flex flex-col items-center justify-center p-6 border-2 border-dashed border-amber-100 dark:border-strokedark rounded-2xl bg-amber-50/30 dark:bg-meta-4/10">
                    <svg class="w-16 h-16 text-amber-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="text-[10px] font-black uppercase text-amber-500 tracking-widest">Scan for Scrap</span>
                </div>

                <div class="w-full md:w-3/4 flex flex-col gap-4">
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-500 mb-2 tracking-widest">Scan SAP Code to Dispose Item</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-amber-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 17h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <input type="text" autofocus placeholder="SCAN BARCODE FOR DISPOSAL..." 
                                class="w-full pl-12 pr-4 py-4 bg-slate-50 dark:bg-meta-4 border border-slate-100 dark:border-strokedark rounded-xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none transition-all text-sm font-black uppercase tracking-widest">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-2 font-black">
                        <div class="bg-slate-50 dark:bg-meta-4 p-3 rounded-xl border border-slate-100 dark:border-strokedark">
                            <p class="text-[9px] text-slate-400 uppercase">Part Name</p>
                            <p class="text-xs text-slate-700 dark:text-white uppercase mt-1">---</p>
                        </div>
                        <div class="bg-slate-50 dark:bg-meta-4 p-3 rounded-xl border border-slate-100 dark:border-strokedark">
                            <p class="text-[9px] text-slate-400 uppercase">Reason</p>
                            <select class="w-full bg-transparent text-xs text-slate-700 dark:text-white uppercase outline-none">
                                <option>Broken</option>
                                <option>Lost</option>
                                <option>Expired</option>
                            </select>
                        </div>
                        <div class="bg-slate-50 dark:bg-meta-4 p-3 rounded-xl border border-slate-100 dark:border-strokedark">
                            <p class="text-[9px] text-slate-400 uppercase">Qty Scrap</p>
                            <input type="number" placeholder="0" class="w-full bg-transparent text-xs text-slate-700 dark:text-white outline-none">
                        </div>
                        <button class="bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-[10px] uppercase tracking-widest shadow-lg shadow-amber-100 dark:shadow-none transition-all">
                            Process Disposal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark shadow-sm overflow-hidden text-slate-900 dark:text-white">
            
            <div class="p-6 border-b border-slate-100 dark:border-strokedark flex justify-between items-center bg-white dark:bg-boxdark">
                <div>
                    <h3 class="text-lg font-black uppercase tracking-tight">Recent Disposal History</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Log for all spareparts removed from inventory as scrap.</p>
                </div>
                <button class="px-4 py-2 bg-slate-50 dark:bg-meta-4 border border-slate-100 dark:border-strokedark rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-100 transition-all text-slate-600 dark:text-white">
                    Download Report
                </button>
            </div>

            <div class="max-w-full overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="border-y border-slate-100 dark:border-strokedark bg-slate-50/50 dark:bg-meta-4/20">
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-widest">Date & Time</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-widest">Part Description</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-widest text-center">SAP Code</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-widest text-center">Qty Scrap</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-widest text-center">Reason</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-widest text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-strokedark font-bold">
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-meta-4/5 transition-all">
                            <td class="px-6 py-6 text-[10px] text-slate-400 font-black uppercase tracking-tighter">
                                14:20:05<br><span class="text-[8px]">31 Mar 2026</span>
                            </td>
                            <td class="px-6 py-6">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-slate-800 dark:text-white uppercase">Feeder 8mm Yamaha</span>
                                    <span class="text-[9px] text-slate-400 uppercase tracking-widest mt-0.5">Physical Damage / Broken Pin</span>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-300 px-3 py-1 rounded text-[10px] font-black">SAP-FDR-YMH08</span>
                            </td>
                            <td class="px-6 py-6 text-center text-sm font-black text-amber-600">
                                1 UNIT
                            </td>
                            <td class="px-6 py-6 text-center uppercase text-[10px] text-slate-600 dark:text-slate-300 font-black tracking-widest">
                                BROKEN
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="px-3 py-1 rounded-full text-[9px] font-black bg-slate-100 text-slate-600 border border-slate-200 uppercase tracking-widest">Approved</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-slate-50 dark:border-strokedark bg-white dark:bg-boxdark">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Showing <span class="text-slate-900 dark:text-white">1 to 1</span> Entries</p>
                <div class="flex items-center gap-1.5">
                    <button class="w-8 h-8 flex items-center justify-center border border-slate-100 dark:border-strokedark rounded-lg text-slate-400 hover:bg-slate-50 transition-all"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                    <button class="w-8 h-8 flex items-center justify-center bg-amber-600 rounded-lg text-white text-[10px] font-black shadow-md shadow-amber-100">1</button>
                    <button class="w-8 h-8 flex items-center justify-center border border-slate-100 dark:border-strokedark rounded-lg text-slate-400 hover:bg-slate-50 transition-all"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection