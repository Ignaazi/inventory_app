@extends('admin')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white uppercase tracking-tight">Stock In</h2>
            <p class="text-xs text-bodydark2 uppercase font-bold tracking-widest mt-1">Engineering Receiving Room</p>
        </div>
        <button class="bg-indigo-600 hover:bg-opacity-90 text-white px-5 py-2.5 rounded-xl font-black text-xs uppercase shadow-md transition-all flex items-center gap-2">
            <i data-feather="maximize"></i> Scan Barcode
        </button>
    </div>

    <div class="rounded-2xl border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-left">
                        <th class="px-4 py-4 text-[10px] font-black uppercase text-bodydark2">Date & Time</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase text-bodydark2">SAP Code</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase text-bodydark2">Part Name</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase text-bodydark2 text-center">Qty In</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase text-bodydark2">Status</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-stroke dark:divide-strokedark">
                    <tr class="hover:bg-slate-50 dark:hover:bg-meta-4/30">
                        <td class="px-4 py-4 font-medium italic text-xs text-bodydark2">29 Mar 2026, 14:20</td>
                        <td class="px-4 py-4 font-mono font-bold text-primary">A1-NOZ-001</td>
                        <td class="px-4 py-4 font-bold text-slate-800 dark:text-white uppercase">Nozzle Yamaha Z1</td>
                        <td class="px-4 py-4 text-center">
                            <span class="bg-emerald-100 text-success px-2 py-1 rounded-md font-black text-xs">+ 25</span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-[10px] font-black uppercase px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded">Received</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection