@extends('admin')

@section('content')
<div class="p-4 md:p-6 lg:p-8">
    <div class="mb-6 border-b border-stroke dark:border-strokedark pb-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <div class="w-2 h-6 bg-emerald-600"></div>
                <h2 class="text-xl md:text-2xl font-black text-slate-800 dark:text-white uppercase tracking-tighter">Material Received Log</h2>
            </div>
            <p class="text-[10px] font-black text-slate-500 dark:text-bodydark2 uppercase tracking-widest ml-5">Costing Section • Arrival Monitoring</p>
        </div>
    </div>

    <div class="rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="hidden md:block">
            <div class="max-w-full overflow-x-auto">
                <table class="w-full table-auto text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-stroke dark:border-strokedark">
                            <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest">Arrival Date</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest">Description</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest text-center">Qty Recv</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y divide-stroke dark:divide-strokedark font-medium">
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-meta-4/10 transition-all">
                            <td class="px-6 py-5 font-black text-slate-800 dark:text-white">30 MAR 2026</td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="font-black text-slate-800 dark:text-white uppercase text-xs">Nozzle Yamaha Type 221</span>
                                    <span class="text-[9px] font-black text-indigo-600 uppercase mt-0.5 tracking-tighter">PO-REF: 99122-SMT</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 rounded border border-stroke dark:border-strokedark font-black">2 PCS</span>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span class="bg-emerald-500/10 text-emerald-600 px-3 py-1.5 rounded font-black text-[10px] uppercase tracking-widest border border-emerald-500/20">Arrived</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="md:hidden divide-y divide-stroke dark:divide-strokedark">
            <div class="p-5 flex justify-between items-start">
                <div>
                    <h4 class="font-black text-slate-800 dark:text-white uppercase text-sm">Nozzle Yamaha 221</h4>
                    <p class="text-[9px] font-bold text-slate-400 uppercase mt-1">Recv: 30 Mar 2026 • PO-99122</p>
                    <span class="inline-block mt-2 bg-emerald-500 text-white px-3 py-1 rounded font-black text-[9px] uppercase">Arrived</span>
                </div>
                <span class="bg-slate-900 text-white px-3 py-1 rounded font-black text-[10px]">2 PCS</span>
            </div>
        </div>
    </div>
</div>
@endsection