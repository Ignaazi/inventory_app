@extends('admin')

@section('content')
<div class="mx-auto w-full max-w-7xl pb-12 px-4 sm:px-6">
    
    @if(session('success'))
    <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-900 text-green-800 dark:text-green-400 shadow-sm flex items-center gap-3">
        <span class="w-2 h-2 rounded-full bg-green-500 shrink-0"></span>
        <div class="text-xs font-bold uppercase tracking-wide flex items-center gap-2">
            <span>SYSTEM NOTIFICATION:</span>
            <span class="font-bold text-slate-900 dark:text-white normal-case">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900 dark:text-white uppercase tracking-tight flex items-center gap-2">
                List Sparepart Request
            </h2>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest mt-0.5">PT SIIX EMS KARAWANG</p>
        </div>
        <a href="{{ route('prod.request.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs uppercase py-2.5 px-4 rounded-xl shadow-md shadow-blue-500/20 hover:shadow-blue-500/30 transition-all active:scale-95 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create New Request
        </a>
    </div>

    <div class="bg-white dark:bg-boxdark border border-slate-200 dark:border-strokedark rounded-xl shadow-sm overflow-hidden">
        
        <div class="p-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-200 dark:border-strokedark bg-slate-50/50 dark:bg-slate-800/40">
            <div>
                <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                    Recent History Request
                </h3>
            </div>

            <div class="flex items-center gap-3 self-end sm:self-auto">
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-inner">
                    <button type="button" onclick="filterRequestTable('all', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 bg-white text-slate-900 shadow-sm dark:bg-slate-700 dark:text-white">
                        All
                    </button>
                    <button type="button" onclick="filterRequestTable('draft', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                        Draft
                    </button>
                    <button type="button" onclick="filterRequestTable('pending', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                        Pending
                    </button>
                    <button type="button" onclick="filterRequestTable('checked', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                        Checked
                    </button>
                    <button type="button" onclick="filterRequestTable('approved', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                        Approved
                    </button>
                    <button type="button" onclick="filterRequestTable('reject', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                        Reject
                    </button>
                    <button type="button" onclick="filterRequestTable('finished', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                        Finished
                    </button>
                </div>
            </div>
        </div>

        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto text-xs text-left border-collapse font-sans" id="request-table">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 uppercase text-[10px] font-bold tracking-wider border-b border-slate-200 dark:border-strokedark">
                        <th class="py-3 px-3 text-center w-12">No</th>
                        <th class="py-3 px-4">Req Sparepart ID</th>
                        <th class="py-3 px-4">NIK</th>
                        <th class="py-3 px-4">No Nozzle</th>
                        <th class="py-3 px-3 text-center">Qty Req</th>
                        <th class="py-3 px-4 text-center">Line</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Prod Sign</th>
                        <th class="py-3 px-4 text-center">Eng Sign</th> <th class="py-3 px-4">Remark</th>
                        <th class="py-3 px-4">Create At</th>
                        <th class="py-3 px-4">Update At</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 dark:divide-strokedark text-slate-900 dark:text-white font-bold">
                    @forelse($requests as $index => $req)
                    <tr class="request-row-item hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition-colors whitespace-nowrap">
                        
                        <td class="py-3.5 px-3 text-center text-slate-900 dark:text-white">
                            {{ $requests->firstItem() + $index }}
                        </td>

                        <td class="py-3.5 px-4 tracking-tight">
                            {{ $req->request_no }}
                        </td>
                        
                        <td class="py-3.5 px-4">
                            {{ $req->nik ?? '-' }}
                        </td>
                        
                        <td class="py-3.5 px-4">
                            {{ $req->sparepart_name }}
                        </td>
                        
                        <td class="py-3.5 px-3 text-center">
                            {{ $req->qty_req }} <span class="text-[10px] text-slate-500 dark:text-slate-400">Pcs</span>
                        </td>

                        <td class="py-3.5 px-4 text-center uppercase">
                            {{ $req->line_machine }}
                        </td>
                        
                        <td class="py-3.5 px-4 text-center status-container">
                            @if(str_contains(strtolower($req->status), 'draft'))
                                <span class="status-badge inline-flex items-center rounded-md bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide border border-amber-200 dark:border-amber-900/50">
                                    Draft
                                </span>
                            @elseif(str_contains(strtolower($req->status), 'pending'))
                                <span class="status-badge inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide border border-blue-200 dark:border-blue-900/50">
                                    Pending
                                </span>
                            @elseif(str_contains(strtolower($req->status), 'staff') || str_contains(strtolower($req->status), 'checked'))
                                <span class="status-badge inline-flex items-center rounded-md bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide border border-indigo-200 dark:border-indigo-900/50">
                                    Checked Staff
                                </span>
                            @elseif(str_contains(strtolower($req->status), 'approved'))
                                <span class="status-badge inline-flex items-center rounded-md bg-green-50 dark:bg-green-950/40 text-green-700 dark:text-green-400 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide border border-green-200 dark:border-green-900/50">
                                    Approved
                                </span>
                            @elseif(str_contains(strtolower($req->status), 'reject'))
                                <span class="status-badge inline-flex items-center rounded-md bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-400 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide border border-red-200 dark:border-red-900/50">
                                    Reject
                                </span>
                            @elseif(str_contains(strtolower($req->status), 'finished'))
                                <span class="status-badge inline-flex items-center rounded-md bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-400 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide border border-purple-200 dark:border-purple-900/50">
                                    Finished
                                </span>
                            @else
                                <span class="status-badge inline-flex items-center rounded-md bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide border border-slate-200 dark:border-slate-700">
                                    {{ $req->status }}
                                </span>
                            @endif
                        </td>

                        <td class="py-3.5 px-4 text-center">
                            @if($req->production_signature || $req->production_stamp)
                                <a href="{{ route('prod.request.preview', $req->id) }}" class="inline-flex items-center gap-1 bg-green-50 hover:bg-green-100 dark:bg-green-950/30 dark:hover:bg-green-900/40 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-900 font-bold text-[10px] py-0.5 px-2 rounded transition-all">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                                    Signed
                                </a>
                            @else
                                <span class="text-slate-400 dark:text-slate-500 italic text-[10px] font-medium">No Sign</span>
                            @endif
                        </td>

                        <td class="py-3.5 px-4 text-center">
                            @if($req->spv_signature)
                                <span class="inline-flex items-center rounded-md bg-green-100 dark:bg-green-950/40 text-green-800 dark:text-green-400 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide border border-green-200 dark:border-green-900/50">
                                    🟢 FULL APP
                                </span>
                            @elseif($req->staff_signature)
                                <span class="inline-flex items-center rounded-md bg-indigo-100 dark:bg-indigo-950/40 text-indigo-800 dark:text-indigo-400 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide border border-indigo-200 dark:border-indigo-900/50">
                                    🔵 BY STAFF
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 px-2 py-0.5 text-[10px] font-medium italic border border-slate-200 dark:border-slate-700">
                                    ⚪ WAITING
                                </span>
                            @endif
                        </td>

                        <td class="py-3.5 px-4 max-w-xs truncate">
                            {{ $req->remark ?? '-' }}
                        </td>

                        <td class="py-3.5 px-4 leading-normal">
                            <div>
                                {{ $req->created_at ? $req->created_at->format('d/m/y') : '-' }}
                            </div>
                            <div class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">
                                {{ $req->created_at ? $req->created_at->format('H:i') . ' WIB' : '' }}
                            </div>
                        </td>

                        <td class="py-3.5 px-4 leading-normal">
                            <div>
                                {{ $req->updated_at ? $req->updated_at->format('d/m/y') : '-' }}
                            </div>
                            <div class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">
                                {{ $req->updated_at ? $req->updated_at->format('H:i') . ' WIB' : '' }}
                            </div>
                        </td>
                        
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('prod.request.preview', $req->id) }}" class="w-7 h-7 inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-md shadow-blue-500/10 transition-all active:scale-90" title="Preview Form">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>

                                @if(str_contains(strtolower($req->status), 'draft'))
                                <a href="{{ route('prod.request.editDraft', $req->id) }}" class="w-7 h-7 inline-flex items-center justify-center bg-amber-500 hover:bg-amber-600 text-white rounded-lg shadow-md shadow-amber-500/10 transition-all active:scale-90" title="Edit Draft">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </a>
                                @endif

                                <form action="{{ route('prod.request.destroy', $req->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus request ini, coy?')" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-7 h-7 inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-md shadow-red-500/10 transition-all active:scale-90" title="Delete">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="text-center py-12 text-slate-400 dark:text-slate-500 font-medium italic">
                            <svg class="w-8 h-8 mx-auto mb-2 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 00-2 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Belum ada data request yang tercatat, coy.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($requests->hasPages())
        <div class="p-4 flex items-center justify-between border-t border-slate-200 dark:border-strokedark bg-slate-50/30 dark:bg-slate-800/10">
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                Showing {{ $requests->firstItem() }} to {{ $requests->lastItem() }} of {{ $requests->total() }} entries
            </p>
            <div class="custom-pagination text-xs">
                {{ $requests->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<script>
  function filterRequestTable(criteria, element) {
    const buttons = document.querySelectorAll('.filter-btn');
    buttons.forEach(btn => {
      btn.classList.remove('bg-white', 'text-slate-900', 'shadow-sm', 'dark:bg-slate-700', 'dark:text-white');
      btn.classList.add('text-slate-500', 'dark:text-slate-400', 'hover:text-slate-900', 'dark:hover:text-white');
    });

    if (element) {
      element.classList.remove('text-slate-500', 'dark:text-slate-400', 'hover:text-slate-900', 'dark:hover:text-white');
      element.classList.add('bg-white', 'text-slate-900', 'shadow-sm', 'dark:bg-slate-700', 'dark:text-white');
    }

    const rows = document.querySelectorAll('.request-row-item');
    rows.forEach(row => {
      if (criteria === 'all') {
        row.style.display = '';
        return;
      }

      const statusBadge = row.querySelector('.status-badge');
      if (statusBadge) {
        const statusText = statusBadge.textContent.trim().toLowerCase();
        
        // JIKA kriteria filter 'checked', tangkap baris yang bertuliskan 'checked staff'
        if (criteria === 'checked') {
          if (statusText.includes('checked') || statusText.includes('staff')) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
          return;
        }

        if (statusText.includes(criteria.toLowerCase())) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      }
    });
  }
</script>

<style>
  .custom-pagination nav svg {
    width: 14px;
    height: 14px;
    display: inline;
  }
  .custom-pagination nav div:first-child {
    display: none;
  }
</style>
@endsection