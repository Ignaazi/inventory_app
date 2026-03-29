@extends('admin')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white uppercase tracking-tight">Disposal Log</h2>
            <p class="text-xs text-bodydark2 uppercase font-bold tracking-widest mt-1">Broken / Scrapped Spareparts</p>
        </div>
        <button class="bg-indigo-600 hover:bg-opacity-90 text-white px-5 py-2.5 rounded-xl font-black text-xs uppercase shadow-md transition-all flex items-center gap-2">
            <i data-feather="maximize"></i> Scan Barcode
        </button>
    </div>

    <div class="rounded-2xl border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-left">
                        <th class="px-4 py-4 text-[10px] font-black uppercase text-bodydark2">Date Scrapped</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase text-bodydark2">Part Name</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase text-bodydark2">Damage Reason</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase text-bodydark2 text-center">Qty</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase text-bodydark2">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-4 italic text-xs">28 Mar 2026</td>
                        <td class="px-4 py-4 font-bold uppercase">Nozzle Tip 221-F</td>
                        <td class="px-4 py-4 text-xs text-danger font-medium uppercase italic">Bent / Broken Head</td>
                        <td class="px-4 py-4 text-center font-black">01</td>
                        <td class="px-4 py-4">
                            <button class="text-primary hover:underline text-[10px] font-black uppercase italic">Details</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection