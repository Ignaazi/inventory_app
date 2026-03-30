@extends('admin')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white uppercase tracking-tight">Purchase Request</h2>
            <p class="text-xs text-bodydark2 uppercase font-bold tracking-widest mt-1">Request Sparepart Replenishment to Costing Dept</p>
        </div>
        <button class="bg-indigo-600 hover:bg-opacity-90 text-white px-6 py-3 rounded-xl font-black text-xs uppercase shadow-md transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create New PR
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total PR Month</p>
            <p class="text-2xl font-bold text-slate-800 dark:text-white">12</p>
        </div>
        <div class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
            <p class="text-[10px] font-black text-warning uppercase tracking-widest">On Process</p>
            <p class="text-2xl font-bold text-warning">4</p>
        </div>
        <div class="bg-white dark:bg-boxdark p-4 rounded-2xl border border-stroke dark:border-strokedark shadow-sm">
            <p class="text-[10px] font-black text-success uppercase tracking-widest">Completed</p>
            <p class="text-2xl font-bold text-success">8</p>
        </div>
    </div>

    <div class="rounded-2xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark overflow-hidden">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-left">
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider">PR Number</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider">Date</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider">Sparepart Name</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider text-center">Qty</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider">Priority</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-stroke dark:divide-strokedark">
                    <tr class="hover:bg-slate-50 dark:hover:bg-meta-4/20 transition-colors">
                        <td class="px-6 py-4 font-mono font-bold text-indigo-600 text-xs">PR-ENG-2026-001</td>
                        <td class="px-6 py-4 text-xs font-medium">30 Mar 2026</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-800 dark:text-white uppercase text-xs">Nozzle Yamaha Type 221</span>
                                <span class="text-[10px] text-slate-400">SAP-NOZ-001</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center font-black">50 PCS</td>
                        <td class="px-6 py-4">
                            <span class="text-[10px] font-black uppercase px-2 py-1 bg-rose-100 text-rose-600 rounded">Urgent</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-indigo-100 text-indigo-600 px-3 py-1 rounded-full font-black text-[10px] uppercase tracking-widest">Sent to Costing</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button class="p-2 text-slate-400 hover:text-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection