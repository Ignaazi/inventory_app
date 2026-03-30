@extends('admin')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white uppercase tracking-tight">List Sparepart Production</h2>
            <p class="text-xs text-bodydark2 uppercase font-bold tracking-widest mt-1">Available Items in Engineering Stock</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('prod.overview') }}" class="bg-white border border-stroke dark:border-strokedark dark:bg-boxdark text-slate-600 dark:text-white px-5 py-2.5 rounded-xl font-black text-xs uppercase shadow-sm hover:bg-slate-50 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Overview
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
        <div class="relative w-full">
            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" placeholder="Search sparepart by name or SAP code..." class="w-full pl-11 pr-5 py-3 rounded-xl border border-stroke bg-slate-50 focus:bg-white focus:border-primary focus-visible:outline-none dark:border-strokedark dark:bg-meta-4 dark:text-white text-sm transition-all">
        </div>
    </div>

    <div class="rounded-2xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark overflow-hidden mb-10">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-left">
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider">SAP Code</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider">Part Description</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider text-center">Current Stock</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider text-center">Unit</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-stroke dark:divide-strokedark font-medium">
                    <tr class="hover:bg-slate-50 dark:hover:bg-meta-4/20 transition-colors">
                        <td class="px-6 py-4 font-mono font-bold text-indigo-600 font-bold">SAP-NOZ-YMH221</td>
                        <td class="px-6 py-4 uppercase">
                            <span class="font-bold text-slate-800 dark:text-white tracking-tight">Nozzle Yamaha Type 221</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-success/10 text-success px-3 py-1 rounded-full font-black text-xs">
                                45
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-slate-500 uppercase text-[10px] font-black tracking-widest">PCS</td>
                        <td class="px-6 py-4 text-center">
                            <button class="bg-primary hover:bg-opacity-90 text-white px-4 py-1.5 rounded-lg font-black text-[10px] uppercase transition-all shadow-md">
                                Request Part
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection