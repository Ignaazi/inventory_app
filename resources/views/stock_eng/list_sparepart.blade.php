@extends('admin')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white uppercase tracking-tight">List Sparepart Eng</h2>
            <p class="text-xs text-bodydark2 uppercase font-bold tracking-widest mt-1">Engineering Master Data Inventory</p>
        </div>
        <div class="flex gap-3">
             <button class="bg-white border border-stroke dark:border-strokedark dark:bg-boxdark text-slate-600 dark:text-white px-5 py-2.5 rounded-xl font-black text-xs uppercase shadow-sm hover:bg-slate-50 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export Excel
            </button>
            <button class="bg-indigo-600 hover:bg-opacity-90 text-white px-5 py-2.5 rounded-xl font-black text-xs uppercase shadow-md transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add New Sparepart
            </button>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-4 items-center justify-between bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
        <div class="relative w-full md:w-1/2">
            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" placeholder="Search by SAP Code or Part Name..." class="w-full pl-11 pr-5 py-3 rounded-xl border border-stroke bg-slate-50 focus:bg-white focus:border-primary focus-visible:outline-none dark:border-strokedark dark:bg-meta-4 dark:text-white text-sm transition-all">
        </div>
        <div class="flex gap-2 w-full md:w-auto">
            <select class="w-full md:w-40 rounded-xl border border-stroke bg-slate-50 px-4 py-3 text-sm dark:border-strokedark dark:bg-meta-4 dark:text-white outline-none">
                <option value="">All Categories</option>
                <option value="nozzle">Nozzle</option>
                <option value="feeder">Feeder</option>
                <option value="motor">Motor</option>
            </select>
        </div>
    </div>

    <div class="rounded-2xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark overflow-hidden">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-left">
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider">No.</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider">SAP Code</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider">Description</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider">Category</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider text-center">Min. Threshold</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-stroke dark:divide-strokedark">
                    <tr class="hover:bg-slate-50 dark:hover:bg-meta-4/20 transition-colors">
                        <td class="px-6 py-4 text-xs font-medium text-slate-500">01</td>
                        <td class="px-6 py-4">
                            <span class="font-mono font-bold text-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 dark:text-indigo-400 px-2 py-1 rounded text-xs">
                                SAP-NOZ-YMH01
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-800 dark:text-white uppercase tracking-tight">Nozzle Yamaha Z1 Type 221</span>
                                <span class="text-[10px] text-slate-400 uppercase italic">Used in Line 1, 2, 4</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-[10px] font-black uppercase px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded text-slate-600 dark:text-slate-400">
                                Nozzle
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400 px-3 py-1 rounded-full font-black text-xs">
                                10 PCS
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-3">
                                <button class="text-slate-400 hover:text-primary transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button class="text-slate-400 hover:text-rose-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="flex items-center justify-between px-6 py-4 bg-slate-50/50 dark:bg-slate-800/30">
            <span class="text-xs text-slate-500 font-medium italic">Showing 1 to 10 of 50 entries</span>
            <div class="flex gap-1">
                <button class="px-3 py-1 text-xs font-bold rounded bg-white border border-stroke dark:bg-boxdark dark:border-strokedark disabled:opacity-50">Prev</button>
                <button class="px-3 py-1 text-xs font-bold rounded bg-indigo-600 text-white shadow-sm">1</button>
                <button class="px-3 py-1 text-xs font-bold rounded bg-white border border-stroke dark:bg-boxdark dark:border-strokedark">2</button>
                <button class="px-3 py-1 text-xs font-bold rounded bg-white border border-stroke dark:bg-boxdark dark:border-strokedark">Next</button>
            </div>
        </div>
    </div>
</div>
@endsection