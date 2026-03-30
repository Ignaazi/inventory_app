@extends('admin')

@section('content')
<div class="p-4 md:p-6 lg:p-8">
    <div class="mb-6 border-b border-stroke dark:border-strokedark pb-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <div class="w-2 h-6 bg-indigo-600"></div>
                <h2 class="text-xl md:text-2xl font-black text-slate-800 dark:text-white uppercase tracking-tighter">
                    Purchase Request Approval
                </h2>
            </div>
            <p class="text-[10px] font-black text-slate-500 dark:text-bodydark2 uppercase tracking-widest ml-5">
                Costing Section • Inventory Authorization
            </p>
        </div>
        <div class="ml-5 md:ml-0 bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 px-4 py-2 rounded shadow-sm">
            <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest flex items-center gap-2">
                <span class="w-2 h-2 bg-indigo-600 rounded-full"></span>
                5 Pending Requests
            </span>
        </div>
    </div>

    <div class="rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        
        <div class="hidden md:block">
            <div class="max-w-full overflow-x-auto">
                <table class="w-full table-auto text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-stroke dark:border-strokedark">
                            <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest">Req ID</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest">Date</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest">Description</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest text-center">Qty</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y divide-stroke dark:divide-strokedark">
                        {{-- @foreach($purchaseRequests as $pr) --}}
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-meta-4/10">
                            <td class="px-6 py-5 font-black text-slate-800 dark:text-white">#PR-2026-001</td>
                            <td class="px-6 py-5 text-slate-500 font-bold uppercase">30 Mar 2026</td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="font-black text-slate-800 dark:text-white uppercase text-xs">Nozzle Yamaha Type 221</span>
                                    <span class="text-[9px] font-black text-indigo-600 uppercase tracking-tighter mt-0.5">SAP: NOZ-092-SMT • Line 04</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 rounded border border-stroke dark:border-strokedark font-black text-slate-800 dark:text-white">2 PCS</span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('costing.incoming.action', 1) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <button class="px-4 py-2 bg-emerald-600 text-white font-black text-[10px] uppercase tracking-widest rounded shadow-sm hover:bg-emerald-700 transition-all">Approve</button>
                                    </form>
                                    <button class="px-4 py-2 border border-rose-600 text-rose-600 font-black text-[10px] uppercase tracking-widest rounded hover:bg-rose-600 hover:text-white transition-all">Reject</button>
                                </div>
                            </td>
                        </tr>
                        {{-- @endforeach --}}
                    </tbody>
                </table>
            </div>
        </div>

        <div class="md:hidden divide-y divide-stroke dark:divide-strokedark">
            {{-- @foreach($purchaseRequests as $pr) --}}
            <div class="p-5 space-y-4">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">#PR-2026-001</span>
                        <h4 class="font-black text-slate-800 dark:text-white uppercase text-sm mt-1">Nozzle Yamaha Type 221</h4>
                        <p class="text-[9px] font-bold text-slate-400 uppercase mt-1">30 Mar 2026 • Line 04</p>
                    </div>
                    <span class="bg-slate-900 text-white dark:bg-white dark:text-boxdark px-3 py-1 rounded font-black text-[10px]">2 PCS</span>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <form action="{{ route('costing.incoming.action', 1) }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" value="approve">
                        <button class="w-full py-3 bg-emerald-600 text-white font-black text-[10px] uppercase tracking-[0.2em] rounded shadow-lg shadow-emerald-500/20">Approve</button>
                    </form>
                    <button class="w-full py-3 border border-rose-600 text-rose-600 font-black text-[10px] uppercase tracking-[0.2em] rounded">Reject</button>
                </div>
            </div>
            {{-- @endforeach --}}
        </div>

        <div class="bg-slate-50 dark:bg-slate-800/20 px-6 py-4 border-t border-stroke dark:border-strokedark">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] text-center md:text-left">
                Authorized Personnel Only • Costing Output
            </p>
        </div>
    </div>
</div>
@endsection