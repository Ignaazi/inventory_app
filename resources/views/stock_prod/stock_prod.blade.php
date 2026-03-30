@extends('admin')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white uppercase tracking-tight">Production Stock Inventory</h2>
            <p class="text-xs text-bodydark2 uppercase font-bold tracking-widest mt-1">Items currently held by Production Dept</p>
        </div>
        <div class="flex gap-3">
             <button class="bg-white border border-stroke dark:border-strokedark dark:bg-boxdark text-slate-600 dark:text-white px-5 py-2.5 rounded-xl font-black text-xs uppercase shadow-sm hover:bg-slate-50 transition-all flex items-center gap-2 font-mono">
                <i data-feather="download" class="w-4 h-4"></i>
                Export Report
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-indigo-600 p-5 rounded-2xl shadow-md text-white">
            <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80">Active Parts on Line</p>
            <h3 class="text-3xl font-bold mt-1 tracking-tight">128 <span class="text-sm font-normal opacity-70 italic">PCS</span></h3>
        </div>
        <div class="bg-white dark:bg-boxdark p-5 rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Last Receipt From Eng</p>
            <h3 class="text-3xl font-bold text-slate-800 dark:text-white mt-1 tracking-tight italic">#REQ-2026-001</h3>
        </div>
    </div>

    <div class="rounded-2xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark overflow-hidden mb-10">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-left">
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider">SAP Code</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider">Part Description</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider text-center">In-Hand Stock</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-stroke dark:divide-strokedark font-medium">
                    <tr class="hover:bg-slate-50 dark:hover:bg-meta-4/20 transition-colors">
                        <td class="px-6 py-4 font-mono font-bold text-indigo-600">SAP-NOZ-YMH221</td>
                        <td class="px-6 py-4 uppercase">
                            <span class="font-bold text-slate-800 dark:text-white tracking-tight">Nozzle Yamaha Type 221</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-black text-lg text-slate-800 dark:text-white">10</span>
                            <span class="text-[10px] text-slate-400 font-black tracking-widest uppercase ml-1">PCS</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-indigo-100 text-indigo-600 px-3 py-1 rounded-full font-black text-[9px] uppercase tracking-widest">In Use</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button title="Return to Eng" class="p-2 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-lg transition-all shadow-sm">
                                <i data-feather="corner-up-left" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endsection