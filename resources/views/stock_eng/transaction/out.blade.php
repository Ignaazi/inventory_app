@extends('admin')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white uppercase tracking-tight">Stock Out</h2>
            <p class="text-xs text-bodydark2 uppercase font-bold tracking-widest mt-1">Engineering to Production Line</p>
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
                        <th class="px-4 py-4 text-[10px] font-black uppercase text-bodydark2">Timestamp</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase text-bodydark2">Ref Number</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase text-bodydark2">Part Details</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase text-bodydark2 text-center">Qty Out</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase text-bodydark2">Destination</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-stroke dark:divide-strokedark">
                    <tr class="hover:bg-slate-50 dark:hover:bg-meta-4/30">
                        <td class="px-4 py-4 font-medium italic text-xs text-bodydark2">29 Mar 2026, 16:05</td>
                        <td class="px-4 py-4 font-mono font-bold">OUT-ENG-0992</td>
                        <td class="px-4 py-4 font-bold text-slate-800 dark:text-white uppercase">Feeder 8mm Gold</td>
                        <td class="px-4 py-4 text-center">
                            <span class="bg-rose-100 text-danger px-2 py-1 rounded-md font-black text-xs">- 02</span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-[10px] font-black uppercase px-2 py-1 bg-primary/10 text-primary rounded">Line 04-SMT</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection