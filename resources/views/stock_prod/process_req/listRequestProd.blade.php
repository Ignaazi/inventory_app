@extends('admin')

@section('content')
<div class="mx-auto w-full max-w-7xl pb-12 px-4 sm:px-6">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-extrabold text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-2">
                <span class="h-5 w-1.5 bg-blue-600 rounded-full"></span>
                List Sparepart Request
            </h2>
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">PT SIIX EMS KARAWANG (Production Dept)</p>
        </div>
        <a href="{{ route('prod.request.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase py-2.5 px-4 rounded-xl shadow-md shadow-blue-500/20 hover:shadow-blue-500/30 transition-all active:scale-95 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create New Request
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold text-sm shadow-sm flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white dark:bg-boxdark border border-stroke dark:border-strokedark rounded-xl shadow-md overflow-hidden">
        <div class="max-w-full overflow-x-auto p-4">
            <table class="w-full table-auto text-sm text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300 uppercase text-[11px] font-bold tracking-wider border-b border-stroke dark:border-strokedark">
                        <th class="py-3 px-4">Doc No</th>
                        <th class="py-3 px-4">Requestor</th>
                        <th class="py-3 px-4">Line Machine</th>
                        <th class="py-3 px-4">No Nozzle</th>
                        <th class="py-3 px-4">SAP Code</th>
                        <th class="py-3 px-4">Qty</th>
                        <th class="py-3 px-4">Status Document</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-strokedark">
                    @forelse($requests as $req)
                    <tr class="border-b border-stroke dark:border-strokedark hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="py-4 px-4 font-mono font-bold text-slate-800 dark:text-white">{{ $req->request_no }}</td>
                        
                        <td class="py-4 px-4 font-medium text-slate-700 dark:text-slate-300">{{ $req->requestor }}</td>
                        
                        <td class="py-4 px-4 font-bold text-blue-600 dark:text-blue-400">{{ $req->line_machine }}</td>
                        
                        <td class="py-4 px-4 text-slate-600 dark:text-slate-400">{{ $req->sparepart_name }}</td>
                        
                        <td class="py-4 px-4 font-mono text-xs text-slate-500">{{ $req->sap_code }}</td>
                        
                        <td class="py-4 px-4 font-extrabold text-slate-800 dark:text-white">{{ $req->qty_req }} <span class="text-xs font-normal text-slate-400">Pcs</span></td>
                        
                        <td class="py-4 px-4">
                            @if($req->status === 'Draft')
                                <span class="inline-flex items-center gap-1.5 rounded-md bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700 border border-amber-200 uppercase tracking-wider">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                    Draft
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-md bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700 border border-blue-200 uppercase tracking-wider">
                                    <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                    Pending Eng
                                </span>
                            @endif
                        </td>
                        
                        <td class="py-4 px-4 text-center">
                            @if($req->status === 'Draft')
                                <a href="{{ route('prod.request.edit_draft', $req->id) }}" class="inline-flex items-center gap-1 bg-amber-500 hover:bg-amber-600 text-white font-bold text-[10px] uppercase py-1.5 px-3 rounded-lg shadow-sm shadow-amber-500/10 hover:shadow-amber-500/20 transition-all active:scale-95">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit Draft
                                </a>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs text-slate-400 font-medium bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-md border border-slate-200 dark:border-slate-700">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    Locked
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-slate-400 italic font-medium bg-slate-50/50 dark:bg-slate-800/10">
                            <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Belum ada data request yang tercatat, coy.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection