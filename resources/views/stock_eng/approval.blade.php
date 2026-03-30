@extends('admin')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white uppercase tracking-tight">Approval Request</h2>
            <p class="text-xs text-bodydark2 uppercase font-bold tracking-widest mt-1">Pending Requests from Production Dept</p>
        </div>
        <div class="flex gap-2">
            <span class="bg-indigo-100 text-indigo-600 px-4 py-2 rounded-lg font-black text-xs uppercase shadow-sm flex items-center gap-2">
                <div class="w-2 h-2 bg-indigo-600 rounded-full animate-pulse"></div>
                5 Pending Requests
            </span>
        </div>
    </div>

    <div class="rounded-2xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark overflow-hidden">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-left">
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider">Req ID</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider">Date</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider">From Line</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider">Part Requested</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider text-center">Qty</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-bodydark2 tracking-wider text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-stroke dark:divide-strokedark">
                    <tr class="hover:bg-slate-50 dark:hover:bg-meta-4/20 transition-colors">
                        <td class="px-6 py-4 font-bold text-slate-800 dark:text-white">#PRD-9921</td>
                        <td class="px-6 py-4 text-xs">30 Mar 2026, 14:20</td>
                        <td class="px-6 py-4">
                            <span class="font-black text-xs uppercase bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-slate-600 dark:text-slate-400">Line 04 - SMT</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-800 dark:text-white uppercase tracking-tight text-xs">Nozzle Yamaha Type 221</span>
                                <span class="text-[10px] font-mono text-indigo-500">SAP-NOZ-001</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center font-black">2 PCS</td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-warning/10 text-warning px-3 py-1 rounded-full font-black text-[10px] uppercase tracking-widest">Pending</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button title="Approve" class="p-2 bg-success/10 text-success hover:bg-success hover:text-white rounded-lg transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                <button title="Reject" class="p-2 bg-danger/10 text-danger hover:bg-danger hover:text-white rounded-lg transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection