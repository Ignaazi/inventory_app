@extends('admin')

@section('content')
<div class="-m-4 md:-m-6 2xl:-m-10 bg-slate-50 dark:bg-boxdark-2 min-h-[calc(100vh-80px)]">
    
    <div class="p-4 md:p-8 2xl:p-10">
        
        <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 text-slate-900 dark:text-white">
            <div>
                <h1 class="text-2xl font-bold tracking-tight uppercase">Purchase Requests</h1>
                <nav class="flex text-sm text-slate-500 font-medium mt-1">
                    <span>Engineering</span>
                    <span class="mx-2 text-slate-300">/</span>
                    <span class="text-primary font-bold">Costing & Procurement</span>
                </nav>
            </div>
        </div>

        <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark shadow-sm overflow-hidden">
            
            <div class="p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-slate-100 dark:border-strokedark">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white uppercase">Requests List</h3>
                    <p class="text-sm text-slate-500 font-bold uppercase tracking-tight mt-1">Manage purchase requests and procurement stock.</p>
                </div>
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <button class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2.5 border border-slate-200 dark:border-strokedark rounded-xl text-sm font-bold text-slate-700 dark:text-white hover:bg-slate-50 transition-all uppercase">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Export
                    </button>
                    <button class="flex-1 md:flex-none flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 rounded-xl text-sm font-bold text-white hover:bg-indigo-700 shadow-md shadow-indigo-100 dark:shadow-none transition-all uppercase">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Add Request
                    </button>
                </div>
            </div>

            <div class="p-6 flex flex-col md:flex-row justify-between items-center gap-4 bg-white dark:bg-boxdark">
                <div class="relative w-full md:w-80">
                    <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <input type="text" placeholder="Search..." class="w-full pl-11 pr-4 py-2.5 border border-slate-200 dark:border-strokedark rounded-xl focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500 outline-none transition-all dark:bg-meta-4 text-sm font-bold uppercase">
                </div>
                <button class="w-full md:w-auto flex items-center justify-center gap-2 px-4 py-2.5 border border-slate-200 dark:border-strokedark rounded-xl text-sm font-bold text-slate-700 dark:text-white hover:bg-slate-50 transition-all uppercase">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.5a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Filter
                </button>
            </div>

            <div class="max-w-full overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="border-y border-slate-100 dark:border-strokedark bg-slate-50/50 dark:bg-meta-4/20">
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-widest">No. PR</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-widest">Item Info</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-widest text-center">Urgency</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-widest text-center">SAP Code</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-widest text-center">Status</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-widest text-center">Req. Date</th>
                            <th class="px-6 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-strokedark font-bold">
                        {{-- Row 1 --}}
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-meta-4/10 transition-all">
                            <td class="px-6 py-6 text-xs text-slate-400 font-black">PR-01</td>
                            <td class="px-6 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-meta-4 flex items-center justify-center overflow-hidden border border-slate-100 dark:border-strokedark">
                                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-width="1.5"/></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-slate-800 dark:text-white uppercase">Nozzle Yamaha 221</span>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">QTY REQ: 50 PCS</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center text-xs text-slate-600 dark:text-bodydark uppercase">Urgent</td>
                            <td class="px-6 py-6 text-center">
                                <span class="bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-300 px-3 py-1 rounded-lg text-xs font-black">SAP-NOZ-YMH01</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase">Approved</span>
                            </td>
                            <td class="px-6 py-6 text-center text-xs font-black text-slate-500 uppercase">31 Mar, 2026</td>
                            <td class="px-6 py-6 text-right">
                                <button class="text-slate-400 hover:text-slate-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/></svg></button>
                            </td>
                        </tr>
                        {{-- Row 2 --}}
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-meta-4/10 transition-all">
                            <td class="px-6 py-6 text-xs text-slate-400 font-black">PR-02</td>
                            <td class="px-6 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-meta-4 flex items-center justify-center overflow-hidden border border-slate-100 dark:border-strokedark">
                                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-width="1.5"/></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-slate-800 dark:text-white uppercase">Feeder 8mm Yamaha</span>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">QTY REQ: 10 UNIT</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center text-xs text-slate-600 dark:text-bodydark uppercase">Normal</td>
                            <td class="px-6 py-6 text-center">
                                <span class="bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-300 px-3 py-1 rounded-lg text-xs font-black">SAP-FDR-YMH08</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black bg-rose-50 text-rose-600 border border-rose-100 uppercase">Rejected</span>
                            </td>
                            <td class="px-6 py-6 text-center text-xs font-black text-slate-500 uppercase">28 Mar, 2026</td>
                            <td class="px-6 py-6 text-right">
                                <button class="text-slate-400 hover:text-slate-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/></svg></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-slate-100 dark:border-strokedark">
                <p class="text-xs font-black text-slate-500 uppercase tracking-widest">Showing <span class="text-slate-800 dark:text-white font-black">1 to 2</span> of 50</p>
                <div class="flex items-center gap-2">
                    <button class="w-9 h-9 flex items-center justify-center border border-slate-200 dark:border-strokedark rounded-xl text-slate-400 hover:bg-slate-50 transition-all"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                    <button class="w-9 h-9 flex items-center justify-center bg-indigo-600 rounded-xl text-white text-sm font-black shadow-lg shadow-indigo-100 dark:shadow-none">1</button>
                    <button class="w-9 h-9 flex items-center justify-center border border-slate-200 dark:border-strokedark rounded-xl text-slate-600 dark:text-white text-sm font-black hover:bg-slate-50 transition-all">2</button>
                    <button class="w-9 h-9 flex items-center justify-center border border-slate-200 dark:border-strokedark rounded-xl text-slate-400 hover:bg-slate-50 transition-all"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection